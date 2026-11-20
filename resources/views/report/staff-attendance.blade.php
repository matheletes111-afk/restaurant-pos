@extends('layouts.app')

@section('title')
<title>Staff Attendance Report - Admin</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/report-analytics.css') }}">
<style>
  .badge-att-p {
    background-color: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .badge-att-h {
    background-color: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .badge-att-el {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .badge-att-wo {
    background-color: #f5f3ff;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .badge-wage-hourly {
    background: #f3e8ff;
    color: #7e22ce;
    border: 1px solid #d8b4fe;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 6px;
  }
  .badge-wage-permanent {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 6px;
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
  .time-pill-in {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    padding: 2px 7px;
    border-radius: 5px;
    font-size: 0.76rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }
  .time-pill-out {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 2px 7px;
    border-radius: 5px;
    font-size: 0.76rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }
  .duration-pill {
    background: #0f172a;
    color: #ffffff;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.2px;
  }
  .quick-preset-btn {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 600;
    transition: all 0.15s ease;
    cursor: pointer;
    text-decoration: none;
  }
  .quick-preset-btn:hover {
    background: #ff5e14;
    color: #ffffff;
    border-color: #ff5e14;
  }
  .report-nav-tabs .nav-link {
    font-size: 0.88rem;
    font-weight: 600;
    color: #64748b;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 10px 16px;
    border-radius: 0;
  }
  .report-nav-tabs .nav-link.active {
    color: #ff5e14;
    border-bottom: 2px solid #ff5e14;
    background: transparent;
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

        {{-- 1. Header Deck --}}
        <div class="rpt-header-deck">
            <div class="rpt-header-left">
                <div class="rpt-header-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-user-clock text-white"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">HR & Workforce Performance</span>
                    <h1 class="rpt-header-title">Staff Attendance Report</h1>
                    <p class="rpt-header-sub">Comprehensive staff attendance logs, login/logout timestamps, worked durations, and wage type classification</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('report.staff.attendance.export', [
                    'from_date' => $fromDate->format('Y-m-d'),
                    'to_date' => $toDate->format('Y-m-d'),
                    'staff_id' => $selectedStaffId,
                    'attendance_type' => $selectedAttendanceType,
                    'employee_type' => $selectedEmployeeType,
                ]) }}" class="btn-export-excel" title="Download Excel (.xlsx)">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
                <button type="button" onclick="window.print()" class="btn-rpt-secondary">
                    <i class="fas fa-print text-primary"></i> Print Report
                </button>
            </div>
        </div>

        {{-- 2. Filter Form --}}
        <div class="rpt-filter-card">
            <form method="GET" action="{{ route('report.staff.attendance') }}" id="attendanceFilterForm" class="row g-3 align-items-end">
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="far fa-calendar-alt me-1 text-primary"></i>From Date</label>
                    <input type="date" name="from_date" id="filterFromDate" value="{{ $fromDate->format('Y-m-d') }}" class="rpt-filter-control" required>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="far fa-calendar-alt me-1 text-primary"></i>To Date</label>
                    <input type="date" name="to_date" id="filterToDate" value="{{ $toDate->format('Y-m-d') }}" class="rpt-filter-control" required>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="fas fa-user me-1 text-primary"></i>Staff Member</label>
                    <select name="staff_id" class="rpt-filter-control">
                        <option value="all">All Staff Members ({{ $staffList->count() }})</option>
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ (string)$selectedStaffId === (string)$staff->id ? 'selected' : '' }}>
                                {{ $staff->name }} ({{ $staff->role_type ?: 'STAFF' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="fas fa-clipboard-check me-1 text-primary"></i>Status</label>
                    <select name="attendance_type" class="rpt-filter-control">
                        <option value="all" {{ $selectedAttendanceType === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="P" {{ $selectedAttendanceType === 'P' ? 'selected' : '' }}>Full Day Present</option>
                        <option value="H" {{ $selectedAttendanceType === 'H' ? 'selected' : '' }}>Half Day</option>
                        <option value="EL" {{ $selectedAttendanceType === 'EL' ? 'selected' : '' }}>Early Leave</option>
                        <option value="WO" {{ $selectedAttendanceType === 'WO' ? 'selected' : '' }}>Weekoff</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="fas fa-briefcase me-1 text-primary"></i>Wage Type</label>
                    <select name="employee_type" class="rpt-filter-control">
                        <option value="all" {{ $selectedEmployeeType === 'all' ? 'selected' : '' }}>All Wage Types</option>
                        <option value="permanent" {{ $selectedEmployeeType === 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="hourly" {{ $selectedEmployeeType === 'hourly' ? 'selected' : '' }}>Hourly Wage</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn-rpt-primary flex-fill" title="Filter Records">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('report.staff.attendance') }}" class="btn-rpt-secondary" title="Reset to Current Month">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>

            {{-- Quick Date Presets --}}
            <div class="d-flex align-items-center gap-2 mt-3 pt-2 border-top flex-wrap">
                <span class="text-muted small fw-semibold"><i class="fas fa-bolt text-warning me-1"></i>Quick Ranges:</span>
                <button type="button" class="quick-preset-btn" onclick="applyPreset('this_month')">This Month</button>
                <button type="button" class="quick-preset-btn" onclick="applyPreset('last_month')">Last Month</button>
                <button type="button" class="quick-preset-btn" onclick="applyPreset('last_30_days')">Last 30 Days</button>
                <button type="button" class="quick-preset-btn" onclick="applyPreset('today')">Today</button>
            </div>
        </div>

        {{-- 3. Summary KPI Deck --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-info">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Report Period</span>
                    <span class="rpt-stat-val" style="font-size: 1.05rem;">{{ $summary['date_range'] }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-primary">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Attendance Records</span>
                    <span class="rpt-stat-val">{{ number_format($summary['total_records']) }} <small class="fs-6 text-muted">logs</small></span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Full Day Present</span>
                    <span class="rpt-stat-val">{{ number_format($summary['present_count']) }} <small class="fs-6 text-muted">days</small></span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Half Days & Early Leaves</span>
                    <span class="rpt-stat-val">{{ number_format($summary['half_day_count'] + $summary['early_leave_count']) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-indigo">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Week Offs</span>
                    <span class="rpt-stat-val">{{ number_format($summary['weekoff_count']) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-calendar-minus"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-purple">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Duty Hours Logged</span>
                    <span class="rpt-stat-val">{{ number_format($summary['total_hours'], 1) }} <small class="fs-6 text-muted">hrs</small></span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-business-time"></i>
                </div>
            </div>
        </div>

        {{-- 4. Content Tabs: Day-wise Log & Staff Summary --}}
        <div class="rpt-table-card">
            <div class="border-bottom px-3 pt-2">
                <ul class="nav nav-tabs report-nav-tabs" id="attendanceReportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="daywise-tab" data-bs-toggle="tab" data-bs-target="#daywise-pane" type="button" role="tab" aria-selected="true">
                            <i class="fas fa-list me-1"></i>Day-wise Attendance Log ({{ $attendances->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary-pane" type="button" role="tab" aria-selected="false">
                            <i class="fas fa-users-cog me-1"></i>Staff-wise Summary ({{ $staffSummaries->count() }})
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="attendanceReportTabsContent">
                {{-- TAB 1: Day-wise Attendance Log --}}
                <div class="tab-pane fade show active p-3" id="daywise-pane" role="tabpanel" aria-labelledby="daywise-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-table text-primary me-2"></i>Detailed Attendance Entries</h6>
                            <span class="text-muted small">Individual check-in & check-out logs for selected dates</span>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-user-friends me-1 text-primary"></i>{{ $summary['unique_staff_count'] }} Active Staff</span>
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-clock me-1 text-purple"></i>{{ number_format($summary['total_hours'], 2) }} Total Hours</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table rpt-table mb-0 align-middle" id="daywiseAttendanceTable">
                            <thead>
                                <tr>
                                    <th style="width: 45px;">#</th>
                                    <th>Attendance Date</th>
                                    <th>Staff Member</th>
                                    <th>Wage Type</th>
                                    <th>Attendance Status</th>
                                    <th>Check-In</th>
                                    <th>Check-Out</th>
                                    <th>Worked Duration</th>
                                    <th>Hours (Dec)</th>
                                    <th>Marked By</th>
                                    <th>Notes / Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $idx => $att)
                                    @php
                                        $user = $att->user;
                                        $attDate = $att->attendance_date ? \Carbon\Carbon::parse($att->attendance_date) : null;
                                        $hrs = (float) $att->worked_hours_decimal;
                                    @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            @if($attDate)
                                                <div class="fw-bold text-dark">{{ $attDate->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $attDate->format('l') }}</small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $user ? $user->name : 'N/A' }}</div>
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">{{ $user ? ($user->role_type ?: 'STAFF') : '—' }}</span>
                                                @if($user && $user->phone)
                                                    <small class="text-muted">{{ $user->phone }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($att->employee_type === 'hourly')
                                                <span class="badge-wage-hourly"><i class="fas fa-stopwatch me-1"></i>Hourly Wage</span>
                                            @else
                                                <span class="badge-wage-permanent"><i class="fas fa-id-badge me-1"></i>Permanent</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($att->attendance_type === 'P')
                                                <span class="badge-att-p"><i class="fas fa-check-circle"></i> Full Present</span>
                                            @elseif($att->attendance_type === 'H')
                                                <span class="badge-att-h"><i class="fas fa-adjust"></i> Half Day</span>
                                            @elseif($att->attendance_type === 'EL')
                                                <span class="badge-att-el"><i class="fas fa-running"></i> Early Leave</span>
                                            @elseif($att->attendance_type === 'WO')
                                                <span class="badge-att-wo"><i class="fas fa-coffee"></i> Weekoff</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $att->attendance_type }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($att->login_time)
                                                <span class="time-pill-in"><i class="fas fa-sign-in-alt text-success"></i> {{ $att->formatted_login_time }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($att->logout_time)
                                                <span class="time-pill-out"><i class="fas fa-sign-out-alt text-secondary"></i> {{ $att->formatted_logout_time }}</span>
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
                                        <td class="fw-semibold text-dark">{{ number_format($hrs, 2) }}</td>
                                        <td>
                                            <small class="text-muted">{{ $att->creator ? $att->creator->name : 'System' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $att->notes ?: '—' }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-5 text-muted">
                                            <i class="fas fa-calendar-times fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                            <h6 class="fw-semibold">No attendance records found for this period</h6>
                                            <p class="small text-muted mb-0">Try expanding your date range or selecting "All Staff Members".</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($attendances->isNotEmpty())
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="7" class="text-end">TOTAL:</td>
                                    <td><span class="duration-pill">{{ number_format($summary['total_hours'], 1) }} hrs</span></td>
                                    <td class="text-dark">{{ number_format($summary['total_hours'], 2) }}</td>
                                    <td colspan="2">({{ $attendances->count() }} records)</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- TAB 2: Staff-wise Summary Breakdown --}}
                <div class="tab-pane fade p-3" id="summary-pane" role="tabpanel" aria-labelledby="summary-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-user-tag text-primary me-2"></i>Staff Consolidated Breakdown</h6>
                            <span class="text-muted small">Aggregated attendance tally and duty hours per employee for the period</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table rpt-table mb-0 align-middle" id="summaryStaffTable">
                            <thead>
                                <tr>
                                    <th style="width: 45px;">#</th>
                                    <th>Staff Name</th>
                                    <th>Role / Designation</th>
                                    <th>Phone</th>
                                    <th>Wage Type</th>
                                    <th class="text-center">Total Logged Days</th>
                                    <th class="text-center">Full Present</th>
                                    <th class="text-center">Half Days</th>
                                    <th class="text-center">Early Leaves</th>
                                    <th class="text-center">Week Offs</th>
                                    <th class="text-end">Total Hours Worked</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffSummaries as $sIdx => $st)
                                    <tr>
                                        <td>{{ $sIdx + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $st['name'] }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border">{{ $st['role_type'] }}</span>
                                        </td>
                                        <td class="text-muted">{{ $st['phone'] }}</td>
                                        <td>
                                            @if($st['wage_type'] === 'Hourly Wage')
                                                <span class="badge-wage-hourly">Hourly Wage</span>
                                            @else
                                                <span class="badge-wage-permanent">Permanent</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold">{{ $st['total_days'] }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">{{ $st['present_days'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($st['half_days'] > 0)
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fw-bold">{{ $st['half_days'] }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($st['early_leaves'] > 0)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fw-bold">{{ $st['early_leaves'] }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($st['weekoffs'] > 0)
                                                <span class="badge bg-secondary-subtle text-dark border px-2 py-1 fw-bold">{{ $st['weekoffs'] }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($st['total_hours'], 2) }} hrs</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-5 text-muted">
                                            <i class="fas fa-users-slash fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                            <h6 class="fw-semibold">No staff attendance summary available</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    if ($('#daywiseAttendanceTable tbody tr td').length > 1) {
        $('#daywiseAttendanceTable').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[1, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search attendance records...",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    }

    if ($('#summaryStaffTable tbody tr td').length > 1) {
        $('#summaryStaffTable').DataTable({
            pageLength: 25,
            order: [[5, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search staff summary...",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    }
});

function applyPreset(preset) {
    const today = new Date();
    const fromInput = document.getElementById('filterFromDate');
    const toInput = document.getElementById('filterToDate');
    
    function formatDate(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    if (preset === 'this_month') {
        const start = new Date(today.getFullYear(), today.getMonth(), 1);
        fromInput.value = formatDate(start);
        toInput.value = formatDate(today);
    } else if (preset === 'last_month') {
        const start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const end = new Date(today.getFullYear(), today.getMonth(), 0);
        fromInput.value = formatDate(start);
        toInput.value = formatDate(end);
    } else if (preset === 'last_30_days') {
        const start = new Date();
        start.setDate(today.getDate() - 30);
        fromInput.value = formatDate(start);
        toInput.value = formatDate(today);
    } else if (preset === 'today') {
        fromInput.value = formatDate(today);
        toInput.value = formatDate(today);
    }

    document.getElementById('attendanceFilterForm').submit();
}
</script>
@endsection
