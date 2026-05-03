<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'samity', 'repayments']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$s%"));
        }
        if ($request->filled('samity_id')) { $query->where('samity_id', $request->samity_id); }
        if ($request->filled('status'))    { $query->where('status', $request->status); }
        if ($request->filled('from'))      { $query->whereDate('issue_date', '>=', $request->from); }
        if ($request->filled('to'))        { $query->whereDate('issue_date', '<=', $request->to); }

        $loans    = $query->latest('issue_date')->paginate(10)->withQueryString();
        $samities = Samity::where('is_active', true)->orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total_amount'    => Loan::sum('amount'),
            'total_count'     => Loan::count(),
            'active_count'    => Loan::where('status', 'active')->count(),
            'active_amount'   => Loan::where('status', 'active')->sum('amount'),
            'completed_count' => Loan::where('status', 'completed')->count(),
            'overdue_count'   => Loan::where('status', 'overdue')->count(),
        ];

        return view('loans.index', compact('loans', 'samities', 'users', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'samity_id'           => ['required', 'exists:samities,id'],
            'user_id'             => ['required', 'exists:users,id'],
            'amount'              => ['required', 'numeric', 'min:0.01'],
            'interest_rate'       => ['required', 'numeric', 'min:0'],
            'duration_months'     => ['required', 'integer', 'min:1'],
            'monthly_installment' => ['required', 'numeric', 'min:0'],
            'issue_date'          => ['required', 'date'],
            'due_date'            => ['nullable', 'date', 'after:issue_date'],
            'purpose'             => ['nullable', 'string', 'max:255'],
            'status'              => ['required', 'in:active,completed,overdue'],
        ]);

        if (empty($data['due_date'])) {
            $data['due_date'] = \Carbon\Carbon::parse($data['issue_date'])
                                              ->addMonths((int) $data['duration_months'])
                                              ->toDateString();
        }

        Loan::create($data);

        return redirect()->route('loans.index')->with('success', 'Loan issued successfully.');
    }

    public function edit(Loan $loan)
    {
        return redirect()->route('loans.index');
    }

    public function update(Request $request, Loan $loan)
    {
        $data = $request->validate([
            'samity_id'           => ['required', 'exists:samities,id'],
            'user_id'             => ['required', 'exists:users,id'],
            'amount'              => ['required', 'numeric', 'min:0.01'],
            'interest_rate'       => ['required', 'numeric', 'min:0'],
            'duration_months'     => ['required', 'integer', 'min:1'],
            'monthly_installment' => ['required', 'numeric', 'min:0'],
            'issue_date'          => ['required', 'date'],
            'due_date'            => ['nullable', 'date'],
            'purpose'             => ['nullable', 'string', 'max:255'],
            'status'              => ['required', 'in:active,completed,overdue'],
        ]);

        if (empty($data['due_date'])) {
            $data['due_date'] = \Carbon\Carbon::parse($data['issue_date'])
                                              ->addMonths((int) $data['duration_months'])
                                              ->toDateString();
        }

        $loan->update($data);

        return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');
    }

    public function destroy(Loan $loan)
    {
        if ($loan->repayments()->count() > 0) {
            return redirect()->route('loans.index')->with('error', 'Cannot delete a loan that has repayments recorded.');
        }

        $loan->delete();

        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully.');
    }
}
