@extends('layouts.app')

@section('title', __('Reports'))

@section('content')

    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Reports & Analytics') }}</h1>
            <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Overview of all financial activity') }}</p>
        </div>
        <button onclick="window.print()" style="display:inline-flex; align-items:center; gap:8px; background:#1e293b; color:#fff; font-size:0.83rem; font-weight:600; padding:9px 18px; border-radius:10px; border:none; cursor:pointer;">
            <i class="fas fa-print"></i> {{ __('Print Report') }}
        </button>
    </div>

    {{-- Quick Report Links --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:24px;">
        <a href="{{ route('reports.members') }}" style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 20px; text-decoration:none; display:flex; align-items:center; gap:14px; transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(37,99,235,0.12)'; this.style.borderColor='#93c5fd';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0';">
            <div style="width:42px; height:42px; background:#eff6ff; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-users" style="color:#2563eb; font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Member Report') }}</div>
                <div style="font-size:0.74rem; color:#94a3b8; margin-top:2px;">{{ __('With loan & deposit summary') }}</div>
            </div>
        </a>
        <a href="{{ route('reports.loans') }}" style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 20px; text-decoration:none; display:flex; align-items:center; gap:14px; transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(234,88,12,0.12)'; this.style.borderColor='#fdba74';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0';">
            <div style="width:42px; height:42px; background:#fff7ed; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-hand-holding-dollar" style="color:#ea580c; font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Loan Report') }}</div>
                <div style="font-size:0.74rem; color:#94a3b8; margin-top:2px;">{{ __('Disbursement & repayment') }}</div>
            </div>
        </a>
        <a href="{{ route('reports.collections') }}" style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 20px; text-decoration:none; display:flex; align-items:center; gap:14px; transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(22,163,74,0.12)'; this.style.borderColor='#86efac';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0';">
            <div style="width:42px; height:42px; background:#f0fdf4; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-receipt" style="color:#16a34a; font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Collection Report') }}</div>
                <div style="font-size:0.74rem; color:#94a3b8; margin-top:2px;">{{ __('Period-wise repayments') }}</div>
            </div>
        </a>
        <a href="{{ route('reports.defaulters') }}" style="background:#fff; border:1px solid #fecaca; border-radius:14px; padding:18px 20px; text-decoration:none; display:flex; align-items:center; gap:14px; transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(220,38,38,0.12)'; this.style.borderColor='#f87171';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#fecaca';">
            <div style="width:42px; height:42px; background:#fef2f2; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-user-slash" style="color:#dc2626; font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:0.88rem; font-weight:700; color:#dc2626;">{{ __('Defaulter Report') }}</div>
                <div style="font-size:0.74rem; color:#94a3b8; margin-top:2px;">{{ __('Overdue installments') }}</div>
            </div>
        </a>
    </div>

    {{-- Summary Stats --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:14px;">
        <x-stat-card :label="__('Total Members')"   :value="$stats['total_members']"                                :sub="$stats['active_members'] . ' active'"                        icon="fas fa-users"              bg="#eff6ff" iconColor="#2563eb" />
        <x-stat-card :label="__('Total Samities')"  :value="$stats['total_samities']"                               :sub="$stats['active_samities'] . ' active'"                       icon="fas fa-people-group"       bg="#f0fdfa" iconColor="#0d9488" />
        <x-stat-card :label="__('Total Deposits')"  :value="$cur . number_format($stats['total_deposits'], 2)"       :sub="__('This month') . ': ' . $cur . number_format($stats['this_month_deposits'], 2)" icon="fas fa-piggy-bank"     bg="#f0fdf4" iconColor="#16a34a" />
        <x-stat-card :label="__('Active Loans')"    :value="$cur . number_format($stats['active_loans'], 2)"         :sub="__('Total') . ': ' . $cur . number_format($stats['total_loans'], 2)"         icon="fas fa-hand-holding-dollar" bg="#fff7ed" iconColor="#ea580c" />
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:24px;">
        <x-stat-card :label="__('Total Repaid')"    :value="$cur . number_format($stats['total_repaid'], 2)"         icon="fas fa-rotate-left"          bg="#eef2ff" iconColor="#4f46e5" />
        <x-stat-card :label="__('Fines Issued')"    :value="$cur . number_format($stats['total_fines'], 2)"          icon="fas fa-triangle-exclamation" bg="#fef2f2" iconColor="#ef4444" />
        <x-stat-card :label="__('Fines Collected')" :value="$cur . number_format($stats['collected_fines'], 2)"      :sub="__('Pending') . ': ' . $cur . number_format($stats['pending_fines'], 2)" icon="fas fa-circle-check" bg="#f0fdf4" iconColor="#16a34a" />
    </div>

    {{-- Charts --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">

        {{-- Deposits Chart --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); padding:20px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                <div style="width:8px; height:8px; border-radius:50%; background:#22c55e;"></div>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Monthly Deposits') }}</span>
                <span style="font-size:0.75rem; color:#94a3b8; margin-left:auto;">{{ __('Last 6 months') }}</span>
            </div>
            @php $maxDep = max(collect($monthlyDeposits)->pluck('amount')->max(), 1); @endphp
            <div style="display:flex; align-items:flex-end; gap:8px; height:140px; padding-bottom:4px;">
                @foreach($monthlyDeposits as $row)
                    @php $h = max(6, round(($row['amount'] / $maxDep) * 120)); @endphp
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; height:100%; justify-content:flex-end;">
                        @if($row['amount'] > 0)
                            <span style="font-size:0.65rem; color:#64748b; white-space:nowrap;">{{ $cur }}{{ number_format($row['amount'], 0) }}</span>
                        @endif
                        <div style="width:100%; height:{{ $h }}px; background:linear-gradient(180deg, #22c55e, #16a34a); border-radius:6px 6px 0 0; transition:all 0.3s;" title="{{ $row['month'] }}: {{ $cur }}{{ number_format($row['amount'], 2) }}"></div>
                        <span style="font-size:0.65rem; color:#94a3b8; text-align:center;">{{ $row['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Loans Chart --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); padding:20px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                <div style="width:8px; height:8px; border-radius:50%; background:#3b82f6;"></div>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Loans Issued') }}</span>
                <span style="font-size:0.75rem; color:#94a3b8; margin-left:auto;">{{ __('Last 6 months') }}</span>
            </div>
            @php $maxLoan = max(collect($monthlyLoans)->pluck('amount')->max(), 1); @endphp
            <div style="display:flex; align-items:flex-end; gap:8px; height:140px; padding-bottom:4px;">
                @foreach($monthlyLoans as $row)
                    @php $h = max(6, round(($row['amount'] / $maxLoan) * 120)); @endphp
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; height:100%; justify-content:flex-end;">
                        @if($row['amount'] > 0)
                            <span style="font-size:0.65rem; color:#64748b; white-space:nowrap;">{{ $cur }}{{ number_format($row['amount'], 0) }}</span>
                        @endif
                        <div style="width:100%; height:{{ $h }}px; background:linear-gradient(180deg, #60a5fa, #2563eb); border-radius:6px 6px 0 0; transition:all 0.3s;" title="{{ $row['month'] }}: {{ $cur }}{{ number_format($row['amount'], 2) }}"></div>
                        <span style="font-size:0.65rem; color:#94a3b8; text-align:center;">{{ $row['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Samity Summary Table --}}
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-people-group" style="color:#0d9488;"></i>
            <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Samity-wise Summary') }}</span>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
                <thead>
                    <tr style="background:#f8fafc;">
                        @foreach([__('Samity Name'), __('Cycle'), __('Members'), __('Total Deposits'), __('Total Loans'), __('Status')] as $th)
                        <th style="text-align:left; padding:11px 16px; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#64748b; white-space:nowrap;">{{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($samities as $samity)
                        <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                            <td style="padding:13px 16px; font-weight:600; color:#1e293b;">{{ $samity->name }}</td>
                            <td style="padding:13px 16px;">
                                <span style="font-size:0.73rem; font-weight:600; background:#eff6ff; color:#2563eb; padding:3px 10px; border-radius:20px; text-transform:capitalize;">{{ $samity->cycle_type }}</span>
                            </td>
                            <td style="padding:13px 16px; font-weight:500; color:#374151;">{{ $samity->members_count }}</td>
                            <td style="padding:13px 16px; font-weight:500; color:#16a34a;">{{ $cur }}{{ number_format($samity->deposits_sum_amount ?? 0, 2) }}</td>
                            <td style="padding:13px 16px; font-weight:500; color:#2563eb;">{{ $cur }}{{ number_format($samity->loans_sum_amount ?? 0, 2) }}</td>
                            <td style="padding:13px 16px;">
                                <span style="font-size:0.73rem; font-weight:700; padding:3px 10px; border-radius:20px; background:{{ $samity->is_active ? '#f0fdf4' : '#fef2f2' }}; color:{{ $samity->is_active ? '#16a34a' : '#ef4444' }};">
                                    {{ $samity->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:32px 16px; text-align:center; color:#94a3b8;">{{ __('No samities found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
