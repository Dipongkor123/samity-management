@extends('layouts.app')
@section('title', __('Member Report'))

@php
$cur = '৳';
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Member Report') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Member-wise loan and deposit summary') }}</p>
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

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:180px;">
        <label style="{{ $lbl }}">{{ __('Search') }}</label>
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;"></i>
            <input name="search" value="{{ request('search') }}" placeholder="{{ __('Name or phone...') }}"
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
            <option value="active"   {{ request('status') === 'active'   ? 'selected':'' }}>{{ __('Active') }}</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected':'' }}>{{ __('Inactive') }}</option>
        </select>
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="background:#0d9488;color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-filter"></i> {{ __('Filter') }}
        </button>
        @if(request()->hasAny(['search','samity_id','status']))
            <a href="{{ route('reports.members') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-times"></i> {{ __('Clear') }}
            </a>
        @endif
    </div>
</form>

<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Member List') }} <span style="color:#94a3b8; font-weight:400;">({{ $members->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach(['#', __('Member'), __('Phone'), __('NID'), __('Loans'), __('Total Loan Amt'), __('Total Deposits'), __('Status')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($members as $m)
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $members->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600; color:#1e293b;">{{ $m->name }}</div>
                        <div style="font-size:0.73rem; color:#94a3b8;">{{ $m->email }}</div>
                    </td>
                    <td style="padding:12px 16px; color:#475569;">{{ $m->phone ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#475569; font-family:monospace; font-size:0.78rem;">{{ $m->nid ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:700;padding:3px 9px;border-radius:20px;background:#eff6ff;color:#2563eb;">{{ $m->loans_count }}</span>
                    </td>
                    <td style="padding:12px 16px; font-weight:500; color:#ea580c;">{{ $cur }}{{ number_format($m->loans_sum_amount ?? 0, 2) }}</td>
                    <td style="padding:12px 16px; font-weight:500; color:#16a34a;">{{ $cur }}{{ number_format($m->deposits_sum_amount ?? 0, 2) }}</td>
                    <td style="padding:12px 16px;">
                        @if($m->is_active)
                            <span style="background:#f0fdf4;color:#16a34a;font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;">● {{ __('Active') }}</span>
                        @else
                            <span style="background:#fef2f2;color:#ef4444;font-size:0.73rem;font-weight:600;padding:3px 10px;border-radius:20px;">● {{ __('Inactive') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-users" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No members found.') }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($members->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $members->links() }}</div>
    @endif
</div>

@endsection
