@extends('layouts.app')
@section('title', __('Repayments'))

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Loan Repayments') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Track all loan installment payments') }}</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-plus"></i> {{ __('Record Repayment') }}
    </button>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card :label="__('Total Paid')"      :value="$cur . number_format($stats['total_paid'], 2)"      icon="fas fa-rotate-left"    bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card :label="__('This Month')"      :value="$cur . number_format($stats['this_month'], 2)"      icon="fas fa-calendar-check" bg="#eff6ff" iconColor="#2563eb" />
    <x-stat-card :label="__('Total Principal')" :value="$cur . number_format($stats['total_principal'], 2)" icon="fas fa-coins"          bg="#f0fdfa" iconColor="#0d9488" />
    <x-stat-card :label="__('Total Interest')"  :value="$cur . number_format($stats['total_interest'], 2)"  icon="fas fa-percent"        bg="#faf5ff" iconColor="#9333ea" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:180px;">
        <label style="{{ $lbl }}">{{ __('Search Member') }}</label>
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;"></i>
            <input name="search" value="{{ request('search') }}" placeholder="Member name..."
                style="{{ $inp }} padding-left:30px;" onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
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
        @if(request()->hasAny(['search','from','to']))
            <a href="{{ route('repayments.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
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
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Repayment Records') }} <span style="color:#94a3b8; font-weight:400;">({{ $repayments->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach([__('#'),__('Member'),__('Samity'),__('Loan'),__('Amount Paid'),__('Principal'),__('Interest'),__('Paid Date'),__('Note'),__('Actions')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($repayments as $r)
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $repayments->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px; font-weight:600; color:#1e293b;">{{ $r->loan?->user?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $r->loan?->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569; font-size:0.78rem;">#{{ $r->loan_id }}</td>
                    <td style="padding:12px 16px; font-weight:500; color:#16a34a;">{{ $cur }}{{ number_format($r->amount_paid, 2) }}</td>
                    <td style="padding:12px 16px; color:#0d9488; font-weight:500;">{{ $cur }}{{ number_format($r->principal, 2) }}</td>
                    <td style="padding:12px 16px; color:#9333ea; font-weight:500;">{{ $cur }}{{ number_format($r->interest, 2) }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $r->paid_date?->format('d M Y') }}</td>
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.78rem; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $r->note ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEditRepayment({{ $r->id }}, {{ $r->loan_id }}, '{{ $r->amount_paid }}', '{{ $r->principal }}', '{{ $r->interest }}', '{{ $r->paid_date?->format('Y-m-d') }}', {{ json_encode($r->note) }})"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="Edit">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="confirmDelete('{{ route('repayments.destroy', $r->id) }}', 'repayment #{{ $r->id }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-rotate-left" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No repayment records found.') }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($repayments->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $repayments->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" :title="__('Record Repayment')" icon="fas fa-rotate-left">
    <form action="{{ route('repayments.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Loan (Member)') }} *</label>
                <select name="loan_id" style="{{ $inp }}" required>
                    <option value="">{{ __('Select loan') }}</option>
                    @foreach($loans as $ln)
                        <option value="{{ $ln->id }}" {{ old('loan_id') == $ln->id ? 'selected':'' }}>
                            #{{ $ln->id }} — {{ $ln->user?->name }} ({{ $cur }}{{ number_format($ln->amount, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Amount Paid') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="amount_paid" value="{{ old('amount_paid') }}" placeholder="0.00" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Paid Date') }} *</label>
                <input type="date" name="paid_date" value="{{ old('paid_date', date('Y-m-d')) }}" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Principal') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="principal" value="{{ old('principal') }}" placeholder="0.00" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Interest') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="interest" value="{{ old('interest', '0') }}" placeholder="0.00" style="{{ $inp }}" required>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Note') }}</label>
                <textarea name="note" rows="2" placeholder="{{ __('Optional note...') }}" style="{{ $inp }} resize:vertical;">{{ old('note') }}</textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Save Repayment') }}</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" :title="__('Edit Repayment')" icon="fas fa-pen-to-square">
    <form id="edit-repayment-form" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Loan (Member)') }} *</label>
                <select name="loan_id" id="er_loan" style="{{ $inp }}" required>
                    @foreach($loans as $ln)
                        <option value="{{ $ln->id }}">#{{ $ln->id }} — {{ $ln->user?->name }} ({{ $cur }}{{ number_format($ln->amount, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Amount Paid') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="amount_paid" id="er_paid" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Paid Date') }} *</label>
                <input type="date" name="paid_date" id="er_date" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Principal') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="principal" id="er_principal" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Interest') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" name="interest" id="er_interest" style="{{ $inp }}" required>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Note') }}</label>
                <textarea name="note" id="er_note" rows="2" style="{{ $inp }} resize:vertical;"></textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Update Repayment') }}</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
function openEditRepayment(id, loanId, paid, principal, interest, date, note) {
    document.getElementById('er_loan').value      = loanId;
    document.getElementById('er_paid').value      = paid;
    document.getElementById('er_principal').value = principal;
    document.getElementById('er_interest').value  = interest;
    document.getElementById('er_date').value      = date;
    document.getElementById('er_note').value      = note || '';
    document.getElementById('edit-repayment-form').action = '/repayments/' + id;
    openModal('modal-edit');
}
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush
