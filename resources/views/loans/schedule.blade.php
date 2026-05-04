@extends('layouts.app')
@section('title', __('EMI Schedule') . ' — ' . ($loan->user?->name ?? ''))

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
            <a href="{{ route('loans.index') }}" style="color:#64748b; text-decoration:none; font-size:0.82rem; display:inline-flex; align-items:center; gap:5px;">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Loans') }}
            </a>
        </div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('EMI Amortization Schedule') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ $loan->user?->name }} &mdash; {{ $loan->samity?->name }}</p>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="{{ route('loans.index') }}" style="background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:9px 16px; font-size:0.85rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
            <i class="fas fa-list"></i> {{ __('All Loans') }}
        </a>
        <button onclick="window.print()" style="background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 18px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);">
            <i class="fas fa-print"></i> {{ __('Print Schedule') }}
        </button>
    </div>
</div>

{{-- Loan Summary Cards --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(170px,1fr)); gap:14px; margin-bottom:20px;">
    @php
        $totalEmi      = $loan->schedules->sum('emi_amount');
        $totalInterest = $loan->schedules->sum('interest');
        $totalPrincipal= $loan->schedules->sum('principal');
        $paidCount     = $loan->schedules->where('status','paid')->count();
        $totalCount    = $loan->schedules->count();
    @endphp
    <x-stat-card :label="__('Loan Amount')"    :value="$cur . number_format($loan->amount, 2)"         icon="fas fa-hand-holding-dollar" bg="#eff6ff" iconColor="#2563eb" />
    <x-stat-card :label="__('Total Interest')" :value="$cur . number_format($totalInterest, 2)"        icon="fas fa-percent"             bg="#fefce8" iconColor="#ca8a04" />
    <x-stat-card :label="__('Total Payable')"  :value="$cur . number_format($totalEmi, 2)"             icon="fas fa-coins"               bg="#f0fdfa" iconColor="#0d9488" />
    <x-stat-card :label="__('Paid / Total')"   :value="$paidCount . ' / ' . $totalCount"               icon="fas fa-calendar-check"      bg="#f0fdf4" iconColor="#16a34a" />
</div>

{{-- Loan Info Panel --}}
<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:20px 24px; margin-bottom:20px; display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:16px;">
    @php
        $typeLabel = $loan->interest_type === 'declining' ? __('Declining Balance') : __('Flat Rate');
        $typeBg    = $loan->interest_type === 'declining' ? '#eff6ff' : '#f0fdfa';
        $typeColor = $loan->interest_type === 'declining' ? '#2563eb' : '#0d9488';
    @endphp
    <div>
        <div style="font-size:0.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">{{ __('Member') }}</div>
        <div style="font-size:0.9rem; font-weight:700; color:#1e293b;">{{ $loan->user?->name }}</div>
    </div>
    <div>
        <div style="font-size:0.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">{{ __('Samity') }}</div>
        <div style="font-size:0.9rem; color:#475569;">{{ $loan->samity?->name }}</div>
    </div>
    <div>
        <div style="font-size:0.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">{{ __('Interest Type') }}</div>
        <span style="font-size:0.78rem; font-weight:700; padding:3px 10px; border-radius:20px; background:{{ $typeBg }}; color:{{ $typeColor }};">{{ $typeLabel }}</span>
    </div>
    <div>
        <div style="font-size:0.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">{{ __('Interest Rate') }}</div>
        <div style="font-size:0.9rem; color:#475569;">
            {{ $loan->interest_rate }}%
            <span style="font-size:0.72rem; color:#94a3b8;">({{ $loan->interest_type === 'declining' ? __('p.a.') : __('flat') }})</span>
        </div>
    </div>
    <div>
        <div style="font-size:0.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">{{ __('Duration') }}</div>
        <div style="font-size:0.9rem; color:#475569;">{{ $loan->duration_months }} {{ __('months') }}</div>
    </div>
    <div>
        <div style="font-size:0.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">{{ __('Issue Date') }}</div>
        <div style="font-size:0.9rem; color:#475569;">{{ $loan->issue_date?->format('d M Y') }}</div>
    </div>
    <div>
        <div style="font-size:0.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">{{ __('Due Date') }}</div>
        <div style="font-size:0.9rem; color:#475569;">{{ $loan->due_date?->format('d M Y') ?? '—' }}</div>
    </div>
    <div>
        <div style="font-size:0.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">{{ __('EMI') }}</div>
        <div style="font-size:0.9rem; font-weight:700; color:#0d9488;">{{ $cur }}{{ number_format($loan->monthly_installment, 2) }}</div>
    </div>
</div>

{{-- EMI Schedule Table --}}
<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">
            {{ __('Installment Schedule') }}
            <span style="color:#94a3b8; font-weight:400;">({{ $totalCount }} {{ __('installments') }})</span>
        </span>
        <div style="display:flex; gap:12px; font-size:0.75rem;">
            <span style="display:inline-flex; align-items:center; gap:5px; color:#16a34a;"><i class="fas fa-circle" style="font-size:8px;"></i>{{ __('Paid') }}</span>
            <span style="display:inline-flex; align-items:center; gap:5px; color:#ca8a04;"><i class="fas fa-circle" style="font-size:8px;"></i>{{ __('Pending') }}</span>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach([__('#'), __('Due Date'), __('Opening Balance'), __('Principal'), __('Interest'), __('EMI Amount'), __('Closing Balance'), __('Status')] as $h)
                    <th style="text-align:{{ $loop->index >= 2 ? 'right' : 'left' }}; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $today = now()->toDateString(); @endphp
                @forelse($loan->schedules as $row)
                @php
                    $isPaid    = $row->status === 'paid';
                    $isOverdue = !$isPaid && $row->due_date->toDateString() < $today;
                    $rowBg     = $isPaid ? 'rgba(240,253,244,0.5)' : ($isOverdue ? 'rgba(254,242,242,0.5)' : '');
                @endphp
                <tr style="border-top:1px solid #f1f5f9; background:{{ $rowBg }};"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='{{ $rowBg }}'">
                    <td style="padding:11px 16px; color:#94a3b8; font-size:0.78rem; font-weight:600;">{{ $row->installment_no }}</td>
                    <td style="padding:11px 16px; color:#475569; white-space:nowrap;">{{ $row->due_date->format('d M Y') }}</td>
                    <td style="padding:11px 16px; text-align:right; color:#374151;">{{ $cur }}{{ number_format($row->opening_balance, 2) }}</td>
                    <td style="padding:11px 16px; text-align:right; font-weight:500; color:#0d9488;">{{ $cur }}{{ number_format($row->principal, 2) }}</td>
                    <td style="padding:11px 16px; text-align:right; color:#ca8a04;">{{ $cur }}{{ number_format($row->interest, 2) }}</td>
                    <td style="padding:11px 16px; text-align:right; font-weight:700; color:#1e293b;">{{ $cur }}{{ number_format($row->emi_amount, 2) }}</td>
                    <td style="padding:11px 16px; text-align:right; color:#475569;">{{ $cur }}{{ number_format($row->closing_balance, 2) }}</td>
                    <td style="padding:11px 16px;">
                        @if($isPaid)
                            <span style="font-size:0.72rem; font-weight:700; padding:3px 10px; border-radius:20px; background:#f0fdf4; color:#16a34a;">{{ __('Paid') }}</span>
                        @elseif($isOverdue)
                            <span style="font-size:0.72rem; font-weight:700; padding:3px 10px; border-radius:20px; background:#fef2f2; color:#dc2626;">{{ __('Overdue') }}</span>
                        @else
                            <span style="font-size:0.72rem; font-weight:700; padding:3px 10px; border-radius:20px; background:#fefce8; color:#ca8a04;">{{ __('Pending') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding:48px; text-align:center; color:#94a3b8;">
                        <i class="fas fa-calendar-xmark" style="font-size:2.5rem; opacity:.2; display:block; margin-bottom:10px;"></i>
                        {{ __('No schedule generated yet.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($loan->schedules->isNotEmpty())
            <tfoot>
                <tr style="background:#f8fafc; border-top:2px solid #e2e8f0;">
                    <td colspan="2" style="padding:12px 16px; font-weight:700; color:#1e293b; font-size:0.82rem;">{{ __('Total') }}</td>
                    <td style="padding:12px 16px; text-align:right; color:#94a3b8; font-size:0.78rem;">—</td>
                    <td style="padding:12px 16px; text-align:right; font-weight:700; color:#0d9488;">{{ $cur }}{{ number_format($totalPrincipal, 2) }}</td>
                    <td style="padding:12px 16px; text-align:right; font-weight:700; color:#ca8a04;">{{ $cur }}{{ number_format($totalInterest, 2) }}</td>
                    <td style="padding:12px 16px; text-align:right; font-weight:700; color:#1e293b;">{{ $cur }}{{ number_format($totalEmi, 2) }}</td>
                    <td colspan="2" style="padding:12px 16px;"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<style>
@media print {
    aside, header, .no-print { display: none !important; }
    #main-content { margin-left: 0 !important; }
    body { font-size: 12px; }
}
</style>

@endsection
