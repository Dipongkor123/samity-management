<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Http\Request;

class RepaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = LoanRepayment::with(['loan.user', 'loan.samity']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('loan.user', fn($q) => $q->where('name', 'like', "%$s%"));
        }
        if ($request->filled('loan_id')) { $query->where('loan_id', $request->loan_id); }
        if ($request->filled('from'))    { $query->whereDate('paid_date', '>=', $request->from); }
        if ($request->filled('to'))      { $query->whereDate('paid_date', '<=', $request->to); }

        $repayments = $query->latest('paid_date')->paginate(10)->withQueryString();
        $loans      = Loan::with('user')->orderByDesc('issue_date')->get();

        $stats = [
            'total_paid'      => LoanRepayment::sum('amount_paid'),
            'total_principal' => LoanRepayment::sum('principal'),
            'total_interest'  => LoanRepayment::sum('interest'),
            'this_month'      => LoanRepayment::whereMonth('paid_date', now()->month)->whereYear('paid_date', now()->year)->sum('amount_paid'),
        ];

        return view('repayments.index', compact('repayments', 'loans', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'loan_id'     => ['required', 'exists:loans,id'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'principal'   => ['required', 'numeric', 'min:0'],
            'interest'    => ['required', 'numeric', 'min:0'],
            'paid_date'   => ['required', 'date'],
            'note'        => ['nullable', 'string'],
        ]);

        LoanRepayment::create($data);

        // Auto-update loan status if fully paid
        $loan = Loan::find($data['loan_id']);
        if ($loan && $loan->remainingBalance() <= 0) {
            $loan->update(['status' => 'completed']);
        }

        return redirect()->route('repayments.index')->with('success', 'Repayment recorded successfully.');
    }

    public function edit(LoanRepayment $repayment)
    {
        return redirect()->route('repayments.index');
    }

    public function update(Request $request, LoanRepayment $repayment)
    {
        $data = $request->validate([
            'loan_id'     => ['required', 'exists:loans,id'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'principal'   => ['required', 'numeric', 'min:0'],
            'interest'    => ['required', 'numeric', 'min:0'],
            'paid_date'   => ['required', 'date'],
            'note'        => ['nullable', 'string'],
        ]);

        $repayment->update($data);

        return redirect()->route('repayments.index')->with('success', 'Repayment updated successfully.');
    }

    public function destroy(LoanRepayment $repayment)
    {
        $repayment->delete();

        return redirect()->route('repayments.index')->with('success', 'Repayment deleted successfully.');
    }
}
