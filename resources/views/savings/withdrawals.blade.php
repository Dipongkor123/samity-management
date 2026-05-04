@extends('layouts.app')
@section('title', __('Savings Withdrawals'))

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#dc2626,#b91c1c); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(220,38,38,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Savings Withdrawals') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Track all savings withdrawal requests and transactions') }}</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-plus"></i> {{ __('Record Withdrawal') }}
    </button>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card :label="__('Total Withdrawn')" :value="$cur . number_format($stats['total_amount'], 2)" icon="fas fa-money-bill-transfer" bg="#fef2f2" iconColor="#dc2626" />
    <x-stat-card :label="__('This Month')"      :value="$cur . number_format($stats['this_month'], 2)"   icon="fas fa-calendar-check"    bg="#eff6ff" iconColor="#2563eb" />
    <x-stat-card :label="__('Total Records')"   :value="$stats['total_count']"                           icon="fas fa-receipt"           bg="#f0fdfa" iconColor="#0d9488" />
    <x-stat-card :label="__('Pending')"         :value="$stats['pending']"                               icon="fas fa-clock"             bg="#fefce8" iconColor="#ca8a04" />
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
        <label style="{{ $lbl }}">{{ __('Status') }}</label>
        <select name="status" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All') }}</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected':'' }}>{{ __('Approved') }}</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected':'' }}>{{ __('Pending') }}</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected':'' }}>{{ __('Rejected') }}</option>
        </select>
    </div>
    <div>
        <label style="{{ $lbl }}">{{ __('From') }}</label>
        <input type="date" name="from" value="{{ request('from') }}" style="{{ $inp }} width:auto;">
    </div>
    <div>
        <label style="{{ $lbl }}">{{ __('To') }}</label>
        <input type="date" name="to" value="{{ request('to') }}" style="{{ $inp }} width:auto;">
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="background:#0d9488;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-filter"></i> {{ __('Filter') }}
        </button>
        @if(request()->hasAny(['search','samity_id','status','from','to']))
            <a href="{{ route('savings.withdrawals.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
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
@if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
        @foreach($errors->all() as $e)
            <p style="margin:2px 0;font-size:0.83rem;color:#991b1b;"><i class="fas fa-circle-exclamation" style="margin-right:6px;"></i>{{ $e }}</p>
        @endforeach
    </div>
@endif

<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Withdrawal Records') }} <span style="color:#94a3b8; font-weight:400;">({{ $withdrawals->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach([__('#'),__('Member'),__('Samity'),__('Plan'),__('Amount'),__('Date'),__('Status'),__('Reason'),__('Actions')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                @php
                    $st = $w->status ?? 'pending';
                    $stBg    = ['approved'=>'#f0fdf4','pending'=>'#fefce8','rejected'=>'#fef2f2'][$st] ?? '#f8fafc';
                    $stColor = ['approved'=>'#16a34a','pending'=>'#ca8a04','rejected'=>'#dc2626'][$st] ?? '#64748b';
                @endphp
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $withdrawals->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px; font-weight:600; color:#1e293b;">{{ $w->user?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $w->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569; font-size:0.78rem;">
                        {{ $w->plan ? ucfirst($w->plan->plan_type) . ' #' . $w->savings_plan_id : '—' }}
                    </td>
                    <td style="padding:12px 16px; font-weight:500; color:#dc2626;">{{ $cur }}{{ number_format($w->amount, 2) }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $w->withdrawal_date?->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $stBg }};color:{{ $stColor }};">{{ ucfirst($st) }}</span>
                    </td>
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.78rem; max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $w->reason ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEditWithdrawal({{ $w->id }}, {{ $w->savings_plan_id }}, {{ $w->samity_id }}, {{ $w->user_id }}, '{{ $w->amount }}', '{{ $w->withdrawal_date?->format('Y-m-d') }}', '{{ $w->status }}', {{ json_encode($w->reason) }}, {{ json_encode($w->note) }})"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="Edit">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="confirmDelete('{{ route('savings.withdrawals.destroy', $w->id) }}', 'withdrawal #{{ $w->id }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-money-bill-transfer" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No withdrawal records found.') }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($withdrawals->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $withdrawals->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" :title="__('Record Withdrawal')" icon="fas fa-money-bill-transfer">
    <form action="{{ route('savings.withdrawals.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Savings Plan') }} *</label>
                <select name="savings_plan_id" id="cw_plan_id" style="{{ $inp }}" required onchange="fillFromPlanW(this)">
                    <option value="">{{ __('Select savings plan') }}</option>
                    @foreach($plans as $pl)
                        <option value="{{ $pl->id }}" data-samity="{{ $pl->samity_id }}" data-user="{{ $pl->user_id }}"
                            {{ old('savings_plan_id') == $pl->id ? 'selected':'' }}>
                            #{{ $pl->id }} — {{ $pl->user?->name }} ({{ ucfirst($pl->plan_type) }}) — {{ $pl->samity?->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Member') }} *</label>
                <select name="user_id" id="cw_user_id" style="{{ $inp }}" required>
                    <option value="">{{ __('Select member') }}</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected':'' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Samity') }} *</label>
                <select name="samity_id" id="cw_samity_id" style="{{ $inp }}" required>
                    <option value="">{{ __('Select samity') }}</option>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}" {{ old('samity_id') == $s->id ? 'selected':'' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Amount') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" placeholder="0.00" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Withdrawal Date') }} *</label>
                <input type="date" name="withdrawal_date" value="{{ old('withdrawal_date', date('Y-m-d')) }}" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Status') }} *</label>
                <select name="status" style="{{ $inp }}" required>
                    <option value="approved" {{ old('status','approved') === 'approved' ? 'selected':'' }}>{{ __('Approved') }}</option>
                    <option value="pending"  {{ old('status') === 'pending'  ? 'selected':'' }}>{{ __('Pending') }}</option>
                    <option value="rejected" {{ old('status') === 'rejected' ? 'selected':'' }}>{{ __('Rejected') }}</option>
                </select>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Reason') }}</label>
                <input name="reason" value="{{ old('reason') }}" placeholder="{{ __('Reason for withdrawal...') }}" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Note') }}</label>
                <textarea name="note" rows="2" placeholder="{{ __('Optional note...') }}" style="{{ $inp }} resize:vertical;">{{ old('note') }}</textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Save Withdrawal') }}</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" :title="__('Edit Withdrawal')" icon="fas fa-pen-to-square">
    <form id="edit-withdrawal-form" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Savings Plan') }} *</label>
                <select name="savings_plan_id" id="ew_plan_id" style="{{ $inp }}" required>
                    @foreach($plans as $pl)
                        <option value="{{ $pl->id }}">
                            #{{ $pl->id }} — {{ $pl->user?->name }} ({{ ucfirst($pl->plan_type) }}) — {{ $pl->samity?->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Member') }} *</label>
                <select name="user_id" id="ew_user_id" style="{{ $inp }}" required>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Samity') }} *</label>
                <select name="samity_id" id="ew_samity_id" style="{{ $inp }}" required>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Amount') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="amount" id="ew_amount" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Withdrawal Date') }} *</label>
                <input type="date" name="withdrawal_date" id="ew_withdrawal_date" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Status') }} *</label>
                <select name="status" id="ew_status" style="{{ $inp }}" required>
                    <option value="approved">{{ __('Approved') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Reason') }}</label>
                <input name="reason" id="ew_reason" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Note') }}</label>
                <textarea name="note" id="ew_note" rows="2" style="{{ $inp }} resize:vertical;"></textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Update Withdrawal') }}</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
function fillFromPlanW(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    document.getElementById('cw_samity_id').value = opt.dataset.samity || '';
    document.getElementById('cw_user_id').value   = opt.dataset.user   || '';
}

function openEditWithdrawal(id, planId, samityId, userId, amount, date, status, reason, note) {
    document.getElementById('ew_plan_id').value          = planId;
    document.getElementById('ew_user_id').value          = userId;
    document.getElementById('ew_samity_id').value        = samityId;
    document.getElementById('ew_amount').value           = amount;
    document.getElementById('ew_withdrawal_date').value  = date;
    document.getElementById('ew_status').value           = status;
    document.getElementById('ew_reason').value           = reason || '';
    document.getElementById('ew_note').value             = note || '';
    document.getElementById('edit-withdrawal-form').action = '/savings/withdrawals/' + id;
    openModal('modal-edit');
}
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush
