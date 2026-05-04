@extends('layouts.app')
@section('title', __('Defaulter Report'))

@php
$cur = '৳';
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Defaulter Report') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Members with overdue installments') }}</p>
    </div>
    <div style="display:flex; gap:8px;">
        <button onclick="window.print()" style="background:#1e293b;color:#fff;border:none;border-radius:10px;padding:10px 18px;font-size:0.85rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:7px;">
            <i class="fas fa-print"></i> {{ __('Print') }}
        </button>
        <a href="{{ route('reports.index') }}" style="background:#f8fafc;color:#64748b;border:1px solid #e2e8f0;border-radius:10px;padding:10px 18px;font-size:0.85rem;font-weight:600;display:inline-flex;align-items:center;gap:7px;text-decoration:none;">
            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
        </a>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card :label="__('Total Defaulters')"   :value="$totalDefaulters"                          icon="fas fa-user-slash"          bg="#fef2f2" iconColor="#ef4444" />
    <x-stat-card :label="__('Total Overdue Amt')"  :value="$cur . number_format($totalOverdueAmt, 2)" icon="fas fa-triangle-exclamation" bg="#fff7ed" iconColor="#ea580c" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
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
        <button type="submit" style="background:#ef4444;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-filter"></i> {{ __('Filter') }}
        </button>
        @if(request('samity_id'))
            <a href="{{ route('reports.defaulters') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-times"></i> {{ __('Clear') }}
            </a>
        @endif
    </div>
</form>

<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Defaulter List') }} <span style="color:#94a3b8; font-weight:400;">({{ $defaulters->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach(['#', __('Member'), __('Phone'), __('Samity'), __('Loan Amount'), __('Overdue'), __('Overdue Amount'), __('Next Due'), __('Actions')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($defaulters as $loan)
                @php
                    $firstOverdue = $loan->schedules
                        ->where('status','!=','paid')
                        ->filter(fn($s) => $s->due_date->isPast())
                        ->sortBy('due_date')->first();
                @endphp
                <tr style="border-top:1px solid #f1f5f9; background:#fffbeb;" onmouseover="this.style.background='#fef9c3'" onmouseout="this.style.background='#fffbeb'">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $defaulters->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px; font-weight:600; color:#1e293b;">{{ $loan->user?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $loan->user?->phone ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $loan->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $cur }}{{ number_format($loan->amount, 2) }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:700;padding:3px 9px;border-radius:20px;background:#fef2f2;color:#ef4444;">{{ $loan->overdue_count }}</span>
                    </td>
                    <td style="padding:12px 16px; font-weight:700; color:#dc2626;">{{ $cur }}{{ number_format($loan->overdue_amount, 2) }}</td>
                    <td style="padding:12px 16px; color:#dc2626; font-weight:600; white-space:nowrap;">{{ $firstOverdue?->due_date->format('d M Y') ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        <a href="{{ route('repayments.index', ['loan_id' => $loan->id]) }}"
                            style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;" title="{{ __('Repay') }}">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="padding:48px;text-align:center;">
                    <i class="fas fa-circle-check" style="font-size:2.5rem;color:#22c55e;opacity:.7;display:block;margin-bottom:10px;"></i>
                    <span style="color:#16a34a;font-weight:600;">{{ __('No defaulters found. All installments are up to date!') }}</span>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($defaulters->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $defaulters->links() }}</div>
    @endif
</div>

@endsection
