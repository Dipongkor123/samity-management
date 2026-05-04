@extends('layouts.app')
@section('title', __('Bulk Collection'))

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
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Bulk Collection Entry') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Record field collection for a samity in one go') }}</p>
    </div>
    <a href="{{ route('collection.daily') }}" style="background:#1e293b; color:#fff; border:none; border-radius:10px; padding:10px 18px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; text-decoration:none;">
        <i class="fas fa-calendar-day"></i> {{ __('Daily Summary') }}
    </a>
</div>

{{-- Samity Selector --}}
<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:20px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:220px;">
        <label style="{{ $lbl }}">{{ __('Select Samity') }} *</label>
        <select name="samity_id" required style="{{ $inp }}">
            <option value="">{{ __('-- Choose Samity --') }}</option>
            @foreach($samities as $s)
                <option value="{{ $s->id }}" {{ request('samity_id') == $s->id ? 'selected':'' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" style="{{ $btn_primary }}">
        <i class="fas fa-users"></i> {{ __('Load Members') }}
    </button>
</form>

@if($selectedSamity)
<form action="{{ route('collection.store-bulk') }}" method="POST">
    @csrf
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
        <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#0d9488,#0f766e);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;">
                    <i class="fas fa-people-group" style="font-size:15px;"></i>
                </div>
                <div>
                    <div style="font-size:0.9rem;font-weight:700;color:#1e293b;">{{ $selectedSamity->name }}</div>
                    <div style="font-size:0.73rem;color:#94a3b8;">{{ $activeLoans->count() }} {{ __('active loans') }}</div>
                </div>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Collection Date') }} *</label>
                <input type="date" name="collection_date" value="{{ date('Y-m-d') }}" required style="{{ $inp }} width:auto;">
            </div>
        </div>

        @if($activeLoans->isEmpty())
            <div style="padding:48px;text-align:center;color:#94a3b8;">
                <i class="fas fa-inbox" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>
                {{ __('No active loans found for this samity.') }}
            </div>
        @else
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
                <thead>
                    <tr style="background:#f8fafc;">
                        @foreach([__('Member'), __('Loan Amount'), __('EMI'), __('Next Due Date'), __('Overdue'), __('Amount to Pay'), __('Note')] as $h)
                        <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeLoans as $i => $loan)
                    @php
                        $nextDue    = $loan->next_due;
                        $isOverdue  = $nextDue && $nextDue->due_date->isPast();
                        $overdueCnt = $loan->schedules->where('status','!=','paid')->filter(fn($s) => $s->due_date->isPast())->count();
                    @endphp
                    <input type="hidden" name="payments[{{ $i }}][loan_id]" value="{{ $loan->id }}">
                    <tr style="border-top:1px solid #f1f5f9; {{ $isOverdue ? 'background:#fffbeb;' : '' }}"
                        onmouseover="this.style.background='{{ $isOverdue ? '#fef9c3':'#f8fafc' }}'"
                        onmouseout="this.style.background='{{ $isOverdue ? '#fffbeb':'' }}'">
                        <td style="padding:12px 16px;">
                            <div style="font-weight:600; color:#1e293b;">{{ $loan->user->name }}</div>
                            <div style="font-size:0.73rem; color:#94a3b8;">{{ $loan->user->phone ?? '' }}</div>
                        </td>
                        <td style="padding:12px 16px; color:#475569;">{{ $cur }}{{ number_format($loan->amount, 2) }}</td>
                        <td style="padding:12px 16px; font-weight:600; color:#0d9488;">{{ $cur }}{{ number_format($loan->monthly_installment, 2) }}</td>
                        <td style="padding:12px 16px;">
                            @if($nextDue)
                                <span style="color:{{ $isOverdue ? '#dc2626':'#374151' }}; font-weight:{{ $isOverdue ? '700':'400' }};">
                                    {{ $nextDue->due_date->format('d M Y') }}
                                    @if($isOverdue)
                                        <span style="font-size:0.7rem;background:#fef2f2;color:#dc2626;padding:1px 6px;border-radius:10px;margin-left:4px;">{{ __('Overdue') }}</span>
                                    @endif
                                </span>
                            @else
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;">
                            @if($overdueCnt > 0)
                                <span style="font-size:0.73rem;font-weight:700;padding:3px 9px;border-radius:20px;background:#fef2f2;color:#ef4444;">{{ $overdueCnt }}</span>
                            @else
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;">
                            <input type="number" name="payments[{{ $i }}][amount_paid]" step="0.01" min="0"
                                placeholder="{{ number_format($loan->monthly_installment, 2) }}"
                                style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:0.83rem;width:130px;color:#1e293b;outline:none;"
                                onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e2e8f0'">
                        </td>
                        <td style="padding:12px 16px;">
                            <input type="text" name="payments[{{ $i }}][note]" placeholder="{{ __('Optional') }}"
                                style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:0.83rem;width:150px;color:#1e293b;outline:none;"
                                onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e2e8f0'">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:14px 20px; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('collection.bulk') }}" style="{{ $btn_cancel }} text-decoration:none;">{{ __('Reset') }}</a>
            <button type="submit" style="background:linear-gradient(135deg,#16a34a,#15803d); color:#fff; border:none; border-radius:10px; padding:10px 22px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(22,163,74,0.3);">
                <i class="fas fa-check-double"></i> {{ __('Save All Collections') }}
            </button>
        </div>
        @endif
    </div>
</form>
@endif

@endsection
