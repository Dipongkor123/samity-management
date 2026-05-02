@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- Welcome Banner --}}
    <div style="background:linear-gradient(135deg, #0f172a 0%, #134e4a 100%); border-radius:16px; padding:22px 24px; margin-bottom:24px; display:flex; align-items:center; gap:16px;">
        <div style="width:50px; height:50px; background:rgba(255,255,255,0.15); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.3rem; font-weight:800; flex-shrink:0; border:2px solid rgba(255,255,255,0.2);">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <div style="color:#f8fafc; font-weight:700; font-size:1.1rem;">Welcome back, {{ $user->name }}!</div>
            <div style="color:#5eead4; font-size:0.82rem; margin-top:2px;">
                {{ $user->isAdmin() ? 'Administrator' : 'Member' }} &bull; {{ now()->format('l, d F Y') }}
            </div>
        </div>
        <div style="margin-left:auto; text-align:right; display:none;" class="hide-mobile">
            <div style="color:#5eead4; font-size:0.75rem;">Last login</div>
            <div style="color:#f8fafc; font-size:0.82rem; font-weight:600;">{{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    {{-- Stats Row 1 --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px; margin-bottom:14px;">
        <a href="{{ route('members.index') }}" style="text-decoration:none; display:block;">
            <x-stat-card label="Total Members" :value="\App\Models\User::count()" icon="fas fa-users" bg="#eff6ff" iconColor="#2563eb" />
        </a>
        <a href="{{ route('samities.index') }}" style="text-decoration:none; display:block;">
            <x-stat-card label="Total Samities" :value="\App\Models\Samity::count()" icon="fas fa-people-group" bg="#f0fdfa" iconColor="#0d9488" />
        </a>
        <a href="{{ route('deposits.index') }}" style="text-decoration:none; display:block;">
            <x-stat-card label="Total Deposits" :value="'৳' . number_format(\App\Models\Deposit::sum('amount'), 2)" icon="fas fa-piggy-bank" bg="#f0fdf4" iconColor="#16a34a" />
        </a>
        <a href="{{ route('loans.index') }}" style="text-decoration:none; display:block;">
            <x-stat-card label="Active Loans" :value="'৳' . number_format(\App\Models\Loan::where('status','active')->sum('amount'), 2)" icon="fas fa-hand-holding-dollar" bg="#fff7ed" iconColor="#ea580c" />
        </a>
    </div>

    {{-- Stats Row 2 --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px; margin-bottom:24px;">
        <a href="{{ route('repayments.index') }}" style="text-decoration:none; display:block;">
            <x-stat-card label="Total Repaid" :value="'৳' . number_format(\App\Models\LoanRepayment::sum('amount_paid'), 2)" icon="fas fa-rotate-left" bg="#eef2ff" iconColor="#4f46e5" />
        </a>
        <a href="{{ route('fines.index') }}" style="text-decoration:none; display:block;">
            <x-stat-card label="Pending Fines" :value="'৳' . number_format(\App\Models\Fine::where('status','pending')->sum('amount'), 2)" icon="fas fa-triangle-exclamation" bg="#fef2f2" iconColor="#ef4444" />
        </a>
        <a href="{{ route('loans.index') }}" style="text-decoration:none; display:block;">
            <x-stat-card label="Overdue Loans" :value="\App\Models\Loan::where('status','overdue')->count()" icon="fas fa-clock" bg="#fefce8" iconColor="#ca8a04" />
        </a>
        <a href="{{ route('members.index') }}" style="text-decoration:none; display:block;">
            <x-stat-card label="Active Members" :value="\App\Models\User::where('is_active', true)->count()" icon="fas fa-user-check" bg="#faf5ff" iconColor="#9333ea" />
        </a>
    </div>

    {{-- Bottom Grid --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

        {{-- Quick Actions --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:20px; box-shadow:0 1px 6px rgba(0,0,0,0.05);">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #f1f5f9;">
                <i class="fas fa-bolt" style="color:#f59e0b;"></i>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">Quick Actions</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                @php
                    $actions = [
                        ['route' => 'deposits.index',   'icon' => 'fas fa-piggy-bank',          'label' => 'Deposits',    'bg' => '#f0fdf4', 'color' => '#16a34a'],
                        ['route' => 'loans.index',      'icon' => 'fas fa-hand-holding-dollar',  'label' => 'Loans',       'bg' => '#eff6ff', 'color' => '#2563eb'],
                        ['route' => 'repayments.index', 'icon' => 'fas fa-rotate-left',          'label' => 'Repayments',  'bg' => '#eef2ff', 'color' => '#4f46e5'],
                        ['route' => 'fines.index',      'icon' => 'fas fa-triangle-exclamation', 'label' => 'Fines',       'bg' => '#fef2f2', 'color' => '#ef4444'],
                        ['route' => 'members.index',    'icon' => 'fas fa-users',                'label' => 'Members',     'bg' => '#faf5ff', 'color' => '#9333ea'],
                        ['route' => 'reports.index',    'icon' => 'fas fa-chart-bar',            'label' => 'Reports',     'bg' => '#f8fafc', 'color' => '#475569'],
                    ];
                @endphp
                @foreach($actions as $action)
                    <a href="{{ route($action['route']) }}" style="display:flex; align-items:center; gap:10px; padding:11px 12px; border-radius:10px; border:1px solid #e2e8f0; text-decoration:none; transition:all 0.15s;" onmouseover="this.style.borderColor='#99f6e4'; this.style.background='#f0fdfa';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#fff';">
                        <div style="width:34px; height:34px; border-radius:8px; background:{{ $action['bg'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="{{ $action['icon'] }}" style="color:{{ $action['color'] }}; font-size:0.85rem;"></i>
                        </div>
                        <span style="font-size:0.83rem; font-weight:600; color:#374151;">{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Account Info --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:20px; box-shadow:0 1px 6px rgba(0,0,0,0.05);">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #f1f5f9;">
                <i class="fas fa-circle-user" style="color:#0d9488;"></i>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">My Account</span>
            </div>
            @php
                $fields = [
                    ['label' => 'Full Name', 'value' => $user->name],
                    ['label' => 'Email',     'value' => $user->email],
                    ['label' => 'Phone',     'value' => $user->phone ?? '—'],
                    ['label' => 'NID',       'value' => $user->nid ?? '—'],
                    ['label' => 'Address',   'value' => $user->address ?? '—'],
                ];
            @endphp
            @foreach($fields as $field)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid #f8fafc;">
                    <span style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8;">{{ $field['label'] }}</span>
                    <span style="font-size:0.83rem; font-weight:500; color:#1e293b; max-width:180px; text-align:right; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $field['value'] }}</span>
                </div>
            @endforeach
            <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid #f8fafc;">
                <span style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8;">Role</span>
                <span style="font-size:0.73rem; font-weight:700; padding:3px 10px; border-radius:20px; background:{{ $user->isAdmin() ? '#faf5ff' : '#f0fdfa' }}; color:{{ $user->isAdmin() ? '#9333ea' : '#0d9488' }};">
                    {{ ucfirst($user->role ?? 'member') }}
                </span>
            </div>
            <div style="margin-top:14px;">
                <a href="{{ route('change-password') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:0.82rem; font-weight:600; color:#0d9488; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    <i class="fas fa-key" style="font-size:0.75rem;"></i> Change Password
                </a>
            </div>
        </div>
    </div>

@endsection
