@extends('layouts.app')

@section('title')
<title>Admin || Staff Operations & Salary Management</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/restaurant-staff.css') }}">
<style>
    .op-banner-gradient {
        background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%);
        border: 1px solid #ccfbf1;
    }
    .op-section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    }
    .op-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
    }
    .op-section-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1.1rem;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .op-section-subtitle {
        color: #64748b;
        font-size: 0.82rem;
        margin-top: 2px;
    }

    /* Leave Policy Cards */
    .leave-category-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 22px;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .leave-category-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }
    .leave-card-topbar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    .leave-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .leave-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .stat-metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
    }
    .stat-metric-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .stat-metric-label {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
    }
    .stat-metric-val {
        font-family: 'Outfit', sans-serif;
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    /* Calendar & List View Styles */
    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
    }
    @media (max-width: 991px) {
        .cal-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 575px) {
        .cal-grid {
            grid-template-columns: 1fr;
        }
    }
    .cal-header-cell {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px;
        text-align: center;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
    }
    .cal-header-cell.weekend {
        color: #dc2626;
        background: #fef2f2;
        border-color: #fecaca;
    }
    .cal-day-cell {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px;
        min-height: 110px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
        position: relative;
    }
    .cal-day-cell:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
        border-color: #cbd5e1;
    }
    .cal-day-cell.is-today {
        border: 2px solid #0d9488 !important;
        background: #f0fdfa !important;
    }
    .cal-day-cell.is-weekend {
        background: #fbfbfe;
    }
    .cal-empty-cell {
        background: #f8fafc;
        border: 1px dashed #e2e8f0;
        border-radius: 12px;
        min-height: 110px;
        opacity: 0.5;
    }
    .badge-attendance-present {
        background-color: #dcfce7 !important;
        color: #15803d !important;
        border: 1px solid #bbf7d0 !important;
    }
    .badge-attendance-halfday {
        background-color: #fef9c3 !important;
        color: #854d0e !important;
        border: 1px solid #fef08a !important;
    }
    .badge-attendance-earlyleave {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        border: 1px solid #bae6fd !important;
    }
    .badge-attendance-weekoff {
        background-color: #ede9fe !important;
        color: #6d28d9 !important;
        border: 1px solid #ddd6fe !important;
    }
    .badge-calendar-leave {
        background-color: #e0e7ff !important;
        color: #4338ca !important;
        border: 1px solid #c7d2fe !important;
    }
    .badge-calendar-absent {
        background-color: #fee2e2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
    }
    .badge-calendar-upcoming {
        background-color: #f1f5f9 !important;
        color: #64748b !important;
        border: 1px solid #e2e8f0 !important;
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

        {{-- 1. Staff Profile Banner --}}
        @php
            $initials = '';
            $nameParts = explode(' ', trim($staff->name));
            foreach(array_slice($nameParts, 0, 2) as $part) {
                if (!empty($part)) $initials .= strtoupper($part[0]);
            }
            if (empty($initials)) $initials = 'ST';

            $roleClass = 'role-Default';
            if (stripos($staff->role_type, 'Manager') !== false) $roleClass = 'role-Manager';
            elseif (stripos($staff->role_type, 'Cashier') !== false) $roleClass = 'role-Cashier';
            elseif (stripos($staff->role_type, 'Waiter') !== false) $roleClass = 'role-Waiter';
            elseif (stripos($staff->role_type, 'Kitchen') !== false) $roleClass = 'role-Kitchen';

            $empType = old('employment_type', $salary ? $salary->employment_type : 'permanent');
            $monthlySalary = old('monthly_salary', $salary && $salary->monthly_salary > 0 ? $salary->monthly_salary : '');
            $pfPct = old('pf_percentage', $salary ? $salary->pf_percentage : 0);
            $esiPct = old('esi_percentage', $salary ? $salary->esi_percentage : 0);
            $ptaxPct = old('ptax_percentage', $salary ? $salary->ptax_percentage : 0);
            $otherPct = old('other_deduction_percentage', $salary ? $salary->other_deduction_percentage : 0);
            $weekoffCount = old('weekoff_count', $salary ? $salary->weekoff_count : 4);
            $hourlyWage = old('hourly_wage', $salary && $salary->hourly_wage > 0 ? $salary->hourly_wage : '');
            $notes = old('notes', $salary ? $salary->notes : '');

            $activeTab = $activeTab ?? session('active_tab', request('tab', 'salary'));

            // Pre-calculate leave aggregates
            $totalAnnualQuota = 0;
            $totalCarriedForward = 0;
            $totalUsed = 0;
            $totalRemaining = 0;
            if (isset($leavePolicies)) {
                foreach ($leavePolicies as $lp) {
                    $totalAnnualQuota += (float) ($lp->annual_quota ?? 0);
                    $totalCarriedForward += (float) ($lp->carried_forward ?? 0);
                    $totalUsed += (float) ($lp->used_count ?? 0);
                    $totalRemaining += (float) $lp->remaining_balance;
                }
            }
        @endphp

        <div class="perm-profile-banner op-banner-gradient mb-4">
            <div class="perm-profile-left">
                <div class="perm-profile-avatar" style="background: #0d9488; color: #ffffff; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);">
                    {{ $initials }}
                </div>
                <div class="perm-profile-meta">
                    <h4>{{ $staff->name }}</h4>
                    <p>
                        <span><i class="fas fa-envelope text-muted me-1"></i> {{ $staff->email }}</span>
                        <span>•</span>
                        <span><i class="fas fa-phone text-muted me-1"></i> {{ $staff->phone }}</span>
                        <span>•</span>
                        <span class="staff-role-badge {{ $roleClass }} py-0 px-2" style="font-size: 0.72rem;">
                            {{ $staff->role_type }}
                        </span>
                        @if($staff->restaurant)
                        <span>•</span>
                        <span class="staff-outlet-badge py-0 px-2" style="font-size: 0.72rem;">
                            <i class="fas fa-store text-primary"></i> {{ $staff->restaurant->name }}
                        </span>
                        @endif
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('restaurant.staff.index') }}" class="btn-staff-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Staff List
                </a>
            </div>
        </div>

        {{-- 2. Operations Nav Tabs --}}
        <div class="bg-white rounded-3 p-3 mb-4 border shadow-sm">
            <ul class="nav staff-op-tabs" id="staffOperationTabs">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'salary' ? 'active' : '' }}" 
                       href="{{ route('restaurant.staff.salary', $staff->id) }}" 
                       id="tab-salary-btn">
                        <i class="fas fa-money-check-alt me-2 text-teal"></i>Salary Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'leave_policy' ? 'active' : '' }}" 
                       href="{{ route('restaurant.staff.leave-policy', $staff->id) }}" 
                       id="tab-leave-btn">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>Leave Policy Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'documents' ? 'active' : '' }}" 
                       href="{{ route('restaurant.staff.documents', $staff->id) }}" 
                       id="tab-doc-btn">
                        <i class="fas fa-folder-open me-2 text-warning"></i>Document Management
                        <span class="badge {{ $documents->count() >= 5 ? 'bg-danger text-white' : 'bg-light text-dark border' }} ms-1" id="doc_counter_badge">{{ $documents->count() }}/5</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'calendar' ? 'active' : '' }}" 
                       href="{{ route('restaurant.staff.calendar', $staff->id) }}" 
                       id="tab-calendar-btn">
                        <i class="fas fa-calendar-alt me-2" style="color: #6366f1;"></i>Calendar
                    </a>
                </li>
            </ul>
        </div>

        {{-- 3. Operations Tab Content --}}
        <div class="staff-operation-content" id="staffOperationTabContent">
            
            @if($activeTab === 'salary')
            {{-- =========================================================================
                 TAB 1: SALARY MANAGEMENT (/admin/restaurant-staff/salary/{id})
                 ========================================================================= --}}
            <div id="tab-salary">
                <form action="{{ route('restaurant.staff.save-salary') }}" method="POST" id="salaryForm">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $staff->id }}">
                    <input type="hidden" name="employment_type" id="employment_type_input" value="{{ $empType }}">

                    {{-- Toolbar / Action Header --}}
                    <div class="staff-toolbar mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="staff-stat-icon" style="width: 38px; height: 38px; font-size: 1.1rem; background: #f0fdfa; color: #0d9488; border: 1px solid #99f6e4;">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; color: var(--staff-dark);">Staff Compensation & Salary Terms</h6>
                                <small class="text-muted">Define employment classification, base compensation, deductions & statutory contributions</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" name="action" value="save" class="btn btn-outline-secondary rounded-pill px-3" style="height: 38px;">
                                <i class="fas fa-save me-1"></i> Save
                            </button>
                            <button type="submit" name="action" value="save_and_close" class="btn rounded-pill px-4 text-white" style="background: #0d9488; border-color: #0d9488; height: 38px; font-weight: 600;">
                                <i class="fas fa-check-circle me-1"></i> Save & Return
                            </button>
                        </div>
                    </div>

                    {{-- 1. Employment Type Section --}}
                    <div class="op-section-card">
                        <div class="op-section-header">
                            <div>
                                <h5 class="op-section-title">
                                    <i class="fas fa-user-tag text-teal"></i> Employment Type
                                </h5>
                                <div class="op-section-subtitle">Select whether this staff member is engaged on a permanent monthly basis or an hourly wage rate</div>
                            </div>
                            <span class="badge bg-light text-dark border px-3 py-2" id="active_emp_badge">
                                {{ ucfirst($empType) }}
                            </span>
                        </div>

                        <div class="employment-type-container">
                            {{-- Permanent Card --}}
                            <div class="employment-card permanent {{ $empType === 'permanent' ? 'selected' : '' }}" id="card_permanent" onclick="selectEmploymentType('permanent')">
                                <div class="emp-icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="emp-info">
                                    <h6>Permanent / Full-Time</h6>
                                    <p>Fixed monthly salary, statutory deductions (PF, ESI, P-Tax, Other) & scheduled weekoffs.</p>
                                </div>
                                <div class="emp-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>

                            {{-- Hourly Card --}}
                            <div class="employment-card hourly {{ $empType === 'hourly' ? 'selected' : '' }}" id="card_hourly" onclick="selectEmploymentType('hourly')">
                                <div class="emp-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="emp-info">
                                    <h6>Hourly / Shift-Based</h6>
                                    <p>Hourly wage based compensation calculated per working shift or logged hours.</p>
                                </div>
                                <div class="emp-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Permanent Salary Structure Section --}}
                    <div id="permanent_section" style="{{ $empType === 'hourly' ? 'display: none;' : '' }}">
                        <div class="op-section-card">
                            <div class="op-section-header">
                                <div>
                                    <h5 class="op-section-title">
                                        <i class="fas fa-wallet text-teal"></i> Permanent Compensation & Allowances
                                    </h5>
                                    <div class="op-section-subtitle">Set monthly gross remuneration and monthly weekly-off quota</div>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-7">
                                    <label class="staff-form-label">Monthly Gross Salary (₹) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light fw-bold text-muted">₹</span>
                                        <input type="number" step="0.01" min="0" class="staff-form-control form-control" id="op_monthly_salary" name="monthly_salary" value="{{ $monthlySalary }}" placeholder="e.g. 25000.00">
                                    </div>
                                    <small class="text-muted">Gross monthly salary before taxes and deductions</small>
                                </div>
                                <div class="col-md-5">
                                    <label class="staff-form-label">Weekoff Count (Days / Month)</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-calendar-day"></i></span>
                                        <input type="number" step="1" min="0" max="15" class="staff-form-control form-control" id="op_weekoff_count" name="weekoff_count" value="{{ $weekoffCount }}" placeholder="4">
                                    </div>
                                    <small class="text-muted">Total allocated paid weekly-off days per month (default 4 days)</small>
                                </div>
                            </div>

                            {{-- Deductions Grid --}}
                            <div class="deductions-box mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">
                                        <i class="fas fa-percentage text-danger me-1"></i> Deductions & Contributions (%)
                                    </span>
                                    <span class="badge bg-white text-muted border small">Calculated dynamically against monthly gross</span>
                                </div>

                                <div class="row g-3">
                                    {{-- PF --}}
                                    <div class="col-md-3 col-sm-6">
                                        <div class="deduction-item-card">
                                            <label>
                                                <span>PF (Provident Fund)</span>
                                                <span class="deduct-val-preview" id="pf_amount_preview">- ₹0.00</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0" max="100" class="form-control deduction-input" id="op_pf_percentage" name="pf_percentage" value="{{ $pfPct }}" placeholder="0.00">
                                                <span class="input-group-text bg-light">%</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ESI --}}
                                    <div class="col-md-3 col-sm-6">
                                        <div class="deduction-item-card">
                                            <label>
                                                <span>ESI</span>
                                                <span class="deduct-val-preview" id="esi_amount_preview">- ₹0.00</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0" max="100" class="form-control deduction-input" id="op_esi_percentage" name="esi_percentage" value="{{ $esiPct }}" placeholder="0.00">
                                                <span class="input-group-text bg-light">%</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- P-Tax --}}
                                    <div class="col-md-3 col-sm-6">
                                        <div class="deduction-item-card">
                                            <label>
                                                <span>P-Tax (Professional Tax)</span>
                                                <span class="deduct-val-preview" id="ptax_amount_preview">- ₹0.00</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0" max="100" class="form-control deduction-input" id="op_ptax_percentage" name="ptax_percentage" value="{{ $ptaxPct }}" placeholder="0.00">
                                                <span class="input-group-text bg-light">%</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Other Deduction --}}
                                    <div class="col-md-3 col-sm-6">
                                        <div class="deduction-item-card">
                                            <label>
                                                <span>Other Deduction</span>
                                                <span class="deduct-val-preview" id="other_amount_preview">- ₹0.00</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0" max="100" class="form-control deduction-input" id="op_other_percentage" name="other_deduction_percentage" value="{{ $otherPct }}" placeholder="0.00">
                                                <span class="input-group-text bg-light">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Live Salary Calculation Card --}}
                            <div class="salary-breakdown-card">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="text-uppercase small fw-bold text-white-50">
                                        <i class="fas fa-calculator me-1"></i> Live Monthly Pay Computation
                                    </span>
                                    <span class="badge bg-dark border border-secondary text-light px-3 py-2" id="total_deduct_pct_pill">
                                        Total Deductions: 0.00%
                                    </span>
                                </div>
                                <div class="breakdown-row">
                                    <span>Gross Monthly Salary</span>
                                    <span class="fw-bold" id="breakdown_gross">₹0.00</span>
                                </div>
                                <div class="breakdown-row total-deduct">
                                    <span>Total Deductions (PF + ESI + P-Tax + Other)</span>
                                    <span class="fw-bold text-danger" id="breakdown_deductions">- ₹0.00</span>
                                </div>
                                <div class="breakdown-net">
                                    <span class="net-label">Estimated Net In-Hand Salary</span>
                                    <span class="net-amount" id="breakdown_net">₹0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Hourly Wage Section --}}
                    <div id="hourly_section" style="{{ $empType === 'permanent' ? 'display: none;' : '' }}">
                        <div class="op-section-card">
                            <div class="op-section-header">
                                <div>
                                    <h5 class="op-section-title">
                                        <i class="fas fa-stopwatch text-teal"></i> Hourly Compensation Rate
                                    </h5>
                                    <div class="op-section-subtitle">Define hourly rate and review standard workday and monthly projections</div>
                                </div>
                            </div>

                            <div class="row g-4 align-items-center">
                                <div class="col-md-6">
                                    <label class="staff-form-label">Hourly Wage Rate (₹ / Hour) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light fw-bold text-muted">₹</span>
                                        <input type="number" step="0.50" min="0" class="staff-form-control form-control" id="op_hourly_wage" name="hourly_wage" value="{{ $hourlyWage }}" placeholder="e.g. 150.00">
                                        <span class="input-group-text bg-light text-muted">/ hr</span>
                                    </div>
                                    <small class="text-muted">Hourly compensation paid per verified shift or clocked hour</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <span class="text-muted small fw-bold text-uppercase d-block mb-2">Standard Projections</span>
                                        <div class="d-flex justify-content-between text-dark mb-2">
                                            <span>Standard 8-hour shift:</span>
                                            <strong id="hourly_daily_est">₹0.00</strong>
                                        </div>
                                        <div class="d-flex justify-content-between text-dark">
                                            <span>26 working days (≈208 hrs):</span>
                                            <strong id="hourly_monthly_est" class="text-success fs-5">₹0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Internal HR & Payroll Notes --}}
                    <div class="op-section-card">
                        <div class="op-section-header">
                            <div>
                                <h5 class="op-section-title">
                                    <i class="fas fa-clipboard-list text-teal"></i> Internal Notes & Terms
                                </h5>
                                <div class="op-section-subtitle">Optional administrative details (e.g. bank details, effective date, probation period)</div>
                            </div>
                        </div>

                        <textarea class="staff-form-control form-control" id="op_notes" name="notes" rows="3" placeholder="e.g. Effective from 1st of month. Payment mode: Bank Transfer. Probation review after 3 months.">{{ $notes }}</textarea>
                    </div>

                    {{-- Form Bottom Actions --}}
                    <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border shadow-sm">
                        <a href="{{ route('restaurant.staff.index') }}" class="btn btn-light rounded-pill px-4">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="save" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                            <button type="submit" name="action" value="save_and_close" class="btn rounded-pill px-5 text-white" style="background: #0d9488; border-color: #0d9488; font-weight: 600;">
                                <i class="fas fa-check-circle me-1"></i> Save & Return to Staff List
                            </button>
                        </div>
                    </div>

                </form>
            </div>
            @endif

            @if($activeTab === 'leave_policy')
            {{-- =========================================================================
                 TAB 2: LEAVE POLICY MANAGEMENT (/admin/restaurant-staff/leave-policy/{id})
                 ========================================================================= --}}
            <div id="tab-leave">
                <form action="{{ route('restaurant.staff.save-leave-policy') }}" method="POST" id="leavePolicyForm">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $staff->id }}">

                    {{-- Toolbar / Action Header --}}
                    <div class="staff-toolbar mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="staff-stat-icon" style="width: 38px; height: 38px; font-size: 1.1rem; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; color: var(--staff-dark);">Staff Leave Policy & Annual Entitlements</h6>
                                <small class="text-muted">Configure staff-specific quotas, carry-forward rules, and track balances for <strong>{{ $staff->name }}</strong></small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted fw-bold mb-0">Policy Year:</label>
                                <select class="form-select form-select-sm" name="year" id="policyYearSelect" style="width: 105px; border-radius: 20px; font-weight: 600;" onchange="switchPolicyYear(this.value)">
                                    @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" name="action" value="save" class="btn btn-outline-secondary rounded-pill px-3" style="height: 38px;">
                                <i class="fas fa-save me-1"></i> Save
                            </button>
                            <button type="submit" name="action" value="save_and_close" class="btn rounded-pill px-4 text-white" style="background: #2563eb; border-color: #2563eb; height: 38px; font-weight: 600;">
                                <i class="fas fa-check-circle me-1"></i> Save & Return
                            </button>
                        </div>
                    </div>

                    {{-- 1. Metric Summary Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-metric-card">
                                <div class="stat-metric-icon" style="background: #eff6ff; color: #2563eb;">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div>
                                    <div class="stat-metric-label">Annual Quota</div>
                                    <div class="stat-metric-val" id="summary_total_quota">{{ number_format($totalAnnualQuota, 1) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="stat-metric-card">
                                <div class="stat-metric-icon" style="background: #ecfdf5; color: #059669;">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div>
                                    <div class="stat-metric-label">Carried Forward</div>
                                    <div class="stat-metric-val" id="summary_total_carried">+{{ number_format($totalCarriedForward, 1) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="stat-metric-card">
                                <div class="stat-metric-icon" style="background: #fef2f2; color: #dc2626;">
                                    <i class="fas fa-calendar-minus"></i>
                                </div>
                                <div>
                                    <div class="stat-metric-label">Used / Taken</div>
                                    <div class="stat-metric-val" id="summary_total_used">{{ number_format($totalUsed, 1) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="stat-metric-card" style="border-color: #99f6e4; background: #f0fdfa;">
                                <div class="stat-metric-icon" style="background: #0d9488; color: #ffffff;">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <div>
                                    <div class="stat-metric-label" style="color: #0d9488;">Net Available</div>
                                    <div class="stat-metric-val" style="color: #0d9488;" id="summary_total_remaining">{{ number_format($totalRemaining, 1) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Leave Policy Categories Grid --}}
                    <div class="row g-4 mb-4">
                        @foreach($leaveDefinitions as $typeKey => $def)
                        @php
                            $policy = $leavePolicies[$typeKey] ?? null;
                            $annualVal = old("policies.$typeKey.annual_quota", $policy ? (float) $policy->annual_quota : 0);
                            $carriedVal = old("policies.$typeKey.carried_forward", $policy ? (float) $policy->carried_forward : 0);
                            $usedVal = (float) ($policy ? $policy->used_count : 0);
                            $isCarry = old("policies.$typeKey.is_carry_forward", $policy ? $policy->is_carry_forward : false);
                            $pNotes = old("policies.$typeKey.notes", $policy ? $policy->notes : '');

                            $totalEnt = (float) $annualVal + (float) $carriedVal;
                            $remBal = max(0, $totalEnt - $usedVal);
                        @endphp
                        <div class="col-lg-6">
                            <div class="leave-category-card" style="border-top: 4px solid {{ $def['color'] }};">
                                <div>
                                    {{-- Card Header --}}
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="leave-icon-box" style="background: {{ $def['bg_color'] }}; color: {{ $def['color'] }}; border: 1px solid {{ $def['border_color'] }};">
                                                <i class="{{ $def['icon'] }}"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 fw-bold" style="color: #0f172a; font-family: 'Outfit', sans-serif;">
                                                    {{ $def['name'] }}
                                                    <span class="badge ms-1" style="background: {{ $def['bg_color'] }}; color: {{ $def['color'] }}; border: 1px solid {{ $def['border_color'] }}; font-size: 0.72rem;">
                                                        {{ $def['code'] }}
                                                    </span>
                                                </h5>
                                                <small class="text-muted">{{ $def['description'] }}</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-secondary border small">
                                            Unit: {{ $def['unit'] }}
                                        </span>
                                    </div>

                                    {{-- Numeric Quota Inputs --}}
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <label class="staff-form-label">
                                                Annual Quota ({{ $def['unit'] }}) <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.5" min="0" max="365" 
                                                       class="form-control leave-calc-input leave-annual-input" 
                                                       id="quota_{{ $typeKey }}" 
                                                       name="policies[{{ $typeKey }}][annual_quota]" 
                                                       value="{{ $annualVal }}" 
                                                       data-type="{{ $typeKey }}"
                                                       placeholder="0" required>
                                                <span class="input-group-text bg-light text-muted small">{{ $def['unit'] }}</span>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.73rem;">Allocated for year {{ $year }}</small>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="staff-form-label">
                                                Carried Forward ({{ $def['unit'] }})
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.5" min="0" max="365" 
                                                       class="form-control leave-calc-input leave-carried-input" 
                                                       id="carried_{{ $typeKey }}" 
                                                       name="policies[{{ $typeKey }}][carried_forward]" 
                                                       value="{{ $carriedVal }}" 
                                                       data-type="{{ $typeKey }}"
                                                       placeholder="0.0">
                                                <span class="input-group-text bg-light text-muted small">{{ $def['unit'] }}</span>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.73rem;">Rollover from prior year</small>
                                        </div>
                                    </div>

                                    {{-- Balance Indicator Bar --}}
                                    <div class="p-3 bg-light rounded-3 border mb-3">
                                        <div class="d-flex align-items-center justify-content-between small text-dark mb-1">
                                            <span>Total Entitled (Quota + Carried):</span>
                                            <strong id="entitled_val_{{ $typeKey }}">{{ number_format($totalEnt, 1) }} {{ $def['unit'] }}</strong>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                            <span>Consumed in {{ $year }}:</span>
                                            <span class="text-danger fw-bold" id="used_val_{{ $typeKey }}">- {{ number_format($usedVal, 1) }} {{ $def['unit'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between small pt-2 border-top">
                                            <span class="fw-bold text-dark">Available Balance:</span>
                                            <span class="fw-bold fs-6" style="color: {{ $def['color'] }};" id="remaining_val_{{ $typeKey }}">
                                                {{ number_format($remBal, 1) }} {{ $def['unit'] }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Carry Forward Switcher (The User's Feature!) --}}
                                    <div class="p-3 rounded-3 border" style="background: {{ $isCarry ? '#f0fdf4' : '#fafafa' }}; border-color: {{ $isCarry ? '#bbf7d0' : '#e2e8f0' }};" id="carry_box_{{ $typeKey }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="form-check form-switch p-0 m-0 d-flex align-items-center gap-2">
                                                    <input class="form-check-input leave-carry-switch" 
                                                           type="checkbox" 
                                                           role="switch" 
                                                           id="carry_switch_{{ $typeKey }}" 
                                                           name="policies[{{ $typeKey }}][is_carry_forward]" 
                                                           value="1" 
                                                           data-type="{{ $typeKey }}"
                                                           {{ $isCarry ? 'checked' : '' }} 
                                                           style="width: 2.2em; height: 1.15em; cursor: pointer;">
                                                    <label class="form-check-label fw-bold text-dark small" for="carry_switch_{{ $typeKey }}" style="cursor: pointer;">
                                                        Carry Forward to Next Year
                                                    </label>
                                                </div>
                                                <small class="text-muted d-block mt-1" id="carry_status_desc_{{ $typeKey }}" style="font-size: 0.74rem;">
                                                    @if($isCarry)
                                                        <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Enabled:</span> Unused {{ strtolower($def['name']) }} balance at year-end will automatically move to next year.
                                                    @else
                                                        <span class="text-muted"><i class="fas fa-ban me-1"></i> Disabled:</span> Any remaining balance expires at year-end and will not rollover.
                                                    @endif
                                                </small>
                                            </div>
                                            <span class="badge {{ $isCarry ? 'bg-success' : 'bg-secondary' }}" id="carry_badge_{{ $typeKey }}">
                                                {{ $isCarry ? 'Carry Forward: ON' : 'Expires: Year-End' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Notes Input --}}
                                <div class="mt-3">
                                    <input type="text" class="form-control form-control-sm" 
                                           name="policies[{{ $typeKey }}][notes]" 
                                           value="{{ $pNotes }}" 
                                           placeholder="Specific notes/terms for {{ strtolower($def['name']) }} (optional)...">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Form Bottom Actions --}}
                    <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border shadow-sm">
                        <a href="{{ route('restaurant.staff.index') }}" class="btn btn-light rounded-pill px-4">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="save" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                            <button type="submit" name="action" value="save_and_close" class="btn rounded-pill px-5 text-white" style="background: #2563eb; border-color: #2563eb; font-weight: 600;">
                                <i class="fas fa-check-circle me-1"></i> Save & Return to Staff List
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif

            @if($activeTab === 'documents')
            {{-- =========================================================================
                 TAB 3: DOCUMENT MANAGEMENT (/admin/restaurant-staff/documents/{id})
                 ========================================================================= --}}
            <div id="tab-doc">
                
                {{-- Toolbar / Info Banner --}}
                <div class="staff-toolbar mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="staff-stat-icon" style="width: 38px; height: 38px; font-size: 1.1rem; background: #fffbeb; color: #d97706; border: 1px solid #fde68a;">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; color: var(--staff-dark);">Staff Document Repository & KYC Vault</h6>
                            <small class="text-muted">Upload and manage employee identity proofs, verification certificates & agreements (Max 5 documents, 5 MB limit per file)</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $documents->count() >= 5 ? 'bg-danger' : 'bg-primary' }} px-3 py-2" style="font-size: 0.82rem; font-weight: 700;">
                            <i class="fas fa-file-alt me-1"></i> {{ $documents->count() }} of 5 Files Used
                        </span>
                    </div>
                </div>

                {{-- Status & Quota Progress --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #eff6ff; color: #2563eb;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">Document Slots</div>
                                <div class="stat-metric-val">{{ $documents->count() }} <span class="text-muted" style="font-size: 1rem; font-weight: 500;">/ 5 max</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #f0fdf4; color: #16a34a;">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">Remaining Slots</div>
                                <div class="stat-metric-val">{{ max(0, 5 - $documents->count()) }} <span class="text-muted" style="font-size: 1rem; font-weight: 500;">available</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #fff7ed; color: #ea580c;">
                                <i class="fas fa-weight-hanging"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">Max File Size</div>
                                <div class="stat-metric-val">5.0 <span class="text-muted" style="font-size: 1rem; font-weight: 500;">MB / file</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Document Upload Card --}}
                @if($documents->count() >= 5)
                    <div class="alert alert-warning border-warning d-flex align-items-center gap-3 p-3 mb-4 rounded-3 shadow-sm" role="alert">
                        <div style="font-size: 1.5rem; color: #b45309;"><i class="fas fa-exclamation-triangle"></i></div>
                        <div>
                            <div class="fw-bold" style="color: #92400e; font-size: 0.95rem;">Maximum Document Limit Reached (5/5)</div>
                            <div class="text-muted" style="font-size: 0.85rem;">This staff member already has 5 documents stored. To upload a different document, please delete an existing document first.</div>
                        </div>
                    </div>
                @else
                    <div class="op-section-card">
                        <div class="op-section-header">
                            <div>
                                <h5 class="op-section-title">
                                    <i class="fas fa-upload text-warning"></i> Upload New Document
                                </h5>
                                <div class="op-section-subtitle">Select a file and provide a title and detailed description for records</div>
                            </div>
                            <span class="badge bg-light text-primary border px-3 py-2">
                                <i class="fas fa-info-circle me-1"></i> Limit: Up to 5MB
                            </span>
                        </div>

                        <form action="{{ route('restaurant.staff.upload-document') }}" method="POST" enctype="multipart/form-data" id="documentUploadForm">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $staff->id }}">

                            <div class="row g-3">
                                {{-- Title --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">
                                        Document Title / Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="title" id="doc_title_input" class="form-control" placeholder="e.g. Aadhaar Card, PAN Card, Employment Agreement, Resume" maxlength="150" required value="{{ old('title') }}">
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <small class="text-muted me-1">Quick pick:</small>
                                        <button type="button" class="badge bg-light text-secondary border doc-quick-tag" onclick="document.getElementById('doc_title_input').value='Aadhaar Card'">Aadhaar Card</button>
                                        <button type="button" class="badge bg-light text-secondary border doc-quick-tag" onclick="document.getElementById('doc_title_input').value='PAN Card'">PAN Card</button>
                                        <button type="button" class="badge bg-light text-secondary border doc-quick-tag" onclick="document.getElementById('doc_title_input').value='Employment Agreement'">Employment Agreement</button>
                                        <button type="button" class="badge bg-light text-secondary border doc-quick-tag" onclick="document.getElementById('doc_title_input').value='Resume / CV'">Resume / CV</button>
                                        <button type="button" class="badge bg-light text-secondary border doc-quick-tag" onclick="document.getElementById('doc_title_input').value='Bank Passbook'">Bank Passbook</button>
                                    </div>
                                </div>

                                {{-- File Picker --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">
                                        Choose File (Max 5 MB) <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="document_file" id="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" required>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted" style="font-size: 0.76rem;">
                                            Allowed: PDF, JPG, PNG, WEBP, DOC, DOCX
                                        </small>
                                        <small class="text-muted fw-bold" style="font-size: 0.76rem;">
                                            Strictly ≤ 5 MB
                                        </small>
                                    </div>
                                    <div id="file_size_error" class="alert alert-danger py-1 px-2 mt-2 small" style="display: none;"></div>
                                </div>

                                {{-- Description --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small mb-1">
                                        Document Description / Notes
                                    </label>
                                    <textarea name="description" id="doc_description_input" class="form-control" rows="3" placeholder="Enter notes or description regarding this document (e.g. Scanned original verified on joining, valid till 2028, remarks, etc.)" maxlength="1000">{{ old('description') }}</textarea>
                                    <div class="text-end">
                                        <small class="text-muted" style="font-size: 0.74rem;">Max 1000 characters</small>
                                    </div>
                                </div>

                                {{-- Upload Submit Button --}}
                                <div class="col-12 text-end">
                                    <button type="submit" id="btnUploadDoc" class="btn rounded-pill px-4 text-white" style="background: #0d9488; border-color: #0d9488; font-weight: 600; height: 42px;">
                                        <i class="fas fa-cloud-upload-alt me-1"></i> Upload Document ({{ $documents->count() }}/5)
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Document List / Records --}}
                <div class="op-section-card">
                    <div class="op-section-header">
                        <div>
                            <h5 class="op-section-title">
                                <i class="fas fa-list text-primary"></i> Uploaded Documents ({{ $documents->count() }})
                            </h5>
                            <div class="op-section-subtitle">Listing of all verified documents attached to {{ $staff->name }}</div>
                        </div>
                    </div>

                    @if($documents->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3" style="width: 64px; height: 64px; margin: 0 auto; border-radius: 50%; background: #f8fafc; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94a3b8;">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No Documents Uploaded Yet</h6>
                            <p class="text-muted small mb-0">Use the upload form above to attach Aadhaar, PAN, Resume or contracts for this staff member (up to 5 documents, max 5 MB each).</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th style="min-width: 220px;">Document Details</th>
                                        <th style="min-width: 260px;">Description</th>
                                        <th style="min-width: 140px;">File Info</th>
                                        <th style="min-width: 130px;">Uploaded On</th>
                                        <th style="width: 140px;" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $idx => $doc)
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                                                        <i class="{{ $doc->icon_class }}"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $doc->title }}</div>
                                                        <div class="text-muted small" style="font-size: 0.78rem;">
                                                            <i class="fas fa-paperclip me-1"></i> {{ $doc->file_name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(!empty($doc->description))
                                                    <span class="text-dark small" style="line-height: 1.4; display: block;">{{ $doc->description }}</span>
                                                @else
                                                    <span class="text-muted fst-italic small">No description provided</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge bg-light text-dark border align-self-start" style="font-size: 0.74rem;">
                                                        {{ strtoupper($doc->file_type) }}
                                                    </span>
                                                    <span class="text-muted small" style="font-size: 0.78rem;">
                                                        {{ $doc->formatted_file_size }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-dark fw-medium">{{ $doc->created_at->format('d M, Y') }}</div>
                                                <div class="text-muted" style="font-size: 0.74rem;">{{ $doc->created_at->format('h:i A') }}</div>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-outline-secondary" title="View / Preview File in new tab">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('restaurant.staff.download-document', $doc->id) }}" class="btn btn-outline-primary" title="Download Document">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('restaurant.staff.delete-document', $doc->id) }}" class="btn btn-outline-danger" title="Delete Document" onclick="return confirm('Are you sure you want to delete \'{{ addslashes($doc->title) }}\'? This cannot be undone.');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
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
            @endif

            @if($activeTab === 'calendar')
            {{-- =========================================================================
                 TAB 4: CALENDAR & MONTHLY ACTIVITY (/admin/restaurant-staff/calendar/{id})
                 ========================================================================= --}}
            <div id="tab-calendar">
                {{-- 1. Month / Year & View Switcher Toolbar --}}
                <div class="staff-toolbar mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="staff-stat-icon" style="width: 38px; height: 38px; font-size: 1.1rem; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; color: var(--staff-dark);">
                                {{ $monthName }} {{ $year }} — Activity &amp; Attendance Calendar
                            </h6>
                            <small class="text-muted">Daily breakdown of present shifts, half days, early leaves, weekoffs, leaves &amp; absences</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        {{-- Month & Year Selector Form --}}
                        <form action="{{ route('restaurant.staff.calendar', $staff->id) }}" method="GET" class="d-flex align-items-center gap-1" id="calendarMonthForm">
                            <input type="hidden" name="view" value="{{ $activeView }}">
                            
                            {{-- Prev Month Arrow --}}
                            <a href="{{ route('restaurant.staff.calendar', ['id' => $staff->id, 'month' => $prevMonth->month, 'year' => $prevMonth->year, 'view' => $activeView]) }}" 
                               class="btn btn-outline-secondary btn-sm" title="Previous Month: {{ $prevMonth->format('F Y') }}">
                                <i class="fas fa-chevron-left"></i>
                            </a>

                            {{-- Month Dropdown --}}
                            <select name="month" id="calendar_month" class="form-select form-select-sm fw-bold" style="width: 130px;" onchange="document.getElementById('calendarMonthForm').submit()">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>

                            {{-- Year Dropdown --}}
                            <select name="year" id="calendar_year" class="form-select form-select-sm fw-bold" style="width: 95px;" onchange="document.getElementById('calendarMonthForm').submit()">
                                @for($y = (int) date('Y') - 2; $y <= (int) date('Y') + 2; $y++)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>

                            {{-- Next Month Arrow --}}
                            <a href="{{ route('restaurant.staff.calendar', ['id' => $staff->id, 'month' => $nextMonth->month, 'year' => $nextMonth->year, 'view' => $activeView]) }}" 
                               class="btn btn-outline-secondary btn-sm" title="Next Month: {{ $nextMonth->format('F Y') }}">
                                <i class="fas fa-chevron-right"></i>
                            </a>

                            {{-- Jump to Current Month --}}
                            @if($month != (int) date('m') || $year != (int) date('Y'))
                            <a href="{{ route('restaurant.staff.calendar', ['id' => $staff->id, 'month' => (int) date('m'), 'year' => (int) date('Y'), 'view' => $activeView]) }}" class="btn btn-sm btn-outline-primary" title="Jump to Current Month">
                                Current Month
                            </a>
                            @endif
                        </form>

                        {{-- Sub-tab View Switcher (Calendar vs List) --}}
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('restaurant.staff.calendar', ['id' => $staff->id, 'month' => $month, 'year' => $year, 'view' => 'calendar']) }}" 
                               class="btn {{ $activeView === 'calendar' ? 'btn-primary text-white' : 'btn-outline-secondary' }}" 
                               style="{{ $activeView === 'calendar' ? 'background: #4f46e5; border-color: #4f46e5;' : '' }}"
                               title="Monthly Calendar Grid View">
                                <i class="fas fa-calendar-alt me-1"></i> Calendar
                            </a>
                            <a href="{{ route('restaurant.staff.calendar', ['id' => $staff->id, 'month' => $month, 'year' => $year, 'view' => 'list']) }}" 
                               class="btn {{ $activeView === 'list' ? 'btn-primary text-white' : 'btn-outline-secondary' }}" 
                               style="{{ $activeView === 'list' ? 'background: #4f46e5; border-color: #4f46e5;' : '' }}"
                               title="Monthly Detailed Activity List">
                                <i class="fas fa-list me-1"></i> List
                            </a>
                        </div>

                        {{-- Download Excel --}}
                        <a href="{{ route('restaurant.staff.calendar.export', ['id' => $staff->id, 'month' => $month, 'year' => $year]) }}" 
                           class="btn btn-outline-success btn-sm rounded-pill px-3" style="font-weight: 600;">
                            <i class="fas fa-file-excel me-1"></i> Download Excel
                        </a>
                    </div>
                </div>

                {{-- 2. Monthly Summary KPI Metrics --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3 col-xl">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #f1f5f9; color: #475569;">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">Month Days</div>
                                <div class="stat-metric-val">{{ $stats['total_days'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #dcfce7; color: #16a34a;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">Full Day (P)</div>
                                <div class="stat-metric-val text-success">{{ $stats['present'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #fef9c3; color: #ca8a04;">
                                <i class="fas fa-adjust"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">Half Day (H)</div>
                                <div class="stat-metric-val text-warning">{{ $stats['half_day'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #e0f2fe; color: #0284c7;">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">Early Leave</div>
                                <div class="stat-metric-val text-info">{{ $stats['early_leave'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #ede9fe; color: #7c3aed;">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">Weekoff (WO)</div>
                                <div class="stat-metric-val" style="color: #7c3aed;">{{ $stats['weekoff'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #e0e7ff; color: #4338ca;">
                                <i class="fas fa-umbrella-beach"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">On Leave</div>
                                <div class="stat-metric-val" style="color: #4338ca;">{{ $stats['leave'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl">
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon" style="background: #fee2e2; color: #dc2626;">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div>
                                <div class="stat-metric-label">Absent / Unmarked</div>
                                <div class="stat-metric-val text-danger">{{ $stats['absent'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($activeView === 'calendar')
                {{-- 3A. CALENDAR VIEW (MONTHLY GRID) --}}
                <div class="bg-white rounded-3 p-4 border shadow-sm mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                        <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Outfit', sans-serif;">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>{{ $monthName }} {{ $year }} — Calendar Grid
                        </h6>
                        <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.76rem;">
                            <span class="badge badge-attendance-present py-1 px-2">P: Present</span>
                            <span class="badge badge-attendance-halfday py-1 px-2">H: Half Day</span>
                            <span class="badge badge-attendance-earlyleave py-1 px-2">EL: Early Leave</span>
                            <span class="badge badge-attendance-weekoff py-1 px-2">WO: Weekoff</span>
                            <span class="badge badge-calendar-leave py-1 px-2">Leave</span>
                            <span class="badge badge-calendar-absent py-1 px-2">Absent</span>
                        </div>
                    </div>

                    {{-- Day of Week Headers --}}
                    <div class="cal-grid mb-2">
                        <div class="cal-header-cell weekend">Sun</div>
                        <div class="cal-header-cell">Mon</div>
                        <div class="cal-header-cell">Tue</div>
                        <div class="cal-header-cell">Wed</div>
                        <div class="cal-header-cell">Thu</div>
                        <div class="cal-header-cell">Fri</div>
                        <div class="cal-header-cell weekend">Sat</div>
                    </div>

                    {{-- Calendar Day Cells --}}
                    <div class="cal-grid">
                        {{-- Leading blank cells --}}
                        @for($i = 0; $i < $firstDayOfWeek; $i++)
                            <div class="cal-empty-cell"></div>
                        @endfor

                        {{-- Day boxes --}}
                        @foreach($calendarData as $dateKey => $dayData)
                            @php
                                $isWeekend = in_array($dayData['day_of_week'], ['Sun', 'Sat']);
                            @endphp
                            <div class="cal-day-cell {{ $dayData['is_today'] ? 'is-today' : '' }} {{ $isWeekend ? 'is-weekend' : '' }}">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted small fw-bold" style="font-size: 0.72rem;">{{ $dayData['day_of_week'] }}</span>
                                    <div class="d-flex align-items-center gap-1">
                                        @if($dayData['is_today'])
                                            <span class="badge bg-teal text-white py-0 px-1" style="font-size: 0.65rem;">TODAY</span>
                                        @endif
                                        <span class="fw-bolder {{ $isWeekend ? 'text-danger' : 'text-dark' }}" style="font-size: 0.95rem; font-family: 'Outfit', sans-serif;">
                                            {{ $dayData['day'] }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Status Pill --}}
                                <div class="my-auto py-1">
                                    <span class="badge {{ $dayData['badge_class'] }} w-100 text-truncate py-1 px-2" style="font-size: 0.72rem;" title="{{ $dayData['status_label'] }}">
                                        @if($dayData['status'] === 'present')
                                            <i class="fas fa-check-circle me-1"></i> Present (P)
                                        @elseif($dayData['status'] === 'half_day')
                                            <i class="fas fa-adjust me-1"></i> Half Day (H)
                                        @elseif($dayData['status'] === 'early_leave')
                                            <i class="fas fa-sign-out-alt me-1"></i> Early Leave
                                        @elseif($dayData['status'] === 'weekoff')
                                            <i class="fas fa-calendar-check me-1"></i> Weekoff (WO)
                                        @elseif($dayData['status'] === 'leave')
                                            <i class="fas fa-umbrella-beach me-1"></i> {{ $dayData['leave']->leave_type_name ?? 'Leave' }}
                                        @elseif($dayData['status'] === 'absent')
                                            <i class="fas fa-times-circle me-1"></i> Absent
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </span>
                                </div>

                                {{-- Sub-info: Timings or notes --}}
                                <div class="small text-muted text-truncate" style="font-size: 0.7rem;">
                                    @if($dayData['attendance'] && !empty($dayData['attendance']->login_time))
                                        <span class="text-dark"><i class="fas fa-clock text-teal me-1"></i>{{ $dayData['attendance']->formatted_login_time }}</span>
                                        @if(!empty($dayData['attendance']->logout_time))
                                            <span> - {{ $dayData['attendance']->formatted_logout_time }}</span>
                                        @endif
                                    @elseif($dayData['leave'])
                                        <span class="text-primary text-truncate d-block" title="{{ $dayData['leave']->reason }}">{{ $dayData['leave']->reason ?: 'Approved Leave' }}</span>
                                    @elseif($dayData['status'] === 'absent')
                                        <span class="text-danger fst-italic">Unmarked</span>
                                    @else
                                        <span>&nbsp;</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @else
                {{-- 3B. SUB-TAB: LIST VIEW (DATE-WISE TABLE) --}}
                <div class="bg-white rounded-3 p-4 border shadow-sm mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Outfit', sans-serif;">
                                <i class="fas fa-list text-primary me-2"></i>{{ $monthName }} {{ $year }} — Day-by-Day Activity List
                            </h6>
                            <small class="text-muted">Chronological view of all {{ $daysInMonth }} days in {{ $monthName }}</small>
                        </div>
                        <a href="{{ route('restaurant.staff.calendar.export', ['id' => $staff->id, 'month' => $month, 'year' => $year]) }}" 
                           class="btn btn-outline-success btn-sm rounded-pill px-3">
                            <i class="fas fa-file-excel me-1"></i> Download Excel
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45px;" class="text-center">#</th>
                                    <th style="min-width: 160px;">Date &amp; Day</th>
                                    <th style="min-width: 170px;">Classification / Status</th>
                                    <th style="min-width: 110px;">Login Time</th>
                                    <th style="min-width: 110px;">Logout Time</th>
                                    <th style="min-width: 100px;">Duration</th>
                                    <th style="min-width: 220px;">Notes / Leave Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calendarData as $dateKey => $dayData)
                                    @php
                                        $isWeekend = in_array($dayData['day_of_week'], ['Sun', 'Sat']);
                                        $att = $dayData['attendance'];
                                        $leave = $dayData['leave'];
                                    @endphp
                                    <tr style="{{ $dayData['is_today'] ? 'background-color: #f0fdfa;' : ($isWeekend ? 'background-color: #fafafc;' : '') }}">
                                        <td class="text-center fw-bold text-muted">{{ $dayData['day'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                                                    {{ date('d M Y', strtotime($dayData['date'])) }}
                                                </div>
                                                <span class="badge {{ $isWeekend ? 'bg-danger-subtle text-danger' : 'bg-light text-muted border' }}" style="font-size: 0.72rem;">
                                                    {{ $dayData['day_of_week_full'] }}
                                                </span>
                                                @if($dayData['is_today'])
                                                    <span class="badge bg-teal text-white py-0 px-1" style="font-size: 0.65rem;">TODAY</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $dayData['badge_class'] }} px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                                                @if($dayData['status'] === 'present')
                                                    <i class="fas fa-check-circle me-1"></i> Full Day Present (P)
                                                @elseif($dayData['status'] === 'half_day')
                                                    <i class="fas fa-adjust me-1"></i> Half Day (H)
                                                @elseif($dayData['status'] === 'early_leave')
                                                    <i class="fas fa-sign-out-alt me-1"></i> Early Leave (EL)
                                                @elseif($dayData['status'] === 'weekoff')
                                                    <i class="fas fa-calendar-check me-1"></i> Weekoff (WO)
                                                @elseif($dayData['status'] === 'leave')
                                                    <i class="fas fa-umbrella-beach me-1"></i> On Leave ({{ $leave->leave_type_name ?? 'Leave' }})
                                                @elseif($dayData['status'] === 'absent')
                                                    <i class="fas fa-times-circle me-1"></i> Absent / Unmarked
                                                @else
                                                    <span class="text-muted">Upcoming</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if($att && !empty($att->login_time))
                                                <span class="fw-semibold text-dark small"><i class="fas fa-clock text-teal me-1"></i>{{ $att->formatted_login_time }}</span>
                                            @else
                                                <span class="text-muted small">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($att && !empty($att->logout_time))
                                                <span class="fw-semibold text-dark small"><i class="fas fa-clock text-danger me-1"></i>{{ $att->formatted_logout_time }}</span>
                                            @elseif($att)
                                                <span class="badge bg-light text-muted border px-1" style="font-size: 0.72rem;">Open</span>
                                            @else
                                                <span class="text-muted small">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($att && $att->work_duration)
                                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                                                    <i class="fas fa-stopwatch text-teal me-1"></i>{{ $att->work_duration }}
                                                </span>
                                            @else
                                                <span class="text-muted small">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($att && !empty($att->notes))
                                                <span class="small text-dark">{{ $att->notes }}</span>
                                            @elseif($leave)
                                                <div class="small">
                                                    <span class="text-primary fw-semibold">{{ $leave->leave_type_name }}</span>
                                                    @if(!empty($leave->reason))
                                                        <span class="text-muted"> — {{ $leave->reason }}</span>
                                                    @endif
                                                </div>
                                            @elseif($dayData['status'] === 'absent')
                                                <span class="text-danger small fst-italic">No attendance or leave recorded</span>
                                            @else
                                                <span class="text-muted small fst-italic">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>
            @endif

        </div>

    </div>

</div>
</div>

@endsection

@section('script')
@include('includes.script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Year Switcher (Navigates directly to leave policy route with ?year=)
        window.switchPolicyYear = function(year) {
            window.location.href = "{{ route('restaurant.staff.leave-policy', $staff->id) }}?year=" + year;
        };

        // Carry Forward Checkbox toggle visual feedback
        const carrySwitches = document.querySelectorAll('.leave-carry-switch');
        carrySwitches.forEach(sw => {
            sw.addEventListener('change', function() {
                const type = this.dataset.type;
                const isChecked = this.checked;
                const box = document.getElementById('carry_box_' + type);
                const badge = document.getElementById('carry_badge_' + type);
                const desc = document.getElementById('carry_status_desc_' + type);

                if (isChecked) {
                    if (box) {
                        box.style.background = '#f0fdf4';
                        box.style.borderColor = '#bbf7d0';
                    }
                    if (badge) {
                        badge.className = 'badge bg-success';
                        badge.textContent = 'Carry Forward: ON';
                    }
                    if (desc) {
                        desc.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Enabled:</span> Unused balance at year-end will automatically move to next year.';
                    }
                } else {
                    if (box) {
                        box.style.background = '#fafafa';
                        box.style.borderColor = '#e2e8f0';
                    }
                    if (badge) {
                        badge.className = 'badge bg-secondary';
                        badge.textContent = 'Expires: Year-End';
                    }
                    if (desc) {
                        desc.innerHTML = '<span class="text-muted"><i class="fas fa-ban me-1"></i> Disabled:</span> Any remaining balance expires at year-end and will not rollover.';
                    }
                }
            });
        });

        // Real-time recalculation of totals and balances
        const calcInputs = document.querySelectorAll('.leave-calc-input');
        function recalculateLeaveTotals() {
            let totalQuota = 0;
            let totalCarried = 0;
            let totalUsed = 0;
            let totalRemaining = 0;

            const leaveTypes = ['sick_leave', 'paid_leave', 'early_leave', 'half_day'];
            leaveTypes.forEach(type => {
                const qInput = document.getElementById('quota_' + type);
                const cInput = document.getElementById('carried_' + type);
                const uEl = document.getElementById('used_val_' + type);

                const quota = parseFloat(qInput?.value) || 0;
                const carried = parseFloat(cInput?.value) || 0;
                let used = 0;
                if (uEl) {
                    const match = uEl.textContent.match(/[\d.]+/);
                    if (match) used = parseFloat(match[0]) || 0;
                }

                const entitled = quota + carried;
                const remaining = Math.max(0, entitled - used);

                totalQuota += quota;
                totalCarried += carried;
                totalUsed += used;
                totalRemaining += remaining;

                // Update category card values
                const entEl = document.getElementById('entitled_val_' + type);
                const remEl = document.getElementById('remaining_val_' + type);
                if (entEl) entEl.textContent = entitled.toFixed(1);
                if (remEl) remEl.textContent = remaining.toFixed(1);
            });

            // Update top metric summary cards
            const tq = document.getElementById('summary_total_quota');
            const tc = document.getElementById('summary_total_carried');
            const tu = document.getElementById('summary_total_used');
            const tr = document.getElementById('summary_total_remaining');

            if (tq) tq.textContent = totalQuota.toFixed(1);
            if (tc) tc.textContent = '+' + totalCarried.toFixed(1);
            if (tu) tu.textContent = totalUsed.toFixed(1);
            if (tr) tr.textContent = totalRemaining.toFixed(1);
        }

        calcInputs.forEach(input => {
            input.addEventListener('input', recalculateLeaveTotals);
            input.addEventListener('change', recalculateLeaveTotals);
            input.addEventListener('keyup', recalculateLeaveTotals);
        });

        // Salary Tab calculation logic
        const empTypeInput = document.getElementById('employment_type_input');
        const cardPermanent = document.getElementById('card_permanent');
        const cardHourly = document.getElementById('card_hourly');
        const permanentSection = document.getElementById('permanent_section');
        const hourlySection = document.getElementById('hourly_section');
        const activeEmpBadge = document.getElementById('active_emp_badge');

        function formatINR(val) {
            return '₹' + Number(val || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        window.selectEmploymentType = function(type) {
            if (!empTypeInput) return;
            empTypeInput.value = type;
            if (activeEmpBadge) {
                activeEmpBadge.textContent = type.charAt(0).toUpperCase() + type.slice(1);
            }

            if (type === 'hourly') {
                if (cardHourly) cardHourly.classList.add('selected');
                if (cardPermanent) cardPermanent.classList.remove('selected');
                if (permanentSection) permanentSection.style.display = 'none';
                if (hourlySection) hourlySection.style.display = 'block';
            } else {
                if (cardPermanent) cardPermanent.classList.add('selected');
                if (cardHourly) cardHourly.classList.remove('selected');
                if (permanentSection) permanentSection.style.display = 'block';
                if (hourlySection) hourlySection.style.display = 'none';
            }
            recalculateSalary();
        };

        function recalculateSalary() {
            const empType = empTypeInput ? empTypeInput.value : 'permanent';

            if (empType === 'permanent') {
                const gross = parseFloat(document.getElementById('op_monthly_salary')?.value) || 0;
                const pfPct = parseFloat(document.getElementById('op_pf_percentage')?.value) || 0;
                const esiPct = parseFloat(document.getElementById('op_esi_percentage')?.value) || 0;
                const ptaxPct = parseFloat(document.getElementById('op_ptax_percentage')?.value) || 0;
                const otherPct = parseFloat(document.getElementById('op_other_percentage')?.value) || 0;

                const pfAmt = (gross * pfPct) / 100;
                const esiAmt = (gross * esiPct) / 100;
                const ptaxAmt = (gross * ptaxPct) / 100;
                const otherAmt = (gross * otherPct) / 100;

                const totalDeduct = pfAmt + esiAmt + ptaxAmt + otherAmt;
                const totalPct = pfPct + esiPct + ptaxPct + otherPct;
                const net = Math.max(0, gross - totalDeduct);

                const pfEl = document.getElementById('pf_amount_preview');
                const esiEl = document.getElementById('esi_amount_preview');
                const ptaxEl = document.getElementById('ptax_amount_preview');
                const otherEl = document.getElementById('other_amount_preview');

                if (pfEl) pfEl.textContent = '- ' + formatINR(pfAmt);
                if (esiEl) esiEl.textContent = '- ' + formatINR(esiAmt);
                if (ptaxEl) ptaxEl.textContent = '- ' + formatINR(ptaxAmt);
                if (otherEl) otherEl.textContent = '- ' + formatINR(otherAmt);

                const bgGross = document.getElementById('breakdown_gross');
                const bgDeduct = document.getElementById('breakdown_deductions');
                const bgNet = document.getElementById('breakdown_net');
                const pctPill = document.getElementById('total_deduct_pct_pill');

                if (bgGross) bgGross.textContent = formatINR(gross);
                if (bgDeduct) bgDeduct.textContent = '- ' + formatINR(totalDeduct);
                if (bgNet) bgNet.textContent = formatINR(net);
                if (pctPill) pctPill.textContent = 'Total Deductions: ' + totalPct.toFixed(2) + '%';
            } else {
                const wage = parseFloat(document.getElementById('op_hourly_wage')?.value) || 0;
                const daily = wage * 8;
                const monthly = wage * 8 * 26;

                const dailyEl = document.getElementById('hourly_daily_est');
                const monthlyEl = document.getElementById('hourly_monthly_est');

                if (dailyEl) dailyEl.textContent = formatINR(daily);
                if (monthlyEl) monthlyEl.textContent = formatINR(monthly);
            }
        }

        const salaryCalcInputs = [
            'op_monthly_salary',
            'op_pf_percentage',
            'op_esi_percentage',
            'op_ptax_percentage',
            'op_other_percentage',
            'op_hourly_wage'
        ];
        salaryCalcInputs.forEach(id => {
            const inputEl = document.getElementById(id);
            if (inputEl) {
                inputEl.addEventListener('input', recalculateSalary);
                inputEl.addEventListener('change', recalculateSalary);
                inputEl.addEventListener('keyup', recalculateSalary);
            }
        });

        // Initial salary calculation
        recalculateSalary();

        // Document Management: File Size Validation (<= 5 MB)
        const docFileInput = document.getElementById('document_file');
        if (docFileInput) {
            docFileInput.addEventListener('change', function() {
                const file = this.files[0];
                const errorDiv = document.getElementById('file_size_error');
                const uploadBtn = document.getElementById('btnUploadDoc');
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        if (errorDiv) {
                            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> File size is <strong>' + (file.size / (1024 * 1024)).toFixed(2) + ' MB</strong>, which exceeds the 5 MB limit. Please select a smaller file.';
                            errorDiv.style.display = 'block';
                        }
                        this.value = '';
                        if (uploadBtn) uploadBtn.disabled = true;
                    } else {
                        if (errorDiv) errorDiv.style.display = 'none';
                        if (uploadBtn) uploadBtn.disabled = false;
                    }
                }
            });
        }
    });
</script>
@endsection
