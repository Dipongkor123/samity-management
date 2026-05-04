@extends('layouts.app')
@section('title', __('Savings Plans'))

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Savings Plans') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Manage weekly and monthly savings plans for members') }}</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-plus"></i> {{ __('New Savings Plan') }}
    </button>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card :label="__('Total Plans')"   :value="$stats['total']"   icon="fas fa-book-open"       bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card :label="__('Active Plans')"  :value="$stats['active']"  icon="fas fa-circle-check"    bg="#eff6ff" iconColor="#2563eb" />
    <x-stat-card :label="__('Weekly Plans')"  :value="$stats['weekly']"  icon="fas fa-calendar-week"   bg="#f0fdfa" iconColor="#0d9488" />
    <x-stat-card :label="__('Monthly Plans')" :value="$stats['monthly']" icon="fas fa-calendar-days"   bg="#fefce8" iconColor="#ca8a04" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:180px;">
        <label style="{{ $lbl }}">{{ __('Search') }}</label>
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;"></i>
            <input name="search" value="{{ request('search') }}" placeholder="{{ __('Member or samity name...') }}"
                style="{{ $inp }} padding-left:30px;" onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
    </div>
    <div>
        <label style="{{ $lbl }}">{{ __('Samity') }}</label>
        <select name="samity_id" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All Samities') }}</option>
            @foreach($samities as $s)
                <option value="{{ $s->id }}" {{ request('samity_id') == $s->id ? 'selected':'' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label style="{{ $lbl }}">{{ __('Plan Type') }}</label>
        <select name="plan_type" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All Types') }}</option>
            <option value="weekly"  {{ request('plan_type') === 'weekly'  ? 'selected':'' }}>{{ __('Weekly') }}</option>
            <option value="monthly" {{ request('plan_type') === 'monthly' ? 'selected':'' }}>{{ __('Monthly') }}</option>
        </select>
    </div>
    <div>
        <label style="{{ $lbl }}">{{ __('Status') }}</label>
        <select name="status" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All') }}</option>
            <option value="active" {{ request('status') === 'active' ? 'selected':'' }}>{{ __('Active') }}</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected':'' }}>{{ __('Closed') }}</option>
        </select>
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="background:#0d9488;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-filter"></i> {{ __('Filter') }}
        </button>
        @if(request()->hasAny(['search','samity_id','plan_type','status']))
            <a href="{{ route('savings.plans.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-times"></i> {{ __('Clear') }}
            </a>
        @endif
    </div>
</form>

@if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#15803d;font-size:0.85rem;">
        <i class="fas fa-circle-check" style="margin-right:6px;"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#991b1b;font-size:0.85rem;">
        <i class="fas fa-circle-exclamation" style="margin-right:6px;"></i>{{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
        @foreach($errors->all() as $e)
            <p style="margin:2px 0;font-size:0.83rem;color:#991b1b;"><i class="fas fa-circle-exclamation" style="margin-right:6px;"></i>{{ $e }}</p>
        @endforeach
    </div>
@endif

<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Savings Plan Records') }} <span style="color:#94a3b8; font-weight:400;">({{ $plans->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach([__('#'),__('Member'),__('Samity'),__('Plan Type'),__('Regular Amt'),__('Target Amt'),__('Start Date'),__('Balance'),__('Status'),__('Actions')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $p)
                @php $st = $p->status ?? 'active'; @endphp
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $plans->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px; font-weight:600; color:#1e293b;">{{ $p->user?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $p->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $p->plan_type === 'weekly' ? '#eff6ff':'#fdf4ff' }};color:{{ $p->plan_type === 'weekly' ? '#2563eb':'#9333ea' }};">
                            {{ ucfirst($p->plan_type) }}
                        </span>
                    </td>
                    <td style="padding:12px 16px; font-weight:500; color:#0d9488;">{{ $cur }}{{ number_format($p->regular_amount, 2) }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $p->target_amount ? $cur . number_format($p->target_amount, 2) : '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $p->start_date?->format('d M Y') }}</td>
                    <td style="padding:12px 16px; font-weight:600; color:#16a34a;">{{ $cur }}{{ number_format($p->balance(), 2) }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $st === 'active' ? '#f0fdf4':'#f8fafc' }};color:{{ $st === 'active' ? '#16a34a':'#64748b' }};">{{ ucfirst($st) }}</span>
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEditPlan({{ $p->id }}, {{ $p->samity_id }}, {{ $p->user_id }}, '{{ $p->plan_type }}', '{{ $p->regular_amount }}', {{ $p->target_amount ?? 'null' }}, '{{ $p->start_date?->format('Y-m-d') }}', {{ $p->end_date ? "'".$p->end_date->format('Y-m-d')."'" : 'null' }}, '{{ $p->status }}', {{ json_encode($p->note) }})"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="Edit">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="confirmDelete('{{ route('savings.plans.destroy', $p->id) }}', 'savings plan #{{ $p->id }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-book-open" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No savings plans found.') }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($plans->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $plans->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" :title="__('New Savings Plan')" icon="fas fa-book-open">
    <form action="{{ route('savings.plans.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">{{ __('Member') }} *</label>
                <select name="user_id" style="{{ $inp }}" required>
                    <option value="">{{ __('Select member') }}</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected':'' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Samity') }} *</label>
                <select name="samity_id" style="{{ $inp }}" required>
                    <option value="">{{ __('Select samity') }}</option>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}" {{ old('samity_id') == $s->id ? 'selected':'' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Plan Type') }} *</label>
                <select name="plan_type" style="{{ $inp }}" required>
                    <option value="weekly"  {{ old('plan_type','weekly') === 'weekly'  ? 'selected':'' }}>{{ __('Weekly') }}</option>
                    <option value="monthly" {{ old('plan_type') === 'monthly' ? 'selected':'' }}>{{ __('Monthly') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Regular Amount') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="regular_amount" value="{{ old('regular_amount') }}" placeholder="0.00" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Target Amount') }} ({{ $cur }})</label>
                <input type="number" step="0.01" name="target_amount" value="{{ old('target_amount') }}" placeholder="{{ __('Optional') }}" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Status') }} *</label>
                <select name="status" style="{{ $inp }}" required>
                    <option value="active" {{ old('status','active') === 'active' ? 'selected':'' }}>{{ __('Active') }}</option>
                    <option value="closed" {{ old('status') === 'closed' ? 'selected':'' }}>{{ __('Closed') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Start Date') }} *</label>
                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('End Date') }}</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Note') }}</label>
                <textarea name="note" rows="2" placeholder="{{ __('Optional note...') }}" style="{{ $inp }} resize:vertical;">{{ old('note') }}</textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Save Plan') }}</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" :title="__('Edit Savings Plan')" icon="fas fa-pen-to-square">
    <form id="edit-plan-form" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">{{ __('Member') }} *</label>
                <select name="user_id" id="ep_user_id" style="{{ $inp }}" required>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Samity') }} *</label>
                <select name="samity_id" id="ep_samity_id" style="{{ $inp }}" required>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Plan Type') }} *</label>
                <select name="plan_type" id="ep_plan_type" style="{{ $inp }}" required>
                    <option value="weekly">{{ __('Weekly') }}</option>
                    <option value="monthly">{{ __('Monthly') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Regular Amount') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="regular_amount" id="ep_regular_amount" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Target Amount') }} ({{ $cur }})</label>
                <input type="number" step="0.01" name="target_amount" id="ep_target_amount" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Status') }} *</label>
                <select name="status" id="ep_status" style="{{ $inp }}" required>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="closed">{{ __('Closed') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Start Date') }} *</label>
                <input type="date" name="start_date" id="ep_start_date" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('End Date') }}</label>
                <input type="date" name="end_date" id="ep_end_date" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Note') }}</label>
                <textarea name="note" id="ep_note" rows="2" style="{{ $inp }} resize:vertical;"></textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Update Plan') }}</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
function openEditPlan(id, samityId, userId, planType, regularAmt, targetAmt, startDate, endDate, status, note) {
    document.getElementById('ep_user_id').value        = userId;
    document.getElementById('ep_samity_id').value      = samityId;
    document.getElementById('ep_plan_type').value      = planType;
    document.getElementById('ep_regular_amount').value = regularAmt;
    document.getElementById('ep_target_amount').value  = targetAmt || '';
    document.getElementById('ep_start_date').value     = startDate;
    document.getElementById('ep_end_date').value       = endDate || '';
    document.getElementById('ep_status').value         = status;
    document.getElementById('ep_note').value           = note || '';
    document.getElementById('edit-plan-form').action   = '/savings/plans/' + id;
    openModal('modal-edit');
}
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush
