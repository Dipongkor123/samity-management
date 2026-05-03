<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $query = Deposit::with(['user', 'samity']);

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

        $stats = [
            'total_amount' => Deposit::sum('amount'),
            'total_count'  => Deposit::count(),
            'this_month'   => Deposit::whereMonth('deposit_date', now()->month)->whereYear('deposit_date', now()->year)->sum('amount'),
            'pending'      => Deposit::where('status', 'pending')->count(),
        ];

        return view('deposits.index', compact('deposits', 'samities', 'users', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'samity_id'      => ['required', 'exists:samities,id'],
            'user_id'        => ['required', 'exists:users,id'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'deposit_date'   => ['required', 'date'],
            'status'         => ['required', 'in:paid,pending'],
            'note'           => ['nullable', 'string'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
        ]);

        if (empty($data['receipt_number'])) {
            $data['receipt_number'] = 'RCP-' . date('Ymd') . '-' . str_pad(Deposit::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        Deposit::create($data);

        return redirect()->route('deposits.index')->with('success', 'Deposit recorded successfully.');
    }

    public function edit(Deposit $deposit)
    {
        return redirect()->route('deposits.index');
    }

    public function update(Request $request, Deposit $deposit)
    {
        $data = $request->validate([
            'samity_id'      => ['required', 'exists:samities,id'],
            'user_id'        => ['required', 'exists:users,id'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'deposit_date'   => ['required', 'date'],
            'status'         => ['required', 'in:paid,pending'],
            'note'           => ['nullable', 'string'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
        ]);

        $deposit->update($data);

        return redirect()->route('deposits.index')->with('success', 'Deposit updated successfully.');
    }

    public function destroy(Deposit $deposit)
    {
        $deposit->delete();

        return redirect()->route('deposits.index')->with('success', 'Deposit deleted successfully.');
    }
}
