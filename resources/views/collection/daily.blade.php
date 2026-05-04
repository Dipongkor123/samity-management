@extends('layouts.app')
@section('title', __('Daily Collection Summary'))

@php
$cur = '৳';
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Daily Collection Summary') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</p>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <button onclick="window.print()" style="background:#1e293b;color:#fff;border:none;border-radius:10px;padding:10px 18px;font-size:0.85rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:7px;">
            <i class="fas fa-print"></i> {{ __('Print') }}
        </button>
        <a href="{{ route('collection.bulk') }}" style="{{ $btn_primary }} text-decoration:none;">
            <i class="fas fa-plus"></i> {{ __('New Collection') }}
        </a>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card :label="__('Collected Today')"  :value="$cur . number_format($totalCollected, 2)" icon="fas fa-money-bill-wave"  bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card :label="__('Members Paid')"     :value="$memberCount"                             icon="fas fa-users"            bg="#eff6ff" iconColor="#2563eb" />
    <x-stat-card :label="__('Overdue Pending')"  :value="$overdueInstallments->count()"            icon="fas fa-clock"            bg="#fef2f2" iconColor="#ef4444" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div>
        <label style="{{ $lbl }}">{{ __('Date') }}</label>
        <input type="date" name="date" value="{{ $date }}" style="{{ $inp }} width:auto;">
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
    <div style="display:flex; gap:8px;">
        <button type="submit" style="background:#0d9488;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-filter"></i> {{ __('View') }}
        </button>
    </div>
</form>

{{-- Collections Table --}}
<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:20px;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Collections Recorded') }} <span style="color:#94a3b8; font-weight:400;">({{ $collections->count() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach([__('#'), __('Member'), __('Samity'), __('Loan Amount'), __('Amount Paid'), __('Note')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($collections as $rep)
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $loop->iteration }}</td>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600; color:#1e293b;">{{ $rep->loan?->user?->name ?? '—' }}</div>
                        <div style="font-size:0.73rem; color:#94a3b8;">{{ $rep->loan?->user?->phone ?? '' }}</div>
                    </td>
                    <td style="padding:12px 16px; color:#475569;">{{ $rep->loan?->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $cur }}{{ number_format($rep->loan?->amount ?? 0, 2) }}</td>
                    <td style="padding:12px 16px; font-weight:700; color:#16a34a;">{{ $cur }}{{ number_format($rep->amount_paid, 2) }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $rep->note ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-inbox" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No collections recorded for this date.') }}
                </td></tr>
                @endforelse
            </tbody>
            @if($collections->isNotEmpty())
            <tfoot>
                <tr style="background:#f0fdf4; border-top:2px solid #bbf7d0;">
                    <td colspan="4" style="padding:12px 16px; font-weight:700; color:#15803d; text-align:right;">{{ __('Total Collected') }}</td>
                    <td style="padding:12px 16px; font-weight:800; color:#15803d; font-size:0.95rem;">{{ $cur }}{{ number_format($totalCollected, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- Overdue Installments --}}
@if($overdueInstallments->isNotEmpty())
<div style="background:#fff; border-radius:14px; border:1px solid #fca5a5; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #fca5a5; background:#fef2f2;">
        <span style="font-size:0.88rem; font-weight:700; color:#dc2626;"><i class="fas fa-triangle-exclamation" style="margin-right:6px;"></i>{{ __('Pending Overdue Installments') }} <span style="color:#ef4444; font-weight:400;">({{ $overdueInstallments->count() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach([__('#'), __('Member'), __('Samity'), __('Installment No'), __('Due Date'), __('EMI Amount')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($overdueInstallments as $schedule)
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#fff7ed'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $loop->iteration }}</td>
                    <td style="padding:12px 16px; font-weight:600; color:#1e293b;">{{ $schedule->loan?->user?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $schedule->loan?->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">#{{ $schedule->installment_no }}</td>
                    <td style="padding:12px 16px; color:#dc2626; font-weight:600;">{{ $schedule->due_date->format('d M Y') }}</td>
                    <td style="padding:12px 16px; font-weight:700; color:#ea580c;">{{ $cur }}{{ number_format($schedule->emi_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
