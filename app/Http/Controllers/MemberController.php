<?php

namespace App\Http\Controllers;

use App\Models\MemberStatusLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    public function show(User $member)
    {
        $member->load('statusLogs', 'samities', 'deposits', 'loans', 'fines');

        $summary = [
            'total_deposits' => $member->deposits()->sum('amount'),
            'total_loans'    => $member->loans()->sum('amount'),
            'active_loans'   => $member->loans()->where('status', 'active')->count(),
            'total_fines'    => $member->fines()->sum('amount'),
            'pending_fines'  => $member->fines()->where('status', 'pending')->sum('amount'),
        ];

        return view('members.show', compact('member', 'summary'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:users'],
            'phone'             => ['nullable', 'string', 'max:20'],
            'nid'               => ['nullable', 'string', 'max:30'],
            'address'           => ['nullable', 'string'],
            'photo'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'date_of_birth'     => ['nullable', 'date'],
            'blood_group'       => ['nullable', 'string', 'max:5'],
            'occupation'        => ['nullable', 'string', 'max:100'],
            'father_name'       => ['nullable', 'string', 'max:100'],
            'mother_name'       => ['nullable', 'string', 'max:100'],
            'spouse_name'       => ['nullable', 'string', 'max:100'],
            'emergency_contact' => ['nullable', 'string', 'max:100'],
            'emergency_phone'   => ['nullable', 'string', 'max:20'],
            'role'              => ['required', 'in:admin,member'],
            'password'          => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'nid', 'address',
            'date_of_birth', 'blood_group', 'occupation',
            'father_name', 'mother_name', 'spouse_name',
            'emergency_contact', 'emergency_phone', 'role',
        ]);
        $data['is_active'] = true;
        $data['password']  = Hash::make($request->password);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        User::create($data);

        return redirect()->route('members.index')->with('success', 'Member added successfully.');
    }

    public function edit(User $member)
    {
        return redirect()->route('members.index');
    }

    public function update(Request $request, User $member)
    {
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:users,email,' . $member->id],
            'phone'             => ['nullable', 'string', 'max:20'],
            'nid'               => ['nullable', 'string', 'max:30'],
            'address'           => ['nullable', 'string'],
            'photo'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'date_of_birth'     => ['nullable', 'date'],
            'blood_group'       => ['nullable', 'string', 'max:5'],
            'occupation'        => ['nullable', 'string', 'max:100'],
            'father_name'       => ['nullable', 'string', 'max:100'],
            'mother_name'       => ['nullable', 'string', 'max:100'],
            'spouse_name'       => ['nullable', 'string', 'max:100'],
            'emergency_contact' => ['nullable', 'string', 'max:100'],
            'emergency_phone'   => ['nullable', 'string', 'max:20'],
            'role'              => ['required', 'in:admin,member'],
            'is_active'         => ['boolean'],
            'password'          => ['nullable', 'string', 'min:6', 'confirmed'],
            'status_note'       => ['nullable', 'string', 'max:255'],
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'nid', 'address',
            'date_of_birth', 'blood_group', 'occupation',
            'father_name', 'mother_name', 'spouse_name',
            'emergency_contact', 'emergency_phone', 'role',
        ]);
        $newActive         = $request->boolean('is_active');
        $data['is_active'] = $newActive;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                $old = public_path('uploads/members/' . $member->photo);
                if (file_exists($old)) { unlink($old); }
            }
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        if ((bool) $member->is_active !== $newActive) {
            MemberStatusLog::create([
                'user_id'    => $member->id,
                'old_status' => $member->is_active ? 'active' : 'inactive',
                'new_status' => $newActive ? 'active' : 'inactive',
                'changed_by' => auth()->user()->name,
                'note'       => $request->status_note,
            ]);
        }

        $member->update($data);

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(User $member)
    {
        if ($member->id === auth()->id()) {
            return redirect()->route('members.index')->with('error', 'You cannot delete your own account.');
        }

        if ($member->photo) {
            $path = public_path('uploads/members/' . $member->photo);
            if (file_exists($path)) { unlink($path); }
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }

    private function uploadPhoto($file): string
    {
        $dir = public_path('uploads/members');
        if (!is_dir($dir)) { mkdir($dir, 0755, true); }
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return $filename;
    }
}
