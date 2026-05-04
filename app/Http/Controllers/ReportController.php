<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_members'       => User::count(),
            'active_members'      => User::where('is_active', true)->count(),
            'total_samities'      => Samity::count(),
            'active_samities'     => Samity::where('is_active', true)->count(),
            'total_deposits'      => Deposit::sum('amount'),
            'this_month_deposits' => Deposit::whereMonth('deposit_date', now()->month)->whereYear('deposit_date', now()->year)->sum('amount'),
            'total_loans'         => Loan::sum('amount'),
            'active_loans'        => Loan::where('status', 'active')->sum('amount'),
            'total_repaid'        => LoanRepayment::sum('amount_paid'),
            'total_fines'         => Fine::sum('amount'),
            'pending_fines'       => Fine::where('status', 'pending')->sum('amount'),
            'collected_fines'     => Fine::where('status', 'paid')->sum('amount'),
        ];

        $monthlyDeposits = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month'  => $date->format('M Y'),
                'amount' => (float) Deposit::whereMonth('deposit_date', $date->month)
                                           ->whereYear('deposit_date', $date->year)
                                           ->sum('amount'),
            ];
        });

        $monthlyLoans = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month'  => $date->format('M Y'),
                'amount' => (float) Loan::whereMonth('issue_date', $date->month)
                                        ->whereYear('issue_date', $date->year)
                                        ->sum('amount'),
            ];
        });

        $samities = Samity::withCount('members')
                          ->withSum('deposits', 'amount')
                          ->withSum('loans', 'amount')
                          ->orderBy('name')
                          ->get();

        return view('reports.index', compact('stats', 'monthlyDeposits', 'monthlyLoans', 'samities'));
    }

    /**
     * Member report: detailed list with loan & deposit summary per member.
     */
    public function members(Request $request)
    {
        $query = User::where('is_staff', false)
                     ->withCount('loans')
                     ->withSum('deposits', 'amount')
                     ->withSum('loans', 'amount');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('phone', 'like', "%$s%"));
        }
        if ($request->filled('samity_id')) {
            $query->whereHas('samities', fn($q) => $q->where('samities.id', $request->samity_id));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $members  = $query->orderBy('name')->paginate(25)->withQueryString();
        $samities = Samity::where('is_active', true)->orderBy('name')->get();

        return view('reports.members', compact('members', 'samities'));
    }

    /**
     * Loan report: all loans with repayment progress.
     */
    public function loans(Request $request)
    {
        $query = Loan::with(['user', 'samity'])
                     ->withSum('repayments', 'amount_paid');

        if ($request->filled('samity_id'))  { $query->where('samity_id', $request->samity_id); }
        if ($request->filled('status'))     { $query->where('status', $request->status); }
        if ($request->filled('from'))       { $query->whereDate('issue_date', '>=', $request->from); }
        if ($request->filled('to'))         { $query->whereDate('issue_date', '<=', $request->to); }

        $loans    = $query->latest('issue_date')->paginate(25)->withQueryString();
        $samities = Samity::where('is_active', true)->orderBy('name')->get();

        $totals = [
            'disbursed' => Loan::sum('amount'),
            'repaid'    => LoanRepayment::sum('amount_paid'),
            'outstanding'=> Loan::where('status', 'active')->sum('amount'),
        ];

        return view('reports.loans', compact('loans', 'samities', 'totals'));
    }

    /**
     * Collection report: daily/period repayment collections.
     */
    public function collections(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to',   now()->toDateString());

        $query = LoanRepayment::with(['loan.user', 'loan.samity'])
                              ->whereDate('paid_date', '>=', $from)
                              ->whereDate('paid_date', '<=', $to);

        if ($request->filled('samity_id')) {
            $query->whereHas('loan', fn($q) => $q->where('samity_id', $request->samity_id));
        }

        $collections = $query->latest('paid_date')->paginate(30)->withQueryString();
        $samities    = Samity::where('is_active', true)->orderBy('name')->get();

        $totalCollected = LoanRepayment::whereDate('paid_date', '>=', $from)
                                        ->whereDate('paid_date', '<=', $to)
                                        ->sum('amount_paid');

        return view('reports.collections', compact('collections', 'samities', 'from', 'to', 'totalCollected'));
    }

    /**
     * Defaulter report: members with overdue installments.
     */
    public function defaulters(Request $request)
    {
        $query = Loan::with(['user', 'samity', 'schedules'])
                     ->where('status', 'active')
                     ->whereHas('schedules', fn($q) =>
                         $q->where('status', '!=', 'paid')->whereDate('due_date', '<', now())
                     );

        if ($request->filled('samity_id')) { $query->where('samity_id', $request->samity_id); }

        $defaulters = $query->orderBy('id')->paginate(25)->withQueryString();
        $samities   = Samity::where('is_active', true)->orderBy('name')->get();

        $defaulters->getCollection()->transform(function ($loan) {
            $loan->overdue_count = $loan->schedules
                ->where('status', '!=', 'paid')
                ->filter(fn($s) => $s->due_date->isPast())
                ->count();
            $loan->overdue_amount = $loan->schedules
                ->where('status', '!=', 'paid')
                ->filter(fn($s) => $s->due_date->isPast())
                ->sum('emi_amount');
            return $loan;
        });

        $totalDefaulters  = $defaulters->total();
        $totalOverdueAmt  = $defaulters->sum('overdue_amount');

        return view('reports.defaulters', compact('defaulters', 'samities', 'totalDefaulters', 'totalOverdueAmt'));
    }
}
