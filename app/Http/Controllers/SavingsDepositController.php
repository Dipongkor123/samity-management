<?php

namespace App\Http\Controllers;

use App\Models\SavingsDeposit;
use App\Models\SavingsPlan;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class SavingsDepositController extends Controller
{
    public function index(Request $request)
    {
        $query = SavingsDeposit::with(['user', 'samity', 'plan']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$s%"))
                  ->orWhereHas('samity', fn($sm) => $sm->where('name', 'like', "%$s%"))
                  ->orWhere('receipt_number', 'like', "%$s%");
            });
        }
        if ($request->filled('samity_id')) { $query->where('samity_id', $request->samity_id); }
        if ($request->filled('status'))    { $query->where('status', $request->status); }
        if ($request->filled('from'))      { $query->whereDate('deposit_date', '>=', $request->from); }
        if ($request->filled('to'))        { $query->whereDate('deposit_date', '<=', $request->to); }

        $deposits = $query->latest('deposit_date')->paginate(10)->withQueryString();
        $samities = Samity::where('is_active', true)->orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();
        $plans    = SavingsPlan::with(['user', 'samity'])->where('status', 'active')->orderBy('id', 'desc')->get();

        $stats = [
            'total_amount' => SavingsDeposit::sum('amount'),
            'total_count'  => SavingsDeposit::count(),
            'this_month'   => SavingsDeposit::whereMonth('deposit_date', now()->month)->whereYear('deposit_date', now()->year)->sum('amount'),
            'pending'      => SavingsDeposit::where('status', 'pending')->count(),
        ];

        return view('savings.deposits', compact('deposits', 'samities', 'users', 'plans', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'savings_plan_id' => ['required', 'exists:savings_plans,id'],
            'samity_id'       => ['required', 'exists:samities,id'],
            'user_id'         => ['required', 'exists:users,id'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'deposit_date'    => ['required', 'date'],
            'status'          => ['required', 'in:paid,pending'],
            'receipt_number'  => ['nullable', 'string', 'max:100'],
            'note'            => ['nullable', 'string'],
        ]);

        if (empty($data['receipt_number'])) {
            $data['receipt_number'] = 'SVD-' . date('Ymd') . '-' . str_pad(SavingsDeposit::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        SavingsDeposit::create($data);

        return redirect()->route('savings.deposits.index')->with('success', 'Savings deposit recorded successfully.');
    }

    public function update(Request $request, SavingsDeposit $deposit)
    {
        $data = $request->validate([
            'savings_plan_id' => ['required', 'exists:savings_plans,id'],
            'samity_id'       => ['required', 'exists:samities,id'],
            'user_id'         => ['required', 'exists:users,id'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'deposit_date'    => ['required', 'date'],
            'status'          => ['required', 'in:paid,pending'],
            'receipt_number'  => ['nullable', 'string', 'max:100'],
            'note'            => ['nullable', 'string'],
        ]);

        $deposit->update($data);

        return redirect()->route('savings.deposits.index')->with('success', 'Savings deposit updated successfully.');
    }

    public function destroy(SavingsDeposit $deposit)
    {
        $deposit->delete();

        return redirect()->route('savings.deposits.index')->with('success', 'Savings deposit deleted successfully.');
    }
}
