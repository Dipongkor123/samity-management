@extends('layouts.app')
@section('title', 'Loans')

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">Loans</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">Manage all member loan accounts</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-plus"></i> Issue Loan
    </button>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card label="Total Disbursed" :value="'৳' . number_format($stats['total_amount'], 2)"  icon="fas fa-hand-holding-dollar" bg="#eff6ff" iconColor="#2563eb" />
    <x-stat-card label="Active Loans"    :value="$stats['active_count']"  :sub="'৳' . number_format($stats['active_amount'], 2)" icon="fas fa-circle-dot" bg="#f0fdfa" iconColor="#0d9488" />
    <x-stat-card label="Completed"       :value="$stats['completed_count']" icon="fas fa-circle-check"       bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card label="Overdue"         :value="$stats['overdue_count']"   icon="fas fa-circle-exclamation" bg="#fef2f2" iconColor="#ef4444" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:180px;">
        <label style="{{ $lbl }}">Search Member</label>
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;"></i>
            <input name="search" value="{{ request('search') }}" placeholder="Member name..."
                style="{{ $inp }} padding-left:30px;" onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
    </div>
    <div>
        <label style="{{ $lbl }}">Samity</label>
        <select name="samity_id" style="{{ $inp }} width:auto;">
            <option value="">All Samities</option>
            @foreach($samities as $s)
                <option value="{{ $s->id }}" {{ request('samity_id') == $s->id ? 'selected':'' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label style="{{ $lbl }}">Status</label>
        <select name="status" style="{{ $inp }} width:auto;">
            <option value="">All</option>
            @foreach(['active','completed','overdue'] as $st)
                <option value="{{ $st }}" {{ request('status') === $st ? 'selected':'' }}>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label style="{{ $lbl }}">From</label>
        <input type="date" name="from" value="{{ request('from') }}" style="{{ $inp }} width:auto;">
    </div>
    <div>
        <label style="{{ $lbl }}">To</label>
        <input type="date" name="to" value="{{ request('to') }}" style="{{ $inp }} width:auto;">
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="background:#0d9488;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-filter"></i> Filter
        </button>
        @if(request()->hasAny(['search','samity_id','status','from','to']))
            <a href="{{ route('loans.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-times"></i> Clear
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
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">Loan Records <span style="color:#94a3b8; font-weight:400;">({{ $loans->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach(['#','Member','Samity','Amount','Interest','Duration','Installment','Issue Date','Due Date','Repaid','Status','Actions'] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $l)
                @php
                    $paid = $l->totalPaid();
                    $pct  = $l->amount > 0 ? min(100, round($paid / $l->amount * 100)) : 0;
                    $stColors = ['active'=>['#eff6ff','#2563eb'],'completed'=>['#f0fdf4','#16a34a'],'overdue'=>['#fef2f2','#ef4444']];
                    [$stBg,$stC] = $stColors[$l->status] ?? ['#f8fafc','#64748b'];
                @endphp
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $loans->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600; color:#1e293b;">{{ $l->user?->name ?? '—' }}</div>
                        <div style="font-size:0.73rem; color:#94a3b8;">{{ $l->purpose }}</div>
                    </td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; font-weight:700; color:#0f172a;">৳{{ number_format($l->amount, 2) }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->interest_rate }}%</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->duration_months }}m</td>
                    <td style="padding:12px 16px; color:#475569;">৳{{ number_format($l->monthly_installment, 2) }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->issue_date?->format('d M Y') }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->due_date?->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; align-items:center; gap:7px;">
                            <div style="width:56px; height:5px; background:#e2e8f0; border-radius:99px; overflow:hidden;">
                                <div style="height:100%; width:{{ $pct }}%; background:#0d9488; border-radius:99px;"></div>
                            </div>
                            <span style="font-size:0.72rem; color:#64748b;">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:capitalize;background:{{ $stBg }};color:{{ $stC }};">{{ $l->status }}</span>
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEditLoan({{ $l->id }}, {{ $l->samity_id }}, {{ $l->user_id }}, '{{ $l->amount }}', '{{ $l->interest_rate }}', '{{ $l->duration_months }}', '{{ $l->monthly_installment }}', '{{ $l->issue_date?->format('Y-m-d') }}', '{{ $l->due_date?->format('Y-m-d') }}', {{ json_encode($l->purpose) }}, '{{ $l->status }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="Edit">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="confirmDelete('{{ route('loans.destroy', $l->id) }}', 'loan for {{ addslashes($l->user?->name) }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="12" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-hand-holding-dollar" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>No loan records found.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($loans->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $loans->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" title="Issue New Loan" icon="fas fa-hand-holding-dollar" width="620px">
    <form action="{{ route('loans.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">Member *</label>
                <select name="user_id" style="{{ $inp }}" required>
                    <option value="">Select member</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected':'' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">Samity *</label>
                <select name="samity_id" style="{{ $inp }}" required>
                    <option value="">Select samity</option>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}" {{ old('samity_id') == $s->id ? 'selected':'' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">Loan Amount (৳) *</label>
                <input type="number" step="0.01" id="c_amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" style="{{ $inp }}" required oninput="calcInstallment('c')">
            </div>
            <div>
                <label style="{{ $lbl }}">Interest Rate (%) *</label>
                <input type="number" step="0.01" id="c_interest" name="interest_rate" value="{{ old('interest_rate') }}" placeholder="e.g. 12" style="{{ $inp }}" required oninput="calcInstallment('c')">
            </div>
            <div>
                <label style="{{ $lbl }}">Duration (Months) *</label>
                <input type="number" id="c_duration" name="duration_months" value="{{ old('duration_months') }}" placeholder="e.g. 12" style="{{ $inp }}" required oninput="calcInstallment('c')">
            </div>
            <div>
                <label style="{{ $lbl }}">Monthly Installment (৳) *</label>
                <input type="number" step="0.01" id="c_installment" name="monthly_installment" value="{{ old('monthly_installment') }}" placeholder="Auto-calculated" style="{{ $inp }} background:#f8fafc;" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Issue Date *</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">Status *</label>
                <select name="status" style="{{ $inp }}" required>
                    <option value="active"    {{ old('status','active') === 'active'    ? 'selected':'' }}>Active</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected':'' }}>Completed</option>
                    <option value="overdue"   {{ old('status') === 'overdue'   ? 'selected':'' }}>Overdue</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">Purpose</label>
                <input name="purpose" value="{{ old('purpose') }}" placeholder="e.g. Business, Medical..." style="{{ $inp }}">
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">Cancel</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> Issue Loan</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" title="Edit Loan" icon="fas fa-pen-to-square" width="620px">
    <form id="edit-loan-form" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">Member *</label>
                <select name="user_id" id="el_user" style="{{ $inp }}" required>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">Samity *</label>
                <select name="samity_id" id="el_samity" style="{{ $inp }}" required>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">Loan Amount (৳) *</label>
                <input type="number" step="0.01" id="e_amount" name="amount" style="{{ $inp }}" required oninput="calcInstallment('e')">
            </div>
            <div>
                <label style="{{ $lbl }}">Interest Rate (%) *</label>
                <input type="number" step="0.01" id="e_interest" name="interest_rate" style="{{ $inp }}" required oninput="calcInstallment('e')">
            </div>
            <div>
                <label style="{{ $lbl }}">Duration (Months) *</label>
                <input type="number" id="e_duration" name="duration_months" style="{{ $inp }}" required oninput="calcInstallment('e')">
            </div>
            <div>
                <label style="{{ $lbl }}">Monthly Installment (৳) *</label>
                <input type="number" step="0.01" id="e_installment" name="monthly_installment" style="{{ $inp }} background:#f8fafc;" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Issue Date *</label>
                <input type="date" name="issue_date" id="el_issue" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Due Date</label>
                <input type="date" name="due_date" id="el_due" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">Status *</label>
                <select name="status" id="el_status" style="{{ $inp }}" required>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">Purpose</label>
                <input name="purpose" id="el_purpose" style="{{ $inp }}">
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">Cancel</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> Update Loan</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
function calcInstallment(prefix) {
    const amt  = parseFloat(document.getElementById(prefix + (prefix==='c'?'_amount':'_amount')).value) || 0;
    const rate = parseFloat(document.getElementById(prefix + (prefix==='c'?'_interest':'_interest')).value) || 0;
    const dur  = parseInt(document.getElementById(prefix + (prefix==='c'?'_duration':'_duration')).value) || 0;

    // Fix element IDs
    const amtEl  = prefix === 'c' ? document.getElementById('c_amount')      : document.getElementById('e_amount');
    const rateEl = prefix === 'c' ? document.getElementById('c_interest')    : document.getElementById('e_interest');
    const durEl  = prefix === 'c' ? document.getElementById('c_duration')    : document.getElementById('e_duration');
    const instEl = prefix === 'c' ? document.getElementById('c_installment') : document.getElementById('e_installment');

    const a = parseFloat(amtEl.value) || 0;
    const r = parseFloat(rateEl.value) || 0;
    const d = parseInt(durEl.value) || 0;
    if (a > 0 && d > 0) {
        const total = a + (a * r / 100);
        instEl.value = (total / d).toFixed(2);
    }
}

function openEditLoan(id, samityId, userId, amount, interest, duration, installment, issueDate, dueDate, purpose, status) {
    document.getElementById('el_user').value        = userId;
    document.getElementById('el_samity').value      = samityId;
    document.getElementById('e_amount').value       = amount;
    document.getElementById('e_interest').value     = interest;
    document.getElementById('e_duration').value     = duration;
    document.getElementById('e_installment').value  = installment;
    document.getElementById('el_issue').value       = issueDate;
    document.getElementById('el_due').value         = dueDate || '';
    document.getElementById('el_purpose').value     = purpose || '';
    document.getElementById('el_status').value      = status;
    document.getElementById('edit-loan-form').action = '/loans/' + id;
    openModal('modal-edit');
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush
