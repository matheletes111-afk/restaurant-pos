@extends('layouts.app')

@section('title')
<title>Admin || Staff Extras (Earnings / Deductions / Advances)</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/restaurant-staff.css') }}">
<style>
    .extras-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .extras-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .extras-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .extras-stat-label {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .extras-stat-num {
        font-family: 'Outfit', sans-serif;
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .extras-stat-icon-wrap {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    /* Type radio button tabs in Modal */
    .type-radio-group {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    .type-radio-card {
        position: relative;
    }
    .type-radio-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .type-radio-card label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px 8px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        font-weight: 600;
        font-size: 0.85rem;
        background: #f8fafc;
        margin-bottom: 0;
    }
    .type-radio-card label i {
        font-size: 1.25rem;
        margin-bottom: 4px;
    }
    /* Earning Selection */
    .type-radio-card.card-earning input:checked + label {
        border-color: #10b981;
        background: #ecfdf5;
        color: #065f46;
        box-shadow: 0 0 0 1px #10b981;
    }
    .type-radio-card.card-earning input:checked + label i {
        color: #10b981;
    }
    /* Deduction Selection */
    .type-radio-card.card-deduction input:checked + label {
        border-color: #ef4444;
        background: #fef2f2;
        color: #991b1b;
        box-shadow: 0 0 0 1px #ef4444;
    }
    .type-radio-card.card-deduction input:checked + label i {
        color: #ef4444;
    }
    /* Advance Selection */
    .type-radio-card.card-advance input:checked + label {
        border-color: #f59e0b;
        background: #fffbeb;
        color: #92400e;
        box-shadow: 0 0 0 1px #f59e0b;
    }
    .type-radio-card.card-advance input:checked + label i {
        color: #f59e0b;
    }

    /* Badges */
    .badge-extra-earning {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-extra-deduction {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .badge-extra-advance {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    /* Toast Notification */
    #extrasToastContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="staff-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        {{-- Toast container for AJAX alerts --}}
        <div id="extrasToastContainer"></div>

        {{-- 1. Header Toolbar --}}
        <div class="staff-header-banner mb-4">
            <div class="staff-header-left">
                <div class="staff-header-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-coins text-white"></i>
                </div>
                <div class="staff-header-title-meta">
                    <span class="staff-header-eyebrow">Staff Operations &amp; Payroll Adjustments</span>
                    <h1 class="staff-header-title">Staff Extras</h1>
                    <p class="staff-header-sub">Manage bonuses, overtime earnings, salary advances &amp; deductions with instant date filters</p>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                @if(isset($availableOutlets) && ($availableOutlets->count() > 1 || (auth()->user() && auth()->user()->role === 'SA')))
                {{-- Outlet Switcher --}}
                <form action="{{ route('restaurant.staff.extras.index') }}" method="GET" class="d-flex align-items-center gap-1" id="outletFilterForm">
                    <input type="hidden" name="from_date" value="{{ $fromDate }}">
                    <input type="hidden" name="to_date" value="{{ $toDate }}">
                    @if($staffId)<input type="hidden" name="staff_id" value="{{ $staffId }}">@endif
                    @if($type)<input type="hidden" name="type" value="{{ $type }}">@endif
                    <div class="input-group input-group-sm" style="min-width: 230px;">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-store text-primary"></i>
                        </span>
                        <select name="restaurant_id" id="restaurantFilterSelect" class="form-select form-select-sm border-start-0 fw-semibold" onchange="document.getElementById('outletFilterForm').submit()">
                            @if(auth()->user() && auth()->user()->role === 'SA')
                                @foreach($availableOutlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ (isset($selectedOutletId) && $selectedOutletId == $outlet->id) ? 'selected' : '' }}>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="all" {{ (isset($selectedOutletId) && $selectedOutletId === 'all') ? 'selected' : '' }}>All Outlets ({{ $availableOutlets->count() }})</option>
                                @foreach($availableOutlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ (isset($selectedOutletId) && $selectedOutletId == $outlet->id) ? 'selected' : '' }}>
                                        {{ $outlet->name }} {{ $outlet->parent_id ? '(Branch)' : '(Main)' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </form>
                @endif

                {{-- Export CSV button --}}
                <a href="{{ route('restaurant.staff.extras.export', array_filter(['from_date' => $fromDate, 'to_date' => $toDate, 'staff_id' => $staffId, 'type' => $type, 'search' => $search, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                   class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
                    <i class="fas fa-file-csv me-1"></i> Export CSV
                </a>

                {{-- Add Extra Button (triggers Modal) --}}
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="openAddExtraModal()">
                    <i class="fas fa-plus-circle me-1"></i> + Add Extra Entry
                </button>
            </div>
        </div>

        {{-- 2. Stats KPI Grid --}}
        <div class="extras-stat-grid">
            {{-- Total Earnings --}}
            <div class="extras-stat-card">
                <div>
                    <div class="extras-stat-label">Total Earnings (+)</div>
                    <div class="extras-stat-num text-success">+₹{{ number_format($stats['total_earnings'], 2) }}</div>
                    <div class="text-muted small mt-1">Incentives, Overtime, Bonus</div>
                </div>
                <div class="extras-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-arrow-trend-up"></i>
                </div>
            </div>

            {{-- Total Deductions --}}
            <div class="extras-stat-card">
                <div>
                    <div class="extras-stat-label">Total Deductions (-)</div>
                    <div class="extras-stat-num text-danger">-₹{{ number_format($stats['total_deductions'], 2) }}</div>
                    <div class="text-muted small mt-1">Penalties, Damages, Charges</div>
                </div>
                <div class="extras-stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-arrow-trend-down"></i>
                </div>
            </div>

            {{-- Total Advances --}}
            <div class="extras-stat-card">
                <div>
                    <div class="extras-stat-label">Total Advances</div>
                    <div class="extras-stat-num text-warning">₹{{ number_format($stats['total_advances'], 2) }}</div>
                    <div class="text-muted small mt-1">Salary Payouts in Advance</div>
                </div>
                <div class="extras-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
            </div>

            {{-- Net Adjustment / Impact --}}
            <div class="extras-stat-card">
                <div>
                    <div class="extras-stat-label">Net Adjustment</div>
                    <div class="extras-stat-num {{ $stats['net_impact'] >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ $stats['net_impact'] >= 0 ? '+' : '' }}₹{{ number_format($stats['net_impact'], 2) }}
                    </div>
                    <div class="text-muted small mt-1">Earnings - (Deductions + Advances)</div>
                </div>
                <div class="extras-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                    <i class="fas fa-scale-balanced"></i>
                </div>
            </div>

            {{-- Total Records Count --}}
            <div class="extras-stat-card">
                <div>
                    <div class="extras-stat-label">Total Entries</div>
                    <div class="extras-stat-num text-dark">{{ $stats['total_count'] }}</div>
                    <div class="text-muted small mt-1">Across {{ $stats['distinct_staff'] }} Staff Member(s)</div>
                </div>
                <div class="extras-stat-icon-wrap" style="background: #f1f5f9; color: #475569;">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>

        {{-- 3. Filter Bar (Date Range, Staff Filter, Type Filter & Keyword Search) --}}
        <div class="bg-white rounded-3 p-3 mb-4 border shadow-sm">
            <form action="{{ route('restaurant.staff.extras.index') }}" method="GET" id="filterForm">
                @if(request('restaurant_id'))
                    <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                @elseif(isset($selectedOutletId))
                    <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                @endif

                <div class="row g-2 align-items-center">
                    {{-- From Date --}}
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small fw-bold text-muted mb-1">From Date</label>
                        <input type="date" name="from_date" id="filter_from_date" class="form-control form-control-sm fw-semibold" value="{{ $fromDate }}">
                    </div>

                    {{-- To Date --}}
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small fw-bold text-muted mb-1">To Date</label>
                        <input type="date" name="to_date" id="filter_to_date" class="form-control form-control-sm fw-semibold" value="{{ $toDate }}">
                    </div>

                    {{-- Staff Filter Dropdown --}}
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <label class="form-label small fw-bold text-muted mb-1">Staff Member</label>
                        <select name="staff_id" id="filter_staff_id" class="form-select form-select-sm">
                            <option value="">-- All Staff Members --</option>
                            @foreach($staffList as $st)
                                <option value="{{ $st->id }}" {{ (string)$staffId === (string)$st->id ? 'selected' : '' }}>
                                    {{ $st->name }} ({{ $st->role_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type Filter Dropdown --}}
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small fw-bold text-muted mb-1">Entry Type</label>
                        <select name="type" id="filter_type" class="form-select form-select-sm">
                            <option value="">-- All Types --</option>
                            <option value="Earning" {{ $type === 'Earning' ? 'selected' : '' }}>Earning (+)</option>
                            <option value="Deduction" {{ $type === 'Deduction' ? 'selected' : '' }}>Deduction (-)</option>
                            <option value="Advance" {{ $type === 'Advance' ? 'selected' : '' }}>Advance</option>
                        </select>
                    </div>

                    {{-- Keyword Search --}}
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label class="form-label small fw-bold text-muted mb-1">Keyword Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" id="filter_search" class="form-control" placeholder="Search staff, remarks, amount..." value="{{ $search }}">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-12 d-flex align-items-center justify-content-between pt-2 border-top mt-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('restaurant.staff.extras.index', array_filter(['restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                               class="btn btn-outline-secondary btn-sm px-3">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                            {{-- Quick Current Month Button --}}
                            @if($fromDate !== date('Y-m-01') || $toDate !== date('Y-m-t'))
                            <a href="{{ route('restaurant.staff.extras.index', array_filter(['from_date' => date('Y-m-01'), 'to_date' => date('Y-m-t'), 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                               class="btn btn-outline-primary btn-sm px-3">
                                <i class="fas fa-calendar-day me-1"></i> Current Month
                            </a>
                            @endif
                        </div>

                        <div class="text-muted small">
                            <i class="fas fa-calendar-check text-primary me-1"></i>
                            Showing: <strong>{{ date('d M Y', strtotime($fromDate)) }}</strong> to <strong>{{ date('d M Y', strtotime($toDate)) }}</strong>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- 4. Records Table --}}
        <div class="bg-white rounded-3 p-4 border shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: #0f172a;">
                        <i class="fas fa-table-list text-primary me-2"></i>Staff Extras Records
                    </h5>
                    <small class="text-muted">Showing {{ $extras->count() }} entries between {{ date('d M Y', strtotime($fromDate)) }} and {{ date('d M Y', strtotime($toDate)) }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold" onclick="openAddExtraModal()">
                        <i class="fas fa-plus me-1"></i> Record New Extra
                    </button>
                </div>
            </div>

            @if($extras->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3" style="width: 64px; height: 64px; margin: 0 auto; border-radius: 50%; background: #f8fafc; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94a3b8;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No Extra Entries Found</h6>
                    <p class="text-muted small mb-3">No earnings, deductions, or advances recorded for the selected filter range.</p>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" onclick="openAddExtraModal()">
                        <i class="fas fa-plus me-1"></i> Add First Entry
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="extrasTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45px;" class="text-center">#</th>
                                <th style="min-width: 110px;">Date</th>
                                <th style="min-width: 200px;">Staff Member</th>
                                <th style="min-width: 130px;" class="text-center">Type</th>
                                <th style="min-width: 120px;" class="text-end">Amount</th>
                                <th style="min-width: 220px;">Remarks</th>
                                <th style="min-width: 140px;">Recorded By</th>
                                <th style="width: 100px;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($extras as $idx => $extra)
                                @php
                                    $staff = $extra->user;
                                    $initials = '';
                                    if ($staff) {
                                        $parts = explode(' ', trim($staff->name));
                                        foreach(array_slice($parts, 0, 2) as $p) {
                                            if (!empty($p)) $initials .= strtoupper($p[0]);
                                        }
                                    }
                                    if (empty($initials)) $initials = 'ST';

                                    $typeBadgeClass = 'badge-extra-earning';
                                    $typeIcon = 'fa-plus-circle';
                                    $amountColorClass = 'text-success';
                                    $amountSign = '+';

                                    if ($extra->type === 'Deduction') {
                                        $typeBadgeClass = 'badge-extra-deduction';
                                        $typeIcon = 'fa-minus-circle';
                                        $amountColorClass = 'text-danger';
                                        $amountSign = '-';
                                    } elseif ($extra->type === 'Advance') {
                                        $typeBadgeClass = 'badge-extra-advance';
                                        $typeIcon = 'fa-hand-holding-dollar';
                                        $amountColorClass = 'text-warning';
                                        $amountSign = '';
                                    }
                                @endphp
                                <tr id="extra-row-{{ $extra->id }}">
                                    <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark small">
                                            <i class="fas fa-calendar-day text-muted me-1"></i>
                                            {{ date('d M Y', strtotime($extra->entry_date)) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 36px; height: 36px; border-radius: 10px; background: #6366f1; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.82rem; flex-shrink: 0;">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                                    {{ $staff ? $staff->name : 'Staff Member' }}
                                                </div>
                                                <div class="text-muted small" style="font-size: 0.76rem;">
                                                    <span class="badge bg-light text-secondary border py-0 px-1">{{ $staff ? $staff->role_type : 'N/A' }}</span>
                                                    @if($staff && $staff->restaurant && (isset($availableOutlets) && $availableOutlets->count() > 1))
                                                        <span>• <i class="fas fa-store"></i> {{ $staff->restaurant->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $typeBadgeClass }} px-3 py-1 fw-bold">
                                            <i class="fas {{ $typeIcon }} me-1"></i> {{ $extra->type }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold {{ $amountColorClass }}" style="font-size: 1rem; font-family: 'Outfit', sans-serif;">
                                            {{ $amountSign }}₹{{ number_format($extra->amount, 2) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-dark" style="max-width: 300px; word-break: break-word;">
                                            {{ $extra->remarks ?: '—' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-dark fw-semibold">
                                            {{ $extra->creator ? $extra->creator->name : 'System' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            {{ $extra->created_at ? $extra->created_at->format('d M, h:i A') : '—' }}
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            {{-- Edit Button --}}
                                            <button type="button" class="btn btn-sm btn-outline-primary" style="padding: 4px 8px;" title="Edit Entry" onclick="editExtraEntry({{ $extra->id }})">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            {{-- Delete Button --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger" style="padding: 4px 8px;" title="Delete Entry" onclick="confirmDeleteExtra({{ $extra->id }}, '{{ addslashes($staff ? $staff->name : 'Staff') }}', '{{ $extra->type }}', '{{ number_format($extra->amount, 2) }}')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
</div>

{{-- ========================================================================= --}}
{{-- 5. Modal: Add / Edit Staff Extra Entry --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="extraModal" tabindex="-1" aria-labelledby="extraModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2" id="extraModalLabel">
                    <i class="fas fa-coins"></i> <span id="modalTitleText">Add New Extra Entry</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="extraForm" onsubmit="handleExtraFormSubmit(event)">
                @csrf
                <input type="hidden" id="entry_id" name="entry_id" value="">
                @if(request('restaurant_id'))
                    <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                @elseif(isset($selectedOutletId))
                    <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                @endif

                <div class="modal-body p-4">
                    {{-- Alert box inside modal --}}
                    <div id="modalAlert" class="alert alert-danger d-none py-2 px-3 small mb-3"></div>

                    {{-- 1. Staff Member Selection --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Staff Member <span class="text-danger">*</span>
                        </label>
                        <select name="user_id" id="modal_user_id" class="form-select" required>
                            <option value="">-- Choose Staff Member --</option>
                            @foreach($staffList as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->name }} ({{ $st->role_type }}){{ (isset($availableOutlets) && $availableOutlets->count() > 1 && $st->restaurant) ? ' — [' . $st->restaurant->name . ']' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @if($staffList->isEmpty())
                            <div class="form-text text-danger">No active staff members found. Please create staff first.</div>
                        @endif
                    </div>

                    {{-- 2. Entry Type Selection (Visual Tabs) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Type of Extra <span class="text-danger">*</span>
                        </label>
                        <div class="type-radio-group">
                            <div class="type-radio-card card-earning">
                                <input type="radio" name="type" id="type_earning" value="Earning" checked>
                                <label for="type_earning">
                                    <i class="fas fa-plus-circle text-success"></i>
                                    <span>Earning</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">+ Bonus / Overtime</small>
                                </label>
                            </div>

                            <div class="type-radio-card card-deduction">
                                <input type="radio" name="type" id="type_deduction" value="Deduction">
                                <label for="type_deduction">
                                    <i class="fas fa-minus-circle text-danger"></i>
                                    <span>Deduction</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">- Penalty / Fine</small>
                                </label>
                            </div>

                            <div class="type-radio-card card-advance">
                                <input type="radio" name="type" id="type_advance" value="Advance">
                                <label for="type_advance">
                                    <i class="fas fa-hand-holding-dollar text-warning"></i>
                                    <span>Advance</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Salary Advance</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Amount & Date in 2 columns --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small mb-1">
                                Amount (₹) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">₹</span>
                                <input type="number" name="amount" id="modal_amount" class="form-control fw-bold" step="0.01" min="0.01" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small mb-1">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="entry_date" id="modal_entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    {{-- 4. Remarks / Reason --}}
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Remarks / Notes <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea name="remarks" id="modal_remarks" rows="3" class="form-control" placeholder="E.g., Diwali incentive, Late arrival penalty, Urgent family advance..." maxlength="1000"></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="modalSubmitBtn" style="min-width: 130px;">
                        <span id="modalSubmitBtnText"><i class="fas fa-check me-1"></i> Save Entry</span>
                        <span id="modalSubmitBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- 6. Modal: Delete Confirmation --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="deleteExtraModal" tabindex="-1" aria-labelledby="deleteExtraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-body text-center p-4">
                <div class="mb-3" style="width: 56px; height: 56px; margin: 0 auto; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Delete Extra Entry?</h5>
                <p class="text-muted small mb-3">
                    Are you sure you want to delete this <strong id="deleteTypeLabel"></strong> entry of <strong id="deleteAmountLabel"></strong> for <strong id="deleteStaffLabel"></strong>?
                </p>
                <input type="hidden" id="deleteEntryId" value="">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="confirmDeleteBtn" onclick="executeDeleteExtra()">
                        <span id="deleteBtnText">Delete</span>
                        <span id="deleteBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@include('includes.script')
<script>
    function getExtraModal() {
        const el = document.getElementById('extraModal');
        if (!el) return null;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            return bootstrap.Modal.getOrCreateInstance(el);
        }
        return null;
    }

    function getDeleteModal() {
        const el = document.getElementById('deleteExtraModal');
        if (!el) return null;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            return bootstrap.Modal.getOrCreateInstance(el);
        }
        return null;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        getExtraModal();
        getDeleteModal();

        // Live client-side keyword search filter on the table rows
        const searchInput = document.getElementById('filter_search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#extrasTable tbody tr');
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }
    });

    // Toast feedback helper
    function showToast(message, type = 'success') {
        const container = document.getElementById('extrasToastContainer');
        if (!container) return;
        const bgClass = type === 'success' ? 'bg-success text-white' : 'bg-danger text-white';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center ${bgClass} border-0 show shadow-lg mb-2`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.style.borderRadius = '10px';
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2 fw-semibold">
                    <i class="fas ${icon}"></i> <span>${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        container.appendChild(toastEl);
        setTimeout(() => {
            toastEl.remove();
        }, 4000);
    }

    // Open Add Modal
    function openAddExtraModal() {
        const form = document.getElementById('extraForm');
        if (form) form.reset();
        document.getElementById('entry_id').value = '';
        document.getElementById('modalTitleText').textContent = 'Add New Extra Entry';
        document.getElementById('modalSubmitBtnText').innerHTML = '<i class="fas fa-check me-1"></i> Save Entry';
        document.getElementById('modal_entry_date').value = '{{ date("Y-m-d") }}';
        const earningRadio = document.getElementById('type_earning');
        if (earningRadio) earningRadio.checked = true;
        
        // Hide modal alert
        const alertEl = document.getElementById('modalAlert');
        if (alertEl) {
            alertEl.classList.add('d-none');
            alertEl.textContent = '';
        }

        const modal = getExtraModal();
        if (modal) modal.show();
    }

    // Open Edit Modal
    async function editExtraEntry(id) {
        const alertEl = document.getElementById('modalAlert');
        if (alertEl) {
            alertEl.classList.add('d-none');
            alertEl.textContent = '';
        }

        try {
            const url = '{{ route("restaurant.staff.extras.show", ":id") }}'.replace(':id', id);
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const res = await response.json();
            if (!res.success) {
                showToast(res.message || 'Failed to fetch entry details', 'error');
                return;
            }

            const data = res.data;
            document.getElementById('entry_id').value = data.id;
            document.getElementById('modal_user_id').value = data.user_id;
            document.getElementById('modal_amount').value = data.amount;
            document.getElementById('modal_entry_date').value = data.entry_date;
            document.getElementById('modal_remarks').value = data.remarks || '';

            // Check matching radio
            if (data.type === 'Earning') {
                document.getElementById('type_earning').checked = true;
            } else if (data.type === 'Deduction') {
                document.getElementById('type_deduction').checked = true;
            } else if (data.type === 'Advance') {
                document.getElementById('type_advance').checked = true;
            }

            document.getElementById('modalTitleText').textContent = 'Edit Extra Entry';
            document.getElementById('modalSubmitBtnText').innerHTML = '<i class="fas fa-save me-1"></i> Update Entry';

            const modal = getExtraModal();
            if (modal) modal.show();
        } catch (err) {
            console.error(err);
            showToast('An error occurred while loading record', 'error');
        }
    }

    // Handle Form Submit (Add or Update)
    async function handleExtraFormSubmit(e) {
        e.preventDefault();
        const alertEl = document.getElementById('modalAlert');
        if (alertEl) {
            alertEl.classList.add('d-none');
            alertEl.textContent = '';
        }

        const entryId = document.getElementById('entry_id').value;
        const isUpdate = Boolean(entryId);
        const updateUrlTemplate = '{{ route("restaurant.staff.extras.update", ":id") }}';
        const url = isUpdate ? updateUrlTemplate.replace(':id', entryId) : '{{ route("restaurant.staff.extras.store") }}';

        const formData = new FormData(document.getElementById('extraForm'));

        const submitBtn = document.getElementById('modalSubmitBtn');
        const submitText = document.getElementById('modalSubmitBtnText');
        const spinner = document.getElementById('modalSubmitBtnSpinner');

        submitBtn.disabled = true;
        submitText.classList.add('d-none');
        spinner.classList.remove('d-none');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const res = await response.json();

            if (response.ok && res.success) {
                showToast(res.message, 'success');
                const modal = getExtraModal();
                if (modal) modal.hide();
                // Reload the page to refresh stats & records table
                setTimeout(() => {
                    window.location.reload();
                }, 600);
            } else {
                let errorMsg = res.message || 'An error occurred while saving.';
                if (res.errors) {
                    const firstKey = Object.keys(res.errors)[0];
                    errorMsg = res.errors[firstKey][0];
                }
                if (alertEl) {
                    alertEl.textContent = errorMsg;
                    alertEl.classList.remove('d-none');
                }
            }
        } catch (err) {
            console.error(err);
            if (alertEl) {
                alertEl.textContent = 'Network or server error. Please try again.';
                alertEl.classList.remove('d-none');
            }
        } finally {
            submitBtn.disabled = false;
            submitText.classList.remove('d-none');
            spinner.classList.add('d-none');
        }
    }

    // Confirm Delete Modal
    function confirmDeleteExtra(id, staffName, type, amount) {
        document.getElementById('deleteEntryId').value = id;
        document.getElementById('deleteTypeLabel').textContent = type;
        document.getElementById('deleteAmountLabel').textContent = '₹' + amount;
        document.getElementById('deleteStaffLabel').textContent = staffName;
        const modal = getDeleteModal();
        if (modal) modal.show();
    }

    // Execute Delete
    async function executeDeleteExtra() {
        const id = document.getElementById('deleteEntryId').value;
        if (!id) return;

        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const deleteText = document.getElementById('deleteBtnText');
        const spinner = document.getElementById('deleteBtnSpinner');

        confirmBtn.disabled = true;
        deleteText.classList.add('d-none');
        spinner.classList.remove('d-none');

        try {
            const deleteUrl = '{{ route("restaurant.staff.extras.destroy", ":id") }}'.replace(':id', id);
            const response = await fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const res = await response.json();

            if (response.ok && res.success) {
                showToast(res.message, 'success');
                const modal = getDeleteModal();
                if (modal) modal.hide();
                // Remove row with animation or reload
                const row = document.getElementById(`extra-row-${id}`);
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        window.location.reload();
                    }, 400);
                } else {
                    window.location.reload();
                }
            } else {
                showToast(res.message || 'Failed to delete extra entry.', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Network error while deleting entry.', 'error');
        } finally {
            confirmBtn.disabled = false;
            deleteText.classList.remove('d-none');
            spinner.classList.add('d-none');
        }
    }
</script>
@endsection
