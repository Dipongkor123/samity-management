@extends('layouts.app')
@section('title', __('Members'))

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Members') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Manage all registered members') }}</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-user-plus"></i> {{ __('Add Member') }}
    </button>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card :label="__('Total Members')" :value="$stats['total']"    icon="fas fa-users"        bg="#eff6ff" iconColor="#2563eb" />
    <x-stat-card :label="__('Active')"        :value="$stats['active']"   icon="fas fa-circle-check" bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card :label="__('Inactive')"      :value="$stats['inactive']" icon="fas fa-circle-xmark" bg="#fef2f2" iconColor="#ef4444" />
    <x-stat-card :label="__('Admins')"        :value="$stats['admins']"   icon="fas fa-user-shield"  bg="#faf5ff" iconColor="#9333ea" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:180px;">
        <label style="{{ $lbl }}">{{ __('Search') }}</label>
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;"></i>
            <input name="search" value="{{ request('search') }}" placeholder="{{ __('Name, email, phone or NID...') }}"
                style="{{ $inp }} padding-left:30px;" onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
    </div>
    <div>
        <label style="{{ $lbl }}">{{ __('Role') }}</label>
        <select name="role" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All Roles') }}</option>
            <option value="admin"  {{ request('role') === 'admin'  ? 'selected':'' }}>{{ __('Admin') }}</option>
            <option value="member" {{ request('role') === 'member' ? 'selected':'' }}>{{ __('Member') }}</option>
        </select>
    </div>
    <div>
        <label style="{{ $lbl }}">{{ __('Status') }}</label>
        <select name="status" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All') }}</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected':'' }}>{{ __('Active') }}</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected':'' }}>{{ __('Inactive') }}</option>
        </select>
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="background:#0d9488;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-filter"></i> {{ __('Filter') }}
        </button>
        @if(request()->hasAny(['search','role','status']))
            <a href="{{ route('members.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-times"></i> {{ __('Clear') }}
            </a>
        @endif
    </div>
</form>

@if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
        @foreach($errors->all() as $e)
            <p style="margin:2px 0;font-size:0.83rem;color:#991b1b;"><i class="fas fa-circle-exclamation" style="margin-right:6px;"></i>{{ $e }}</p>
        @endforeach
    </div>
@endif

<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('All Members') }} <span style="color:#94a3b8; font-weight:400;">({{ $members->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach(['#', __('Member'), __('Phone'), __('NID'), __('Role'), __('Status'), __('Joined'), __('Actions')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($members as $m)
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $members->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0f766e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                                {{ strtoupper(substr($m->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600; color:#1e293b;">{{ $m->name }}</div>
                                <div style="font-size:0.75rem; color:#94a3b8;">{{ $m->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px; color:#475569;">{{ $m->phone ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569; font-family:monospace; font-size:0.78rem;">{{ $m->nid ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $m->isAdmin() ? '#faf5ff':'#f0fdfa' }};color:{{ $m->isAdmin() ? '#9333ea':'#0d9488' }};">
                            {{ $m->isAdmin() ? __('Admin') : __('Member') }}
                        </span>
                    </td>
                    <td style="padding:12px 16px;">
                        @if($m->is_active)
                            <span style="background:#f0fdf4;color:#16a34a;font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;">● {{ __('Active') }}</span>
                        @else
                            <span style="background:#fef2f2;color:#ef4444;font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;">● {{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px; color:#475569;">{{ $m->created_at->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEditMember({{ $m->id }}, {{ json_encode($m->name) }}, {{ json_encode($m->email) }}, '{{ $m->phone }}', '{{ $m->nid }}', {{ json_encode($m->address) }}, '{{ $m->role }}', {{ $m->is_active ? 1:0 }})"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="{{ __('Edit') }}">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="confirmDelete('{{ route('members.destroy', $m->id) }}', '{{ addslashes($m->name) }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-users" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No members found.') }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($members->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $members->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" :title="__('Add New Member')" icon="fas fa-user-plus">
    <form action="{{ route('members.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">{{ __('Full Name') }} *</label>
                <input name="name" value="{{ old('name') }}" placeholder="{{ __('Full Name') }}" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Email') }} *</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Phone') }}</label>
                <input name="phone" value="{{ old('phone') }}" placeholder="+880 1XX-XXXXXXX" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('NID') }}</label>
                <input name="nid" value="{{ old('nid') }}" placeholder="{{ __('NID') }}" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Address') }}</label>
                <input name="address" value="{{ old('address') }}" placeholder="{{ __('Address') }}" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Role') }} *</label>
                <select name="role" style="{{ $inp }}" required>
                    <option value="member" {{ old('role') === 'member' ? 'selected':'' }}>{{ __('Member') }}</option>
                    <option value="admin"  {{ old('role') === 'admin'  ? 'selected':'' }}>{{ __('Admin') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Password') }} *</label>
                <input type="password" name="password" placeholder="{{ __('Min 6 characters') }}" style="{{ $inp }}" required>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Confirm Password') }} *</label>
                <input type="password" name="password_confirmation" placeholder="{{ __('Repeat password') }}" style="{{ $inp }}" required>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Save Member') }}</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" :title="__('Edit Member')" icon="fas fa-user-pen">
    <form id="edit-member-form" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">{{ __('Full Name') }} *</label>
                <input name="name" id="em_name" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Email') }} *</label>
                <input type="email" name="email" id="em_email" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Phone') }}</label>
                <input name="phone" id="em_phone" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('NID') }}</label>
                <input name="nid" id="em_nid" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Address') }}</label>
                <input name="address" id="em_address" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Role') }} *</label>
                <select name="role" id="em_role" style="{{ $inp }}" required>
                    <option value="member">{{ __('Member') }}</option>
                    <option value="admin">{{ __('Admin') }}</option>
                </select>
            </div>
            <div style="display:flex; align-items:center; gap:10px; padding-top:22px;">
                <input type="checkbox" name="is_active" value="1" id="em_is_active"
                    style="width:16px;height:16px;accent-color:#0d9488;cursor:pointer;">
                <label for="em_is_active" style="font-size:0.85rem; font-weight:600; color:#374151; cursor:pointer;">{{ __('Active Account') }}</label>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('New Password') }} <span style="color:#94a3b8;font-weight:400;">({{ __('leave blank to keep') }})</span></label>
                <input type="password" name="password" placeholder="{{ __('New password') }}" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Confirm New Password') }}</label>
                <input type="password" name="password_confirmation" placeholder="{{ __('Confirm new password') }}" style="{{ $inp }}">
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Update Member') }}</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
function openEditMember(id, name, email, phone, nid, address, role, isActive) {
    document.getElementById('em_name').value     = name;
    document.getElementById('em_email').value    = email;
    document.getElementById('em_phone').value    = phone || '';
    document.getElementById('em_nid').value      = nid || '';
    document.getElementById('em_address').value  = address || '';
    document.getElementById('em_role').value     = role;
    document.getElementById('em_is_active').checked = isActive == 1;
    document.getElementById('edit-member-form').action = '/members/' + id;
    openModal('modal-edit');
}
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush
