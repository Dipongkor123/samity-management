<?php

namespace App\Http\Controllers;

use App\Models\Samity;
use Illuminate\Http\Request;

class SamityController extends Controller
{
    public function index(Request $request)
    {
        $query = Samity::withCount('members');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('cycle_type')) {
            $query->where('cycle_type', $request->cycle_type);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $samities = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total'    => Samity::count(),
            'active'   => Samity::where('is_active', true)->count(),
            'inactive' => Samity::where('is_active', false)->count(),
            'members'  => \App\Models\User::count(),
        ];

        return view('samities.index', compact('samities', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255', 'unique:samities'],
            'description'    => ['nullable', 'string'],
            'cycle_type'     => ['required', 'in:weekly,monthly,yearly'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'start_date'     => ['nullable', 'date'],
            'meeting_day'    => ['nullable', 'string', 'max:50'],
            'is_active'      => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Samity::create($data);

        return redirect()->route('samities.index')->with('success', 'Samity created successfully.');
    }

    public function edit(Samity $samity)
    {
        return redirect()->route('samities.index');
    }

    public function update(Request $request, Samity $samity)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255', 'unique:samities,name,' . $samity->id],
            'description'    => ['nullable', 'string'],
            'cycle_type'     => ['required', 'in:weekly,monthly,yearly'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'start_date'     => ['nullable', 'date'],
            'meeting_day'    => ['nullable', 'string', 'max:50'],
            'is_active'      => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $samity->update($data);

        return redirect()->route('samities.index')->with('success', 'Samity updated successfully.');
    }

    public function destroy(Samity $samity)
    {
        if ($samity->members()->count() > 0) {
            return redirect()->route('samities.index')->with('error', 'Cannot delete samity with active members.');
        }

        $samity->delete();

        return redirect()->route('samities.index')->with('success', 'Samity deleted successfully.');
    }
}
