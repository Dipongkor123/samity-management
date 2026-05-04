<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Samity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('is_staff', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('phone', 'like', "%$s%"));
        }
        if ($request->filled('role')) { $query->where('role', $request->role); }

        $staff    = $query->latest()->paginate(15)->withQueryString();
        $samities = Samity::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total'         => User::where('is_staff', true)->count(),
            'admins'        => User::where('is_staff', true)->where('role', 'admin')->count(),
            'field_officers'=> User::where('is_staff', true)->where('role', 'field_officer')->count(),
            'active'        => User::where('is_staff', true)->where('is_active', true)->count(),
        ];

        return view('staff.index', compact('staff', 'samities', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'role'          => ['required', 'in:admin,field_officer,staff'],
            'designation'   => ['nullable', 'string', 'max:100'],
            'assigned_area' => ['nullable', 'string', 'max:255'],
            'joining_date'  => ['nullable', 'date'],
            'password'      => ['required', 'string', 'min:6'],
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_staff']  = true;
        $data['is_active'] = true;

        User::create($data);

        return redirect()->route('staff.index')->with('success', 'Staff member added successfully.');
    }

    public function update(Request $request, User $staff)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email,' . $staff->id],
            'phone'         => ['nullable', 'string', 'max:20'],
            'role'          => ['required', 'in:admin,field_officer,staff'],
            'designation'   => ['nullable', 'string', 'max:100'],
            'assigned_area' => ['nullable', 'string', 'max:255'],
            'joining_date'  => ['nullable', 'date'],
            'is_active'     => ['boolean'],
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:6']]);
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return redirect()->route('staff.index')->with('error', 'You cannot delete your own account.');
        }

        $staff->update(['is_staff' => false, 'is_active' => false]);

        return redirect()->route('staff.index')->with('success', 'Staff member deactivated.');
    }
}
