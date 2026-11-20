@extends('layouts.app')

@section('title')
<title>Admin || Staff Attendance Management</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/restaurant-staff.css') }}">
<style>
    .att-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .att-stat-card {
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
    .att-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .att-stat-label {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .att-stat-num {
        font-family: 'Outfit', sans-serif;
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }
    .att-stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .att-type-radio-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
    }
    .att-type-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .att-type-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .att-type-card.selected {
        border-color: #0d9488;
        background: #f0fdfa;
        box-shadow: 0 0 0 1px #0d9488;
    }
    .att-type-card input[type="radio"] {
        margin-top: 3px;
        accent-color: #0d9488;
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
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="staff-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        {{-- 1. Header Toolbar --}}
        <div class="staff-header-banner mb-4">
            <div class="staff-header-left">
                <div class="staff-header-icon" style="background: linear-gradient(135deg, #0d9488 0%, #047857 100%);">
                    <i class="fas fa-user-clock text-white"></i>
                </div>
                <div class="staff-header-title-meta">
                    <span class="staff-header-eyebrow">Daily Attendance &amp; Shift Logs</span>
                    <h1 class="staff-header-title">Staff Attendance Management</h1>
                    <p class="staff-header-sub">Track daily logins, half-days, early leaves &amp; automatic leave quota deductions</p>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                @if(isset($availableOutlets) && ($availableOutlets->count() > 1 || (auth()->user() && auth()->user()->role === 'SA')))
                {{-- Outlet / Restaurant Filter --}}
                <form action="{{ route('restaurant.attendance.index') }}" method="GET" class="d-flex align-items-center gap-1" id="outletFilterForm">
                    <input type="hidden" name="date" value="{{ $date }}">
                    <div class="input-group input-group-sm" style="min-width: 230px;">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-store text-teal"></i>
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

                {{-- Date Selector Form --}}
                <form action="{{ route('restaurant.attendance.index') }}" method="GET" class="d-flex align-items-center gap-1" id="dateFilterForm">
                    @if(request('restaurant_id'))
                        <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                    @elseif(isset($selectedOutletId))
                        <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                    @endif
                    <a href="{{ route('restaurant.attendance.index', array_filter(['date' => date('Y-m-d', strtotime($date . ' -1 day')), 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="btn btn-outline-secondary btn-sm" title="Previous Day">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <input type="date" name="date" id="dateInput" class="form-control form-control-sm text-center fw-bold" value="{{ $date }}" style="width: 145px; border-radius: 8px;" onchange="document.getElementById('dateFilterForm').submit()">
                    <a href="{{ route('restaurant.attendance.index', array_filter(['date' => date('Y-m-d', strtotime($date . ' +1 day')), 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="btn btn-outline-secondary btn-sm" title="Next Day">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    @if($date !== date('Y-m-d'))
                    <a href="{{ route('restaurant.attendance.index', array_filter(['date' => date('Y-m-d'), 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="btn btn-sm btn-outline-primary" title="Jump to Today">
                        Today
                    </a>
                    @endif
                </form>

                {{-- Download Excel --}}
                <a href="{{ route('restaurant.attendance.export', array_filter(['date' => $date, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="btn btn-outline-success btn-sm rounded-pill px-3" style="font-weight: 600;">
                    <i class="fas fa-file-excel me-1"></i> Download Excel
                </a>

                {{-- Manage Staff --}}
                <a href="{{ route('restaurant.staff.index', array_filter(['restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="btn-staff-secondary btn-sm">
                    <i class="fas fa-users me-1"></i> Manage Staff
                </a>
            </div>
        </div>

        {{-- 2. Stats Grid --}}
        <div class="att-stat-grid">
            <div class="att-stat-card">
                <div>
                    <div class="att-stat-label">Total Staff</div>
                    <div class="att-stat-num">{{ $stats['total_staff'] }}</div>
                </div>
                <div class="att-stat-icon-wrap" style="background: #f1f5f9; color: #475569;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="att-stat-card">
                <div>
                    <div class="att-stat-label">Present (Full Day)</div>
                    <div class="att-stat-num text-success">{{ $stats['present_count'] }}</div>
                </div>
                <div class="att-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="att-stat-card">
                <div>
                    <div class="att-stat-label">Half Day</div>
                    <div class="att-stat-num text-warning">{{ $stats['half_day_count'] }}</div>
                </div>
                <div class="att-stat-icon-wrap" style="background: #fef9c3; color: #ca8a04;">
                    <i class="fas fa-adjust"></i>
                </div>
            </div>
            <div class="att-stat-card">
                <div>
                    <div class="att-stat-label">Early Leave</div>
                    <div class="att-stat-num text-info">{{ $stats['early_leave_count'] }}</div>
                </div>
                <div class="att-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
            </div>
            <div class="att-stat-card">
                <div>
                    <div class="att-stat-label">Weekoff</div>
                    <div class="att-stat-num" style="color: #7c3aed;">{{ $stats['weekoff_count'] ?? 0 }}</div>
                </div>
                <div class="att-stat-icon-wrap" style="background: #ede9fe; color: #7c3aed;">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="att-stat-card">
                <div>
                    <div class="att-stat-label">Unmarked / Absent</div>
                    <div class="att-stat-num text-danger">{{ $stats['unmarked_count'] }}</div>
                </div>
                <div class="att-stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>

        {{-- 3. Mark Attendance Form Card --}}
        <div class="bg-white rounded-3 p-4 mb-4 border shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: #0f172a;">
                        <i class="fas fa-clipboard-check text-teal me-2"></i>Mark Attendance for {{ date('d M Y (l)', strtotime($date)) }}
                    </h5>
                    <small class="text-muted">Select staff member, attendance classification, and shift times</small>
                </div>
                <span class="badge bg-light text-dark border px-3 py-2">
                    <i class="fas fa-calendar-day text-primary me-1"></i> Date: {{ date('d-m-Y', strtotime($date)) }}
                </span>
            </div>

            <form action="{{ route('restaurant.attendance.store') }}" method="POST" id="attendanceForm">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                @if(request('restaurant_id'))
                    <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                @elseif(isset($selectedOutletId))
                    <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                @endif

                <div class="row g-3">
                    {{-- 1. Select Staff Member --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Select Staff Member <span class="text-danger">*</span>
                        </label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">-- Choose Staff Member --</option>
                            @forelse($staffList as $staff)
                                @php
                                    $alreadyMarked = in_array($staff->id, $markedUserIds);
                                    $isHourly = ($staff->salary && ($staff->salary->employment_type === 'hourly' || (float)$staff->salary->hourly_wage > 0));
                                @endphp
                                <option value="{{ $staff->id }}" 
                                        data-employee-type="{{ $isHourly ? 'hourly' : 'permanent' }}"
                                        {{ $alreadyMarked ? 'disabled' : '' }} 
                                        {{ old('user_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ $staff->role_type }}){{ (isset($availableOutlets) && $availableOutlets->count() > 1 && $staff->restaurant) ? ' — [' . $staff->restaurant->name . ']' : '' }}{{ $alreadyMarked ? ' — [Already Marked Today]' : '' }}
                                </option>
                            @empty
                                <option value="" disabled>No staff members found for this restaurant</option>
                            @endforelse
                        </select>
                        @if($staffList->isEmpty())
                            <div class="form-text text-danger mt-1">
                                <i class="fas fa-exclamation-circle me-1"></i> No staff found for this restaurant. <a href="{{ route('restaurant.staff.index', array_filter(['restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="fw-bold text-decoration-underline">Manage / Add Staff here</a>.
                            </div>
                        @else
                            <small class="text-muted" style="font-size: 0.76rem;">Staff marked today are disabled.</small>
                        @endif
                    </div>

                    {{-- 2. Employee Wage Type --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Employee Wage Type <span class="text-danger">*</span>
                        </label>
                        <select name="employee_type" id="employee_type" class="form-select fw-semibold" required>
                            <option value="permanent" {{ old('employee_type', 'permanent') === 'permanent' ? 'selected' : '' }}>Permanent (Monthly)</option>
                            <option value="hourly" {{ old('employee_type') === 'hourly' ? 'selected' : '' }}>Hourly Wage (Duty)</option>
                        </select>
                        <small class="text-muted" style="font-size: 0.76rem;">Determines wage calculation.</small>
                    </div>

                    {{-- 3. Login Time --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Login Time <span class="text-danger">*</span>
                        </label>
                        <input type="time" name="login_time" id="login_time" class="form-control" value="{{ old('login_time', '09:30') }}" required>
                        <small class="text-muted" style="font-size: 0.76rem;">Start time of staff shift.</small>
                    </div>

                    {{-- 4. Logout Time (Optional) --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Logout Time <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <input type="time" name="logout_time" id="logout_time" class="form-control" value="{{ old('logout_time') }}">
                        <small class="text-muted" style="font-size: 0.76rem;">Optional. Set at shift end.</small>
                    </div>

                    {{-- 5. Attendance Type Radio Selection --}}
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark small mb-2">
                            Attendance Classification <span class="text-danger">*</span>
                        </label>
                        <div class="att-type-radio-group">
                            {{-- Full Day Present (Default) --}}
                            <label class="att-type-card selected" id="card_type_P">
                                <input type="radio" name="attendance_type" value="P" {{ old('attendance_type', 'P') === 'P' ? 'checked' : '' }} onchange="updateTypeSelection('P')">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                        <i class="fas fa-check-circle text-success"></i> Full Day Present (P)
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Standard full-day attendance.</small>
                                </div>
                            </label>

                            {{-- Half Day --}}
                            <label class="att-type-card" id="card_type_H">
                                <input type="radio" name="attendance_type" value="H" {{ old('attendance_type') === 'H' ? 'checked' : '' }} onchange="updateTypeSelection('H')">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                        <i class="fas fa-adjust text-warning"></i> Half Day (H)
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <span class="text-warning-emphasis fw-bold">Deducts 1 Half Day</span> from annual leave policy.
                                    </small>
                                </div>
                            </label>

                            {{-- Early Leave --}}
                            <label class="att-type-card" id="card_type_EL">
                                <input type="radio" name="attendance_type" value="EL" {{ old('attendance_type') === 'EL' ? 'checked' : '' }} onchange="updateTypeSelection('EL')">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                        <i class="fas fa-sign-out-alt text-info"></i> Early Leave (EL)
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <span class="text-info fw-bold">Deducts 1 Early Leave</span> from annual leave policy.
                                    </small>
                                </div>
                            </label>

                            {{-- Weekoff --}}
                            <label class="att-type-card" id="card_type_WO">
                                <input type="radio" name="attendance_type" value="WO" {{ old('attendance_type') === 'WO' ? 'checked' : '' }} onchange="updateTypeSelection('WO')">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                        <i class="fas fa-calendar-check" style="color: #7c3aed;"></i> Weekoff (WO)
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        Scheduled weekly off day. No leave deducted.
                                    </small>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- 5. Note / Remarks --}}
                    <div class="col-md-9">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Notes / Remarks <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional remarks, e.g. Left early due to doctor appointment, late shift swap, etc." value="{{ old('notes') }}" maxlength="500">
                    </div>

                    {{-- 6. Save Button --}}
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn w-100 rounded-pill text-white" style="background: #0d9488; border-color: #0d9488; font-weight: 600; height: 42px;">
                            <i class="fas fa-save me-1"></i> Save Attendance
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 4. Attendance Records Table --}}
        <div class="bg-white rounded-3 p-4 border shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: #0f172a;">
                        <i class="fas fa-list-check text-primary me-2"></i>Attendance Log — {{ date('d M Y (l)', strtotime($date)) }}
                    </h5>
                    <small class="text-muted">Showing {{ $attendances->count() }} staff attendance records</small>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">P: {{ $stats['present_count'] }}</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">H: {{ $stats['half_day_count'] }}</span>
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">EL: {{ $stats['early_leave_count'] }}</span>
                    <span class="badge px-2 py-1" style="background-color: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe;">WO: {{ $stats['weekoff_count'] ?? 0 }}</span>
                </div>
            </div>

            @if($attendances->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3" style="width: 64px; height: 64px; margin: 0 auto; border-radius: 50%; background: #f8fafc; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94a3b8;">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No Attendance Records for This Date</h6>
                    <p class="text-muted small mb-0">Use the form above to mark staff attendance for {{ date('d M Y', strtotime($date)) }}.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45px;" class="text-center">#</th>
                                <th style="min-width: 190px;">Staff Member</th>
                                <th style="min-width: 130px;">Wage Type</th>
                                <th style="min-width: 150px;">Classification</th>
                                <th style="min-width: 110px;">Login Time</th>
                                <th style="min-width: 130px;">Logout Time</th>
                                <th style="min-width: 100px;">Duration</th>
                                <th style="min-width: 160px;">Notes</th>
                                <th style="width: 120px;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $idx => $att)
                                @php
                                    $staff = $att->user;
                                    $initials = '';
                                    if ($staff) {
                                        $parts = explode(' ', trim($staff->name));
                                        foreach(array_slice($parts, 0, 2) as $p) {
                                            if (!empty($p)) $initials .= strtoupper($p[0]);
                                        }
                                    }
                                    if (empty($initials)) $initials = 'ST';
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 38px; height: 38px; border-radius: 10px; background: #0d9488; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                                                    {{ $staff ? $staff->name : 'Deleted Staff' }}
                                                </div>
                                                <div class="text-muted small" style="font-size: 0.76rem;">
                                                    <span class="badge bg-light text-secondary border py-0 px-1">{{ $staff ? $staff->role_type : 'N/A' }}</span>
                                                    @if($staff && $staff->phone)
                                                        <span>• <i class="fas fa-phone-alt"></i> {{ $staff->phone }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($att->employee_type === 'hourly')
                                            <span class="badge px-2 py-1 fw-bold" style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 0.74rem;">
                                                <i class="fas fa-stopwatch me-1"></i> Hourly Wage
                                            </span>
                                        @else
                                            <span class="badge px-2 py-1 fw-bold" style="background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.74rem;">
                                                <i class="fas fa-calendar-alt me-1"></i> Permanent
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($att->attendance_type === 'P')
                                            <span class="badge badge-attendance-present px-3 py-1 fw-bold">
                                                <i class="fas fa-check-circle me-1"></i> Full Day Present (P)
                                            </span>
                                        @elseif($att->attendance_type === 'H')
                                            <span class="badge badge-attendance-halfday px-3 py-1 fw-bold">
                                                <i class="fas fa-adjust me-1"></i> Half Day (H)
                                            </span>
                                        @elseif($att->attendance_type === 'EL')
                                            <span class="badge badge-attendance-earlyleave px-3 py-1 fw-bold">
                                                <i class="fas fa-sign-out-alt me-1"></i> Early Leave (EL)
                                            </span>
                                        @elseif($att->attendance_type === 'WO')
                                            <span class="badge badge-attendance-weekoff px-3 py-1 fw-bold">
                                                <i class="fas fa-calendar-check me-1"></i> Weekoff (WO)
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            <i class="fas fa-clock text-success small me-1"></i>{{ $att->formatted_login_time }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!empty($att->logout_time))
                                            <span class="fw-semibold text-dark">
                                                <i class="fas fa-clock text-danger small me-1"></i>{{ $att->formatted_logout_time }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.75rem;">
                                                <i class="fas fa-hourglass-half text-warning me-1"></i> Open
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($att->work_duration)
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.76rem;">
                                                <i class="fas fa-stopwatch text-teal me-1"></i>{{ $att->work_duration }}
                                            </span>
                                        @else
                                            <span class="text-muted small">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($att->notes))
                                            <span class="small text-dark">{{ $att->notes }}</span>
                                        @else
                                            <span class="text-muted fst-italic small">No remarks</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            {{-- Quick Edit Logout Time Modal Button --}}
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-edit-att" 
                                                    title="Edit / Set Logout Time"
                                                    data-id="{{ $att->id }}"
                                                    data-staff="{{ $staff ? $staff->name : '' }}"
                                                    data-type="{{ $att->attendance_type }}"
                                                    data-employee-type="{{ $att->employee_type ?: 'permanent' }}"
                                                    data-login="{{ $att->login_time }}"
                                                    data-logout="{{ $att->logout_time }}"
                                                    data-notes="{{ $att->notes }}">
                                                <i class="fas fa-pen"></i>
                                            </button>

                                            {{-- Delete Attendance --}}
                                            <a href="{{ route('restaurant.attendance.delete', array_filter(['attendance_id' => $att->id, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                                               class="btn btn-outline-danger" 
                                               title="Delete Attendance Record" 
                                               onclick="return confirm('Are you sure you want to delete attendance record for {{ addslashes($staff ? $staff->name : 'this staff') }}? Any deducted leave quota will be restored.');">
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

</div>
</div>

{{-- 5. Edit Attendance Modal --}}
<div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-labelledby="editAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="editAttendanceForm">
                @csrf
                <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id', $selectedOutletId ?? '') }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editAttendanceModalLabel">
                        <i class="fas fa-user-edit text-teal me-2"></i>Update Attendance — <span id="modalStaffName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Employee Wage Type</label>
                        <select name="employee_type" id="modalEmployeeType" class="form-select fw-semibold">
                            <option value="permanent">Permanent (Monthly)</option>
                            <option value="hourly">Hourly Wage (Duty)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Attendance Classification</label>
                        <select name="attendance_type" id="modalAttendanceType" class="form-select">
                            <option value="P">Full Day Present (P)</option>
                            <option value="H">Half Day (H) — (Adjusts Leave Policy)</option>
                            <option value="EL">Early Leave (EL) — (Adjusts Leave Policy)</option>
                            <option value="WO">Weekoff (WO)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Login Time <span class="text-danger">*</span></label>
                            <input type="time" name="login_time" id="modalLoginTime" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Logout Time <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="time" name="logout_time" id="modalLogoutTime" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Notes / Remarks</label>
                        <textarea name="notes" id="modalNotes" class="form-control" rows="2" maxlength="500" placeholder="Optional remarks"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: #0d9488; border-color: #0d9488;">
                        <i class="fas fa-check-circle me-1"></i> Save Changes
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
    function updateTypeSelection(type) {
        document.querySelectorAll('.att-type-card').forEach(c => c.classList.remove('selected'));
        const card = document.getElementById('card_type_' + type);
        if (card) {
            card.classList.add('selected');
        }
        const loginInput = document.getElementById('login_time');
        if (loginInput) {
            loginInput.required = (type !== 'WO');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-detect employee wage type when staff member is selected
        const userSelect = document.getElementById('user_id');
        const empTypeSelect = document.getElementById('employee_type');
        if (userSelect && empTypeSelect) {
            userSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.dataset.employeeType) {
                    empTypeSelect.value = opt.dataset.employeeType;
                }
            });
            // If already preselected on load
            if (userSelect.selectedIndex > 0) {
                const opt = userSelect.options[userSelect.selectedIndex];
                if (opt && opt.dataset.employeeType && !empTypeSelect.value) {
                    empTypeSelect.value = opt.dataset.employeeType;
                }
            }
        }

        // Initial sync of login_time required attribute
        const checkedType = document.querySelector('input[name="attendance_type"]:checked');
        if (checkedType) {
            updateTypeSelection(checkedType.value);
        }

        // Edit Attendance Modal initialization
        const editButtons = document.querySelectorAll('.btn-edit-att');
        const modal = new bootstrap.Modal(document.getElementById('editAttendanceModal'));
        const editForm = document.getElementById('editAttendanceForm');
        const modalTypeSelect = document.getElementById('modalAttendanceType');
        const modalLogin = document.getElementById('modalLoginTime');

        function syncModalLoginRequired() {
            if (modalLogin && modalTypeSelect) {
                modalLogin.required = (modalTypeSelect.value !== 'WO');
            }
        }

        if (modalTypeSelect) {
            modalTypeSelect.addEventListener('change', syncModalLoginRequired);
        }

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const staffName = this.dataset.staff;
                const type = this.dataset.type;
                const empType = this.dataset.employeeType || 'permanent';
                const login = this.dataset.login;
                const logout = this.dataset.logout;
                const notes = this.dataset.notes;

                document.getElementById('modalStaffName').textContent = staffName;
                document.getElementById('modalAttendanceType').value = type;
                if (document.getElementById('modalEmployeeType')) {
                    document.getElementById('modalEmployeeType').value = empType;
                }
                document.getElementById('modalLoginTime').value = login || '';
                document.getElementById('modalLogoutTime').value = logout || '';
                document.getElementById('modalNotes').value = notes || '';

                syncModalLoginRequired();

                editForm.action = '{{ url("admin/attendance/update") }}/' + id;
                modal.show();
            });
        });
    });
</script>
@endsection

