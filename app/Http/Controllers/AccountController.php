<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::with(['user', 'samity']);

        if ($request->filled('type'))     { $query->where('type', $request->type); }
        if ($request->filled('category')) { $query->where('category', $request->category); }
        if ($request->filled('from'))     { $query->whereDate('transaction_date', '>=', $request->from); }
        if ($request->filled('to'))       { $query->whereDate('transaction_date', '<=', $request->to); }

        $entries  = $query->latest('transaction_date')->paginate(20)->withQueryString();
        $samities = Samity::where('is_active', true)->orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();

        $totalIncome  = Account::where('type', 'income')->sum('amount');
        $totalExpense = Account::where('type', 'expense')->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        // This-month figures
        $monthIncome  = Account::where('type', 'income')
                                ->whereMonth('transaction_date', now()->month)
                                ->whereYear('transaction_date', now()->year)
                                ->sum('amount');
        $monthExpense = Account::where('type', 'expense')
                                ->whereMonth('transaction_date', now()->month)
                                ->whereYear('transaction_date', now()->year)
                                ->sum('amount');

        $categories = Account::categories();

        return view('accounts.index', compact(
            'entries', 'samities', 'users',
            'totalIncome', 'totalExpense', 'balance',
            'monthIncome', 'monthExpense', 'categories'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'             => ['required', 'in:income,expense'],
            'category'         => ['required', 'string', 'max:100'],
            'reference'        => ['nullable', 'string', 'max:100'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'description'      => ['nullable', 'string', 'max:500'],
            'user_id'          => ['nullable', 'exists:users,id'],
            'samity_id'        => ['nullable', 'exists:samities,id'],
        ]);

        Account::create($data);

        return redirect()->route('accounts.index')->with('success', 'Transaction recorded successfully.');
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'type'             => ['required', 'in:income,expense'],
            'category'         => ['required', 'string', 'max:100'],
            'reference'        => ['nullable', 'string', 'max:100'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'description'      => ['nullable', 'string', 'max:500'],
            'user_id'          => ['nullable', 'exists:users,id'],
            'samity_id'        => ['nullable', 'exists:samities,id'],
        ]);

        $account->update($data);

        return redirect()->route('accounts.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Transaction deleted.');
    }
}
