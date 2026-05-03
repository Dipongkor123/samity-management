<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('nid', 'like', "%$s%");
            });
        }
        if ($request->filled('role'))   { $query->where('role', $request->role); }
        if ($request->filled('status')) { $query->where('is_active', $request->status === 'active'); }

        $members = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total'    => User::count(),
            'active'   => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'admins'   => User::where('role', 'admin')->count(),
        ];

        return view('members.index', compact('members', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'nid'      => ['nullable', 'string', 'max:30'],
            'address'  => ['nullable', 'string'],
            'role'     => ['required', 'in:admin,member'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'nid'       => $request->nid,
            'address'   => $request->address,
            'role'      => $request->role,
            'is_active' => true,
            'password'  => Hash::make($request->password),
        ]);

        return redirect()->route('members.index')->with('success', 'Member added successfully.');
    }

    public function edit(User $member)
    {
        return redirect()->route('members.index');
    }

    public function update(Request $request, User $member)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,' . $member->id],
            'phone'    => ['nullable', 'string', 'max:20'],
            'nid'      => ['nullable', 'string', 'max:30'],
            'address'  => ['nullable', 'string'],
            'role'     => ['required', 'in:admin,member'],
            'is_active'=> ['boolean'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $data = $request->only(['name', 'email', 'phone', 'nid', 'address', 'role']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $member->update($data);

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(User $member)
    {
        if ($member->id === auth()->id()) {
            return redirect()->route('members.index')->with('error', 'You cannot delete your own account.');
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }
}
