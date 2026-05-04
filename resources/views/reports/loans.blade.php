@extends('layouts.app')
@section('title', __('Loan Report'))

@php
$cur = '৳';
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Loan Report') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Loan-wise disbursement and repayment overview') }}</p>
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
    <x-stat-card :label="__('Total Disbursed')"  :value="$cur . number_format($totals['disbursed'],    2)" icon="fas fa-hand-holding-dollar" bg="#fff7ed" iconColor="#ea580c" />
    <x-stat-card :label="__('Total Repaid')"      :value="$cur . number_format($totals['repaid'],       2)" icon="fas fa-rotate-left"          bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card :label="__('Outstanding')"       :value="$cur . number_format($totals['outstanding'],  2)" icon="fas fa-circle-exclamation"    bg="#fef2f2" iconColor="#ef4444" />
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
    <div>
        <label style="{{ $lbl }}">{{ __('Status') }}</label>
        <select name="status" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All') }}</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected':'' }}>{{ __('Active') }}</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected':'' }}>{{ __('Completed') }}</option>
            <option value="overdue"   {{ request('status') === 'overdue'   ? 'selected':'' }}>{{ __('Overdue') }}</option>
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
        @if(request()->hasAny(['samity_id','status','from','to']))
            <a href="{{ route('reports.loans') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-times"></i> {{ __('Clear') }}
            </a>
        @endif
    </div>
</form>

<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Loan List') }} <span style="color:#94a3b8; font-weight:400;">({{ $loans->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach(['#', __('Member'), __('Samity'), __('Loan Amount'), __('Repaid'), __('Outstanding'), __('Issue Date'), __('Due Date'), __('Status')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $l)
                @php
                    $repaid      = (float)($l->repayments_sum_amount_paid ?? 0);
                    $outstanding = max(0, (float)$l->amount - $repaid);
                    $pct         = $l->amount > 0 ? min(100, round($repaid / $l->amount * 100)) : 0;
                    $stColors    = ['active'=>['#eff6ff','#2563eb'],'completed'=>['#f0fdf4','#16a34a'],'overdue'=>['#fef2f2','#ef4444']];
                    [$stBg,$stC] = $stColors[$l->status] ?? ['#f8fafc','#64748b'];
                @endphp
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $loans->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px; font-weight:600; color:#1e293b;">{{ $l->user?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; font-weight:500; color:#374151;">{{ $cur }}{{ number_format($l->amount, 2) }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; align-items:center; gap:7px;">
                            <div style="width:56px; height:5px; background:#e2e8f0; border-radius:99px; overflow:hidden;">
                                <div style="height:100%; width:{{ $pct }}%; background:#16a34a; border-radius:99px;"></div>
                            </div>
                            <span style="font-size:0.73rem; color:#16a34a; font-weight:600;">{{ $cur }}{{ number_format($repaid, 2) }}</span>
                        </div>
                    </td>
                    <td style="padding:12px 16px; font-weight:500; color:{{ $outstanding > 0 ? '#ef4444':'#94a3b8' }};">{{ $cur }}{{ number_format($outstanding, 2) }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->issue_date->format('d M Y') }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->due_date?->format('d M Y') ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:capitalize;background:{{ $stBg }};color:{{ $stC }};">{{ __($l->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-hand-holding-dollar" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No loans found.') }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($loans->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $loans->links() }}</div>
    @endif
</div>

@endsection
