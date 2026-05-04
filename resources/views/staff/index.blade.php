@extends('layouts.app')
@section('title', __('Staff Management'))

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Staff Management') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Field officers, admins and role permissions') }}</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-user-plus"></i> {{ __('Add Staff') }}
    </button>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card :label="__('Total Staff')"     :value="$stats['total']"          icon="fas fa-users"          bg="#faf5ff" iconColor="#9333ea" />
    <x-stat-card :label="__('Admins')"          :value="$stats['admins']"         icon="fas fa-shield-halved"  bg="#eff6ff" iconColor="#2563eb" />
    <x-stat-card :label="__('Field Officers')"  :value="$stats['field_officers']" icon="fas fa-person-walking" bg="#f0fdfa" iconColor="#0d9488" />
    <x-stat-card :label="__('Active')"          :value="$stats['active']"         icon="fas fa-circle-check"   bg="#f0fdf4" iconColor="#16a34a" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:180px;">
        <label style="{{ $lbl }}">{{ __('Search') }}</label>
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;"></i>
            <input name="search" value="{{ request('search') }}" placeholder="{{ __('Name, email, phone...') }}"
                style="{{ $inp }} padding-left:30px;" onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
    </div>
    <div>
        <label style="{{ $lbl }}">{{ __('Role') }}</label>
        <select name="role" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All Roles') }}</option>
            <option value="admin"         {{ request('role') === 'admin'         ? 'selected':'' }}>{{ __('Admin') }}</option>
            <option value="field_officer" {{ request('role') === 'field_officer' ? 'selected':'' }}>{{ __('Field Officer') }}</option>
            <option value="staff"         {{ request('role') === 'staff'         ? 'selected':'' }}>{{ __('Staff') }}</option>
        </select>
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="background:#0d9488;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-filter"></i> {{ __('Filter') }}
        </button>
        @if(request()->hasAny(['search','role']))
            <a href="{{ route('staff.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
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
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Staff List') }} <span style="color:#94a3b8; font-weight:400;">({{ $staff->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach(['#', __('Name'), __('Phone / Email'), __('Role'), __('Designation'), __('Area'), __('Joined'), __('Status'), __('Actions')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $m)
                @php
                    $roleColors = ['admin'=>['#faf5ff','#9333ea'], 'field_officer'=>['#f0fdfa','#0d9488'], 'staff'=>['#eff6ff','#2563eb']];
                    [$rcBg, $rcC] = $roleColors[$m->role] ?? ['#f1f5f9','#64748b'];
                @endphp
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $staff->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#6d28d9);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                                {{ strtoupper(substr($m->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600; color:#1e293b;">{{ $m->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px; color:#475569;">
                        {{ $m->phone ?? '—' }}<br>
                        <span style="font-size:0.75rem; color:#94a3b8;">{{ $m->email }}</span>
                    </td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $rcBg }};color:{{ $rcC }};">
                            {{ str_replace('_', ' ', ucwords($m->role, '_')) }}
                        </span>
                    </td>
                    <td style="padding:12px 16px; color:#475569;">{{ $m->designation ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569; font-size:0.8rem;">{{ $m->assigned_area ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $m->joining_date?->format('d M Y') ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        @if($m->is_active)
                            <span style="background:#f0fdf4;color:#16a34a;font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;">● {{ __('Active') }}</span>
                        @else
                            <span style="background:#fef2f2;color:#ef4444;font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;">● {{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEditStaff({{ $m->id }}, {{ json_encode($m->name) }}, {{ json_encode($m->email) }}, {{ json_encode($m->phone) }}, '{{ $m->role }}', {{ json_encode($m->designation) }}, {{ json_encode($m->assigned_area) }}, '{{ $m->joining_date?->format('Y-m-d') ?? '' }}', {{ $m->is_active ? 1:0 }})"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="{{ __('Edit') }}">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            @if($m->id !== auth()->id())
                            <button onclick="confirmDelete('{{ route('staff.destroy', $m) }}', '{{ addslashes($m->name) }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="{{ __('Deactivate') }}">
                                <i class="fas fa-user-slash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-id-badge" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No staff members found.') }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($staff->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $staff->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" :title="__('Add Staff Member')" icon="fas fa-user-plus" width="600px">
    <form action="{{ route('staff.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Full Name') }} *</label>
                <input type="text" name="name" required placeholder="{{ __('Full name') }}" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Email') }} *</label>
                <input type="email" name="email" required style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Phone') }}</label>
                <input type="text" name="phone" placeholder="+880 1XX-XXXXXXX" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Role') }} *</label>
                <select name="role" required style="{{ $inp }}">
                    <option value="field_officer">{{ __('Field Officer') }}</option>
                    <option value="staff">{{ __('Staff') }}</option>
                    <option value="admin">{{ __('Admin') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Designation') }}</label>
                <input type="text" name="designation" placeholder="{{ __('e.g. Senior Officer') }}" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Assigned Area') }}</label>
                <input type="text" name="assigned_area" placeholder="{{ __('e.g. Mirpur Zone') }}" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Joining Date') }}</label>
                <input type="date" name="joining_date" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Password') }} *</label>
                <input type="password" name="password" required minlength="6" placeholder="{{ __('Min 6 characters') }}" style="{{ $inp }}">
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Add Staff') }}</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" :title="__('Edit Staff Member')" icon="fas fa-pen-to-square" width="600px">
    <form id="edit-staff-form" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Full Name') }} *</label>
                <input type="text" name="name" id="es-name" required style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Email') }} *</label>
                <input type="email" name="email" id="es-email" required style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Phone') }}</label>
                <input type="text" name="phone" id="es-phone" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Role') }} *</label>
                <select name="role" id="es-role" required style="{{ $inp }}">
                    <option value="field_officer">{{ __('Field Officer') }}</option>
                    <option value="staff">{{ __('Staff') }}</option>
                    <option value="admin">{{ __('Admin') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Designation') }}</label>
                <input type="text" name="designation" id="es-desig" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Assigned Area') }}</label>
                <input type="text" name="assigned_area" id="es-area" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Joining Date') }}</label>
                <input type="date" name="joining_date" id="es-join" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('New Password') }} <span style="color:#94a3b8;font-weight:400;">({{ __('leave blank to keep') }})</span></label>
                <input type="password" name="password" minlength="6" placeholder="{{ __('New password') }}" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.85rem; font-weight:600; color:#374151;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="es-active" value="1" style="width:16px;height:16px;accent-color:#0d9488;">
                    {{ __('Active') }}
                </label>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Update Staff') }}</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
function openEditStaff(id, name, email, phone, role, desig, area, join, isActive) {
    document.getElementById('edit-staff-form').action = '/staff/' + id;
    document.getElementById('es-name').value  = name  || '';
    document.getElementById('es-email').value = email || '';
    document.getElementById('es-phone').value = phone || '';
    document.getElementById('es-role').value  = role;
    document.getElementById('es-desig').value = desig || '';
    document.getElementById('es-area').value  = area  || '';
    document.getElementById('es-join').value  = join  || '';
    document.getElementById('es-active').checked = isActive == 1;
    openModal('modal-edit');
}
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush
