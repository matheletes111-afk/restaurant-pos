@extends('layouts.app')

@section('title')
<title>Admin || Staff Salary Management &amp; Permanent Payroll</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/restaurant-staff.css') }}">
<style>
    .payroll-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .payroll-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .payroll-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .payroll-stat-label {
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .payroll-stat-num {
        font-family: 'Outfit', sans-serif;
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .payroll-stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* Payroll Table Styling */
    .table-payroll {
        font-size: 0.84rem;
    }
    .table-payroll thead th {
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #475569;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 10px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .table-payroll tbody td {
        padding: 10px;
        vertical-align: middle;
    }
    .table-payroll tr.row-paid {
        background-color: #f0fdf4 !important;
    }
    .table-payroll tr.row-paid td {
        background-color: transparent !important;
    }

    /* Custom Checkbox Pill */
    .paid-toggle-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .paid-checkbox-input {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #10b981;
    }

    /* Mini Leave Pills */
    .mini-pill {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        margin-right: 2px;
        margin-bottom: 2px;
        white-space: nowrap;
    }
    .mini-pill-sl { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .mini-pill-pl { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .mini-pill-hd { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
    .mini-pill-el { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .mini-pill-wo { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

    /* Nav Tabs */
    .payroll-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
    }
    .payroll-nav-tabs .nav-link {
        font-weight: 700;
        font-size: 0.92rem;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 10px 18px;
        background: transparent;
        transition: all 0.2s ease;
    }
    .payroll-nav-tabs .nav-link:hover {
        color: #4f46e5;
    }
    .payroll-nav-tabs .nav-link.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
        background: transparent;
    }

    /* Hourly View Specifics */
    .card-rest-due {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 2px solid #86efac !important;
    }
    .card-rest-due-pending {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 2px solid #fcd34d !important;
    }
    .badge-mode-cash {
        background-color: #10b981;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.76rem;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .badge-mode-online {
        background-color: #0284c7;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.76rem;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .mode-select-pill {
        display: inline-flex;
        border-radius: 10px;
        background: #f1f5f9;
        padding: 4px;
        width: 100%;
        gap: 6px;
    }
    .mode-select-pill label {
        flex: 1;
        text-align: center;
        padding: 8px 12px;
        margin: 0;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }
    .mode-select-pill input[type="radio"] {
        display: none;
    }
    .mode-select-pill input[type="radio"]:checked + label.lbl-cash {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
    }
    .mode-select-pill input[type="radio"]:checked + label.lbl-online {
        background: #0284c7;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.35);
    }

    /* Toast Container */
    #payrollToastContainer {
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
        {{-- Flash Messages --}}
        @include('includes.message')

        {{-- Toast Container --}}
        <div id="payrollToastContainer"></div>

        {{-- 1. Header Toolbar --}}
        <div class="staff-header-banner mb-4">
            <div class="staff-header-left">
                <div class="staff-header-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-money-check-alt text-white"></i>
                </div>
                <div class="staff-header-title-meta">
                    <span class="staff-header-eyebrow">Human Resources &amp; Payroll Management</span>
                    <h1 class="staff-header-title">{{ $activeTab === 'hourly' ? 'Hourly Base Staff Management' : 'Permanent Staff Payroll' }}</h1>
                    <p class="staff-header-sub">
                        @if($activeTab === 'hourly')
                            Calculate hourly duty wages (Login/Logout hours), add extra earnings, deduct penalties &amp; advances, and track disbursement payments.
                        @else
                            Corporate monthly payroll computation: attendance, leaves, statutory deductions (PF/ESI), absent deductions, extras &amp; actual payable
                        @endif
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                @if(isset($availableOutlets) && ($availableOutlets->count() > 1 || (auth()->user() && auth()->user()->role === 'SA')))
                {{-- Outlet Switcher --}}
                <form action="{{ route('restaurant.salary.index') }}" method="GET" class="d-flex align-items-center gap-1" id="outletFilterForm">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    @if($activeTab === 'hourly')
                        @if(isset($hourlyData['selectedStaffId']))<input type="hidden" name="staff_id" value="{{ $hourlyData['selectedStaffId'] }}">@endif
                        @if(isset($hourlyData['fromDate']))<input type="hidden" name="from_date" value="{{ $hourlyData['fromDate'] }}">@endif
                        @if(isset($hourlyData['toDate']))<input type="hidden" name="to_date" value="{{ $hourlyData['toDate'] }}">@endif
                    @else
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                        @if($paymentFilter !== 'all')<input type="hidden" name="payment_status" value="{{ $paymentFilter }}">@endif
                    @endif
                    <div class="input-group input-group-sm" style="min-width: 220px;">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-store text-primary"></i>
                        </span>
                        <select name="restaurant_id" id="restaurantFilterSelect" class="form-select form-select-sm border-start-0 fw-semibold" onchange="document.getElementById('outletFilterForm').submit()">
                            @if(auth()->user() && auth()->user()->role === 'SA')
                                <option value="all" {{ (isset($selectedOutletId) && $selectedOutletId === 'all') ? 'selected' : '' }}>All Outlets ({{ $availableOutlets->count() }})</option>
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

                @if($activeTab === 'permanent')
                    {{-- Excel / CSV Download --}}
                    <a href="{{ route('restaurant.salary.export', array_filter(['year' => $year, 'month' => $month, 'search' => $search, 'payment_status' => $paymentFilter, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                       class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
                        <i class="fas fa-file-excel me-1"></i> Download Excel (CSV)
                    </a>

                    {{-- Batch Save Button --}}
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" id="batchSaveBtn" onclick="saveAllPayrollRows()">
                        <span id="batchSaveText"><i class="fas fa-save me-1"></i> Save All Payroll Changes</span>
                        <span id="batchSaveSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                @endif
            </div>
        </div>

        {{-- 2. Navigation Tabs (Strictly Permanent Payroll & Hourly Base Staff) --}}
        <div class="payroll-nav-tabs">
            <a href="{{ route('restaurant.salary.index', array_filter(['tab' => 'permanent', 'month' => $month, 'year' => $year, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
               class="nav-link {{ $activeTab === 'permanent' ? 'active' : '' }}">
                <i class="fas fa-calendar-check me-1"></i> Permanent Payroll ({{ $monthName }} {{ $year }})
            </a>
            <a href="{{ route('restaurant.salary.index', array_filter(['tab' => 'hourly', 'staff_id' => $hourlyData['selectedStaffId'] ?? null, 'from_date' => $hourlyData['fromDate'] ?? null, 'to_date' => $hourlyData['toDate'] ?? null, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
               class="nav-link {{ $activeTab === 'hourly' ? 'active' : '' }}">
                <i class="fas fa-user-clock me-1"></i> Hourly Base Staff
            </a>
        </div>

        @if($activeTab === 'permanent')
        {{-- 3. Month & Year Filter Bar --}}
        <div class="bg-white rounded-3 p-3 mb-4 border shadow-sm">
            <form action="{{ route('restaurant.salary.index') }}" method="GET" id="payrollFilterForm">
                @if(request('restaurant_id'))
                    <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                @elseif(isset($selectedOutletId))
                    <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                @endif

                <div class="row g-2 align-items-center">
                    {{-- Month Dropdown --}}
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <label class="form-label small fw-bold text-muted mb-1">Payroll Month</label>
                        <select name="month" id="filter_month" class="form-select form-select-sm fw-semibold" onchange="document.getElementById('payrollFilterForm').submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create(2026, $m, 1)->format('F') }} (Month {{ sprintf('%02d', $m) }})
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Year Dropdown --}}
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small fw-bold text-muted mb-1">Payroll Year</label>
                        <select name="year" id="filter_year" class="form-select form-select-sm fw-semibold" onchange="document.getElementById('payrollFilterForm').submit()">
                            @for($y = (int)date('Y') + 1; $y >= 2022; $y--)
                                <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Payment Status Filter --}}
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small fw-bold text-muted mb-1">Disbursement Status</label>
                        <select name="payment_status" id="filter_payment_status" class="form-select form-select-sm fw-semibold" onchange="document.getElementById('payrollFilterForm').submit()">
                            <option value="all" {{ $paymentFilter === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="paid" {{ $paymentFilter === 'paid' ? 'selected' : '' }}>Paid Only</option>
                            <option value="unpaid" {{ $paymentFilter === 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                        </select>
                    </div>

                    {{-- Keyword Search --}}
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <label class="form-label small fw-bold text-muted mb-1">Search Staff</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" id="filter_search" class="form-control" placeholder="Search staff name, phone..." value="{{ $search }}">
                        </div>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="col-lg-2 col-md-12 col-sm-12 text-end pt-3 pt-lg-0">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('restaurant.salary.index', array_filter(['month' => date('n'), 'year' => date('Y'), 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                               class="btn btn-outline-secondary btn-sm px-2" title="Reset to Current Month">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- 4. Stats KPI Grid --}}
        <div class="payroll-stat-grid">
            {{-- Total Permanent Staff --}}
            <div class="payroll-stat-card">
                <div>
                    <div class="payroll-stat-label">Permanent Staff</div>
                    <div class="payroll-stat-num text-dark">{{ $stats['staff_count'] }}</div>
                    <div class="text-muted small mt-1">Total in {{ $monthName }} {{ $year }} ({{ $daysInMonth }} Days)</div>
                </div>
                <div class="payroll-stat-icon-wrap" style="background: #f1f5f9; color: #475569;">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            {{-- Base Salary Committed --}}
            <div class="payroll-stat-card">
                <div>
                    <div class="payroll-stat-label">Base Salary</div>
                    <div class="payroll-stat-num text-secondary">₹{{ number_format($stats['total_base_committed'], 2) }}</div>
                    <div class="text-muted small mt-1">Full-month committed gross</div>
                </div>
                <div class="payroll-stat-icon-wrap" style="background: #e2e8f0; color: #334155;">
                    <i class="fas fa-coins"></i>
                </div>
            </div>

            {{-- Absent Deductions --}}
            <div class="payroll-stat-card">
                <div>
                    <div class="payroll-stat-label">Absent Reductions</div>
                    <div class="payroll-stat-num text-danger">-₹{{ number_format($stats['total_absent_deduction'], 2) }}</div>
                    <div class="text-muted small mt-1">Reduced for non-duty days</div>
                </div>
                <div class="payroll-stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>

            {{-- Statutory Deductions --}}
            <div class="payroll-stat-card">
                <div>
                    <div class="payroll-stat-label">PF + ESI + Other</div>
                    <div class="payroll-stat-num text-warning">-₹{{ number_format($stats['total_statutory_deduction'], 2) }}</div>
                    <div class="text-muted small mt-1">Statutory employee cuts</div>
                </div>
                <div class="payroll-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>

            {{-- Extras Impact --}}
            @php
                $extrasNet = $stats['total_extra_earnings'] - $stats['total_extra_penalties'] - $stats['total_advances'];
            @endphp
            <div class="payroll-stat-card">
                <div>
                    <div class="payroll-stat-label">Extras Impact</div>
                    <div class="payroll-stat-num {{ $extrasNet >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $extrasNet >= 0 ? '+' : '' }}₹{{ number_format($extrasNet, 2) }}
                    </div>
                    <div class="text-muted small mt-1">+Earn -Penalties -Advance</div>
                </div>
                <div class="payroll-stat-icon-wrap" style="background: #ecfdf5; color: #10b981;">
                    <i class="fas fa-balance-scale"></i>
                </div>
            </div>

            {{-- Actual Total Payable --}}
            <div class="payroll-stat-card" style="border: 2px solid #6366f1;">
                <div>
                    <div class="payroll-stat-label" style="color: #4f46e5;">Actual Total Payable</div>
                    <div class="payroll-stat-num text-primary">₹{{ number_format($stats['total_actual_payable'], 2) }}</div>
                    <div class="text-muted small mt-1">
                        <span class="badge bg-success-subtle text-success">{{ $stats['paid_count'] }} Paid</span>
                        <span class="badge bg-secondary-subtle text-secondary">{{ $stats['unpaid_count'] }} Unpaid</span>
                    </div>
                </div>
                <div class="payroll-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>

        {{-- 5. Corporate Payroll Table --}}
        <div class="bg-white rounded-3 p-4 border shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: #0f172a;">
                        <i class="fas fa-table text-primary me-2"></i>Permanent Staff Salary Sheet — {{ $monthName }} {{ $year }}
                    </h5>
                    <small class="text-muted">Total {{ count($staffList) }} permanent employee(s). Formula: Present Salary - Absent Salary - (PF + ESI + Other) + Extra Earnings - Penalties - Advances = Final Salary</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" onclick="markAllPaidToggle()">
                        <i class="fas fa-check-double me-1"></i> Toggle All Paid
                    </button>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold" onclick="saveAllPayrollRows()">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>

            @if(empty($staffList))
                <div class="text-center py-5">
                    <div class="mb-3" style="width: 64px; height: 64px; margin: 0 auto; border-radius: 50%; background: #f8fafc; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94a3b8;">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No Permanent Staff Found</h6>
                    <p class="text-muted small mb-3">No active staff members are configured with permanent salary structure for this restaurant outlet.</p>
                    <a href="{{ route('restaurant.staff.index') }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">
                        <i class="fas fa-user-plus me-1"></i> Configure Staff Salaries
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-payroll align-middle mb-0" id="payrollTable">
                        <thead>
                            <tr>
                                <th style="width: 45px;" class="text-center">Paid?</th>
                                <th style="min-width: 170px;">Staff Member</th>
                                <th style="min-width: 110px;" class="text-end">Base Salary</th>
                                <th style="min-width: 90px;" class="text-center">Duty Days</th>
                                <th style="min-width: 130px;">Leaves Breakdown</th>
                                <th style="min-width: 110px;" class="text-center">Absent Days</th>
                                <th style="min-width: 110px;" class="text-end">Earned Salary</th>
                                <th style="min-width: 120px;" class="text-end">PF / ESI / Other</th>
                                <th style="min-width: 110px;" class="text-end">Net Salary</th>
                                <th style="min-width: 130px;">Extras (+ / -)</th>
                                <th style="min-width: 110px;" class="text-end bg-light fw-bold">Final Salary</th>
                                <th style="min-width: 130px;" class="text-center bg-primary-subtle text-primary">Actual Payable (₹)</th>
                                <th style="min-width: 120px;" class="text-center">Payment Date</th>
                                <th style="min-width: 140px;">Notes / Remarks</th>
                                <th style="width: 80px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffList as $idx => $row)
                                @php
                                    $staff = $row['staff'];
                                    $isPaid = $row['is_paid'];
                                    $initials = '';
                                    if ($staff) {
                                        $parts = explode(' ', trim($staff->name));
                                        foreach(array_slice($parts, 0, 2) as $p) {
                                            if (!empty($p)) $initials .= strtoupper($p[0]);
                                        }
                                    }
                                    if (empty($initials)) $initials = 'ST';
                                @endphp
                                <tr id="payroll-row-{{ $row['user_id'] }}" 
                                    class="{{ $isPaid ? 'row-paid' : '' }}" 
                                    data-user-id="{{ $row['user_id'] }}"
                                    data-days-in-month="{{ $daysInMonth }}"
                                    data-absent-days="{{ $row['absent_days'] }}"
                                    data-pf-pct="{{ $row['pf_percentage'] }}"
                                    data-esi-pct="{{ $row['esi_percentage'] }}"
                                    data-ptax-type="{{ $row['ptax_type'] ?? 'percentage' }}"
                                    data-ptax-val="{{ $row['ptax_val'] ?? 0 }}"
                                    data-other-type="{{ $row['other_type'] ?? 'percentage' }}"
                                    data-other-val="{{ $row['other_val'] ?? 0 }}"
                                    data-other-pct="{{ $row['other_deductions_percentage'] }}"
                                    data-extra-earn="{{ $row['extra_earnings'] }}"
                                    data-extra-penalty="{{ $row['extra_deductions'] }}"
                                    data-extra-advance="{{ $row['advance_amount'] }}">
                                    {{-- 1. Checkbox Paid (FIRST COLUMN) --}}
                                    <td class="text-center">
                                        <div class="paid-toggle-wrap">
                                            <input type="checkbox" 
                                                   class="paid-checkbox-input" 
                                                   id="paid_chk_{{ $row['user_id'] }}" 
                                                   {{ $isPaid ? 'checked' : '' }} 
                                                   onchange="onPaidCheckboxChange({{ $row['user_id'] }})"
                                                   title="Check when salary is paid">
                                        </div>
                                    </td>

                                    {{-- 2. Staff Details --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 34px; height: 34px; border-radius: 8px; background: #4f46e5; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; flex-shrink: 0;">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                                    {{ $staff->name }}
                                                </div>
                                                <div class="text-muted" style="font-size: 0.74rem;">
                                                    <span class="badge bg-light text-secondary border py-0 px-1">{{ $staff->role_type }}</span>
                                                    @if($staff->phone)<span>• {{ $staff->phone }}</span>@endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 3. Base Monthly Salary (Editable inline) --}}
                                    <td class="text-end" style="min-width: 125px;">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white fw-bold px-1" style="font-size: 0.75rem;">₹</span>
                                            <input type="number" 
                                                   class="form-control form-control-sm text-end fw-bold base-salary-input" 
                                                   id="base_salary_{{ $row['user_id'] }}" 
                                                   value="{{ number_format($row['base_salary'], 2, '.', '') }}" 
                                                   step="0.01" 
                                                   min="0"
                                                   oninput="recalculateRowFromBaseSalary({{ $row['user_id'] }})"
                                                   placeholder="0.00"
                                                   title="Monthly Base Salary">
                                        </div>
                                        <div class="text-muted mt-1" style="font-size: 0.7rem;" id="per_day_label_{{ $row['user_id'] }}">
                                            (₹{{ number_format($row['per_day_rate'], 2) }}/day)
                                        </div>
                                    </td>

                                    {{-- 4. Days Present & Duty --}}
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            {{ number_format($row['present_days'], 1) }} Present
                                        </span>
                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                            +{{ number_format($row['weekoff_days'], 0) }} WO
                                        </div>
                                    </td>

                                    {{-- 5. Leaves Breakdown (SL, PL, HD, EL, WO) --}}
                                    <td>
                                        <div>
                                            @if($row['leave_breakdown']['sl'] > 0)
                                                <span class="mini-pill mini-pill-sl" title="Sick Leave">SL: {{ number_format($row['leave_breakdown']['sl'], 1) }}</span>
                                            @endif
                                            @if($row['leave_breakdown']['pl'] > 0)
                                                <span class="mini-pill mini-pill-pl" title="Paid Leave">PL: {{ number_format($row['leave_breakdown']['pl'], 1) }}</span>
                                            @endif
                                            @if($row['leave_breakdown']['hd'] > 0)
                                                <span class="mini-pill mini-pill-hd" title="Half Day">HD: {{ number_format($row['leave_breakdown']['hd'], 1) }}</span>
                                            @endif
                                            @if($row['leave_breakdown']['el'] > 0)
                                                <span class="mini-pill mini-pill-el" title="Early Leave">EL: {{ number_format($row['leave_breakdown']['el'], 1) }}</span>
                                            @endif
                                            @if($row['leave_days'] == 0)
                                                <span class="text-muted small">0 Leaves</span>
                                            @endif
                                        </div>
                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                            Paid Duty: <strong>{{ number_format($row['total_paid_days'], 1) }}</strong>/{{ $daysInMonth }}d
                                        </div>
                                    </td>

                                    {{-- 6. Absent Days & Absent Salary Deduction --}}
                                    <td class="text-center">
                                        @if($row['absent_days'] > 0)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold">
                                                {{ number_format($row['absent_days'], 1) }} Days
                                            </span>
                                            <div class="text-danger mt-1 fw-semibold" style="font-size: 0.74rem;" id="absent_deduction_label_{{ $row['user_id'] }}">
                                                -₹{{ number_format($row['absent_deduction'], 2) }}
                                            </div>
                                        @else
                                            <span class="badge bg-light text-success border px-2 py-1">0 Absent</span>
                                            <div class="text-muted mt-1" style="font-size: 0.74rem;" id="absent_deduction_label_{{ $row['user_id'] }}">-₹0.00</div>
                                        @endif
                                    </td>

                                    {{-- 7. Earned Gross Salary --}}
                                    <td class="text-end">
                                        <div class="fw-bold text-dark" style="font-family: 'Outfit', sans-serif;" id="earned_salary_label_{{ $row['user_id'] }}">
                                            ₹{{ number_format($row['earned_salary'], 2) }}
                                        </div>
                                    </td>

                                    {{-- 8. Statutory Deductions (PF, ESI, Other) --}}
                                    <td class="text-end">
                                        <div id="statutory_label_{{ $row['user_id'] }}">
                                            @if($row['total_statutory'] > 0)
                                                <div class="text-danger fw-bold" style="font-size: 0.86rem;">
                                                    -₹{{ number_format($row['total_statutory'], 2) }}
                                                </div>
                                                <div class="text-muted" style="font-size: 0.68rem;">
                                                    PF: ₹{{ number_format($row['pf_amount'], 0) }} | ESI: ₹{{ number_format($row['esi_amount'], 0) }} | Other: ₹{{ number_format($row['other_deductions'], 0) }}
                                                </div>
                                            @else
                                                <span class="text-muted small">₹0.00</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 9. Net Salary --}}
                                    <td class="text-end">
                                        <div class="fw-bold text-secondary" style="font-family: 'Outfit', sans-serif;" id="net_salary_label_{{ $row['user_id'] }}">
                                            ₹{{ number_format($row['net_salary'], 2) }}
                                        </div>
                                    </td>

                                    {{-- 10. Extras (+Earnings, -Penalties, -Advances) --}}
                                    <td>
                                        <div style="font-size: 0.74rem;">
                                            @if($row['extra_earnings'] > 0)
                                                <div class="text-success fw-bold">+₹{{ number_format($row['extra_earnings'], 2) }} (Earn)</div>
                                            @endif
                                            @if($row['extra_deductions'] > 0)
                                                <div class="text-danger fw-semibold">-₹{{ number_format($row['extra_deductions'], 2) }} (Penalty)</div>
                                            @endif
                                            @if($row['advance_amount'] > 0)
                                                <div class="text-warning fw-semibold">-₹{{ number_format($row['advance_amount'], 2) }} (Advance)</div>
                                            @endif
                                            @if($row['extra_earnings'] == 0 && $row['extra_deductions'] == 0 && $row['advance_amount'] == 0)
                                                <span class="text-muted">₹0.00</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 11. Final Calculated Salary --}}
                                    <td class="text-end bg-light">
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem; font-family: 'Outfit', sans-serif;" id="final_salary_label_{{ $row['user_id'] }}">
                                            ₹{{ number_format($row['final_salary'], 2) }}
                                        </div>
                                    </td>

                                    {{-- 12. Actual Payable (Editable Input) --}}
                                    <td class="bg-primary-subtle">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white fw-bold px-1" style="font-size: 0.75rem;">₹</span>
                                            <input type="number" 
                                                   class="form-control form-control-sm text-end fw-bold actual-payable-input" 
                                                   id="actual_payable_{{ $row['user_id'] }}" 
                                                   value="{{ number_format($row['actual_payable'], 2, '.', '') }}" 
                                                   step="0.01" 
                                                   min="0"
                                                   style="min-width: 95px; color: #4338ca;">
                                        </div>
                                    </td>

                                    {{-- 13. Payment Date Input --}}
                                    <td>
                                        <input type="date" 
                                               class="form-control form-control-sm paid-date-input" 
                                               id="paid_date_{{ $row['user_id'] }}" 
                                               value="{{ $row['paid_date'] }}"
                                               style="font-size: 0.78rem;">
                                    </td>

                                    {{-- 14. Notes / Remarks Input --}}
                                    <td>
                                        <input type="text" 
                                               class="form-control form-control-sm notes-input" 
                                               id="notes_{{ $row['user_id'] }}" 
                                               value="{{ $row['notes'] }}" 
                                               placeholder="Notes..."
                                               maxlength="500"
                                               style="font-size: 0.78rem;">
                                    </td>

                                    {{-- 15. Actions --}}
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            {{-- Save Single Row Button --}}
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    style="padding: 4px 7px;" 
                                                    title="Save this row" 
                                                    onclick="saveSinglePayrollRow({{ $row['user_id'] }})">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            {{-- Payslip Modal View Button --}}
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info" 
                                                    style="padding: 4px 7px;" 
                                                    title="View Breakdown / Payslip" 
                                                    onclick="viewPayslipModal({{ json_encode($row) }}, '{{ addslashes($staff->name) }}', '{{ $monthName }}', {{ $year }})">
                                                <i class="fas fa-file-invoice"></i>
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

        {{-- ========================================================================= --}}
        {{-- B. HOURLY BASE STAFF PANE --}}
        {{-- ========================================================================= --}}
        @else
            {{-- 1. Hourly Filter Bar: Select Staff, From Date, To Date --}}
            <div class="bg-white rounded-3 p-3 mb-4 border shadow-sm">
                <form action="{{ route('restaurant.salary.index') }}" method="GET" id="hourlyFilterForm">
                    <input type="hidden" name="tab" value="hourly">
                    @if(request('restaurant_id'))
                        <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                    @elseif(isset($selectedOutletId))
                        <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                    @endif

                    <div class="row g-2 align-items-end">
                        {{-- Select Staff --}}
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="fas fa-user text-primary me-1"></i> Select Staff Member
                            </label>
                            <select name="staff_id" id="hourly_staff_id" class="form-select form-select-sm fw-semibold" onchange="document.getElementById('hourlyFilterForm').submit()">
                                @forelse($hourlyData['staffMembers'] as $sm)
                                    @php
                                        $smWage = (float)($sm->salary->hourly_wage ?? 0);
                                        $isHourlyType = $sm->salary && $sm->salary->employment_type === 'hourly';
                                    @endphp
                                    <option value="{{ $sm->id }}" {{ (string)$hourlyData['selectedStaffId'] === (string)$sm->id ? 'selected' : '' }}>
                                        {{ $sm->name }} ({{ $sm->role_type }}) — ₹{{ number_format($smWage, 2) }}/hr {{ $isHourlyType ? '• [Hourly]' : '' }}
                                    </option>
                                @empty
                                    <option value="">No hourly wage staff found</option>
                                @endforelse
                            </select>
                        </div>

                        {{-- From Date --}}
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="fas fa-calendar-alt text-primary me-1"></i> From Date
                            </label>
                            <input type="date" name="from_date" id="hourly_from_date" class="form-control form-control-sm fw-semibold" value="{{ $hourlyData['fromDate'] }}">
                        </div>

                        {{-- To Date --}}
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="fas fa-calendar-check text-primary me-1"></i> To Date
                            </label>
                            <input type="date" name="to_date" id="hourly_to_date" class="form-control form-control-sm fw-semibold" value="{{ $hourlyData['toDate'] }}">
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-lg-2 col-md-2 col-sm-12 text-end">
                            <div class="d-flex align-items-center gap-1">
                                <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold flex-grow-1">
                                    <i class="fas fa-calculator me-1"></i> View Calculation
                                </button>
                                <a href="{{ route('restaurant.salary.index', array_filter(['tab' => 'hourly', 'staff_id' => $hourlyData['selectedStaffId'] ?? null, 'from_date' => date('Y-m-01'), 'to_date' => date('Y-m-d'), 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                                   class="btn btn-outline-secondary btn-sm px-2" title="Reset to This Month">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if($hourlyData['selectedStaff'])
                @php
                    $curStaff = $hourlyData['selectedStaff'];
                    $curWage = $hourlyData['hourlyWage'];
                    $curRest = $hourlyData['restAmount'];
                @endphp

                {{-- 2. Staff Profile Header & Wage Bar --}}
                <div class="bg-white rounded-3 p-3 mb-4 border shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="staff-avatar-circle" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.25rem;">
                            {{ strtoupper(substr($curStaff->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="mb-0 fw-bold text-dark">{{ $curStaff->name }}</h4>
                                <span class="badge bg-light text-dark border">{{ $curStaff->role_type }}</span>
                                @if($curStaff->restaurant)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <i class="fas fa-store me-1"></i> {{ $curStaff->restaurant->name }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="fas fa-phone-alt me-1 text-secondary"></i> {{ $curStaff->phone ?? 'No phone' }} &nbsp;|&nbsp; 
                                Period: <strong class="text-dark">{{ Carbon\Carbon::parse($hourlyData['fromDate'])->format('d M Y') }}</strong> to <strong class="text-dark">{{ Carbon\Carbon::parse($hourlyData['toDate'])->format('d M Y') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 px-3 rounded-3 bg-light border text-end">
                            <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Assigned Hourly Wage</div>
                            <div class="fs-5 fw-bold text-primary">₹{{ number_format($curWage, 2) }} <span class="text-muted fw-normal fs-6">/hr</span></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="openHourlyWageModal({{ $curStaff->id }}, {{ $curWage }}, '{{ addslashes($curStaff->name) }}')">
                            <i class="fas fa-edit me-1"></i> Edit Wage
                        </button>
                    </div>
                </div>

                {{-- 3. Hourly Summary Metric Cards --}}
                <div class="payroll-stat-grid">
                    {{-- Total Hours Worked --}}
                    <div class="payroll-stat-card">
                        <div>
                            <div class="payroll-stat-label">Total Duty Hours</div>
                            <div class="payroll-stat-num text-dark">{{ number_format($hourlyData['totalDutyHours'], 2) }} <span class="fs-6 text-muted fw-normal">hrs</span></div>
                            <div class="text-muted small mt-1">{{ count($hourlyData['dutyRecords']) }} duty days logged</div>
                        </div>
                        <div class="payroll-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>

                    {{-- Total Duty Earnings --}}
                    <div class="payroll-stat-card">
                        <div>
                            <div class="payroll-stat-label">Base Duty Earning</div>
                            <div class="payroll-stat-num text-primary">₹{{ number_format($hourlyData['totalDutyEarnings'], 2) }}</div>
                            <div class="text-muted small mt-1">{{ number_format($hourlyData['totalDutyHours'], 2) }} hrs × ₹{{ number_format($curWage, 2) }}</div>
                        </div>
                        <div class="payroll-stat-icon-wrap" style="background: #ede9fe; color: #6d28d9;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>

                    {{-- Net Extras Impact --}}
                    <div class="payroll-stat-card">
                        <div>
                            <div class="payroll-stat-label">Extras Impact (Net)</div>
                            <div class="payroll-stat-num {{ $hourlyData['netExtras'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $hourlyData['netExtras'] >= 0 ? '+' : '' }}₹{{ number_format($hourlyData['netExtras'], 2) }}
                            </div>
                            <div class="text-muted small mt-1">
                                +₹{{ number_format($hourlyData['extraEarnings'], 2) }} | -₹{{ number_format($hourlyData['extraPenalties'] + $hourlyData['advanceAmount'], 2) }}
                            </div>
                        </div>
                        <div class="payroll-stat-icon-wrap" style="background: {{ $hourlyData['netExtras'] >= 0 ? '#dcfce7' : '#fee2e2' }}; color: {{ $hourlyData['netExtras'] >= 0 ? '#15803d' : '#b91c1c' }};">
                            <i class="fas fa-plus-minus"></i>
                        </div>
                    </div>

                    {{-- Gross Due Amount --}}
                    <div class="payroll-stat-card">
                        <div>
                            <div class="payroll-stat-label">Total Gross Due</div>
                            <div class="payroll-stat-num text-dark">₹{{ number_format($hourlyData['grossDue'], 2) }}</div>
                            <div class="text-muted small mt-1">Duty + Extras Net</div>
                        </div>
                        <div class="payroll-stat-icon-wrap" style="background: #f1f5f9; color: #334155;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>

                    {{-- Total Payments Disbursed --}}
                    <div class="payroll-stat-card">
                        <div>
                            <div class="payroll-stat-label">Total Paid Disbursed</div>
                            <div class="payroll-stat-num text-info">₹{{ number_format($hourlyData['totalPayments'], 2) }}</div>
                            <div class="text-muted small mt-1">{{ $hourlyData['payments']->count() }} payments recorded</div>
                        </div>
                        <div class="payroll-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                            <i class="fas fa-hand-holding-dollar"></i>
                        </div>
                    </div>

                    {{-- Rest Amount Need to Pay --}}
                    <div class="payroll-stat-card {{ $curRest > 0 ? 'card-rest-due-pending' : 'card-rest-due' }}">
                        <div>
                            <div class="payroll-stat-label fw-bold" style="color: {{ $curRest > 0 ? '#b45309' : '#15803d' }};">
                                <i class="fas fa-exclamation-circle me-1"></i> Rest Amount Need to Pay
                            </div>
                            <div class="payroll-stat-num" style="color: {{ $curRest > 0 ? '#b45309' : '#15803d' }};">
                                ₹{{ number_format($curRest, 2) }}
                            </div>
                            <div class="small fw-semibold mt-1" style="color: {{ $curRest > 0 ? '#b45309' : '#15803d' }};">
                                {{ $curRest > 0 ? 'Estimated balance remaining' : 'Fully settled' }}
                            </div>
                        </div>
                        <div class="payroll-stat-icon-wrap" style="background: {{ $curRest > 0 ? '#fef3c7' : '#dcfce7' }}; color: {{ $curRest > 0 ? '#b45309' : '#15803d' }};">
                            <i class="fas {{ $curRest > 0 ? 'fa-wallet' : 'fa-check-double' }}"></i>
                        </div>
                    </div>
                </div>

                {{-- 4. Day-wise Duty List Table --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="fas fa-calendar-day text-primary me-2"></i> Day-wise Duty Breakdown
                            </h5>
                            <span class="text-muted small">Daily attendance hours (Login &amp; Logout) and calculated earnings at ₹{{ number_format($curWage, 2) }}/hr</span>
                        </div>
                        <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold">
                            {{ count($hourlyData['dutyRecords']) }} Days
                        </span>
                    </div>

                    <div class="card-body p-0">
                        @if(empty($hourlyData['dutyRecords']))
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-user-clock fs-1 mb-2 d-block text-secondary"></i>
                                <h6 class="fw-bold">No duty records found for this period</h6>
                                <p class="small mb-0">No attendances logged between {{ Carbon\Carbon::parse($hourlyData['fromDate'])->format('d M Y') }} and {{ Carbon\Carbon::parse($hourlyData['toDate'])->format('d M Y') }}.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3" style="width: 50px;">#</th>
                                            <th>Duty Date &amp; Day</th>
                                            <th>Attendance Status</th>
                                            <th>Login Time</th>
                                            <th>Logout Time</th>
                                            <th class="text-center">Hours Worked (hr)</th>
                                            <th class="text-end">Hourly Wage</th>
                                            <th class="text-end pe-3">Daily Duty Earning</th>
                                            <th>Notes / Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hourlyData['dutyRecords'] as $idx => $record)
                                            <tr>
                                                <td class="ps-3 text-muted fw-bold">{{ $idx + 1 }}</td>
                                                <td>
                                                    <span class="fw-bold text-dark">{{ $record['formatted_date'] }}</span>
                                                    <span class="badge bg-light text-secondary border ms-1">{{ $record['day_name'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $record['badge_class'] }}">
                                                        {{ $record['type_label'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($record['raw_login_time'])
                                                        <span class="fw-semibold text-dark"><i class="fas fa-sign-in-alt text-success me-1"></i> {{ $record['login_time'] }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($record['raw_logout_time'])
                                                        <span class="fw-semibold text-dark"><i class="fas fa-sign-out-alt text-danger me-1"></i> {{ $record['logout_time'] }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge px-2 py-1 fw-bold" style="background: #e0e7ff; color: #4338ca; font-size: 0.82rem;">
                                                        {{ number_format($record['hours'], 2) }} hrs
                                                    </span>
                                                    @if($record['work_duration'] && $record['work_duration'] !== '--')
                                                        <span class="text-muted small ms-1">({{ $record['work_duration'] }})</span>
                                                    @endif
                                                </td>
                                                <td class="text-end text-muted fw-semibold">
                                                    ₹{{ number_format($record['hourly_wage'], 2) }}
                                                </td>
                                                <td class="text-end pe-3">
                                                    <span class="fw-bold text-dark fs-6">₹{{ number_format($record['earning'], 2) }}</span>
                                                </td>
                                                <td class="text-muted small">
                                                    {{ $record['notes'] ?: '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light border-top">
                                        <tr class="fw-bold">
                                            <td colspan="5" class="ps-3 text-uppercase text-secondary">
                                                Total Duty Hours &amp; Base Earning
                                            </td>
                                            <td class="text-center text-primary fs-6">
                                                {{ number_format($hourlyData['totalDutyHours'], 2) }} hrs
                                            </td>
                                            <td></td>
                                            <td class="text-end pe-3 text-primary fs-6">
                                                ₹{{ number_format($hourlyData['totalDutyEarnings'], 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 5. Extras Table: Earnings (+), Penalties (-), Advances (-) --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="fas fa-coins text-warning me-2"></i> Extras During This Period
                            </h5>
                            <span class="text-muted small">Plus (+) Extra Earnings | Minus (-) Penalties &amp; Advance Amounts</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                + Earning: ₹{{ number_format($hourlyData['extraEarnings'], 2) }}
                            </span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                - Penalty: ₹{{ number_format($hourlyData['extraPenalties'], 2) }}
                            </span>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                - Advance: ₹{{ number_format($hourlyData['advanceAmount'], 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if($hourlyData['extras']->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <p class="small mb-0">No extras (earnings, penalties, or advances) recorded in this period.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.84rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Date</th>
                                            <th>Type</th>
                                            <th>Impact</th>
                                            <th class="text-end">Amount</th>
                                            <th>Remarks</th>
                                            <th>Logged By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hourlyData['extras'] as $ex)
                                            <tr>
                                                <td class="ps-3 fw-semibold text-dark">
                                                    {{ Carbon\Carbon::parse($ex->entry_date)->format('d M Y') }}
                                                </td>
                                                <td>
                                                    <span class="badge {{ $ex->type_badge['class'] }}">
                                                        <i class="fas {{ $ex->type_badge['icon'] }} me-1"></i> {{ $ex->type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($ex->type === 'Earning')
                                                        <span class="text-success fw-bold"><i class="fas fa-plus me-1"></i> Added to Salary</span>
                                                    @elseif($ex->type === 'Deduction')
                                                        <span class="text-danger fw-bold"><i class="fas fa-minus me-1"></i> Penalty Deduction</span>
                                                    @else
                                                        <span class="text-warning fw-bold"><i class="fas fa-minus me-1"></i> Advance Payout</span>
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold {{ $ex->type === 'Earning' ? 'text-success' : 'text-danger' }}">
                                                    {{ $ex->type === 'Earning' ? '+' : '-' }}₹{{ number_format($ex->amount, 2) }}
                                                </td>
                                                <td class="text-muted">{{ $ex->remarks ?: '—' }}</td>
                                                <td class="text-muted small">{{ $ex->creator ? $ex->creator->name : 'System' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light border-top">
                                        <tr class="fw-bold">
                                            <td colspan="3" class="ps-3 text-uppercase text-secondary">Net Extras Balance Impact</td>
                                            <td class="text-end fs-6 {{ $hourlyData['netExtras'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $hourlyData['netExtras'] >= 0 ? '+' : '' }}₹{{ number_format($hourlyData['netExtras'], 2) }}
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 6. Lower Two-Column Section: Payments Disbursed Table + Record Payment Form --}}
                <div class="row g-4 mb-4">
                    {{-- Left Column: Payments Table --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title fw-bold mb-0 text-dark">
                                        <i class="fas fa-receipt text-info me-2"></i> Payments Made Against This Period
                                    </h5>
                                    <span class="text-muted small">Payments already disbursed to reduce outstanding rest payment</span>
                                </div>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 fw-bold">
                                    Total Paid: ₹{{ number_format($hourlyData['totalPayments'], 2) }}
                                </span>
                            </div>

                            <div class="card-body p-0">
                                @if($hourlyData['payments']->isEmpty())
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-hand-holding-dollar fs-1 mb-2 d-block text-secondary"></i>
                                        <h6 class="fw-bold">No payments recorded yet</h6>
                                        <p class="small mb-0">Use the form on the right to disburse and record payments for this period.</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3">Payment Date</th>
                                                    <th class="text-end">Amount</th>
                                                    <th>Mode</th>
                                                    <th>Period Ref</th>
                                                    <th>Remarks</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($hourlyData['payments'] as $p)
                                                    <tr id="payment_row_{{ $p->id }}">
                                                        <td class="ps-3 fw-bold text-dark">
                                                            {{ Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}
                                                        </td>
                                                        <td class="text-end fw-bold text-success fs-6">
                                                            ₹{{ number_format($p->amount, 2) }}
                                                        </td>
                                                        <td>
                                                            @if($p->payment_method === 'ONLINE')
                                                                <span class="badge-mode-online">
                                                                    <i class="fas fa-globe me-1"></i> ONLINE
                                                                </span>
                                                            @else
                                                                <span class="badge-mode-cash">
                                                                    <i class="fas fa-money-bill-wave me-1"></i> CASH
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-muted small">
                                                            @if($p->period_from && $p->period_to)
                                                                {{ Carbon\Carbon::parse($p->period_from)->format('d M') }} - {{ Carbon\Carbon::parse($p->period_to)->format('d M Y') }}
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td class="text-muted">{{ $p->remarks ?: '—' }}</td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-danger" style="padding: 3px 8px;" title="Delete Payment Record" onclick="deleteHourlyPaymentRow({{ $p->id }}, {{ $p->amount }})">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light border-top">
                                                <tr class="fw-bold">
                                                    <td class="ps-3 text-uppercase text-secondary">Total Payments Disbursed</td>
                                                    <td class="text-end text-success fs-6">₹{{ number_format($hourlyData['totalPayments'], 2) }}</td>
                                                    <td colspan="4"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Payment Option Form --}}
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="card-title fw-bold mb-0 text-dark">
                                    <i class="fas fa-credit-card text-success me-2"></i> Payment Option
                                </h5>
                                <span class="text-muted small">Record and disburse payment against this time period</span>
                            </div>

                            <div class="card-body p-4">
                                {{-- Outstanding Balance Alert Banner --}}
                                <div class="p-3 rounded-3 mb-3 border {{ $curRest > 0 ? 'card-rest-due-pending' : 'card-rest-due' }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="small fw-bold text-uppercase" style="color: {{ $curRest > 0 ? '#b45309' : '#15803d' }};">
                                                Estimation Rest Payment Need to Pay
                                            </div>
                                            <div class="fs-4 fw-bold" style="color: {{ $curRest > 0 ? '#b45309' : '#15803d' }};">
                                                ₹{{ number_format($curRest, 2) }}
                                            </div>
                                        </div>
                                        <div>
                                            @if($curRest > 0)
                                                <span class="badge bg-warning text-dark px-3 py-2 fw-bold">Due</span>
                                            @else
                                                <span class="badge bg-success px-3 py-2 fw-bold"><i class="fas fa-check-circle me-1"></i> Settled</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment Form --}}
                                <form id="hourlyPaymentForm" onsubmit="submitHourlyPayment(event)">
                                    <input type="hidden" name="user_id" value="{{ $curStaff->id }}">
                                    <input type="hidden" name="period_from" value="{{ $hourlyData['fromDate'] }}">
                                    <input type="hidden" name="period_to" value="{{ $hourlyData['toDate'] }}">

                                    {{-- Amount Input --}}
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted mb-1">
                                            Payment Amount (₹) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white fw-bold">₹</span>
                                            <input type="number" 
                                                   name="amount" 
                                                   id="payment_amount_input" 
                                                   class="form-control fw-bold text-dark fs-5" 
                                                   value="{{ $curRest > 0 ? number_format($curRest, 2, '.', '') : '0.00' }}" 
                                                   step="0.01" 
                                                   min="0.01" 
                                                   required>
                                            @if($curRest > 0)
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('payment_amount_input').value='{{ number_format($curRest, 2, '.', '') }}'">
                                                    Full Rest
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Payment Date --}}
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted mb-1">
                                            Payment Date <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               name="payment_date" 
                                               id="payment_date_input" 
                                               class="form-control fw-semibold" 
                                               value="{{ date('Y-m-d') }}" 
                                               required>
                                    </div>

                                    {{-- Mode of Payment (CASH / ONLINE) --}}
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted mb-1">
                                            Mode of Payment <span class="text-danger">*</span>
                                        </label>
                                        <div class="mode-select-pill">
                                            <input type="radio" name="payment_method" id="pay_mode_cash" value="CASH" checked>
                                            <label for="pay_mode_cash" class="lbl-cash">
                                                <i class="fas fa-money-bill-wave me-1"></i> CASH
                                            </label>

                                            <input type="radio" name="payment_method" id="pay_mode_online" value="ONLINE">
                                            <label for="pay_mode_online" class="lbl-online">
                                                <i class="fas fa-globe me-1"></i> ONLINE
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Remarks --}}
                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-muted mb-1">Remarks / Note</label>
                                        <input type="text" 
                                               name="remarks" 
                                               id="payment_remarks_input" 
                                               class="form-control" 
                                               placeholder="e.g. Settle duty payment for this period, UPI txn #..." 
                                               maxlength="250">
                                    </div>

                                    {{-- Submit Button --}}
                                    <button type="submit" class="btn btn-success fw-bold w-100 py-2 shadow-sm rounded-pill" id="recordPaymentBtn">
                                        <span id="recordPaymentText">
                                            <i class="fas fa-check-circle me-1"></i> Record &amp; Disburse Payment
                                        </span>
                                        <span id="recordPaymentSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <div class="card border-0 shadow-sm rounded-3 p-5 text-center text-muted">
                    <i class="fas fa-user-clock fs-1 mb-2 text-secondary"></i>
                    <h5 class="fw-bold">No Hourly Wage Staff Found</h5>
                    <p class="small mb-0">Only staff who do duty as hourly wage (or have attendance captured as Hourly Wage) appear here.<br>Permanent employees without hourly duty are excluded. You can mark attendance as <strong>Hourly Wage</strong> in <a href="{{ route('restaurant.attendance.index', array_filter(['restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="fw-bold text-primary">Attendance Management</a> or set their hourly wage in Staff Salary.</p>
                </div>
            @endif

        @endif
    </div>

</div>
</div>

{{-- ========================================================================= --}}
{{-- 6. Modal: Detailed Corporate Payslip Breakdown --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="payslipModal" tabindex="-1" aria-labelledby="payslipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2" id="payslipModalLabel">
                    <i class="fas fa-file-invoice-dollar"></i> Salary Slip: <span id="slipStaffName">Employee</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" id="printablePayslipContent">
                {{-- Slip Header --}}
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark" id="slipRestaurantName">{{ auth()->user()->restaurant ? auth()->user()->restaurant->name : 'Restaurant POS' }}</h4>
                        <div class="text-muted small">Corporate Permanent Employee Payroll Slip</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary px-3 py-2 fs-6" id="slipMonthYear">Month Year</span>
                    </div>
                </div>

                {{-- Employee Meta Grid --}}
                <div class="row g-2 mb-4 bg-light p-3 rounded border">
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Employee Name</div>
                        <div class="fw-bold text-dark" id="slipEmpName">—</div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Role / Designation</div>
                        <div class="fw-bold text-dark" id="slipEmpRole">—</div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Days in Month</div>
                        <div class="fw-bold text-dark" id="slipTotalDays">30</div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Paid Duty Days</div>
                        <div class="fw-bold text-success" id="slipPaidDays">30</div>
                    </div>
                </div>

                {{-- Two Column Breakdown: Earnings vs Deductions --}}
                <div class="row g-4">
                    {{-- Left Column: Earnings --}}
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-success border-bottom pb-2 mb-3">
                            <i class="fas fa-plus-circle me-1"></i> Earnings &amp; Credits
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Base Monthly Salary:</td>
                                    <td class="text-end fw-semibold" id="slipBaseSalary">₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Earned Duty Salary:</td>
                                    <td class="text-end fw-semibold text-dark" id="slipEarnedSalary">₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Extra Earnings / Bonus:</td>
                                    <td class="text-end fw-semibold text-success" id="slipExtraEarnings">+₹0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-dark">Total Gross Credits:</td>
                                    <td class="text-end fw-bold text-dark" id="slipTotalGross">₹0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Right Column: Deductions --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-danger border-bottom pb-2 mb-3">
                            <i class="fas fa-minus-circle me-1"></i> Deductions &amp; Recoveries
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Absent Deduction:</td>
                                    <td class="text-end fw-semibold text-danger" id="slipAbsentDeduction">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Provident Fund (PF):</td>
                                    <td class="text-end fw-semibold" id="slipPf">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ESI Deduction:</td>
                                    <td class="text-end fw-semibold" id="slipEsi">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Other / P-Tax:</td>
                                    <td class="text-end fw-semibold" id="slipOther">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Penalties / Fines:</td>
                                    <td class="text-end fw-semibold text-danger" id="slipPenalty">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Advance Recovery:</td>
                                    <td class="text-end fw-semibold text-warning" id="slipAdvance">-₹0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-danger">Total Deductions:</td>
                                    <td class="text-end fw-bold text-danger" id="slipTotalDeductions">-₹0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Net Take Home Box --}}
                <div class="mt-4 p-3 rounded bg-light border d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Net Final Calculated Salary</div>
                        <div class="fs-4 fw-bold text-dark" id="slipFinalSalary">₹0.00</div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small fw-bold text-uppercase">Actual Payable</div>
                        <div class="fs-4 fw-bold text-primary" id="slipActualPayable">₹0.00</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Payslip
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- 7. Modal: Quick Edit Hourly Wage Rate --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="hourlyWageModal" tabindex="-1" aria-labelledby="hourlyWageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h6 class="modal-title fw-bold text-white mb-0" id="hourlyWageModalLabel">
                    <i class="fas fa-stopwatch me-1"></i> Edit Hourly Wage Rate
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="hourlyWageForm" onsubmit="saveHourlyWageRate(event)">
                <div class="modal-body p-3">
                    <input type="hidden" name="user_id" id="wage_user_id">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Staff Member</label>
                        <div class="fw-bold text-dark fs-6" id="wage_staff_name">Employee</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Hourly Wage Rate (₹/hr) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white fw-bold">₹</span>
                            <input type="number" name="hourly_wage" id="wage_rate_input" class="form-control fw-bold" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-bold" id="wageSaveBtn">
                        <span id="wageSaveText">Update Rate</span>
                        <span id="wageSaveSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
@include('includes.script')
<script>
    let payslipModalInstance = null;

    function getPayslipModal() {
        const el = document.getElementById('payslipModal');
        if (!el) return null;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            return bootstrap.Modal.getOrCreateInstance(el);
        }
        return null;
    }

    document.addEventListener('DOMContentLoaded', function() {
        getPayslipModal();

        // Live keyword search filter on payroll table
        const searchInput = document.getElementById('filter_search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#payrollTable tbody tr');
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }
    });

    // Toast notification helper
    function showToast(message, type = 'success') {
        const container = document.getElementById('payrollToastContainer');
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

    // Toggle Paid Checkbox UI change
    function onPaidCheckboxChange(userId) {
        const chk = document.getElementById(`paid_chk_${userId}`);
        const row = document.getElementById(`payroll-row-${userId}`);
        const dateInput = document.getElementById(`paid_date_${userId}`);

        if (chk.checked) {
            row.classList.add('row-paid');
            if (!dateInput.value) {
                dateInput.value = '{{ date("Y-m-d") }}';
            }
        } else {
            row.classList.remove('row-paid');
        }
    }

    // Toggle all paid checkboxes
    function markAllPaidToggle() {
        const checkboxes = document.querySelectorAll('.paid-checkbox-input');
        if (checkboxes.length === 0) return;

        // Check if at least one is unchecked
        let anyUnchecked = Array.from(checkboxes).some(cb => !cb.checked);

        checkboxes.forEach(cb => {
            cb.checked = anyUnchecked;
            const row = cb.closest('tr');
            const userId = row.getAttribute('data-user-id');
            onPaidCheckboxChange(userId);
        });
    }

    // Recalculate row on base salary change
    function recalculateRowFromBaseSalary(userId) {
        const row = document.getElementById(`payroll-row-${userId}`);
        if (!row) return;

        const daysInMonth = parseFloat(row.getAttribute('data-days-in-month')) || 30;
        const absentDays = parseFloat(row.getAttribute('data-absent-days')) || 0;
        const pfPct = parseFloat(row.getAttribute('data-pf-pct')) || 0;
        const esiPct = parseFloat(row.getAttribute('data-esi-pct')) || 0;
        const ptaxType = row.getAttribute('data-ptax-type') || 'percentage';
        const ptaxVal = parseFloat(row.getAttribute('data-ptax-val')) || 0;
        const otherType = row.getAttribute('data-other-type') || 'percentage';
        const otherVal = parseFloat(row.getAttribute('data-other-val')) || 0;

        const extraEarn = parseFloat(row.getAttribute('data-extra-earn')) || 0;
        const extraPenalty = parseFloat(row.getAttribute('data-extra-penalty')) || 0;
        const extraAdvance = parseFloat(row.getAttribute('data-extra-advance')) || 0;

        const baseSalaryInput = document.getElementById(`base_salary_${userId}`);
        const baseSalary = parseFloat(baseSalaryInput.value) || 0;

        const perDayRate = daysInMonth > 0 ? (baseSalary / daysInMonth) : 0;
        const absentDeduction = Math.round(absentDays * perDayRate * 100) / 100;
        const earnedSalary = Math.max(0, Math.round((baseSalary - absentDeduction) * 100) / 100);

        const pfAmount = Math.round((earnedSalary * pfPct / 100) * 100) / 100;
        const esiAmount = Math.round((earnedSalary * esiPct / 100) * 100) / 100;
        const ptaxAmount = ptaxType === 'fixed' ? ptaxVal : Math.round((earnedSalary * ptaxVal / 100) * 100) / 100;
        const otherDedAmount = otherType === 'fixed' ? otherVal : Math.round((earnedSalary * otherVal / 100) * 100) / 100;
        const otherDeductions = Math.round((ptaxAmount + otherDedAmount) * 100) / 100;
        const totalStatutory = Math.round((pfAmount + esiAmount + otherDeductions) * 100) / 100;

        const netSalary = Math.max(0, Math.round((earnedSalary - totalStatutory) * 100) / 100);
        const finalSalary = Math.max(0, Math.round((netSalary + extraEarn - extraPenalty - extraAdvance) * 100) / 100);

        // Update labels
        const perDayLabel = document.getElementById(`per_day_label_${userId}`);
        if (perDayLabel) perDayLabel.textContent = `(₹${perDayRate.toFixed(2)}/day)`;

        const absentLabel = document.getElementById(`absent_deduction_label_${userId}`);
        if (absentLabel) absentLabel.textContent = `-₹${absentDeduction.toFixed(2)}`;

        const earnedLabel = document.getElementById(`earned_salary_label_${userId}`);
        if (earnedLabel) earnedLabel.textContent = `₹${earnedSalary.toFixed(2)}`;

        const statLabel = document.getElementById(`statutory_label_${userId}`);
        if (statLabel) {
            if (totalStatutory > 0) {
                statLabel.innerHTML = `
                    <div class="text-danger fw-bold" style="font-size: 0.86rem;">-₹${totalStatutory.toFixed(2)}</div>
                    <div class="text-muted" style="font-size: 0.68rem;">PF: ₹${pfAmount.toFixed(0)} | ESI: ₹${esiAmount.toFixed(0)} | Other: ₹${otherDeductions.toFixed(0)}</div>
                `;
            } else {
                statLabel.innerHTML = `<span class="text-muted small">₹0.00</span>`;
            }
        }

        const netLabel = document.getElementById(`net_salary_label_${userId}`);
        if (netLabel) netLabel.textContent = `₹${netSalary.toFixed(2)}`;

        const finalLabel = document.getElementById(`final_salary_label_${userId}`);
        if (finalLabel) finalLabel.textContent = `₹${finalSalary.toFixed(2)}`;

        const actualPayableInput = document.getElementById(`actual_payable_${userId}`);
        if (actualPayableInput) actualPayableInput.value = finalSalary.toFixed(2);
    }

    // Save single row via AJAX
    async function saveSinglePayrollRow(userId) {
        const row = document.getElementById(`payroll-row-${userId}`);
        if (!row) return;

        const isPaid = document.getElementById(`paid_chk_${userId}`).checked;
        const baseSalary = document.getElementById(`base_salary_${userId}`) ? document.getElementById(`base_salary_${userId}`).value : 0;
        const actualPayable = document.getElementById(`actual_payable_${userId}`).value;
        const paidDate = document.getElementById(`paid_date_${userId}`).value;
        const notes = document.getElementById(`notes_${userId}`).value;

        const year = {{ $year }};
        const month = {{ $month }};

        try {
            const response = await fetch('{{ route("restaurant.salary.save-single") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    year: year,
                    month: month,
                    base_salary: baseSalary,
                    actual_payable: actualPayable,
                    is_paid: isPaid,
                    paid_date: paidDate,
                    notes: notes,
                    restaurant_id: '{{ request("restaurant_id", $selectedOutletId ?? "") }}'
                })
            });

            const res = await response.json();
            if (response.ok && res.success) {
                showToast(res.message || 'Payroll row saved successfully!', 'success');
            } else {
                showToast(res.message || 'Failed to save row.', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Network error while saving row.', 'error');
        }
    }

    // Save All rows via batch AJAX
    async function saveAllPayrollRows() {
        const rows = document.querySelectorAll('#payrollTable tbody tr');
        if (rows.length === 0) return;

        const batchBtn = document.getElementById('batchSaveBtn');
        const batchText = document.getElementById('batchSaveText');
        const spinner = document.getElementById('batchSaveSpinner');

        batchBtn.disabled = true;
        batchText.classList.add('d-none');
        spinner.classList.remove('d-none');

        const payrollRows = [];
        rows.forEach(row => {
            const userId = row.getAttribute('data-user-id');
            if (userId) {
                const isPaid = document.getElementById(`paid_chk_${userId}`).checked;
                const baseSalary = document.getElementById(`base_salary_${userId}`) ? document.getElementById(`base_salary_${userId}`).value : 0;
                const actualPayable = document.getElementById(`actual_payable_${userId}`).value;
                const paidDate = document.getElementById(`paid_date_${userId}`).value;
                const notes = document.getElementById(`notes_${userId}`).value;

                payrollRows.push({
                    user_id: userId,
                    base_salary: baseSalary,
                    is_paid: isPaid,
                    actual_payable: actualPayable,
                    paid_date: paidDate,
                    notes: notes
                });
            }
        });

        try {
            const response = await fetch('{{ route("restaurant.salary.save-batch") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    year: {{ $year }},
                    month: {{ $month }},
                    payroll_rows: payrollRows,
                    restaurant_id: '{{ request("restaurant_id", $selectedOutletId ?? "") }}'
                })
            });

            const res = await response.json();
            if (response.ok && res.success) {
                showToast(res.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 700);
            } else {
                showToast(res.message || 'Failed to save batch payroll.', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Server error while saving payroll.', 'error');
        } finally {
            batchBtn.disabled = false;
            batchText.classList.remove('d-none');
            spinner.classList.add('d-none');
        }
    }

    // View Payslip Modal
    function viewPayslipModal(data, staffName, monthName, year) {
        document.getElementById('slipStaffName').textContent = staffName;
        document.getElementById('slipEmpName').textContent = staffName;
        document.getElementById('slipEmpRole').textContent = data.staff.role_type || 'Staff';
        document.getElementById('slipMonthYear').textContent = `${monthName} ${year}`;
        document.getElementById('slipTotalDays').textContent = '{{ $daysInMonth }} Days';
        document.getElementById('slipPaidDays').textContent = `${data.total_paid_days} Days`;

        // Earnings
        document.getElementById('slipBaseSalary').textContent = '₹' + parseFloat(data.base_salary).toFixed(2);
        document.getElementById('slipEarnedSalary').textContent = '₹' + parseFloat(data.earned_salary).toFixed(2);
        document.getElementById('slipExtraEarnings').textContent = '+₹' + parseFloat(data.extra_earnings).toFixed(2);
        const grossCredits = parseFloat(data.earned_salary) + parseFloat(data.extra_earnings);
        document.getElementById('slipTotalGross').textContent = '₹' + grossCredits.toFixed(2);

        // Deductions
        document.getElementById('slipAbsentDeduction').textContent = '-₹' + parseFloat(data.absent_deduction).toFixed(2);
        document.getElementById('slipPf').textContent = '-₹' + parseFloat(data.pf_amount).toFixed(2);
        document.getElementById('slipEsi').textContent = '-₹' + parseFloat(data.esi_amount).toFixed(2);
        document.getElementById('slipOther').textContent = '-₹' + parseFloat(data.other_deductions).toFixed(2);
        document.getElementById('slipPenalty').textContent = '-₹' + parseFloat(data.extra_deductions).toFixed(2);
        document.getElementById('slipAdvance').textContent = '-₹' + parseFloat(data.advance_amount).toFixed(2);

        const totalDeductions = parseFloat(data.total_statutory) + parseFloat(data.extra_deductions) + parseFloat(data.advance_amount);
        document.getElementById('slipTotalDeductions').textContent = '-₹' + totalDeductions.toFixed(2);

        // Net & Actual
        document.getElementById('slipFinalSalary').textContent = '₹' + parseFloat(data.final_salary).toFixed(2);
        document.getElementById('slipActualPayable').textContent = '₹' + parseFloat(data.actual_payable).toFixed(2);

        const modal = getPayslipModal();
        if (modal) modal.show();
    }

    // =========================================================================
    // Hourly Staff Operations
    // =========================================================================

    // Open Modal to update hourly wage rate
    function openHourlyWageModal(userId, currentWage, staffName) {
        document.getElementById('wage_user_id').value = userId;
        document.getElementById('wage_rate_input').value = parseFloat(currentWage).toFixed(2);
        document.getElementById('wage_staff_name').textContent = staffName;
        const modalEl = document.getElementById('hourlyWageModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }

    // Save Hourly Wage Rate via AJAX
    async function saveHourlyWageRate(e) {
        e.preventDefault();
        const userId = document.getElementById('wage_user_id').value;
        const wage = document.getElementById('wage_rate_input').value;
        const btn = document.getElementById('wageSaveBtn');
        const text = document.getElementById('wageSaveText');
        const spin = document.getElementById('wageSaveSpinner');

        btn.disabled = true;
        text.classList.add('d-none');
        spin.classList.remove('d-none');

        try {
            const resp = await fetch('{{ route("restaurant.salary.save-hourly-wage") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    hourly_wage: wage
                })
            });
            const res = await resp.json();
            if (resp.ok && res.success) {
                showToast(res.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(res.message || 'Failed to update wage rate.', 'error');
            }
        } catch (err) {
            showToast('Server error while updating wage rate.', 'error');
        } finally {
            btn.disabled = false;
            text.classList.remove('d-none');
            spin.classList.add('d-none');
        }
    }

    // Submit Hourly Payment Record
    async function submitHourlyPayment(e) {
        e.preventDefault();
        const form = document.getElementById('hourlyPaymentForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const btn = document.getElementById('recordPaymentBtn');
        const text = document.getElementById('recordPaymentText');
        const spin = document.getElementById('recordPaymentSpinner');

        btn.disabled = true;
        text.classList.add('d-none');
        spin.classList.remove('d-none');

        try {
            const resp = await fetch('{{ route("restaurant.salary.record-hourly-payment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const res = await resp.json();
            if (resp.ok && res.success) {
                showToast(res.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(res.message || 'Failed to record payment.', 'error');
            }
        } catch (err) {
            showToast('Server error while recording payment.', 'error');
        } finally {
            btn.disabled = false;
            text.classList.remove('d-none');
            spin.classList.add('d-none');
        }
    }

    // Delete Hourly Payment Record
    async function deleteHourlyPaymentRow(paymentId, amount) {
        if (!confirm(`Are you sure you want to delete this payment record of ₹${parseFloat(amount).toFixed(2)}?`)) {
            return;
        }

        try {
            const resp = await fetch(`{{ url('admin/salary-management/hourly-payment') }}/${paymentId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const res = await resp.json();
            if (resp.ok && res.success) {
                showToast(res.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(res.message || 'Failed to delete payment.', 'error');
            }
        } catch (err) {
            showToast('Server error while deleting payment.', 'error');
        }
    }
</script>
@endsection
