@extends('layouts.app')
@section('title', __('Loans'))

@php
$inp = 'width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; color:#374151; outline:none; box-sizing:border-box; background:#fff;';
$lbl = 'display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;';
$btn_primary = 'background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(13,148,136,0.3);';
$btn_cancel  = 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:10px; padding:10px 20px; font-size:0.85rem; font-weight:600; cursor:pointer;';
@endphp

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0;">{{ __('Loans') }}</h1>
        <p style="font-size:0.8rem; color:#64748b; margin:3px 0 0;">{{ __('Manage all member loan accounts') }}</p>
    </div>
    <button onclick="openModal('modal-create')" style="{{ $btn_primary }}">
        <i class="fas fa-plus"></i> {{ __('Issue Loan') }}
    </button>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
    <x-stat-card :label="__('Total Disbursed')" :value="$cur . number_format($stats['total_amount'], 2)"  icon="fas fa-hand-holding-dollar" bg="#eff6ff" iconColor="#2563eb" />
    <x-stat-card :label="__('Active Loans')"    :value="$stats['active_count']"  :sub="$cur . number_format($stats['active_amount'], 2)" icon="fas fa-circle-dot" bg="#f0fdfa" iconColor="#0d9488" />
    <x-stat-card :label="__('Completed')"       :value="$stats['completed_count']" icon="fas fa-circle-check"       bg="#f0fdf4" iconColor="#16a34a" />
    <x-stat-card :label="__('Overdue')"         :value="$stats['overdue_count']"   icon="fas fa-circle-exclamation" bg="#fef2f2" iconColor="#ef4444" />
</div>

<form method="GET" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
    <div style="flex:1; min-width:180px;">
        <label style="{{ $lbl }}">{{ __('Search Member') }}</label>
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;"></i>
            <input name="search" value="{{ request('search') }}" placeholder="{{ __('Member name...') }}"
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
        <label style="{{ $lbl }}">{{ __('Interest Type') }}</label>
        <select name="interest_type" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All Types') }}</option>
            <option value="flat"      {{ request('interest_type') === 'flat'      ? 'selected':'' }}>{{ __('Flat Rate') }}</option>
            <option value="declining" {{ request('interest_type') === 'declining' ? 'selected':'' }}>{{ __('Declining Balance') }}</option>
        </select>
    </div>
    <div>
        <label style="{{ $lbl }}">{{ __('Status') }}</label>
        <select name="status" style="{{ $inp }} width:auto;">
            <option value="">{{ __('All') }}</option>
            @foreach(['active','completed','overdue'] as $st)
                <option value="{{ $st }}" {{ request('status') === $st ? 'selected':'' }}>{{ ucfirst($st) }}</option>
            @endforeach
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
        @if(request()->hasAny(['search','samity_id','status','interest_type','from','to']))
            <a href="{{ route('loans.index') }}" style="background:#f1f5f9;color:#64748b;border-radius:8px;padding:9px 14px;font-size:0.83rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-times"></i> {{ __('Clear') }}
            </a>
        @endif
    </div>
</form>

@if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#15803d;font-size:0.85rem;">
        <i class="fas fa-circle-check" style="margin-right:6px;"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#991b1b;font-size:0.85rem;">
        <i class="fas fa-circle-exclamation" style="margin-right:6px;"></i>{{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
        @foreach($errors->all() as $e)
            <p style="margin:2px 0;font-size:0.83rem;color:#991b1b;"><i class="fas fa-circle-exclamation" style="margin-right:6px;"></i>{{ $e }}</p>
        @endforeach
    </div>
@endif

<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,0.05); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9;">
        <span style="font-size:0.88rem; font-weight:700; color:#1e293b;">{{ __('Loan Records') }} <span style="color:#94a3b8; font-weight:400;">({{ $loans->total() }})</span></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    @foreach([__('#'),__('Member'),__('Samity'),__('Amount'),__('Interest Type'),__('Rate'),__('Duration'),__('EMI'),__('Issue Date'),__('Due Date'),__('Repaid'),__('Status'),__('Actions')] as $h)
                    <th style="text-align:left; padding:10px 16px; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $l)
                @php
                    $paid = $l->totalPaid();
                    $pct  = $l->amount > 0 ? min(100, round($paid / $l->amount * 100)) : 0;
                    $stColors = ['active'=>['#eff6ff','#2563eb'],'completed'=>['#f0fdf4','#16a34a'],'overdue'=>['#fef2f2','#ef4444']];
                    [$stBg,$stC] = $stColors[$l->status] ?? ['#f8fafc','#64748b'];
                    $itLabel = $l->interest_type === 'declining' ? __('Declining') : __('Flat');
                    $itBg    = $l->interest_type === 'declining' ? '#eff6ff' : '#f0fdfa';
                    $itColor = $l->interest_type === 'declining' ? '#2563eb' : '#0d9488';
                @endphp
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px; color:#94a3b8; font-size:0.8rem;">{{ $loans->firstItem() + $loop->index }}</td>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600; color:#1e293b;">{{ $l->user?->name ?? '—' }}</div>
                        <div style="font-size:0.73rem; color:#94a3b8;">{{ $l->purpose }}</div>
                    </td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->samity?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; font-weight:500; color:#374151;">{{ $cur }}{{ number_format($l->amount, 2) }}</td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.72rem; font-weight:700; padding:3px 9px; border-radius:20px; background:{{ $itBg }}; color:{{ $itColor }};">{{ $itLabel }}</span>
                    </td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->interest_rate }}%</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->duration_months }}m</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $cur }}{{ number_format($l->monthly_installment, 2) }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->issue_date?->format('d M Y') }}</td>
                    <td style="padding:12px 16px; color:#475569;">{{ $l->due_date?->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; align-items:center; gap:7px;">
                            <div style="width:56px; height:5px; background:#e2e8f0; border-radius:99px; overflow:hidden;">
                                <div style="height:100%; width:{{ $pct }}%; background:#0d9488; border-radius:99px;"></div>
                            </div>
                            <span style="font-size:0.72rem; color:#64748b;">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td style="padding:12px 16px;">
                        <span style="font-size:0.73rem;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:capitalize;background:{{ $stBg }};color:{{ $stC }};">{{ $l->status }}</span>
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:6px;">
                            <a href="{{ route('loans.schedule', $l->id) }}"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #c4b5fd;background:#faf5ff;color:#7c3aed;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;" title="{{ __('View EMI Schedule') }}">
                                <i class="fas fa-table-list"></i>
                            </a>
                            <button onclick="openEditLoan({{ $l->id }}, {{ $l->samity_id }}, {{ $l->user_id }}, '{{ $l->amount }}', '{{ $l->interest_rate }}', '{{ $l->interest_type }}', '{{ $l->duration_months }}', '{{ $l->monthly_installment }}', '{{ $l->issue_date?->format('Y-m-d') }}', '{{ $l->due_date?->format('Y-m-d') }}', {{ json_encode($l->purpose) }}, '{{ $l->status }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #99f6e4;background:#f0fdfa;color:#0d9488;cursor:pointer;font-size:12px;" title="{{ __('Edit') }}">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="confirmDelete('{{ route('loans.destroy', $l->id) }}', 'loan for {{ addslashes($l->user?->name) }}')"
                                style="width:30px;height:30px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#ef4444;cursor:pointer;font-size:12px;" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="13" style="padding:48px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-hand-holding-dollar" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px;"></i>{{ __('No loan records found.') }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($loans->hasPages())
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9;">{{ $loans->links() }}</div>
    @endif
</div>

{{-- ═══ CREATE MODAL ═══ --}}
<x-modal id="modal-create" :title="__('Issue New Loan')" icon="fas fa-hand-holding-dollar" width="680px">
    <form action="{{ route('loans.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">{{ __('Member') }} *</label>
                <select name="user_id" style="{{ $inp }}" required>
                    <option value="">{{ __('Select member') }}</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected':'' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Samity') }} *</label>
                <select name="samity_id" style="{{ $inp }}" required>
                    <option value="">{{ __('Select samity') }}</option>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}" {{ old('samity_id') == $s->id ? 'selected':'' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Interest Type full-width with visual selector --}}
            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Interest Type') }} *</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:4px;">
                    <label id="c_label_flat" style="display:flex; align-items:flex-start; gap:10px; padding:12px 14px; border:2px solid #0d9488; border-radius:10px; cursor:pointer; background:#f0fdfa;">
                        <input type="radio" name="interest_type" value="flat" id="c_type_flat"
                            {{ old('interest_type','flat') === 'flat' ? 'checked':'' }}
                            onchange="onTypeChange('c')" style="margin-top:2px; accent-color:#0d9488;">
                        <div>
                            <div style="font-size:0.85rem; font-weight:700; color:#0f766e;">{{ __('Flat Rate') }}</div>
                            <div style="font-size:0.73rem; color:#64748b; margin-top:2px;">{{ __('Fixed interest on original principal. Rate = % of loan amount.') }}</div>
                        </div>
                    </label>
                    <label id="c_label_declining" style="display:flex; align-items:flex-start; gap:10px; padding:12px 14px; border:2px solid #e2e8f0; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="interest_type" value="declining" id="c_type_declining"
                            {{ old('interest_type') === 'declining' ? 'checked':'' }}
                            onchange="onTypeChange('c')" style="margin-top:2px; accent-color:#2563eb;">
                        <div>
                            <div style="font-size:0.85rem; font-weight:700; color:#1e40af;">{{ __('Declining Balance') }}</div>
                            <div style="font-size:0.73rem; color:#64748b; margin-top:2px;">{{ __('Interest on outstanding balance. Rate = annual % (p.a.).') }}</div>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label style="{{ $lbl }}">{{ __('Loan Amount') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" id="c_amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" style="{{ $inp }}" required oninput="calcEmi('c')">
            </div>
            <div>
                <label style="{{ $lbl }}" id="c_rate_label">{{ __('Interest Rate (%)') }} *</label>
                <input type="number" step="0.01" id="c_interest" name="interest_rate" value="{{ old('interest_rate') }}" placeholder="e.g. 12" style="{{ $inp }}" required oninput="calcEmi('c')">
                <div id="c_rate_hint" style="font-size:0.71rem; color:#94a3b8; margin-top:3px;">{{ __('Flat: % of principal | Declining: annual rate') }}</div>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Duration (Months)') }} *</label>
                <input type="number" id="c_duration" name="duration_months" value="{{ old('duration_months') }}" placeholder="e.g. 12" style="{{ $inp }}" required oninput="calcEmi('c')">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Monthly Installment (EMI)') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" id="c_installment" name="monthly_installment" value="{{ old('monthly_installment') }}"
                    placeholder="{{ __('Auto-calculated') }}" style="{{ $inp }} background:#f8fafc; color:#0d9488; font-weight:600;" required readonly>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Issue Date') }} *</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Due Date') }}</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Status') }} *</label>
                <select name="status" style="{{ $inp }}" required>
                    <option value="active"    {{ old('status','active') === 'active'    ? 'selected':'' }}>{{ __('Active') }}</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected':'' }}>{{ __('Completed') }}</option>
                    <option value="overdue"   {{ old('status') === 'overdue'   ? 'selected':'' }}>{{ __('Overdue') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Purpose') }}</label>
                <input name="purpose" value="{{ old('purpose') }}" placeholder="e.g. Business, Medical..." style="{{ $inp }}">
            </div>
        </div>

        {{-- EMI Preview --}}
        <div id="c_emi_preview" style="display:none; margin-top:14px; padding:12px 16px; background:#f0fdfa; border:1px solid #99f6e4; border-radius:10px; font-size:0.82rem; color:#0f766e;">
            <i class="fas fa-calculator" style="margin-right:6px;"></i>
            <span id="c_emi_text"></span>
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-create')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Issue Loan') }}</button>
        </div>
    </form>
</x-modal>

{{-- ═══ EDIT MODAL ═══ --}}
<x-modal id="modal-edit" :title="__('Edit Loan')" icon="fas fa-pen-to-square" width="680px">
    <form id="edit-loan-form" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label style="{{ $lbl }}">{{ __('Member') }} *</label>
                <select name="user_id" id="el_user" style="{{ $inp }}" required>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Samity') }} *</label>
                <select name="samity_id" id="el_samity" style="{{ $inp }}" required>
                    @foreach($samities as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="grid-column:1/-1;">
                <label style="{{ $lbl }}">{{ __('Interest Type') }} *</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:4px;">
                    <label id="e_label_flat" style="display:flex; align-items:flex-start; gap:10px; padding:12px 14px; border:2px solid #e2e8f0; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="interest_type" value="flat" id="e_type_flat" onchange="onTypeChange('e')" style="margin-top:2px; accent-color:#0d9488;">
                        <div>
                            <div style="font-size:0.85rem; font-weight:700; color:#0f766e;">{{ __('Flat Rate') }}</div>
                            <div style="font-size:0.73rem; color:#64748b; margin-top:2px;">{{ __('Fixed interest on original principal. Rate = % of loan amount.') }}</div>
                        </div>
                    </label>
                    <label id="e_label_declining" style="display:flex; align-items:flex-start; gap:10px; padding:12px 14px; border:2px solid #e2e8f0; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="interest_type" value="declining" id="e_type_declining" onchange="onTypeChange('e')" style="margin-top:2px; accent-color:#2563eb;">
                        <div>
                            <div style="font-size:0.85rem; font-weight:700; color:#1e40af;">{{ __('Declining Balance') }}</div>
                            <div style="font-size:0.73rem; color:#64748b; margin-top:2px;">{{ __('Interest on outstanding balance. Rate = annual % (p.a.).') }}</div>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label style="{{ $lbl }}">{{ __('Loan Amount') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" id="e_amount" name="amount" style="{{ $inp }}" required oninput="calcEmi('e')">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Interest Rate (%)') }} *</label>
                <input type="number" step="0.01" id="e_interest" name="interest_rate" style="{{ $inp }}" required oninput="calcEmi('e')">
                <div id="e_rate_hint" style="font-size:0.71rem; color:#94a3b8; margin-top:3px;">{{ __('Flat: % of principal | Declining: annual rate') }}</div>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Duration (Months)') }} *</label>
                <input type="number" id="e_duration" name="duration_months" style="{{ $inp }}" required oninput="calcEmi('e')">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Monthly Installment (EMI)') }} ({{ $cur }}) *</label>
                <input type="number" step="0.01" id="e_installment" name="monthly_installment"
                    style="{{ $inp }} background:#f8fafc; color:#0d9488; font-weight:600;" required readonly>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Issue Date') }} *</label>
                <input type="date" name="issue_date" id="el_issue" style="{{ $inp }}" required>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Due Date') }}</label>
                <input type="date" name="due_date" id="el_due" style="{{ $inp }}">
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Status') }} *</label>
                <select name="status" id="el_status" style="{{ $inp }}" required>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="completed">{{ __('Completed') }}</option>
                    <option value="overdue">{{ __('Overdue') }}</option>
                </select>
            </div>
            <div>
                <label style="{{ $lbl }}">{{ __('Purpose') }}</label>
                <input name="purpose" id="el_purpose" style="{{ $inp }}">
            </div>
        </div>

        <div id="e_emi_preview" style="display:none; margin-top:14px; padding:12px 16px; background:#f0fdfa; border:1px solid #99f6e4; border-radius:10px; font-size:0.82rem; color:#0f766e;">
            <i class="fas fa-calculator" style="margin-right:6px;"></i>
            <span id="e_emi_text"></span>
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="closeModal('modal-edit')" style="{{ $btn_cancel }}">{{ __('Cancel') }}</button>
            <button type="submit" style="{{ $btn_primary }}"><i class="fas fa-save"></i> {{ __('Update Loan') }}</button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
// ── EMI Calculator ────────────────────────────────────────────────────────────
function getType(prefix) {
    const flat = document.getElementById(prefix + (prefix==='c'?'_type_flat':'_type_flat'));
    return flat && flat.checked ? 'flat' : 'declining';
}

function calcEmi(prefix) {
    const p  = prefix === 'c' ? 'c' : 'e';
    const P  = parseFloat(document.getElementById(p + '_amount').value)   || 0;
    const R  = parseFloat(document.getElementById(p + '_interest').value) || 0;
    const N  = parseInt(document.getElementById(p + '_duration').value)   || 0;
    const type = getType(p);

    const instEl    = document.getElementById(p + '_installment');
    const previewEl = document.getElementById(p + '_emi_preview');
    const textEl    = document.getElementById(p + '_emi_text');

    if (P <= 0 || N <= 0) { instEl.value = ''; previewEl.style.display='none'; return; }

    let emi = 0, totalInterest = 0;

    if (type === 'flat') {
        totalInterest = P * R / 100;
        emi = (P + totalInterest) / N;
    } else {
        // Declining: R is annual %, convert to monthly
        const r = R / 100 / 12;
        if (r === 0) {
            emi = P / N;
        } else {
            emi = P * r * Math.pow(1+r, N) / (Math.pow(1+r, N) - 1);
        }
        totalInterest = (emi * N) - P;
    }

    instEl.value = emi.toFixed(2);

    const cur = '{{ $cur }}';
    textEl.innerHTML =
        '<strong>' + '{{ __("EMI") }}' + ': ' + cur + parseFloat(emi).toLocaleString('en', {minimumFractionDigits:2}) + '</strong>' +
        ' &nbsp;|&nbsp; ' + '{{ __("Total Interest") }}' + ': ' + cur + parseFloat(totalInterest).toLocaleString('en', {minimumFractionDigits:2}) +
        ' &nbsp;|&nbsp; ' + '{{ __("Total Payable") }}' + ': ' + cur + parseFloat(P + totalInterest).toLocaleString('en', {minimumFractionDigits:2});
    previewEl.style.display = 'block';
}

// ── Interest type visual toggle ───────────────────────────────────────────────
function onTypeChange(prefix) {
    const p   = prefix;
    const type = getType(p);
    const lFlat = document.getElementById(p + '_label_flat');
    const lDecl = document.getElementById(p + '_label_declining');
    if (lFlat && lDecl) {
        lFlat.style.border     = type === 'flat'      ? '2px solid #0d9488' : '2px solid #e2e8f0';
        lFlat.style.background = type === 'flat'      ? '#f0fdfa'           : '';
        lDecl.style.border     = type === 'declining' ? '2px solid #2563eb' : '2px solid #e2e8f0';
        lDecl.style.background = type === 'declining' ? '#eff6ff'           : '';
    }
    calcEmi(prefix);
}

// ── Open edit modal ───────────────────────────────────────────────────────────
function openEditLoan(id, samityId, userId, amount, interest, interestType, duration, installment, issueDate, dueDate, purpose, status) {
    document.getElementById('el_user').value       = userId;
    document.getElementById('el_samity').value     = samityId;
    document.getElementById('e_amount').value      = amount;
    document.getElementById('e_interest').value    = interest;
    document.getElementById('e_duration').value    = duration;
    document.getElementById('e_installment').value = installment;
    document.getElementById('el_issue').value      = issueDate;
    document.getElementById('el_due').value        = dueDate || '';
    document.getElementById('el_purpose').value    = purpose || '';
    document.getElementById('el_status').value     = status;
    document.getElementById('edit-loan-form').action = '/loans/' + id;

    // Set interest type radio
    document.getElementById('e_type_flat').checked      = interestType !== 'declining';
    document.getElementById('e_type_declining').checked  = interestType === 'declining';
    onTypeChange('e');

    openModal('modal-edit');
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush
