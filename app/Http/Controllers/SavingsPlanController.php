<?php

namespace App\Http\Controllers;

use App\Models\SavingsPlan;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class SavingsPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = SavingsPlan::with(['user', 'samity']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$s%"))
                  ->orWhereHas('samity', fn($sm) => $sm->where('name', 'like', "%$s%"));
            });
        }
        if ($request->filled('samity_id')) { $query->where('samity_id', $request->samity_id); }
        if ($request->filled('plan_type'))  { $query->where('plan_type', $request->plan_type); }
        if ($request->filled('status'))     { $query->where('status', $request->status); }

        $plans    = $query->latest()->paginate(10)->withQueryString();
        $samities = Samity::where('is_active', true)->orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total'         => SavingsPlan::count(),
            'active'        => SavingsPlan::where('status', 'active')->count(),
            'weekly'        => SavingsPlan::where('plan_type', 'weekly')->count(),
            'monthly'       => SavingsPlan::where('plan_type', 'monthly')->count(),
        ];

        return view('savings.plans', compact('plans', 'samities', 'users', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'samity_id'      => ['required', 'exists:samities,id'],
            'user_id'        => ['required', 'exists:users,id'],
            'plan_type'      => ['required', 'in:weekly,monthly'],
            'regular_amount' => ['required', 'numeric', 'min:0.01'],
            'target_amount'  => ['nullable', 'numeric', 'min:0.01'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['nullable', 'date', 'after:start_date'],
            'status'         => ['required', 'in:active,closed'],
            'note'           => ['nullable', 'string'],
        ]);

        SavingsPlan::create($data);

        return redirect()->route('savings.plans.index')->with('success', 'Savings plan created successfully.');
    }

    public function update(Request $request, SavingsPlan $plan)
    {
        $data = $request->validate([
            'samity_id'      => ['required', 'exists:samities,id'],
            'user_id'        => ['required', 'exists:users,id'],
            'plan_type'      => ['required', 'in:weekly,monthly'],
            'regular_amount' => ['required', 'numeric', 'min:0.01'],
            'target_amount'  => ['nullable', 'numeric', 'min:0.01'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['nullable', 'date', 'after:start_date'],
            'status'         => ['required', 'in:active,closed'],
            'note'           => ['nullable', 'string'],
        ]);

        $plan->update($data);

        return redirect()->route('savings.plans.index')->with('success', 'Savings plan updated successfully.');
    }

    public function destroy(SavingsPlan $plan)
    {
        if ($plan->deposits()->exists() || $plan->withdrawals()->exists()) {
            return redirect()->route('savings.plans.index')
                ->with('error', 'Cannot delete plan with existing deposits or withdrawals.');
        }

        $plan->delete();

        return redirect()->route('savings.plans.index')->with('success', 'Savings plan deleted successfully.');
    }
}
