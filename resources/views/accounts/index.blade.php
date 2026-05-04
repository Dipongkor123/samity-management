@extends('layouts.app')
@section('title', __('Cash Book'))

@php
$cur = '৳';
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Cash Book') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Income, expenses and running balance') }}</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-plus"></i> {{ __('Add Transaction') }}
    </button>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card :label="__('Total Income')"  :value="$cur . number_format($totalIncome,  2)" :sub="__('This month').': '.$cur.number_format($monthIncome,  2)" icon="fas fa-arrow-down" bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card :label="__('Total Expense')" :value="$cur . number_format($totalExpense, 2)" :sub="__('This month').': '.$cur.number_format($monthExpense, 2)" icon="fas fa-arrow-up"   bg="#fef2f2" iconColor="#ef4444" />
    <x-stat-card :label="$balance >= 0 ? __('Net Surplus') : __('Net Deficit')"
                 :value="$cur . number_format(abs($balance), 2)"
                 icon="fas fa-scale-balanced"
                 :bg="$balance >= 0 ? '#f0fdfa' : '#fff7ed'"
                 :iconColor="$balance >= 0 ? '#0d9488' : '#ea580c'" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div>
        <label style="{{ $lbl }}">{{ __('Type') }}</label>
        <select name="type" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All Types') }}</option>
            <option value="income"  {{ request('type') === 'income'  ? 'selected' : '' }}>{{ __('Income') }}</option>
            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>{{ __('Expense') }}</option>
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
        @if(request()->hasAny(['type','from','to']))
            <a href="{{ route('accounts.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
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
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Transaction Ledger') }} <span style="color:#94a3b8; font-weight:400;">({{ $entries->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach([__('Date'), __('Type'), __('Category'), __('Description'), __('Reference'), __('Member'), __('Amount'), __('Actions')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#475569; font-size:0.8rem; white-space:nowrap;">{{ $entry->transaction_date->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.72rem; font-weight:700; padding:3px 9px; border-radius:20px; background:{{ $entry->type==='income' ? '#f0fdf4':'#fef2f2' }}; color:{{ $entry->type==='income' ? '#16a34a':'#dc2626' }}; text-transform:uppercase;">
                            {{ $entry->type === 'income' ? __('Income') : __('Expense') }}
                        </span>
                    </td>
                    <td style="padding:12px 16px; font-weight:500; color:#374151;">{{ ucwords(str_replace('_',' ',$entry->category)) }}</td>
                    <td style="padding:12px 16px; color:#475569; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $entry->description ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $entry->reference ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569; font-size:0.8rem;">
                        {{ $entry->user?->name ?? '—' }}
                        @if($entry->samity) <span style="color:#94a3b8;">({{ $entry->samity->name }})</span> @endif
                    </td>
                    <td style="padding:12px 16px; font-weight:700; color:{{ $entry->type==='income' ? '#16a34a':'#dc2626' }}; white-space:nowrap;">
                        {{ $entry->type==='income' ? '+' : '−' }}{{ $cur }}{{ number_format($entry->amount, 2) }}
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEditModal({{ $entry->id }}, '{{ $entry->type }}', '{{ $entry->category }}', '{{ $entry->amount }}', '{{ $entry->transaction_date->format('Y-m-d') }}', {{ json_encode($entry->description ?? '') }}, '{{ $entry->reference ?? '' }}', {{ $entry->user_id ?? 'null' }}, {{ $entry->samity_id ?? 'null' }})"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="{{ __('Edit') }}">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="confirmDelete('{{ route('accounts.destroy', $entry) }}', '{{ addslashes(ucwords(str_replace('_',' ',$entry->category))) }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-book-open" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No transactions found.') }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($entries->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $entries->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" :title="__('Add Transaction')" icon="fas fa-plus" width="600px">
    <form action="{{ route('accounts.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">{{ __('Type') }} *</label>
                <select name="type" id="add-type" onchange="updateCategories('add')" required style="{{ $inp }}">
                    <option value="income">{{ __('Income') }}</option>
                    <option value="expense">{{ __('Expense') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Category') }} *</label>
                <select name="category" id="add-category" required style="{{ $inp }}"></select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Amount') }} ({{ $cur }}) *</label>
                <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Date') }} *</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Reference / Voucher') }}</label>
                <input type="text" name="reference" placeholder="{{ __('Optional') }}" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Member') }}</label>
                <select name="user_id" style="{{ $inp }}">
                    <option value="">{{ __('None') }}</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Samity') }}</label>
                <select name="samity_id" style="{{ $inp }}">
                    <option value="">{{ __('None') }}</option>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Description') }}</label>
                <textarea name="description" rows="2" placeholder="{{ __('Optional notes...') }}" style="{{ $inp }} resize:vertical;"></textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Save Transaction') }}</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" :title="__('Edit Transaction')" icon="fas fa-pen-to-square" width="600px">
    <form id="edit-entry-form" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">{{ __('Type') }} *</label>
                <select name="type" id="edit-type" onchange="updateCategories('edit')" required style="{{ $inp }}">
                    <option value="income">{{ __('Income') }}</option>
                    <option value="expense">{{ __('Expense') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Category') }} *</label>
                <select name="category" id="edit-category" required style="{{ $inp }}"></select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Amount') }} ({{ $cur }}) *</label>
                <input type="number" name="amount" id="edit-amount" step="0.01" min="0.01" required style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Date') }} *</label>
                <input type="date" name="transaction_date" id="edit-date" required style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Reference / Voucher') }}</label>
                <input type="text" name="reference" id="edit-reference" style="{{ $inp }}">
            </div>
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Description') }}</label>
                <textarea name="description" id="edit-desc" rows="2" style="{{ $inp }} resize:vertical;"></textarea>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Update') }}</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
const incomeCategories  = @json($categories['income']);
const expenseCategories = @json($categories['expense']);

function updateCategories(prefix) {
    const type = document.getElementById(prefix + '-type').value;
    const sel  = document.getElementById(prefix + '-category');
    const cats = type === 'income' ? incomeCategories : expenseCategories;
    sel.innerHTML = cats.map(c => `<option value="${c}">${c.replace(/_/g,' ').replace(/\b\w/g, l => l.toUpperCase())}</option>`).join('');
}

function openEditModal(id, type, category, amount, date, desc, reference) {
    document.getElementById('edit-entry-form').action = '/accounts/' + id;
    document.getElementById('edit-type').value     = type;
    updateCategories('edit');
    document.getElementById('edit-category').value = category;
    document.getElementById('edit-amount').value   = amount;
    document.getElementById('edit-date').value     = date;
    document.getElementById('edit-desc').value     = desc;
    document.getElementById('edit-reference').value= reference;
    openModal('modal-edit');
}

// Init add modal category list
document.addEventListener('DOMContentLoaded', () => updateCategories('add'));
</script>
@endpush
