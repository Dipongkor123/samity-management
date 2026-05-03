@extends('layouts.app')
@section('title', $samity->name)

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
@endphp

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:12px;">
        <a href="{{ route('samities.index') }}" style="width:34px;height:34px;border-radius:8px;background:#f1f5f9;border:1px solid #e2e8f0;display:inline-flex;align-items:center;justify-content:center;color:#64748b;text-decoration:none;">
            <i class="fas fa-arrow-left" style="font-size:13px;"></i>
        </a>
        <div>
            <h1 style="font-size:1.2rem; font-weight:800; color:#0f172a; margin:0;">{{ $samity->name }}</h1>
            <p style="font-size:0.78rem; color:#64748b; margin:2px 0 0;">{{ __('Samity Details & Member Management') }}</p>
        </div>
    </div>
    <span style="font-size:0.78rem;font-weight:700;padding:5px 14px;border-radius:20px;background:{{ $samity->is_active ? '#f0fdf4':'#fef2f2' }};color:{{ $samity->is_active ? '#16a34a':'#ef4444' }};">
        ● {{ $samity->is_active ? __('Active') : __('Inactive') }}
    </span>
</div>

{{-- Flash messages handled globally via SweetAlert2 in layouts/app.blade.php --}}

<div style="display:grid; grid-template-columns:300px 1fr; gap:18px; align-items:start;">

    {{-- LEFT: Info + Summary --}}
    <div style="display:flex; flex-direction:column; gap:14px;">

        {{-- Samity Info Card --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:20px; box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <div style="display:flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#0d9488,#0f766e);margin:0 auto 14px;">
                <i class="fas fa-people-group" style="color:#fff;font-size:22px;"></i>
            </div>
            <h3 style="text-align:center;font-size:1rem;font-weight:700;color:#1e293b;margin:0 0 4px;">{{ $samity->name }}</h3>
            @if($samity->description)
                <p style="text-align:center;font-size:0.78rem;color:#94a3b8;margin:0 0 14px;">{{ $samity->description }}</p>
            @endif

            @php
            $cycleColors = ['weekly'=>['#eff6ff','#2563eb'],'monthly'=>['#f0fdfa','#0d9488'],'yearly'=>['#faf5ff','#9333ea']];
            $cc = $cycleColors[$samity->cycle_type] ?? ['#f8fafc','#64748b'];
            @endphp

            <div style="text-align:center;margin-bottom:16px;">
                <span style="background:{{ $cc[0] }};color:{{ $cc[1] }};font-size:0.78rem;font-weight:700;padding:4px 14px;border-radius:20px;text-transform:capitalize;">
                    <i class="fas fa-rotate"></i> {{ ucfirst($samity->cycle_type) }}
                </span>
            </div>

            @php
            $info = [
                ['fas fa-coins',       __('Deposit Amount'),  $cur . number_format($samity->deposit_amount, 2)],
                ['fas fa-calendar',    __('Start Date'),      $samity->start_date?->format('d M Y') ?? '—'],
                ['fas fa-clock',       __('Meeting Day'),     $samity->meeting_day ?? '—'],
                ['fas fa-users',       __('Total Members'),   $samity->members_count],
            ];
            @endphp
            @foreach($info as [$icon, $label, $val])
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-top:1px solid #f1f5f9;">
                <span style="font-size:0.78rem;color:#64748b;display:flex;align-items:center;gap:7px;">
                    <i class="{{ $icon }}" style="color:#94a3b8;width:14px;text-align:center;"></i> {{ $label }}
                </span>
                <span style="font-size:0.82rem;font-weight:600;color:#1e293b;">{{ $val }}</span>
            </div>
            @endforeach
        </div>

        {{-- Financial Summary --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:18px; box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <p style="font-size:0.78rem;font-weight:700;color:#1e293b;margin:0 0 12px;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-chart-pie" style="color:#0d9488;"></i> {{ __('Financial Summary') }}
            </p>
            @php $fin = [
                [__('Total Deposits'), $cur.number_format($summary['total_deposits'],2), '#16a34a','#f0fdf4'],
                [__('Total Loans'),    $cur.number_format($summary['total_loans'],2),    '#2563eb','#eff6ff'],
                [__('Active Loans'),   $summary['active_loans'].' '.__('loan(s)'),       '#ea580c','#fff7ed'],
                [__('Total Fines'),    $cur.number_format($summary['total_fines'],2),    '#ef4444','#fef2f2'],
            ]; @endphp
            @foreach($fin as [$label,$val,$color,$bg])
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-top:1px solid #f1f5f9;">
                <span style="font-size:0.8rem;color:#64748b;">{{ $label }}</span>
                <span style="font-size:0.8rem;font-weight:600;color:{{ $color }};background:{{ $bg }};padding:2px 10px;border-radius:20px;">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- RIGHT: Member Management --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Assign Member Form --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.04); overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-user-plus" style="color:#0d9488;"></i>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Assign Member to Samity') }}</span>
            </div>
            <div style="padding:18px 20px;">
                @if($availableUsers->count())
                <form action="{{ route('samities.members.assign', $samity) }}" method="POST">
                    @csrf
                    <div style="display:grid; grid-template-columns:1fr 1fr auto; gap:12px; align-items:flex-end;">
                        <div>
                            <label style="{{ $lbl }}">{{ __('Select Member') }} *</label>
                            <select name="user_id" style="{{ $inp }}" required>
                                <option value="">{{ __('Choose a member...') }}</option>
                                @foreach($availableUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->phone ?? $u->email }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="{{ $lbl }}">{{ __('Join Date') }} *</label>
                            <input type="date" name="joined_at" value="{{ date('Y-m-d') }}" style="{{ $inp }}" required>
                        </div>
                        <button type="submit" style="background:linear-gradient(135deg,#0d9488,#0f766e);color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:0.83rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;">
                            <i class="fas fa-plus"></i> {{ __('Assign') }}
                        </button>
                    </div>
                </form>
                @else
                <p style="font-size:0.83rem;color:#94a3b8;margin:0;text-align:center;padding:10px 0;">
                    <i class="fas fa-circle-info" style="margin-right:6px;"></i>{{ __('All active members are already assigned to this samity.') }}
                </p>
                @endif
            </div>
        </div>

        {{-- Member List --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.04); overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-users" style="color:#0d9488;"></i>
                <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Assigned Members') }}</span>
                <span style="margin-left:auto;background:#f0fdfa;color:#0d9488;font-size:0.75rem;font-weight:700;padding:2px 10px;border-radius:20px;">
                    {{ $samity->members_count }} {{ __('member(s)') }}
                </span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            @foreach(['#', __('Member'), __('Phone'), __('Joined'), __('Status'), __('Actions')] as $h)
                            <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($samity->members as $i => $m)
                        <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <td style="padding:11px 16px; color:#94a3b8; font-size:0.8rem;">{{ $i + 1 }}</td>
                            <td style="padding:11px 16px;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    @if($m->photo)
                                        <img src="{{ $m->photo_url }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0;">
                                    @else
                                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0f766e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;flex-shrink:0;">
                                            {{ strtoupper(substr($m->name,0,1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight:600;color:#1e293b;">{{ $m->name }}</div>
                                        <div style="font-size:0.73rem;color:#94a3b8;">{{ $m->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:11px 16px; color:#475569;">{{ $m->phone ?? '—' }}</td>
                            <td style="padding:11px 16px; color:#475569;">
                                {{ $m->pivot->joined_at ? \Carbon\Carbon::parse($m->pivot->joined_at)->format('d M Y') : '—' }}
                            </td>
                            <td style="padding:11px 16px;">
                                @if($m->pivot->is_active)
                                    <span style="background:#f0fdf4;color:#16a34a;font-size:0.72rem;font-weight:600;padding:3px 10px;border-radius:20px;">● {{ __('Active') }}</span>
                                @else
                                    <span style="background:#fef2f2;color:#ef4444;font-size:0.72rem;font-weight:600;padding:3px 10px;border-radius:20px;">● {{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td style="padding:11px 16px;">
                                <div style="display:flex;gap:6px;">
                                    {{-- Toggle Active --}}
                                    <button type="button"
                                        onclick="confirmToggleMember('{{ route('samities.members.toggle', [$samity, $m]) }}','{{ addslashes($m->name) }}',{{ $m->pivot->is_active ? 'true':'false' }})"
                                        style="width:30px;height:30px;border-radius:8px;border:1px solid {{ $m->pivot->is_active ? '#fcd34d':'#99f6e4' }};background:{{ $m->pivot->is_active ? '#fffbeb':'#f0fdfa' }};color:{{ $m->pivot->is_active ? '#d97706':'#0d9488' }};cursor:pointer;font-size:12px;"
                                        title="{{ $m->pivot->is_active ? __('Set Inactive') : __('Set Active') }}">
                                        <i class="fas fa-{{ $m->pivot->is_active ? 'toggle-on':'toggle-off' }}"></i>
                                    </button>
                                    {{-- Remove --}}
                                    <button type="button"
                                        onclick="confirmRemoveMember('{{ route('samities.members.remove', [$samity, $m]) }}','{{ addslashes($m->name) }}')"
                                        style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;"
                                        title="{{ __('Remove') }}">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding:40px; text-align:center; color:#94a3b8;">
                                <i class="fas fa-users" style="font-size:2rem;opacity:.2;display:block;margin-bottom:8px;"></i>
                                {{ __('No members assigned yet. Use the form above to assign members.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function _swalSubmit(url, method) {
    const f = document.createElement('form');
    f.method = 'POST'; f.action = url;
    f.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="${method}">`;
    document.body.appendChild(f); f.submit();
}

function confirmToggleMember(url, name, isActive) {
    const toActive = !isActive;
    Swal.fire({
        html: toActive
            ? `<div class="sm-dlg-icon sm-dlg-icon--teal"><i class="fas fa-circle-check"></i></div>
               <h3 class="sm-dlg-title">{{ __("Set Active?") }}</h3>
               <p class="sm-dlg-body">{{ __("Member") }} <strong style="color:#1e293b;">${name}</strong> {{ __("will be marked as active in this samity.") }}</p>`
            : `<div class="sm-dlg-icon sm-dlg-icon--warning"><i class="fas fa-circle-pause"></i></div>
               <h3 class="sm-dlg-title">{{ __("Set Inactive?") }}</h3>
               <p class="sm-dlg-body">{{ __("Member") }} <strong style="color:#1e293b;">${name}</strong> {{ __("will be marked as inactive in this samity.") }}</p>`,
        showCancelButton: true,
        confirmButtonText: toActive
            ? '<i class="fas fa-toggle-on"></i> {{ __("Set Active") }}'
            : '<i class="fas fa-toggle-off"></i> {{ __("Set Inactive") }}',
        cancelButtonText: '{{ __("Cancel") }}',
        reverseButtons: true,
        focusCancel: true,
        buttonsStyling: false,
        customClass: {
            popup:         'sm-dlg-popup',
            actions:       'sm-dlg-actions',
            confirmButton: toActive ? 'sm-dlg-btn sm-dlg-btn--teal' : 'sm-dlg-btn sm-dlg-btn--warning',
            cancelButton:  'sm-dlg-btn sm-dlg-btn--ghost',
        }
    }).then(r => { if (r.isConfirmed) _swalSubmit(url, 'PATCH'); });
}

function confirmRemoveMember(url, name) {
    Swal.fire({
        html: `<div class="sm-dlg-icon sm-dlg-icon--danger"><i class="fas fa-user-minus"></i></div>
               <h3 class="sm-dlg-title">{{ __("Remove Member?") }}</h3>
               <p class="sm-dlg-body">{{ __("Remove") }} <strong style="color:#1e293b;">${name}</strong> {{ __("from this samity?") }}<br>{{ __("This action cannot be undone.") }}</p>`,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-user-minus"></i> {{ __("Remove") }}',
        cancelButtonText: '{{ __("Cancel") }}',
        reverseButtons: true,
        focusCancel: true,
        buttonsStyling: false,
        customClass: {
            popup:         'sm-dlg-popup',
            actions:       'sm-dlg-actions',
            confirmButton: 'sm-dlg-btn sm-dlg-btn--danger',
            cancelButton:  'sm-dlg-btn sm-dlg-btn--ghost',
        }
    }).then(r => { if (r.isConfirmed) _swalSubmit(url, 'DELETE'); });
}
</script>
@endpush
