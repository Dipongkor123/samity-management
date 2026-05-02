@extends('layouts.app')
@section('title', 'Samities')

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">Samities</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">Manage all cooperative groups</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-plus"></i> Add Samity
    </button>
</div>

{{-- Stats --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card label="Total Samities" :value="$stats['total']"    icon="fas fa-people-group"  bg="#f0fdfa" iconColor="#0d9488" />
    <x-stat-card label="Active"         :value="$stats['active']"   icon="fas fa-circle-check"  bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card label="Inactive"       :value="$stats['inactive']" icon="fas fa-circle-xmark"  bg="#fef2f2" iconColor="#ef4444" />
    <x-stat-card label="Total Members"  :value="$stats['members']"  icon="fas fa-users"         bg="#eff6ff" iconColor="#2563eb" />
</div>

{{-- Filter --}}
<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:180px;">
        <label style="{{ $lbl }}">Search</label>
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;"></i>
            <input name="search" value="{{ request('search') }}" placeholder="Name or description..."
                style="{{ $inp }} padding-left:30px;" onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
    </div>
    <div>
        <label style="{{ $lbl }}">Cycle Type</label>
        <select name="cycle_type" style="{{ $inp }} width:auto;">
            <option value="">All Cycles</option>
            @foreach(['weekly','monthly','yearly'] as $c)
                <option value="{{ $c }}" {{ request('cycle_type') === $c ? 'selected':'' }}>{{ ucfirst($c) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label style="{{ $lbl }}">Status</label>
        <select name="status" style="{{ $inp }} width:auto;">
            <option value="">All</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected':'' }}>Inactive</option>
        </select>
    </div>
    <div style="display:flex; gap:8px; padding-top:1px;">
        <button type="submit" style="background:#0d9488;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-filter"></i> Filter
        </button>
        @if(request()->hasAny(['search','cycle_type','status']))
            <a href="{{ route('samities.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-times"></i> Clear
            </a>
        @endif
    </div>
</form>

{{-- Validation errors (re-open correct modal) --}}
@if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
        @foreach($errors->all() as $e)
            <p style="margin:2px 0;font-size:0.83rem;color:#991b1b;"><i class="fas fa-circle-exclamation" style="margin-right:6px;"></i>{{ $e }}</p>
        @endforeach
    </div>
@endif

{{-- Table --}}
<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">All Samities <span style="color:#94a3b8; font-weight:400;">({{ $samities->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach(['#','Name','Cycle','Deposit Amt','Members','Start Date','Meeting Day','Status','Actions'] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($samities as $s)
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $samities->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600; color:#1e293b;">{{ $s->name }}</div>
                        <div style="font-size:0.75rem; color:#94a3b8; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $s->description }}</div>
                    </td>
                    <td style="padding:12px 16px;"><span style="background:#eff6ff;color:#2563eb;font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;text-transform:capitalize;">{{ $s->cycle_type }}</span></td>
                    <td style="padding:12px 16px; font-weight:700; color:#0f172a;">৳{{ number_format($s->deposit_amount, 2) }}</td>
                    <td style="padding:12px 16px; font-weight:700; color:#0f172a;">{{ $s->members_count }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $s->start_date?->format('d M Y') ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569; text-transform:capitalize;">{{ $s->meeting_day ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        @if($s->is_active)
                            <span style="background:#f0fdf4;color:#16a34a;font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;">● Active</span>
                        @else
                            <span style="background:#fef2f2;color:#ef4444;font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;">● Inactive</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEditSamity({{ $s->id }}, {{ json_encode($s->name) }}, {{ json_encode($s->description) }}, '{{ $s->cycle_type }}', '{{ $s->deposit_amount }}', '{{ $s->start_date?->format('Y-m-d') }}', '{{ $s->meeting_day }}', {{ $s->is_active ? 1 : 0 }})"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="Edit">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="confirmDelete('{{ route('samities.destroy', $s->id) }}', '{{ addslashes($s->name) }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-people-group" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>No samities found.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($samities->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $samities->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" title="Add New Samity" icon="fas fa-people-group">
    <form action="{{ route('samities.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Samity Name *</label>
                <input name="name" value="{{ old('name') }}" placeholder="e.g. Dhaka North Samity" style="{{ $inp }}" required>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Description</label>
                <textarea name="description" rows="2" placeholder="Short description..." style="{{ $inp }} resize:vertical;">{{ old('description') }}</textarea>
            </div>
            <div>
                <label style="{{ $lbl }}">Cycle Type *</label>
                <select name="cycle_type" style="{{ $inp }}" required>
                    <option value="">Select cycle</option>
                    @foreach(['weekly','monthly','yearly'] as $c)
                        <option value="{{ $c }}" {{ old('cycle_type') === $c ? 'selected':'' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">Deposit Amount (৳) *</label>
                <input type="number" step="0.01" name="deposit_amount" value="{{ old('deposit_amount') }}" placeholder="0.00" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">Meeting Day</label>
                <select name="meeting_day" style="{{ $inp }}">
                    <option value="">Select day</option>
                    @foreach(['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'] as $d)
                        <option value="{{ $d }}" {{ old('meeting_day') === $d ? 'selected':'' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div style="grid-column:1/-1; display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="is_active" value="1" id="create_is_active" {{ old('is_active', '1') ? 'checked':'' }}
                    style="width:16px;height:16px;accent-color:#0d9488;cursor:pointer;">
                <label for="create_is_active" style="font-size:0.85rem; font-weight:600; color:#374151; cursor:pointer;">Active</label>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">Cancel</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> Save Samity</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" title="Edit Samity" icon="fas fa-pen-to-square">
    <form id="edit-samity-form" method="POST">
        @csrf @method('PUT')
        <input type="hidden" name="_edit_id" id="edit_edit_id">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Samity Name *</label>
                <input name="name" id="edit_name" placeholder="Samity name" style="{{ $inp }}" required>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Description</label>
                <textarea name="description" id="edit_description" rows="2" style="{{ $inp }} resize:vertical;"></textarea>
            </div>
            <div>
                <label style="{{ $lbl }}">Cycle Type *</label>
                <select name="cycle_type" id="edit_cycle_type" style="{{ $inp }}" required>
                    @foreach(['weekly','monthly','yearly'] as $c)
                        <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">Deposit Amount (৳) *</label>
                <input type="number" step="0.01" name="deposit_amount" id="edit_deposit_amount" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Start Date</label>
                <input type="date" name="start_date" id="edit_start_date" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">Meeting Day</label>
                <select name="meeting_day" id="edit_meeting_day" style="{{ $inp }}">
                    <option value="">Select day</option>
                    @foreach(['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'] as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div style="grid-column:1/-1; display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="is_active" value="1" id="edit_is_active"
                    style="width:16px;height:16px;accent-color:#0d9488;cursor:pointer;">
                <label for="edit_is_active" style="font-size:0.85rem; font-weight:600; color:#374151; cursor:pointer;">Active</label>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">Cancel</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> Update Samity</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
function openEditSamity(id, name, description, cycle, deposit, startDate, meetingDay, isActive) {
    document.getElementById('edit_edit_id').value       = id;
    document.getElementById('edit_name').value          = name;
    document.getElementById('edit_description').value   = description;
    document.getElementById('edit_deposit_amount').value= deposit;
    document.getElementById('edit_start_date').value    = startDate;
    document.getElementById('edit_cycle_type').value    = cycle;
    document.getElementById('edit_meeting_day').value   = meetingDay;
    document.getElementById('edit_is_active').checked   = isActive == 1;
    document.getElementById('edit-samity-form').action  = '/samities/' + id;
    openModal('modal-edit');
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush
