@extends('layouts.app')
@section('title', 'Fines')

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">Fines</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">Manage all member fines and penalties</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-plus"></i> Add Fine
    </button>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card label="Total Fines"  :value="'৳' . number_format($stats['total_amount'], 2)"   icon="fas fa-triangle-exclamation" bg="#fef2f2" iconColor="#ef4444" />
    <x-stat-card label="Collected"    :value="'৳' . number_format($stats['paid_amount'], 2)"    icon="fas fa-circle-check"         bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card label="Pending"      :value="'৳' . number_format($stats['pending_amount'], 2)" icon="fas fa-clock"                bg="#fefce8" iconColor="#ca8a04" />
    <x-stat-card label="Total Count"  :value="$stats['total_count']"                            icon="fas fa-list"                 bg="#f8fafc" iconColor="#64748b" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:180px;">
        <label style="{{ $lbl }}">Search</label>
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;"></i>
            <input name="search" value="{{ request('search') }}" placeholder="Member name or reason..."
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
            @foreach(['pending','paid','waived'] as $st)
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
            <a href="{{ route('fines.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
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
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">Fine Records <span style="color:#94a3b8; font-weight:400;">({{ $fines->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach(['#','Member','Samity','Reason','Amount','Fine Date','Status','Actions'] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($fines as $f)
                @php
                    $stColors = ['paid'=>['#f0fdf4','#16a34a'],'pending'=>['#fefce8','#ca8a04'],'waived'=>['#f8fafc','#64748b']];
                    [$stBg,$stC] = $stColors[$f->status] ?? ['#f8fafc','#64748b'];
                @endphp
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $fines->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px; font-weight:600; color:#1e293b;">{{ $f->user?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $f->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569; max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $f->reason }}">{{ $f->reason ?? '—' }}</td>
                    <td style="padding:12px 16px; font-weight:700; color:#ef4444;">৳{{ number_format($f->amount, 2) }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $f->fine_date?->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:capitalize;background:{{ $stBg }};color:{{ $stC }};">{{ $f->status }}</span>
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEditFine({{ $f->id }}, {{ $f->samity_id }}, {{ $f->user_id }}, {{ json_encode($f->reason) }}, '{{ $f->amount }}', '{{ $f->fine_date?->format('Y-m-d') }}', '{{ $f->status }}', {{ json_encode($f->note) }})"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="Edit">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="confirmDelete('{{ route('fines.destroy', $f->id) }}', 'fine for {{ addslashes($f->user?->name) }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-triangle-exclamation" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>No fine records found.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($fines->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $fines->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" title="Add Fine" icon="fas fa-triangle-exclamation">
    <form action="{{ route('fines.store') }}" method="POST">
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
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Reason *</label>
                <input name="reason" value="{{ old('reason') }}" placeholder="Reason for fine..." style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Amount (৳) *</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" placeholder="0.00" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Fine Date *</label>
                <input type="date" name="fine_date" value="{{ old('fine_date', date('Y-m-d')) }}" style="{{ $inp }}" required>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Status *</label>
                <select name="status" style="{{ $inp }}" required>
                    <option value="pending" {{ old('status','pending') === 'pending' ? 'selected':'' }}>Pending</option>
                    <option value="paid"    {{ old('status') === 'paid'    ? 'selected':'' }}>Paid</option>
                    <option value="waived"  {{ old('status') === 'waived'  ? 'selected':'' }}>Waived</option>
                </select>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Note</label>
                <textarea name="note" rows="2" placeholder="Optional note..." style="{{ $inp }} resize:vertical;">{{ old('note') }}</textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">Cancel</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> Save Fine</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" title="Edit Fine" icon="fas fa-pen-to-square">
    <form id="edit-fine-form" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">Member *</label>
                <select name="user_id" id="ef_user" style="{{ $inp }}" required>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">Samity *</label>
                <select name="samity_id" id="ef_samity" style="{{ $inp }}" required>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Reason *</label>
                <input name="reason" id="ef_reason" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Amount (৳) *</label>
                <input type="number" step="0.01" name="amount" id="ef_amount" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">Fine Date *</label>
                <input type="date" name="fine_date" id="ef_date" style="{{ $inp }}" required>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Status *</label>
                <select name="status" id="ef_status" style="{{ $inp }}" required>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="waived">Waived</option>
                </select>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">Note</label>
                <textarea name="note" id="ef_note" rows="2" style="{{ $inp }} resize:vertical;"></textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">Cancel</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> Update Fine</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
function openEditFine(id, samityId, userId, reason, amount, date, status, note) {
    document.getElementById('ef_user').value   = userId;
    document.getElementById('ef_samity').value = samityId;
    document.getElementById('ef_reason').value = reason || '';
    document.getElementById('ef_amount').value = amount;
    document.getElementById('ef_date').value   = date;
    document.getElementById('ef_status').value = status;
    document.getElementById('ef_note').value   = note || '';
    document.getElementById('edit-fine-form').action = '/fines/' + id;
    openModal('modal-edit');
}
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush
