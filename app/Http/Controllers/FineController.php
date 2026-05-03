<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function index(Request $request)
    {
        $query = Fine::with(['user', 'samity']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$s%"))
                  ->orWhere('reason', 'like', "%$s%");
            });
        }
        if ($request->filled('samity_id')) { $query->where('samity_id', $request->samity_id); }
        if ($request->filled('status'))    { $query->where('status', $request->status); }
        if ($request->filled('from'))      { $query->whereDate('fine_date', '>=', $request->from); }
        if ($request->filled('to'))        { $query->whereDate('fine_date', '<=', $request->to); }

        $fines    = $query->latest('fine_date')->paginate(10)->withQueryString();
        $samities = Samity::where('is_active', true)->orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total_amount'   => Fine::sum('amount'),
            'total_count'    => Fine::count(),
            'paid_amount'    => Fine::where('status', 'paid')->sum('amount'),
            'pending_amount' => Fine::where('status', 'pending')->sum('amount'),
        ];

        return view('fines.index', compact('fines', 'samities', 'users', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'samity_id' => ['required', 'exists:samities,id'],
            'user_id'   => ['required', 'exists:users,id'],
            'reason'    => ['required', 'string', 'max:255'],
            'amount'    => ['required', 'numeric', 'min:0.01'],
            'fine_date' => ['required', 'date'],
            'status'    => ['required', 'in:pending,paid,waived'],
            'note'      => ['nullable', 'string'],
        ]);

        Fine::create($request->only(['samity_id', 'user_id', 'reason', 'amount', 'fine_date', 'status', 'note']));

        return redirect()->route('fines.index')->with('success', 'Fine added successfully.');
    }

    public function edit(Fine $fine)
    {
        return redirect()->route('fines.index');
    }

    public function update(Request $request, Fine $fine)
    {
        $request->validate([
            'samity_id' => ['required', 'exists:samities,id'],
            'user_id'   => ['required', 'exists:users,id'],
            'reason'    => ['required', 'string', 'max:255'],
            'amount'    => ['required', 'numeric', 'min:0.01'],
            'fine_date' => ['required', 'date'],
            'status'    => ['required', 'in:pending,paid,waived'],
            'note'      => ['nullable', 'string'],
        ]);

        $fine->update($request->only(['samity_id', 'user_id', 'reason', 'amount', 'fine_date', 'status', 'note']));

        return redirect()->route('fines.index')->with('success', 'Fine updated successfully.');
    }

    public function destroy(Fine $fine)
    {
        $fine->delete();

        return redirect()->route('fines.index')->with('success', 'Fine deleted successfully.');
    }
}
