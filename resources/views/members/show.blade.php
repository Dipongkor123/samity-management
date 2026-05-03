@extends('layouts.app')
@section('title', $member->name)

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:12px;">
        <a href="{{ route('members.index') }}" style="width:34px;height:34px;border-radius:8px;background:#f1f5f9;border:1px solid #e2e8f0;display:inline-flex;align-items:center;justify-content:center;color:#64748b;text-decoration:none;">
            <i class="fas fa-arrow-left" style="font-size:13px;"></i>
        </a>
        <div>
            <h1 style="font-size:1.2rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Member Profile') }}</h1>
            <p style="font-size:0.78rem; color:#64748b; margin:2px 0 0;">{{ __('Full details and activity history') }}</p>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:320px 1fr; gap:18px; align-items:start;">

    {{-- LEFT: Profile Card --}}
    <div style="display:flex; flex-direction:column; gap:14px;">

        {{-- Photo + Basic Info --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:24px; text-align:center; box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            @if($member->photo)
                <img src="{{ $member->photo_url }}" alt="{{ $member->name }}"
                    style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid #e2e8f0;margin:0 auto 12px;display:block;">
            @else
                <div style="width:96px;height:96px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0f766e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:2rem;margin:0 auto 12px;">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
            @endif

            <h2 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin:0 0 4px;">{{ $member->name }}</h2>
            <p style="font-size:0.78rem;color:#64748b;margin:0 0 10px;">{{ $member->email }}</p>

            <div style="display:flex;justify-content:center;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
                <span style="font-size:0.72rem;font-weight:700;padding:3px 12px;border-radius:20px;background:{{ $member->isAdmin() ? '#faf5ff':'#f0fdfa' }};color:{{ $member->isAdmin() ? '#9333ea':'#0d9488' }};">
                    {{ $member->isAdmin() ? __('Admin') : __('Member') }}
                </span>
                <span style="font-size:0.72rem;font-weight:700;padding:3px 12px;border-radius:20px;background:{{ $member->is_active ? '#f0fdf4':'#fef2f2' }};color:{{ $member->is_active ? '#16a34a':'#ef4444' }};">
                    {{ $member->is_active ? __('Active') : __('Inactive') }}
                </span>
            </div>

            @php
            $rows = [
                ['fas fa-phone',      $member->phone,   __('Phone')],
                ['fas fa-id-card',    $member->nid,     __('NID')],
                ['fas fa-map-marker-alt', $member->address, __('Address')],
                ['fas fa-briefcase',  $member->occupation, __('Occupation')],
                ['fas fa-tint',       $member->blood_group, __('Blood Group')],
                ['fas fa-birthday-cake', $member->date_of_birth ? $member->date_of_birth->format('d M Y') . ($member->age ? ' (' . $member->age . ' yrs)' : '') : null, __('Date of Birth')],
                ['fas fa-calendar',   $member->created_at->format('d M Y'), __('Joined')],
            ];
            @endphp
            @foreach($rows as [$icon, $val, $label])
                @if($val)
                <div style="display:flex;align-items:flex-start;gap:10px;text-align:left;padding:7px 0;border-top:1px solid #f1f5f9;">
                    <i class="{{ $icon }}" style="color:#94a3b8;font-size:12px;margin-top:3px;width:14px;flex-shrink:0;"></i>
                    <div>
                        <div style="font-size:0.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">{{ $label }}</div>
                        <div style="font-size:0.82rem;color:#374151;font-weight:500;">{{ $val }}</div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        {{-- Financial Summary --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:18px; box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <p style="font-size:0.78rem;font-weight:700;color:#1e293b;margin:0 0 12px;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-chart-pie" style="color:#0d9488;"></i> {{ __('Financial Summary') }}
            </p>
            @php
            $fin = [
                [__('Total Deposits'),  $cur.number_format($summary['total_deposits'],2),  '#16a34a', '#f0fdf4'],
                [__('Total Loans'),     $cur.number_format($summary['total_loans'],2),     '#2563eb', '#eff6ff'],
                [__('Active Loans'),    $summary['active_loans'] . ' ' . __('loan(s)'),    '#ea580c', '#fff7ed'],
                [__('Total Fines'),     $cur.number_format($summary['total_fines'],2),     '#ef4444', '#fef2f2'],
                [__('Pending Fines'),   $cur.number_format($summary['pending_fines'],2),   '#f59e0b', '#fffbeb'],
            ];
            @endphp
            @foreach($fin as [$label, $val, $color, $bg])
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-top:1px solid #f1f5f9;">
                <span style="font-size:0.8rem;color:#64748b;">{{ $label }}</span>
                <span style="font-size:0.82rem;font-weight:600;color:{{ $color }};background:{{ $bg }};padding:2px 10px;border-radius:20px;">{{ $val }}</span>
            </div>
            @endforeach
        </div>

        {{-- Samities --}}
        @if($member->samities->count())
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:18px; box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <p style="font-size:0.78rem;font-weight:700;color:#1e293b;margin:0 0 10px;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-people-group" style="color:#0d9488;"></i> {{ __('Samities') }}
            </p>
            @foreach($member->samities as $s)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-top:1px solid #f1f5f9;">
                <span style="font-size:0.82rem;color:#374151;font-weight:500;">{{ $s->name }}</span>
                <span style="font-size:0.7rem;background:#eff6ff;color:#2563eb;padding:2px 8px;border-radius:20px;font-weight:600;text-transform:capitalize;">{{ $s->cycle_type }}</span>
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- RIGHT: Tabs --}}
    <div>

        {{-- Family Info --}}
        @php
        $hasFamily = $member->father_name || $member->mother_name || $member->spouse_name || $member->emergency_contact || $member->emergency_phone;
        @endphp
        @if($hasFamily)
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:16px; box-shadow:0 1px 6px rgba(0,0,0,0.04); overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-users" style="color:#0d9488;"></i>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Family Information') }}</span>
            </div>
            <div style="padding:18px 20px; display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:16px;">
                @php
                $fam = [
                    [__('Father\'s Name'),        $member->father_name,       'fas fa-user'],
                    [__('Mother\'s Name'),        $member->mother_name,       'fas fa-user'],
                    [__('Spouse\'s Name'),        $member->spouse_name,       'fas fa-heart'],
                    [__('Emergency Contact'),     $member->emergency_contact, 'fas fa-phone-volume'],
                    [__('Emergency Phone'),       $member->emergency_phone,   'fas fa-phone'],
                ];
                @endphp
                @foreach($fam as [$label, $val, $icon])
                    @if($val)
                    <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;">
                        <div style="font-size:0.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;display:flex;align-items:center;gap:5px;">
                            <i class="{{ $icon }}" style="font-size:10px;"></i> {{ $label }}
                        </div>
                        <div style="font-size:0.85rem;font-weight:600;color:#1e293b;">{{ $val }}</div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Status Change Log --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:16px; box-shadow:0 1px 6px rgba(0,0,0,0.04); overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-timeline" style="color:#0d9488;"></i>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Status History') }}</span>
                <span style="margin-left:auto;font-size:0.75rem;color:#94a3b8;">{{ $member->statusLogs->count() }} {{ __('record(s)') }}</span>
            </div>
            <div style="padding:16px 20px;">
                @forelse($member->statusLogs as $log)
                <div style="display:flex;gap:14px;margin-bottom:14px;position:relative;">
                    <div style="flex-shrink:0;width:32px;height:32px;border-radius:50%;background:{{ $log->new_status === 'active' ? '#f0fdf4':'#fef2f2' }};display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-{{ $log->new_status === 'active' ? 'circle-check':'circle-xmark' }}" style="color:{{ $log->new_status === 'active' ? '#16a34a':'#ef4444' }};font-size:14px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:0.82rem;font-weight:600;color:#1e293b;">
                            <span style="color:{{ $log->old_status === 'active' ? '#16a34a':'#ef4444' }};">{{ ucfirst($log->old_status) }}</span>
                            <i class="fas fa-arrow-right" style="font-size:10px;color:#94a3b8;margin:0 6px;"></i>
                            <span style="color:{{ $log->new_status === 'active' ? '#16a34a':'#ef4444' }};">{{ ucfirst($log->new_status) }}</span>
                        </div>
                        <div style="font-size:0.75rem;color:#94a3b8;margin-top:2px;">
                            {{ __('Changed by') }}: <strong style="color:#64748b;">{{ $log->changed_by ?? '—' }}</strong>
                            &nbsp;·&nbsp; {{ $log->created_at->format('d M Y, h:i A') }}
                        </div>
                        @if($log->note)
                        <div style="font-size:0.78rem;color:#64748b;background:#f8fafc;border-radius:6px;padding:6px 10px;margin-top:6px;border-left:3px solid #e2e8f0;">
                            {{ $log->note }}
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:24px;color:#94a3b8;font-size:0.83rem;">
                    <i class="fas fa-timeline" style="font-size:1.8rem;opacity:0.2;display:block;margin-bottom:8px;"></i>
                    {{ __('No status changes recorded yet.') }}
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Deposits --}}
        @if($member->deposits->count())
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:16px; box-shadow:0 1px 6px rgba(0,0,0,0.04); overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-piggy-bank" style="color:#16a34a;"></i>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Recent Deposits') }}</span>
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                <thead><tr style="background:#f8fafc;">
                    <th style="padding:9px 16px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#64748b;">{{ __('Receipt') }}</th>
                    <th style="padding:9px 16px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#64748b;">{{ __('Amount') }}</th>
                    <th style="padding:9px 16px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#64748b;">{{ __('Date') }}</th>
                </tr></thead>
                <tbody>
                @foreach($member->deposits->take(5) as $d)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:9px 16px;color:#64748b;font-family:monospace;">{{ $d->receipt_no ?? '—' }}</td>
                    <td style="padding:9px 16px;font-weight:500;color:#16a34a;">{{ $cur }}{{ number_format($d->amount,2) }}</td>
                    <td style="padding:9px 16px;color:#94a3b8;">{{ $d->deposit_date?->format('d M Y') }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Recent Loans --}}
        @if($member->loans->count())
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.04); overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-hand-holding-dollar" style="color:#2563eb;"></i>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Loans') }}</span>
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                <thead><tr style="background:#f8fafc;">
                    <th style="padding:9px 16px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#64748b;">{{ __('Amount') }}</th>
                    <th style="padding:9px 16px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#64748b;">{{ __('Interest') }}</th>
                    <th style="padding:9px 16px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#64748b;">{{ __('Issue Date') }}</th>
                    <th style="padding:9px 16px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#64748b;">{{ __('Status') }}</th>
                </tr></thead>
                <tbody>
                @foreach($member->loans->take(5) as $ln)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:9px 16px;font-weight:500;color:#2563eb;">{{ $cur }}{{ number_format($ln->amount,2) }}</td>
                    <td style="padding:9px 16px;color:#64748b;">{{ $ln->interest_rate }}%</td>
                    <td style="padding:9px 16px;color:#94a3b8;">{{ $ln->issue_date?->format('d M Y') }}</td>
                    <td style="padding:9px 16px;">
                        @php $sc = ['active'=>['#2563eb','#eff6ff'],'completed'=>['#16a34a','#f0fdf4'],'overdue'=>['#ef4444','#fef2f2']]; $c=$sc[$ln->status]??['#64748b','#f8fafc']; @endphp
                        <span style="font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:20px;color:{{$c[0]}};background:{{$c[1]}};">{{ ucfirst($ln->status) }}</span>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>
</div>

@endsection
