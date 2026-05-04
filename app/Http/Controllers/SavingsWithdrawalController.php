<?php

namespace App\Http\Controllers;

use App\Models\SavingsWithdrawal;
use App\Models\SavingsPlan;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class SavingsWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = SavingsWithdrawal::with(['user', 'samity', 'plan']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$s%"))
                  ->orWhereHas('samity', fn($sm) => $sm->where('name', 'like', "%$s%"));
            });
        }
        if ($request->filled('samity_id')) { $query->where('samity_id', $request->samity_id); }
        if ($request->filled('status'))    { $query->where('status', $request->status); }
        if ($request->filled('from'))      { $query->whereDate('withdrawal_date', '>=', $request->from); }
        if ($request->filled('to'))        { $query->whereDate('withdrawal_date', '<=', $request->to); }

        $withdrawals = $query->latest('withdrawal_date')->paginate(10)->withQueryString();
        $samities    = Samity::where('is_active', true)->orderBy('name')->get();
        $users       = User::where('is_active', true)->orderBy('name')->get();
        $plans       = SavingsPlan::with(['user', 'samity'])->where('status', 'active')->orderBy('id', 'desc')->get();

        $stats = [
            'total_amount' => SavingsWithdrawal::where('status', 'approved')->sum('amount'),
            'total_count'  => SavingsWithdrawal::count(),
            'this_month'   => SavingsWithdrawal::where('status', 'approved')->whereMonth('withdrawal_date', now()->month)->whereYear('withdrawal_date', now()->year)->sum('amount'),
            'pending'      => SavingsWithdrawal::where('status', 'pending')->count(),
        ];

        return view('savings.withdrawals', compact('withdrawals', 'samities', 'users', 'plans', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'savings_plan_id' => ['required', 'exists:savings_plans,id'],
            'samity_id'       => ['required', 'exists:samities,id'],
            'user_id'         => ['required', 'exists:users,id'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'withdrawal_date' => ['required', 'date'],
            'status'          => ['required', 'in:approved,pending,rejected'],
            'reason'          => ['nullable', 'string'],
            'note'            => ['nullable', 'string'],
        ]);

        SavingsWithdrawal::create($data);

        return redirect()->route('savings.withdrawals.index')->with('success', 'Withdrawal recorded successfully.');
    }

    public function update(Request $request, SavingsWithdrawal $withdrawal)
    {
        $data = $request->validate([
            'savings_plan_id' => ['required', 'exists:savings_plans,id'],
            'samity_id'       => ['required', 'exists:samities,id'],
            'user_id'         => ['required', 'exists:users,id'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'withdrawal_date' => ['required', 'date'],
            'status'          => ['required', 'in:approved,pending,rejected'],
            'reason'          => ['nullable', 'string'],
            'note'            => ['nullable', 'string'],
        ]);

        $withdrawal->update($data);

        return redirect()->route('savings.withdrawals.index')->with('success', 'Withdrawal updated successfully.');
    }

    public function destroy(SavingsWithdrawal $withdrawal)
    {
        $withdrawal->delete();

        return redirect()->route('savings.withdrawals.index')->with('success', 'Withdrawal deleted successfully.');
    }
}
