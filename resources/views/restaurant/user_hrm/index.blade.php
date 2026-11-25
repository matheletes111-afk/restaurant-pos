@extends('layouts.app')

@section('title')
<title>User HRM - My Portal - {{ auth()->user()->name }}</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/report-analytics.css') }}">
<style>
  .user-hrm-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #ffffff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
  }
  .user-avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.4rem;
    box-shadow: 0 4px 15px rgba(255, 94, 20, 0.35);
  }
  .nav-pills-hrm {
    background: #ffffff;
    padding: 6px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    display: flex;
    gap: 6px;
    margin-bottom: 24px;
    border: 1px solid #e2e8f0;
    overflow-x: auto;
  }
  .nav-pills-hrm .nav-link {
    font-size: 0.88rem;
    font-weight: 600;
    color: #64748b;
    border-radius: 8px;
    padding: 10px 18px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    white-space: nowrap;
    text-decoration: none;
  }
  .nav-pills-hrm .nav-link:hover {
    color: #0f172a;
    background: #f8fafc;
  }
  .nav-pills-hrm .nav-link.active {
    background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(255, 94, 20, 0.3);
  }
  .btn-export-excel {
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  .btn-export-excel:hover {
    background: #059669;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
  }
  .duration-pill {
    background: #0f172a;
    color: #ffffff;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.74rem;
    font-weight: 700;
  }
  .badge-att-p {
    background-color: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
  }
  .badge-att-h {
    background-color: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
  }
  .badge-att-el {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
  }
  .badge-att-wo {
    background-color: #f5f3ff;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
  }
  .time-pill-in {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    padding: 2px 7px;
    border-radius: 5px;
    font-size: 0.76rem;
    font-weight: 600;
  }
  .time-pill-out {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 2px 7px;
    border-radius: 5px;
    font-size: 0.76rem;
    font-weight: 600;
  }
  .payslip-watermark {
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
  }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="rpt-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        @php
            $initials = 'ST';
            if ($user && $user->name) {
                $words = explode(' ', trim($user->name));
                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            }
        @endphp

        {{-- 1. User HRM Header Card --}}
        <div class="user-hrm-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar-circle">
                    {{ $initials }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h3 class="fw-bold mb-0 text-white" style="font-family: 'Outfit', sans-serif;">{{ $user->name }}</h3>
                        <span class="badge bg-white text-dark fw-bold px-2 py-1" style="font-size: 0.75rem;">{{ $user->role_type ?: 'STAFF' }}</span>
                    </div>
                    <div class="text-white-50 small d-flex flex-wrap align-items-center gap-3">
                        <span><i class="fas fa-building me-1 text-warning"></i>{{ $restaurant ? $restaurant->name : 'Restaurant' }}</span>
                        <span><i class="fas fa-envelope me-1"></i>{{ $user->email }}</span>
                        @if($user->phone)
                            <span><i class="fas fa-phone me-1"></i>{{ $user->phone }}</span>
                        @endif
                        <span><i class="fas fa-id-badge me-1"></i>Emp ID: #{{ $user->id }}</span>
                    </div>
                </div>
            </div>

            {{-- Period Filter for Attendance & Extras --}}
            <div class="d-flex align-items-center gap-2">
                <form method="GET" action="{{ route('user.hrm.index') }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <select name="month" class="form-select form-select-sm bg-dark text-white border-secondary" onchange="this.form.submit()" style="min-width: 120px;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="form-select form-select-sm bg-dark text-white border-secondary" onchange="this.form.submit()" style="min-width: 90px;">
                        @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
        </div>

        {{-- 2. User HRM Nav Pills --}}
        <ul class="nav-pills-hrm" role="tablist">
            <a href="{{ route('user.hrm.index', ['tab' => 'attendance', 'month' => $month, 'year' => $year]) }}" 
               class="nav-link {{ $tab === 'attendance' ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i> My Attendance
                <span class="badge {{ $tab === 'attendance' ? 'bg-white text-dark' : 'bg-light text-secondary' }}">{{ $attendances->count() }}</span>
            </a>
            <a href="{{ route('user.hrm.index', ['tab' => 'leaves', 'year' => $year]) }}" 
               class="nav-link {{ $tab === 'leaves' ? 'active' : '' }}">
                <i class="fas fa-calendar-minus"></i> My Leaves
                <span class="badge {{ $tab === 'leaves' ? 'bg-white text-dark' : 'bg-light text-secondary' }}">{{ $leaves->count() }}</span>
            </a>
            <a href="{{ route('user.hrm.index', ['tab' => 'extras', 'month' => $month, 'year' => $year]) }}" 
               class="nav-link {{ $tab === 'extras' ? 'active' : '' }}">
                <i class="fas fa-coins"></i> My Extras
                <span class="badge {{ $tab === 'extras' ? 'bg-white text-dark' : 'bg-light text-secondary' }}">{{ $extras->count() }}</span>
            </a>
            <a href="{{ route('user.hrm.index', ['tab' => 'salary', 'year' => $year]) }}" 
               class="nav-link {{ $tab === 'salary' ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> My Salary &amp; Slips
                <span class="badge {{ $tab === 'salary' ? 'bg-white text-dark' : 'bg-light text-secondary' }}">{{ $payrolls->count() }}</span>
            </a>
        </ul>

        {{-- ========================================================================= --}}
        {{-- TAB 1: MY ATTENDANCE --}}
        {{-- ========================================================================= --}}
        @if($tab === 'attendance')
            {{-- Clock In / Clock Out Punch Card --}}
            <div class="rpt-table-card mb-4 p-4 border" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: linear-gradient(135deg, #0d9488 0%, #047857 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                            <i class="fas fa-fingerprint"></i>
                        </div>
                        <div>
                            <div class="text-uppercase small fw-bold text-muted">Daily Duty &bull; Geofence Attendance</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="fw-bold fs-4 text-dark font-monospace" id="liveClockDisplay">--:--:--</span>
                                <span class="badge bg-light text-secondary border small">{{ date('l, d M Y') }}</span>
                            </div>
                            <div class="mt-1 small text-muted">
                                @if(isset($restaurant) && $restaurant->latitude && $restaurant->longitude)
                                    <i class="fas fa-map-marker-alt text-primary me-1"></i> {{ $restaurant->name }} &bull; Max allowed radius: <strong>{{ $restaurant->geofence_radius_meters ?? 50 }}m</strong>
                                @else
                                    <i class="fas fa-store text-muted me-1"></i> {{ $restaurant->name ?? 'Restaurant' }}
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Punch Buttons & Shift Status --}}
                    <div>
                        @if(!$todayAttendance || empty($todayAttendance->login_time))
                            {{-- Not Clocked In Yet --}}
                            <form action="{{ route('user.hrm.attendance.clock-in') }}" method="POST" id="userClockInForm" class="d-inline">
                                @csrf
                                <input type="hidden" name="latitude" id="clockin_lat">
                                <input type="hidden" name="longitude" id="clockin_lng">
                                <button type="button" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm" id="btnClockIn">
                                    <i class="fas fa-sign-in-alt me-1"></i> Mark Attendance / Clock In
                                </button>
                            </form>
                        @elseif(empty($todayAttendance->logout_time))
                            {{-- On Duty -> Needs Clock Out --}}
                            <div class="d-flex align-items-center gap-2">
                                <div class="text-end me-2">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold">
                                        <i class="fas fa-circle text-success me-1" style="font-size: 0.6rem;"></i> ON DUTY
                                    </span>
                                    <div class="text-muted small mt-1">In: <strong>{{ \Carbon\Carbon::parse($todayAttendance->login_time)->format('h:i A') }}</strong></div>
                                </div>
                                <form action="{{ route('user.hrm.attendance.clock-out') }}" method="POST" id="userClockOutForm" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="latitude" id="clockout_lat">
                                    <input type="hidden" name="longitude" id="clockout_lng">
                                    <button type="button" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm" id="btnClockOut">
                                        <i class="fas fa-sign-out-alt me-1"></i> Clock Out / End Duty
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Shift Completed --}}
                            <div class="p-2 px-3 rounded border bg-light d-flex align-items-center gap-2">
                                <i class="fas fa-calendar-check text-success fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark small">Shift Completed Today</div>
                                    <div class="text-muted small" style="font-size: 0.76rem;">
                                        {{ \Carbon\Carbon::parse($todayAttendance->login_time)->format('h:i A') }} &mdash; {{ \Carbon\Carbon::parse($todayAttendance->logout_time)->format('h:i A') }}
                                        ({{ number_format($todayAttendance->worked_hours_decimal, 1) }} hrs worked)
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rpt-stats-grid mb-4">
                <div class="rpt-stat-card stat-info">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Month Period</span>
                        <span class="rpt-stat-val" style="font-size: 1.1rem;">{{ $startDate->format('F Y') }}</span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-success">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Full Day Present</span>
                        <span class="rpt-stat-val">{{ $attSummary['present_count'] }} <small class="fs-6 text-muted">days</small></span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-warning">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Half Days &amp; Early Leaves</span>
                        <span class="rpt-stat-val">{{ $attSummary['half_day_count'] + $attSummary['early_leave_count'] }}</span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-indigo">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Week Offs</span>
                        <span class="rpt-stat-val">{{ $attSummary['weekoff_count'] }}</span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-calendar-minus"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-purple">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Total Worked Hours</span>
                        <span class="rpt-stat-val">{{ number_format($attSummary['total_hours'], 1) }} <small class="fs-6 text-muted">hrs</small></span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-business-time"></i>
                    </div>
                </div>
            </div>

            <div class="rpt-table-card">
                <div class="rpt-table-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="rpt-table-title"><i class="fas fa-clipboard-user me-2 text-primary"></i>My Attendance Sheet ({{ $startDate->format('F Y') }})</h5>
                        <p class="rpt-table-sub">Detailed day-by-day record of your check-in, check-out, duration, and duty classification</p>
                    </div>
                    <div>
                        <a href="{{ route('user.hrm.attendance.export', ['month' => $month, 'year' => $year]) }}" class="btn-export-excel">
                            <i class="fas fa-file-excel"></i> Download Excel
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table rpt-table mb-0 align-middle" id="userAttendanceTable">
                        <thead>
                            <tr>
                                <th style="width: 45px;">#</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Wage Type</th>
                                <th>Attendance Status</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Worked Duration</th>
                                <th>Hours</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $idx => $att)
                                @php
                                    $attDate = $att->attendance_date ? \Carbon\Carbon::parse($att->attendance_date) : null;
                                    $hrs = (float) $att->worked_hours_decimal;
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $attDate ? $attDate->format('d M Y') : '—' }}</td>
                                    <td class="text-muted">{{ $attDate ? $attDate->format('l') : '—' }}</td>
                                    <td>
                                        @if($att->employee_type === 'hourly')
                                            <span class="badge bg-purple-subtle text-purple border" style="font-size: 0.72rem;"><i class="fas fa-stopwatch me-1"></i>Hourly Wage</span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.72rem;"><i class="fas fa-id-badge me-1"></i>Permanent</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($att->attendance_type === 'P')
                                            <span class="badge-att-p"><i class="fas fa-check-circle me-1"></i> Full Present</span>
                                        @elseif($att->attendance_type === 'H')
                                            <span class="badge-att-h"><i class="fas fa-adjust me-1"></i> Half Day</span>
                                        @elseif($att->attendance_type === 'EL')
                                            <span class="badge-att-el"><i class="fas fa-running me-1"></i> Early Leave</span>
                                        @elseif($att->attendance_type === 'WO')
                                            <span class="badge-att-wo"><i class="fas fa-coffee me-1"></i> Weekoff</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $att->attendance_type }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($att->login_time)
                                            <span class="time-pill-in"><i class="fas fa-sign-in-alt text-success me-1"></i>{{ $att->formatted_login_time }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($att->logout_time)
                                            <span class="time-pill-out"><i class="fas fa-sign-out-alt text-secondary me-1"></i>{{ $att->formatted_logout_time }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($att->work_duration)
                                            <span class="duration-pill">{{ $att->work_duration }}</span>
                                        @elseif($hrs > 0)
                                            <span class="duration-pill">{{ $hrs }} hrs</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-dark">{{ number_format($hrs, 2) }}</td>
                                    <td><small class="text-muted">{{ $att->notes ?: '—' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-times fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                        <h6 class="fw-semibold">No attendance entries recorded for {{ $startDate->format('F Y') }}</h6>
                                        <p class="small text-muted mb-0">Use the month &amp; year selector above to view other months.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($attendances->isNotEmpty())
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="7" class="text-end">TOTAL WORKED HOURS:</td>
                                <td><span class="duration-pill">{{ number_format($attSummary['total_hours'], 1) }} hrs</span></td>
                                <td class="text-dark">{{ number_format($attSummary['total_hours'], 2) }}</td>
                                <td>({{ $attendances->count() }} logged days)</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- TAB 2: MY LEAVES --}}
        {{-- ========================================================================= --}}
        @if($tab === 'leaves')
            <div class="rpt-stats-grid mb-4">
                <div class="rpt-stat-card stat-primary">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Total Leave Quota ({{ $year }})</span>
                        <span class="rpt-stat-val">{{ number_format($leaveSummary['total_quota'], 1) }} <small class="fs-6 text-muted">days</small></span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-danger">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Approved Leaves Taken</span>
                        <span class="rpt-stat-val">{{ number_format($leaveSummary['used_days'], 1) }} <small class="fs-6 text-muted">days</small></span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-success">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Remaining Balance</span>
                        <span class="rpt-stat-val">{{ number_format($leaveSummary['remaining_days'], 1) }} <small class="fs-6 text-muted">days</small></span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-battery-three-quarters"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-warning">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Pending Approval</span>
                        <span class="rpt-stat-val">{{ $leaveSummary['pending_count'] }} <small class="fs-6 text-muted">request(s)</small></span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>

            <div class="rpt-table-card">
                <div class="rpt-table-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="rpt-table-title"><i class="fas fa-calendar-minus me-2 text-primary"></i>My Leave History &amp; Requests</h5>
                        <p class="rpt-table-sub">Track your approved leaves, submitted applications, and status updates for year {{ $year }}</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                            <i class="fas fa-plus me-1"></i> Apply for Leave
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table rpt-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 45px;">#</th>
                                <th>Leave Type</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th class="text-center">Days Count</th>
                                <th>Status</th>
                                <th>Reason / Purpose</th>
                                <th>Applied Date</th>
                                <th>Manager Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $idx => $leave)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <i class="{{ $leave->leave_type_icon }} text-primary me-1"></i>{{ $leave->leave_type_name }}
                                        </div>
                                    </td>
                                    <td>{{ $leave->start_date ? \Carbon\Carbon::parse($leave->start_date)->format('d M Y') : '—' }}</td>
                                    <td>{{ $leave->end_date ? \Carbon\Carbon::parse($leave->end_date)->format('d M Y') : '—' }}</td>
                                    <td class="text-center fw-bold">{{ number_format($leave->days_count, 1) }}</td>
                                    <td>
                                        @if($leave->status === 'approved')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i>Approved</span>
                                        @elseif($leave->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="fas fa-clock me-1"></i>Pending Approval</span>
                                        @elseif($leave->status === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                                        @elseif($leave->status === 'cancelled')
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><i class="fas fa-ban me-1"></i>Cancelled</span>
                                        @else
                                            <span class="badge bg-light text-dark border">{{ ucfirst($leave->status) }}</span>
                                        @endif
                                    </td>
                                    <td><small class="text-dark">{{ $leave->reason ?: '—' }}</small></td>
                                    <td><small class="text-muted">{{ $leave->created_at ? $leave->created_at->format('d M Y') : '—' }}</small></td>
                                    <td><small class="text-muted">{{ $leave->admin_remarks ?: ($leave->cancellation_remarks ?: '—') }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-check fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                        <h6 class="fw-semibold">No leave applications found for year {{ $year }}</h6>
                                        <p class="small text-muted mb-0">Click "Apply for Leave" above to submit a new leave request.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- TAB 3: MY EXTRAS --}}
        {{-- ========================================================================= --}}
        @if($tab === 'extras')
            <div class="rpt-stats-grid mb-4">
                <div class="rpt-stat-card stat-success">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Extra Earnings (+)</span>
                        <span class="rpt-stat-val text-success">₹{{ number_format($extrasSummary['total_earnings'], 2) }}</span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-danger">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Deductions / Penalties (-)</span>
                        <span class="rpt-stat-val text-danger">₹{{ number_format($extrasSummary['total_deductions'], 2) }}</span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-warning">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Salary Advances (-)</span>
                        <span class="rpt-stat-val text-warning">₹{{ number_format($extrasSummary['total_advances'], 2) }}</span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>

                <div class="rpt-stat-card stat-primary">
                    <div class="rpt-stat-info">
                        <span class="rpt-stat-label">Net Extras Impact</span>
                        <span class="rpt-stat-val {{ $extrasSummary['net_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $extrasSummary['net_balance'] >= 0 ? '+' : '' }}₹{{ number_format($extrasSummary['net_balance'], 2) }}
                        </span>
                    </div>
                    <div class="rpt-stat-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                </div>
            </div>

            <div class="rpt-table-card">
                <div class="rpt-table-header">
                    <div>
                        <h5 class="rpt-table-title"><i class="fas fa-coins me-2 text-primary"></i>My Extras Log ({{ $startDate->format('F Y') }})</h5>
                        <p class="rpt-table-sub">Transparent record of all bonuses, earnings, penalties, and salary advances for the period</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table rpt-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 45px;">#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th class="text-end">Amount</th>
                                <th>Remarks / Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($extras as $idx => $extra)
                                @php
                                    $eDate = $extra->entry_date ? \Carbon\Carbon::parse($extra->entry_date) : null;
                                    $b = $extra->type_badge;
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $eDate ? $eDate->format('d M Y') : '—' }}</td>
                                    <td>
                                        <span class="badge {{ $b['class'] }} px-2 py-1">
                                            <i class="{{ $b['icon'] }} me-1"></i>{{ $b['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold {{ $extra->type === 'Earning' ? 'text-success' : 'text-danger' }}" style="font-size: 0.95rem;">
                                        {{ $extra->type === 'Earning' ? '+' : '-' }}₹{{ number_format($extra->amount, 2) }}
                                    </td>
                                    <td><small class="text-dark">{{ $extra->remarks ?: '—' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-coins fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                        <h6 class="fw-semibold">No extras recorded for {{ $startDate->format('F Y') }}</h6>
                                        <p class="small text-muted mb-0">Use the period selector to check other months.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($extras->isNotEmpty())
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">NET EXTRAS BALANCE:</td>
                                <td class="text-end {{ $extrasSummary['net_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $extrasSummary['net_balance'] >= 0 ? '+' : '' }}₹{{ number_format($extrasSummary['net_balance'], 2) }}
                                </td>
                                <td>({{ $extras->count() }} entries)</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- TAB 4: MY SALARY & SLIPS --}}
        {{-- ========================================================================= --}}
        @if($tab === 'salary')
            {{-- Salary Profile Card --}}
            @if($salary)
                <div class="rpt-table-card mb-4 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div>
                            <span class="text-uppercase small fw-bold text-muted">Current Compensation Structure</span>
                            <h5 class="fw-bold text-dark mb-0">Employment &amp; Wage Profile</h5>
                        </div>
                        <div>
                            <span class="badge bg-primary px-3 py-2 text-white fw-bold">
                                {{ ucfirst($salary->employment_type) }} Employee
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        @if($salary->employment_type === 'permanent')
                            <div class="col-md-3 col-6">
                                <div class="payslip-watermark">
                                    <div class="text-muted small">Base Monthly Salary</div>
                                    <div class="fs-5 fw-bold text-dark">₹{{ number_format($salary->monthly_salary, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="payslip-watermark">
                                    <div class="text-muted small">Provident Fund (PF)</div>
                                    <div class="fs-5 fw-bold text-dark">{{ $salary->pf_percentage }}%</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="payslip-watermark">
                                    <div class="text-muted small">ESI Deduction</div>
                                    <div class="fs-5 fw-bold text-dark">{{ $salary->esi_percentage }}%</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="payslip-watermark">
                                    <div class="text-muted small">P-Tax &amp; Other Deductions</div>
                                    <div class="fs-5 fw-bold text-dark">
                                        @if(($salary->ptax_type ?? 'percentage') === 'fixed')
                                            ₹{{ number_format($salary->ptax_amount, 2) }} Fixed
                                        @else
                                            {{ $salary->ptax_percentage }}%
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-4 col-6">
                                <div class="payslip-watermark">
                                    <div class="text-muted small">Hourly Wage Rate</div>
                                    <div class="fs-4 fw-bold text-primary">₹{{ number_format($salary->hourly_wage, 2) }} / hr</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="payslip-watermark">
                                    <div class="text-muted small">Estimated 8h Daily Pay</div>
                                    <div class="fs-4 fw-bold text-dark">₹{{ number_format($salary->hourly_wage * 8, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="payslip-watermark">
                                    <div class="text-muted small">Estimated 26-Day Month</div>
                                    <div class="fs-4 fw-bold text-success">₹{{ number_format($salary->hourly_wage * 8 * 26, 2) }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Salary History Table --}}
            <div class="rpt-table-card">
                <div class="rpt-table-header">
                    <div>
                        <h5 class="rpt-table-title"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>My Salary History &amp; Monthly Pay Slips ({{ $year }})</h5>
                        <p class="rpt-table-sub">Review your disbursed monthly salaries, breakdown of credits, deductions, and download payslips</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table rpt-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 45px;">#</th>
                                <th>Salary Month</th>
                                <th class="text-center">Paid Duty Days</th>
                                <th class="text-end">Base Salary</th>
                                <th class="text-end">Earned Salary</th>
                                <th class="text-end">Statutory Deductions</th>
                                <th class="text-end">Extras Impact</th>
                                <th class="text-end">Actual Payable</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Pay Slip</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $idx => $pr)
                                @php
                                    $mName = \Carbon\Carbon::create($pr->year, $pr->month, 1)->format('F Y');
                                    $extrasImpact = (float) $pr->extra_earnings - (float) $pr->extra_deductions - (float) $pr->advance_amount;
                                    $statutoryTotal = (float) $pr->pf_amount + (float) $pr->esi_amount + (float) $pr->other_deductions;
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $mName }}</div>
                                        <small class="text-muted">Total {{ $pr->total_days }} days</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            {{ number_format($pr->present_days, 1) }} Days
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">₹{{ number_format($pr->base_salary, 2) }}</td>
                                    <td class="text-end fw-bold text-dark">₹{{ number_format($pr->earned_salary, 2) }}</td>
                                    <td class="text-end fw-semibold text-danger">
                                        @if($statutoryTotal > 0)
                                            -₹{{ number_format($statutoryTotal, 2) }}
                                        @else
                                            <span class="text-muted">₹0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold {{ $extrasImpact >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $extrasImpact >= 0 ? '+' : '' }}₹{{ number_format($extrasImpact, 2) }}
                                    </td>
                                    <td class="text-end fw-bold text-primary" style="font-size: 1rem;">
                                        ₹{{ number_format($pr->actual_payable, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($pr->is_paid)
                                            <span class="badge bg-success text-white px-2 py-1"><i class="fas fa-check-circle me-1"></i>PAID</span>
                                            @if($pr->paid_date)
                                                <div class="text-muted mt-1" style="font-size: 0.72rem;">{{ \Carbon\Carbon::parse($pr->paid_date)->format('d M Y') }}</div>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-clock me-1"></i>PENDING</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick="viewPayslipModal({{ $pr->id }})" title="View Pay Slip">
                                                <i class="fas fa-receipt me-1"></i> Slip
                                            </button>
                                            <a href="{{ route('user.hrm.salary.slip.print', $pr->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print Pay Slip">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fas fa-file-invoice-dollar fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                        <h6 class="fw-semibold">No salary records generated for {{ $year }} yet</h6>
                                        <p class="small text-muted mb-0">Salary slips will appear here once monthly payroll is finalized by management.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

</div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 1: PAY SLIP VIEWER --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="userPayslipModal" tabindex="-1" aria-labelledby="userPayslipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-receipt text-warning fs-5"></i>
                    <h5 class="modal-title fw-bold text-white mb-0" id="userPayslipModalLabel">Employee Pay Slip</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="payslipModalBody">
                {{-- Slip Header --}}
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark" id="modalRestName">Restaurant POS</h4>
                        <div class="text-muted small">Official Salary Disbursal Slip</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary px-3 py-2 fs-6" id="modalMonthName">November 2026</span>
                        <div class="text-muted small mt-1" id="modalPaymentStatus">PAID</div>
                    </div>
                </div>

                {{-- Employee Info Box --}}
                <div class="row g-3 p-3 bg-light rounded mb-4">
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Employee Name</div>
                        <div class="fw-bold text-dark" id="modalStaffName">{{ $user->name }}</div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Designation / Role</div>
                        <div class="fw-bold text-dark" id="modalRoleType">{{ $user->role_type }}</div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Total Month Days</div>
                        <div class="fw-bold text-dark" id="modalTotalDays">30</div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Paid Duty Days</div>
                        <div class="fw-bold text-success" id="modalPaidDays">26</div>
                    </div>
                </div>

                {{-- Earnings vs Deductions Tables --}}
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-success border-bottom pb-2 mb-3">
                            <i class="fas fa-plus-circle me-1"></i> Earnings &amp; Credits
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Base Monthly Salary:</td>
                                    <td class="text-end fw-semibold" id="modalBaseSalary">₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Earned Duty Salary:</td>
                                    <td class="text-end fw-semibold text-dark" id="modalEarnedSalary">₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Extra Earnings / Bonus:</td>
                                    <td class="text-end fw-semibold text-success" id="modalExtraEarnings">+₹0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-dark">Total Gross Credits:</td>
                                    <td class="text-end fw-bold text-dark" id="modalTotalGross">₹0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold text-danger border-bottom pb-2 mb-3">
                            <i class="fas fa-minus-circle me-1"></i> Deductions &amp; Recoveries
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Absent Deduction:</td>
                                    <td class="text-end fw-semibold text-danger" id="modalAbsentDeduction">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Provident Fund (PF):</td>
                                    <td class="text-end fw-semibold" id="modalPf">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ESI Deduction:</td>
                                    <td class="text-end fw-semibold" id="modalEsi">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Other / P-Tax:</td>
                                    <td class="text-end fw-semibold" id="modalOther">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Penalties / Fines:</td>
                                    <td class="text-end fw-semibold text-danger" id="modalPenalty">-₹0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Advance Recovery:</td>
                                    <td class="text-end fw-semibold text-warning" id="modalAdvance">-₹0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-danger">Total Deductions:</td>
                                    <td class="text-end fw-bold text-danger" id="modalTotalDeductions">-₹0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Net & Actual Payable --}}
                <div class="mt-4 p-3 rounded bg-light border d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Net Calculated Salary</div>
                        <div class="fs-4 fw-bold text-dark" id="modalFinalSalary">₹0.00</div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small fw-bold text-uppercase">Actual Disbursed Payable</div>
                        <div class="fs-4 fw-bold text-primary" id="modalActualPayable">₹0.00</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="printPayslipElement()">
                    <i class="fas fa-print me-1"></i> Print Pay Slip
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 2: APPLY FOR LEAVE --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-labelledby="applyLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-plus fs-5"></i>
                    <h5 class="modal-title fw-bold text-white mb-0" id="applyLeaveModalLabel">Apply for Leave</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.hrm.leaves.apply') }}" method="POST" id="userApplyLeaveForm">
                @csrf
                <div class="modal-body p-4">
                    {{-- Status Banner Notice --}}
                    <div class="alert alert-info py-2 px-3 small mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle fa-lg text-primary"></i>
                        <div>
                            Submitted applications are set to <strong>Pending Approval</strong>. Leave is only adjusted from your quota once approved by administration.
                        </div>
                    </div>

                    {{-- Leave Type Selection --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Select Leave Type <span class="text-danger">*</span>
                        </label>
                        <select name="leave_type" id="user_leave_type" class="form-select" required onchange="calculateUserLeave()">
                            @if(isset($leaveDefinitions))
                                @foreach($leaveDefinitions as $typeKey => $def)
                                    <option value="{{ $typeKey }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $def['name'] }} ({{ $def['code'] }})
                                    </option>
                                @endforeach
                            @else
                                <option value="sick_leave">Sick Leave (SL)</option>
                                <option value="paid_leave">Paid Leave (PL)</option>
                                <option value="half_day">Half Day (HD)</option>
                                <option value="early_leave">Early Leave (EL)</option>
                            @endif
                        </select>
                    </div>

                    {{-- Live Quota Availability Box --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Available Leave Balance</label>
                        <div class="p-2 rounded border bg-light d-flex align-items-center justify-content-between" style="min-height: 38px;">
                            <span class="text-muted small" id="userQuotaTypeLabel">Remaining Quota:</span>
                            <span class="badge bg-success rounded-pill px-3 py-1" id="userQuotaRemainingBadge">0.00 Days Available</span>
                        </div>
                    </div>

                    {{-- Dates Grid --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small mb-1">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="user_leave_start_date" class="form-control" value="{{ date('Y-m-d') }}" required onchange="onUserStartDateChange()">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small mb-1">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="user_leave_end_date" class="form-control" value="{{ date('Y-m-d') }}" required onchange="calculateUserLeave()">
                        </div>
                    </div>

                    {{-- Duration Preview Box --}}
                    <div class="mb-3">
                        <div class="p-2 rounded border bg-light d-flex align-items-center justify-content-between">
                            <span class="text-muted small">Estimated Duration:</span>
                            <span class="fw-bold text-dark" id="userDurationBadge">1.00 Day(s)</span>
                        </div>
                    </div>

                    {{-- Quota Exceeded Warning --}}
                    <div class="mb-3 d-none" id="userQuotaWarning">
                        <div class="alert alert-danger py-2 px-3 mb-0 small d-flex align-items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <span id="userQuotaWarningText">Requested days exceed your unused leave balance.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Reason / Purpose --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Reason / Purpose</label>
                        <textarea name="reason" id="user_leave_reason" rows="3" class="form-control" placeholder="Specify the reason or purpose for your leave application..." maxlength="1000"></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="userSubmitLeaveBtn">
                        <i class="fas fa-paper-plane me-1"></i> Submit Application (Pending Approval)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
const userPolicies = {!! json_encode($staffPolicyMap ?? []) !!};

$(document).ready(function() {
    if ($('#userAttendanceTable tbody tr td').length > 1) {
        $('#userAttendanceTable').DataTable({
            pageLength: 31,
            lengthMenu: [[10, 25, 31, 50, -1], [10, 25, 31, 50, "All"]],
            order: [[1, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search attendance...",
            }
        });
    }

    calculateUserLeave();
});

function onUserStartDateChange() {
    const startVal = document.getElementById('user_leave_start_date').value;
    const endInput = document.getElementById('user_leave_end_date');
    if (!endInput.value || endInput.value < startVal) {
        endInput.value = startVal;
    }
    calculateUserLeave();
}

function calculateUserLeave() {
    const typeSelect = document.getElementById('user_leave_type');
    const startInput = document.getElementById('user_leave_start_date');
    const endInput = document.getElementById('user_leave_end_date');
    const durationBadge = document.getElementById('userDurationBadge');
    const quotaBadge = document.getElementById('userQuotaRemainingBadge');
    const quotaWarning = document.getElementById('userQuotaWarning');
    const warningText = document.getElementById('userQuotaWarningText');

    if (!typeSelect || !startInput || !endInput) return;

    const leaveType = typeSelect.value;
    const policy = userPolicies[leaveType] || { remaining: 0, total: 0, unit: 'Days', name: leaveType };

    // Update Quota Box
    if (quotaBadge) {
        quotaBadge.textContent = `${parseFloat(policy.remaining).toFixed(2)} ${policy.unit || 'Days'} Available`;
        if (policy.remaining <= 0) {
            quotaBadge.className = 'badge bg-danger rounded-pill px-3 py-1';
        } else {
            quotaBadge.className = 'badge bg-success rounded-pill px-3 py-1';
        }
    }

    let days = 1.0;
    if (leaveType === 'half_day') {
        days = 0.5;
        if (durationBadge) durationBadge.textContent = '0.50 Day (Half Day Session)';
    } else if (leaveType === 'early_leave') {
        days = 1.0;
        if (durationBadge) durationBadge.textContent = '1.00 Occasion (Early Leave)';
    } else {
        const d1 = new Date(startInput.value);
        const d2 = new Date(endInput.value);
        if (!isNaN(d1) && !isNaN(d2) && d2 >= d1) {
            const diffTime = Math.abs(d2 - d1);
            days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        } else {
            days = 1.0;
        }
        if (durationBadge) durationBadge.textContent = `${days.toFixed(2)} Day(s)`;
    }

    // Check quota exceeded
    if (policy.remaining !== undefined && days > policy.remaining) {
        if (quotaWarning) {
            quotaWarning.classList.remove('d-none');
            warningText.textContent = `Warning: Requested ${days.toFixed(2)} exceeds remaining balance of ${parseFloat(policy.remaining).toFixed(2)} ${policy.unit || 'Days'}.`;
        }
    } else {
        if (quotaWarning) quotaWarning.classList.add('d-none');
    }
}

function viewPayslipModal(id) {
    fetch(`{{ url('user-hrm/salary/slip') }}/${id}`)
        .then(res => res.json())
        .then(result => {
            if (!result.success) {
                alert(result.message || 'Unable to fetch payslip.');
                return;
            }

            const data = result.data;
            document.getElementById('modalRestName').textContent = data.restaurant_name;
            document.getElementById('modalMonthName').textContent = data.month_name;
            document.getElementById('modalPaymentStatus').textContent = data.is_paid ? `PAID on ${data.paid_date}` : 'UNPAID';
            document.getElementById('modalStaffName').textContent = data.staff_name;
            document.getElementById('modalRoleType').textContent = data.role_type;
            document.getElementById('modalTotalDays').textContent = data.total_days;
            document.getElementById('modalPaidDays').textContent = data.present_days;

            document.getElementById('modalBaseSalary').textContent = '₹' + data.base_salary.toFixed(2);
            document.getElementById('modalEarnedSalary').textContent = '₹' + data.earned_salary.toFixed(2);
            document.getElementById('modalExtraEarnings').textContent = '+₹' + data.extra_earnings.toFixed(2);
            const grossCredits = data.earned_salary + data.extra_earnings;
            document.getElementById('modalTotalGross').textContent = '₹' + grossCredits.toFixed(2);

            document.getElementById('modalAbsentDeduction').textContent = '-₹' + data.absent_deduction.toFixed(2);
            document.getElementById('modalPf').textContent = '-₹' + data.pf_amount.toFixed(2);
            document.getElementById('modalEsi').textContent = '-₹' + data.esi_amount.toFixed(2);
            document.getElementById('modalOther').textContent = '-₹' + data.other_deductions.toFixed(2);
            document.getElementById('modalPenalty').textContent = '-₹' + data.extra_deductions.toFixed(2);
            document.getElementById('modalAdvance').textContent = '-₹' + data.advance_amount.toFixed(2);

            const totalDeductions = data.total_statutory + data.extra_deductions + data.advance_amount;
            document.getElementById('modalTotalDeductions').textContent = '-₹' + totalDeductions.toFixed(2);

            document.getElementById('modalFinalSalary').textContent = '₹' + data.final_salary.toFixed(2);
            document.getElementById('modalActualPayable').textContent = '₹' + data.actual_payable.toFixed(2);

            const modal = new bootstrap.Modal(document.getElementById('userPayslipModal'));
            modal.show();
        })
        .catch(err => {
            console.error(err);
            alert('Failed to load payslip data.');
        });
}

function printPayslipElement() {
    window.print();
}

// Live Digital Clock Ticker
function updateLiveClock() {
    const el = document.getElementById('liveClockDisplay');
    if (!el) return;
    const now = new Date();
    el.textContent = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
setInterval(updateLiveClock, 1000);
updateLiveClock();

// Geolocation Clock In Handler
document.addEventListener('DOMContentLoaded', function() {
    const btnClockIn = document.getElementById('btnClockIn');
    const formClockIn = document.getElementById('userClockInForm');
    if (btnClockIn && formClockIn) {
        btnClockIn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser. Please use a device with GPS support.');
                return;
            }

            btnClockIn.disabled = true;
            btnClockIn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Verifying GPS Location...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('clockin_lat').value = position.coords.latitude;
                    document.getElementById('clockin_lng').value = position.coords.longitude;
                    formClockIn.submit();
                },
                function(error) {
                    btnClockIn.disabled = false;
                    btnClockIn.innerHTML = '<i class="fas fa-sign-in-alt me-1"></i> Mark Attendance / Clock In';
                    let msg = 'Unable to capture GPS location.';
                    if (error.code === error.PERMISSION_DENIED) {
                        msg = 'Location permission denied. Please allow location access in your browser to clock in.';
                    } else if (error.code === error.TIMEOUT) {
                        msg = 'Location request timed out. Please try again.';
                    }
                    alert(msg);
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        });
    }

    // Geolocation Clock Out Handler
    const btnClockOut = document.getElementById('btnClockOut');
    const formClockOut = document.getElementById('userClockOutForm');
    if (btnClockOut && formClockOut) {
        btnClockOut.addEventListener('click', function() {
            if (!confirm('Are you sure you want to clock out and complete your duty for today?')) {
                return;
            }

            btnClockOut.disabled = true;
            btnClockOut.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Clocking Out...';

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('clockout_lat').value = position.coords.latitude;
                        document.getElementById('clockout_lng').value = position.coords.longitude;
                        formClockOut.submit();
                    },
                    function(error) {
                        // Submit without coordinates on clock out fallback
                        formClockOut.submit();
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                formClockOut.submit();
            }
        });
    }
});
</script>
@endsection
