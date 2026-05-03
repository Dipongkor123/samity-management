<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_members'      => User::count(),
            'active_members'     => User::where('is_active', true)->count(),
            'total_samities'     => Samity::count(),
            'active_samities'    => Samity::where('is_active', true)->count(),
            'total_deposits'     => Deposit::sum('amount'),
            'this_month_deposits'=> Deposit::whereMonth('deposit_date', now()->month)->whereYear('deposit_date', now()->year)->sum('amount'),
            'total_loans'        => Loan::sum('amount'),
            'active_loans'       => Loan::where('status', 'active')->sum('amount'),
            'total_repaid'       => LoanRepayment::sum('amount_paid'),
            'total_fines'        => Fine::sum('amount'),
            'pending_fines'      => Fine::where('status', 'pending')->sum('amount'),
            'collected_fines'    => Fine::where('status', 'paid')->sum('amount'),
        ];

        // Monthly deposits for the last 6 months
        $monthlyDeposits = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month'  => $date->format('M Y'),
                'amount' => (float) Deposit::whereMonth('deposit_date', $date->month)
                                           ->whereYear('deposit_date', $date->year)
                                           ->sum('amount'),
            ];
        });

        // Monthly loan issuance for last 6 months
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
}
