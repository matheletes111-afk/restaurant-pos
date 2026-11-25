@extends('layouts.app')

@section('title')
<title>Admin || Staff Leave Management</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/restaurant-staff.css') }}">
<style>
    .leave-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .leave-stat-card {
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
    .leave-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .leave-stat-label {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .leave-stat-num {
        font-family: 'Outfit', sans-serif;
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }
    .leave-stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .quota-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }
    .quota-badge-pill.has-balance {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #15803d;
    }
    .quota-badge-pill.zero-balance {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }
    .leave-row-cancelled {
        background-color: #fafafa !important;
        opacity: 0.92;
    }
    .leave-row-cancelled td {
        color: #64748b;
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
                <div class="staff-header-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
                    <i class="fas fa-calendar-minus text-white"></i>
                </div>
                <div class="staff-header-title-meta">
                    <span class="staff-header-eyebrow">Staff Operations &amp; HR Leave Management</span>
                    <h1 class="staff-header-title">Staff Leave Management</h1>
                    <p class="staff-header-sub">Apply leaves, check annual quota limits, cancel entries with remarks &amp; audit history</p>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                @if(isset($availableOutlets) && ($availableOutlets->count() > 1 || (auth()->user() && auth()->user()->role === 'SA')))
                {{-- Outlet / Restaurant Switcher --}}
                <form action="{{ route('restaurant.leaves.index') }}" method="GET" class="d-flex align-items-center gap-1" id="outletFilterForm">
                    <input type="hidden" name="from_date" value="{{ $fromDate }}">
                    <input type="hidden" name="to_date" value="{{ $toDate }}">
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

                {{-- Date Range Filter Form --}}
                <form action="{{ route('restaurant.leaves.index') }}" method="GET" class="d-flex align-items-center gap-1 flex-wrap" id="dateRangeForm">
                    @if(request('restaurant_id'))
                        <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                    @elseif(isset($selectedOutletId))
                        <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                    @endif
                    <div class="d-flex align-items-center gap-1 bg-white p-1 rounded border shadow-sm">
                        <span class="text-muted small ps-1 fw-bold">From:</span>
                        <input type="date" name="from_date" id="from_date" class="form-control form-control-sm border-0 fw-bold" value="{{ $fromDate }}" style="width: 135px;" onchange="document.getElementById('dateRangeForm').submit()">
                        <span class="text-muted small fw-bold">To:</span>
                        <input type="date" name="to_date" id="to_date" class="form-control form-control-sm border-0 fw-bold" value="{{ $toDate }}" style="width: 135px;" onchange="document.getElementById('dateRangeForm').submit()">
                    </div>
                    {{-- Quick Current Month Jump --}}
                    @if($fromDate !== date('Y-m-01') || $toDate !== date('Y-m-t'))
                    <a href="{{ route('restaurant.leaves.index', array_filter(['from_date' => date('Y-m-01'), 'to_date' => date('Y-m-t'), 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="btn btn-sm btn-outline-primary" title="Jump to Current Month">
                        This Month
                    </a>
                    @endif
                </form>

                {{-- Download Excel --}}
                <a href="{{ route('restaurant.leaves.export', array_filter(['from_date' => $fromDate, 'to_date' => $toDate, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="btn btn-outline-success btn-sm rounded-pill px-3" style="font-weight: 600;">
                    <i class="fas fa-file-excel me-1"></i> Download Excel
                </a>

                {{-- Attendance Link --}}
                <a href="{{ route('restaurant.attendance.index', array_filter(['restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="btn-staff-secondary btn-sm">
                    <i class="fas fa-user-clock me-1"></i> Attendance
                </a>
            </div>
        </div>

        {{-- 2. Stats KPI Grid --}}
        <div class="leave-stat-grid">
            <div class="leave-stat-card">
                <div>
                    <div class="leave-stat-label">Total Applications</div>
                    <div class="leave-stat-num">{{ $stats['total_applications'] }}</div>
                </div>
                <div class="leave-stat-icon-wrap" style="background: #f1f5f9; color: #475569;">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <a href="{{ route('restaurant.leaves.index', array_filter(['status' => 'pending', 'from_date' => $fromDate, 'to_date' => $toDate, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" class="text-decoration-none">
                <div class="leave-stat-card" style="{{ ($statusFilter ?? 'all') === 'pending' ? 'background: #fffbeb; border: 2px solid #f59e0b !important;' : '' }}">
                    <div>
                        <div class="leave-stat-label text-warning">Pending Requests</div>
                        <div class="leave-stat-num text-warning">{{ $stats['pending_count'] ?? 0 }}</div>
                    </div>
                    <div class="leave-stat-icon-wrap" style="background: #fef3c7; color: #b45309;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </a>
            <div class="leave-stat-card">
                <div>
                    <div class="leave-stat-label">Approved Leaves</div>
                    <div class="leave-stat-num text-success">{{ $stats['approved_count'] }}</div>
                </div>
                <div class="leave-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="leave-stat-card">
                <div>
                    <div class="leave-stat-label">Total Days Taken</div>
                    <div class="leave-stat-num text-primary">{{ number_format($stats['total_days_taken'], 1) }}</div>
                </div>
                <div class="leave-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="leave-stat-card">
                <div>
                    <div class="leave-stat-label">Cancelled / Deleted</div>
                    <div class="leave-stat-num text-danger">{{ ($stats['cancelled_count'] ?? 0) + ($stats['rejected_count'] ?? 0) }}</div>
                </div>
                <div class="leave-stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-ban"></i>
                </div>
            </div>
            <div class="leave-stat-card">
                <div>
                    <div class="leave-stat-label">Staff on Leave</div>
                    <div class="leave-stat-num text-info">{{ $stats['distinct_staff'] }}</div>
                </div>
                <div class="leave-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        {{-- 3. Apply / Record Leave Form Card --}}
        <div class="bg-white rounded-3 p-4 mb-4 border shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: #0f172a;">
                        <i class="fas fa-calendar-plus text-primary me-2"></i>Apply / Record Staff Leave
                    </h5>
                    <small class="text-muted">Validate leave duration against staff's unused policy quota before booking</small>
                </div>
                <span class="badge bg-light text-dark border px-3 py-2">
                    <i class="fas fa-calendar-alt text-primary me-1"></i> Active Range: {{ date('d M Y', strtotime($fromDate)) }} — {{ date('d M Y', strtotime($toDate)) }}
                </span>
            </div>

            <form action="{{ route('restaurant.leaves.store') }}" method="POST" id="leaveApplicationForm">
                @csrf
                <input type="hidden" name="from_date" value="{{ $fromDate }}">
                <input type="hidden" name="to_date" value="{{ $toDate }}">
                @if(request('restaurant_id'))
                    <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                @elseif(isset($selectedOutletId))
                    <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                @endif

                <div class="row g-3">
                    {{-- 1. Select Staff Member --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Select Staff Member <span class="text-danger">*</span>
                        </label>
                        <select name="user_id" id="leave_user_id" class="form-select" required onchange="onStaffSelected()">
                            <option value="">-- Choose Staff Member --</option>
                            @forelse($staffList as $staff)
                                <option value="{{ $staff->id }}" {{ old('user_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ $staff->role_type }}){{ (isset($availableOutlets) && $availableOutlets->count() > 1 && $staff->restaurant) ? ' — [' . $staff->restaurant->name . ']' : '' }}
                                </option>
                            @empty
                                <option value="" disabled>No staff members found for this restaurant</option>
                            @endforelse
                        </select>
                        @if($staffList->isEmpty())
                            <div class="form-text text-danger mt-1">
                                <i class="fas fa-exclamation-circle me-1"></i> No staff found. <a href="{{ route('restaurant.staff.index') }}" class="fw-bold text-decoration-underline">Add staff in Staff Management</a>.
                            </div>
                        @endif
                    </div>

                    {{-- 2. Select Leave Type --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Leave Type Selection <span class="text-danger">*</span>
                        </label>
                        <select name="leave_type" id="leave_type" class="form-select" required onchange="calculateDurationAndQuota()">
                            <option value="">-- Choose Leave Type --</option>
                            @foreach($leaveDefinitions as $typeKey => $def)
                                <option value="{{ $typeKey }}" {{ old('leave_type', 'sick_leave') === $typeKey ? 'selected' : '' }}>
                                    {{ $def['name'] }} ({{ $def['code'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Live Quota Balance Banner --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Available Leave Quota
                        </label>
                        <div id="quotaDisplayBox" class="p-2 rounded border bg-light d-flex align-items-center justify-content-between" style="min-height: 38px;">
                            <span class="text-muted small" id="quotaStatusText">Select a staff member to view available quota</span>
                            <span class="badge bg-primary rounded-pill px-3 py-1 d-none" id="quotaBadgeNumber">0 Days Left</span>
                        </div>
                    </div>

                    {{-- 4. Start Date --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Leave Start Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="start_date" id="leave_start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required onchange="syncEndDate()">
                    </div>

                    {{-- 5. End Date --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Leave End Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="end_date" id="leave_end_date" class="form-control" value="{{ old('end_date', date('Y-m-d')) }}" required onchange="calculateDurationAndQuota()">
                    </div>

                    {{-- 6. Calculated Duration Preview --}}
                    <div class="col-md-2 d-flex flex-column justify-content-end">
                        <div class="p-2 rounded border bg-light text-center" style="height: 38px;">
                            <span class="fw-bold text-dark small" id="durationBadge">1.00 Day(s)</span>
                        </div>
                    </div>

                    {{-- 7. Reason / Remarks --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Leave Reason / Remarks <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <input type="text" name="reason" id="leave_reason" class="form-control" placeholder="E.g. Medical recovery, urgent family event..." value="{{ old('reason') }}" maxlength="1000">
                    </div>

                    {{-- Dynamic Warning Alert if requested days > available leave --}}
                    <div class="col-12 d-none" id="quotaExceededAlert">
                        <div class="alert alert-danger py-2 px-3 mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                            <div>
                                <strong id="quotaExceededMessage">Cannot be done: Requested days exceed unused leave balance.</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="col-12 text-end">
                        <button type="submit" class="btn rounded-pill text-white px-4" id="submitLeaveBtn" style="background: #4f46e5; border-color: #4f46e5; font-weight: 600; height: 42px;">
                            <i class="fas fa-paper-plane me-1"></i> Apply &amp; Record Leave
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 4. Leave Records Table --}}
        <div class="bg-white rounded-3 p-4 border shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: #0f172a;">
                        <i class="fas fa-list text-primary me-2"></i>Leave Applications &amp; Requests — {{ date('d M Y', strtotime($fromDate)) }} to {{ date('d M Y', strtotime($toDate)) }}
                    </h5>
                    <small class="text-muted">Showing {{ $leaves->count() }} leave applications. Manage approvals, edits, and cancellations seamlessly.</small>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @php $currStatus = $statusFilter ?? 'all'; @endphp
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('restaurant.leaves.index', array_filter(['status' => 'all', 'from_date' => $fromDate, 'to_date' => $toDate, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                           class="btn {{ $currStatus === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            All ({{ $stats['total_applications'] }})
                        </a>
                        <a href="{{ route('restaurant.leaves.index', array_filter(['status' => 'pending', 'from_date' => $fromDate, 'to_date' => $toDate, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                           class="btn {{ $currStatus === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                            Pending ({{ $stats['pending_count'] }})
                        </a>
                        <a href="{{ route('restaurant.leaves.index', array_filter(['status' => 'approved', 'from_date' => $fromDate, 'to_date' => $toDate, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                           class="btn {{ $currStatus === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                            Approved ({{ $stats['approved_count'] }})
                        </a>
                        <a href="{{ route('restaurant.leaves.index', array_filter(['status' => 'rejected', 'from_date' => $fromDate, 'to_date' => $toDate, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                           class="btn {{ $currStatus === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                            Rejected ({{ $stats['rejected_count'] }})
                        </a>
                        <a href="{{ route('restaurant.leaves.index', array_filter(['status' => 'cancelled', 'from_date' => $fromDate, 'to_date' => $toDate, 'restaurant_id' => request('restaurant_id', $selectedOutletId ?? null)])) }}" 
                           class="btn {{ $currStatus === 'cancelled' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                            Cancelled ({{ $stats['cancelled_count'] }})
                        </a>
                    </div>
                </div>
            </div>

            @if($leaves->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3" style="width: 64px; height: 64px; margin: 0 auto; border-radius: 50%; background: #f8fafc; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94a3b8;">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No Leave Records Found</h6>
                    <p class="text-muted small mb-0">No records match the current filter criteria between {{ date('d M Y', strtotime($fromDate)) }} and {{ date('d M Y', strtotime($toDate)) }}.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45px;" class="text-center">#</th>
                                <th style="min-width: 220px;">Staff Member</th>
                                <th style="min-width: 140px;">Leave Type</th>
                                <th style="min-width: 170px;">Date Range</th>
                                <th style="min-width: 100px;" class="text-center">Duration</th>
                                <th style="min-width: 180px;">Reason &amp; Remarks</th>
                                <th style="min-width: 140px;" class="text-center">Status</th>
                                <th style="min-width: 170px;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $idx => $leave)
                                @php
                                    $staff = $leave->user;
                                    $isCancelled = ($leave->status === 'cancelled');
                                    $isPending = ($leave->status === 'pending');
                                    $isApproved = ($leave->status === 'approved');
                                    $isRejected = ($leave->status === 'rejected');

                                    $initials = '';
                                    if ($staff) {
                                        $parts = explode(' ', trim($staff->name));
                                        foreach(array_slice($parts, 0, 2) as $p) {
                                            if (!empty($p)) $initials .= strtoupper($p[0]);
                                        }
                                    }
                                    if (empty($initials)) $initials = 'ST';
                                @endphp
                                <tr class="{{ $isCancelled ? 'leave-row-cancelled' : '' }}" style="{{ $isPending ? 'background-color: #fffdf5;' : '' }}">
                                    <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 38px; height: 38px; border-radius: 10px; background: {{ $isCancelled ? '#94a3b8' : ($isPending ? '#f59e0b' : '#4f46e5') }}; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <div class="fw-bold {{ $isCancelled ? 'text-decoration-line-through text-muted' : 'text-dark' }}" style="font-size: 0.92rem;">
                                                    {{ $staff ? $staff->name : 'Deleted Staff' }}
                                                </div>
                                                <div class="text-muted small" style="font-size: 0.76rem;">
                                                    <span class="badge bg-light text-secondary border py-0 px-1">{{ $staff ? $staff->role_type : 'N/A' }}</span>
                                                    @if($staff && $staff->restaurant)
                                                        <span>• <i class="fas fa-store"></i> {{ $staff->restaurant->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge px-3 py-1 fw-bold" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;">
                                            <i class="{{ $leave->leave_type_icon }} me-1"></i> {{ $leave->leave_type_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark small">
                                            <i class="fas fa-calendar-alt text-primary me-1"></i>{{ $leave->formatted_date_range }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 0.82rem;">
                                            {{ number_format($leave->days_count, 1) }} Day(s)
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small text-dark">{{ $leave->reason ?: 'No reason stated' }}</div>
                                        @if(!empty($leave->admin_remarks))
                                            <div class="p-2 mt-1 rounded border border-warning-subtle bg-warning-subtle text-dark small" style="font-size: 0.76rem;">
                                                <i class="fas fa-comment-dots text-warning me-1"></i><strong>Admin Remarks:</strong> {{ $leave->admin_remarks }}
                                            </div>
                                        @endif
                                        @if($isCancelled && !empty($leave->cancellation_remarks))
                                            <div class="p-2 mt-1 rounded border border-danger-subtle bg-danger-subtle text-danger small">
                                                <i class="fas fa-comment-slash me-1 fw-bold"></i><strong>Cancellation Remarks:</strong> {{ $leave->cancellation_remarks }}
                                                <div class="text-muted" style="font-size: 0.72rem;">
                                                    Cancelled by {{ $leave->canceller ? $leave->canceller->name : 'Admin' }} on {{ $leave->cancelled_at ? $leave->cancelled_at->format('d M Y h:i A') : '-' }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($isApproved)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold">
                                                <i class="fas fa-check-circle me-1"></i> Approved
                                            </span>
                                            @if($leave->approver)
                                                <div class="text-muted small mt-1" style="font-size: 0.68rem;">
                                                    By {{ $leave->approver->name }}
                                                </div>
                                            @endif
                                        @elseif($isPending)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 fw-bold">
                                                <i class="fas fa-hourglass-half me-1"></i> Pending Approval
                                            </span>
                                        @elseif($isRejected)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-bold">
                                                <i class="fas fa-times-circle me-1"></i> Rejected
                                            </span>
                                        @elseif($isCancelled)
                                            <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 fw-bold">
                                                <i class="fas fa-ban me-1"></i> Cancelled / Deleted
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1 flex-wrap">
                                            @if($isPending)
                                                {{-- Accept / Approve Button --}}
                                                <button type="button" 
                                                        class="btn btn-success btn-sm rounded-pill btn-approve-leave px-2 py-1" 
                                                        data-id="{{ $leave->id }}"
                                                        data-staff="{{ $staff ? $staff->name : 'Staff' }}"
                                                        data-days="{{ $leave->days_count }}"
                                                        data-type="{{ $leave->leave_type_name }}"
                                                        title="Accept & Deduct Quota">
                                                    <i class="fas fa-check me-1"></i> Accept
                                                </button>

                                                {{-- Reject Button --}}
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm rounded-pill btn-reject-leave px-2 py-1" 
                                                        data-id="{{ $leave->id }}"
                                                        data-staff="{{ $staff ? $staff->name : 'Staff' }}"
                                                        title="Reject Request">
                                                    <i class="fas fa-times me-1"></i> Reject
                                                </button>
                                            @endif

                                            {{-- Edit Button (Accessible for pending, approved, rejected, cancelled) --}}
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm rounded-pill btn-edit-leave px-2 py-1" 
                                                    data-id="{{ $leave->id }}"
                                                    title="Edit Leave Application">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </button>

                                            @if($isApproved)
                                                {{-- Cancel / Delete Button --}}
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm rounded-pill btn-cancel-leave px-2 py-1" 
                                                        data-id="{{ $leave->id }}"
                                                        data-staff="{{ $staff ? $staff->name : 'Staff' }}"
                                                        data-days="{{ $leave->days_count }}"
                                                        data-type="{{ $leave->leave_type_name }}"
                                                        title="Cancel / Delete & Restore Quota">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                            @endif
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

{{-- 5. Cancel / Delete Leave Modal --}}
<div class="modal fade" id="cancelLeaveModal" tabindex="-1" aria-labelledby="cancelLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="cancelLeaveForm">
                @csrf
                <input type="hidden" name="from_date" value="{{ $fromDate }}">
                <input type="hidden" name="to_date" value="{{ $toDate }}">
                @if(request('restaurant_id'))
                    <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                @elseif(isset($selectedOutletId))
                    <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                @endif

                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger" id="cancelLeaveModalLabel">
                        <i class="fas fa-trash-alt me-2"></i>Delete / Cancel Leave
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning small mb-3">
                        <i class="fas fa-info-circle me-1"></i> 
                        Cancelling will restore <strong id="modalLeaveDays"></strong> day(s) back to <strong id="modalStaffName"></strong>'s annual leave policy balance. The record will remain visible in the history log with your remarks.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">
                            Cancellation Remarks / Reason <span class="text-danger">*</span>
                        </label>
                        <textarea name="cancellation_remarks" id="cancellation_remarks" class="form-control" rows="3" required maxlength="500" placeholder="Please enter remarks e.g. Staff cancelled vacation, personal emergency resolved, mistaken entry, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4" style="font-weight: 600;">
                        <i class="fas fa-check me-1"></i> Confirm Cancellation &amp; Restore Quota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 6. Approve Leave Request Modal --}}
<div class="modal fade" id="approveLeaveModal" tabindex="-1" aria-labelledby="approveLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="approveLeaveForm">
                @csrf
                <input type="hidden" name="from_date" value="{{ $fromDate }}">
                <input type="hidden" name="to_date" value="{{ $toDate }}">
                @if(request('restaurant_id'))
                    <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                @elseif(isset($selectedOutletId))
                    <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                @endif

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold text-white" id="approveLeaveModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Accept &amp; Approve Leave Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-success small mb-3">
                        <i class="fas fa-info-circle me-1"></i> 
                        Accepting will change status to <strong>Approved</strong> and deduct <strong id="approveModalDays"></strong> day(s) (<strong id="approveModalType"></strong>) from <strong id="approveModalStaff"></strong>'s annual leave quota balance.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">
                            Approval Remarks / Notes <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <input type="text" name="admin_remarks" id="approve_admin_remarks" class="form-control" placeholder="E.g. Approved by Branch Manager, coverage arranged...">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="fas fa-check me-1"></i> Accept &amp; Deduct Quota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 7. Reject Leave Request Modal --}}
<div class="modal fade" id="rejectLeaveModal" tabindex="-1" aria-labelledby="rejectLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="rejectLeaveForm">
                @csrf
                <input type="hidden" name="from_date" value="{{ $fromDate }}">
                <input type="hidden" name="to_date" value="{{ $toDate }}">
                @if(request('restaurant_id'))
                    <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                @elseif(isset($selectedOutletId))
                    <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                @endif

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold text-white" id="rejectLeaveModalLabel">
                        <i class="fas fa-times-circle me-2"></i>Reject Leave Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-danger small mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i> 
                        Rejecting leave request for <strong id="rejectModalStaff"></strong>. No quota will be deducted from their balance.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">
                            Rejection Reason / Remarks <span class="text-danger">*</span>
                        </label>
                        <textarea name="admin_remarks" id="reject_admin_remarks" class="form-control" rows="3" required placeholder="Specify why the leave request is rejected (e.g. Peak business hours, inadequate shift coverage, etc.)"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="fas fa-times me-1"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 8. Edit Leave Application Modal --}}
<div class="modal fade" id="editLeaveModal" tabindex="-1" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="" method="POST" id="editLeaveForm">
                @csrf
                <input type="hidden" name="from_date" value="{{ $fromDate }}">
                <input type="hidden" name="to_date" value="{{ $toDate }}">
                @if(request('restaurant_id'))
                    <input type="hidden" name="restaurant_id" value="{{ request('restaurant_id') }}">
                @elseif(isset($selectedOutletId))
                    <input type="hidden" name="restaurant_id" value="{{ $selectedOutletId }}">
                @endif

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="editLeaveModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Leave Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Staff Member</label>
                            <input type="text" id="edit_staff_name" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Leave Type <span class="text-danger">*</span></label>
                            <select name="leave_type" id="edit_leave_type" class="form-select" required>
                                @foreach($leaveDefinitions as $typeKey => $def)
                                    <option value="{{ $typeKey }}">{{ $def['name'] }} ({{ $def['code'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-select fw-bold" required>
                                <option value="pending" class="text-warning">Pending Approval</option>
                                <option value="approved" class="text-success">Approved</option>
                                <option value="rejected" class="text-danger">Rejected</option>
                                <option value="cancelled" class="text-secondary">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Reason / Remarks from Staff</label>
                            <textarea name="reason" id="edit_reason" rows="2" class="form-control" placeholder="Staff reason..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Admin Remarks / Notes</label>
                            <textarea name="admin_remarks" id="edit_admin_remarks" rows="2" class="form-control" placeholder="Manager/Admin remarks..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fas fa-save me-1"></i> Update Leave Record
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
    // Staff policy balances injected from controller
    const staffPolicyMap = @json($staffPolicyMap ?? []);

    function onStaffSelected() {
        calculateDurationAndQuota();
    }

    function syncEndDate() {
        const start = document.getElementById('leave_start_date').value;
        const endInput = document.getElementById('leave_end_date');
        if (start && (!endInput.value || endInput.value < start)) {
            endInput.value = start;
        }
        calculateDurationAndQuota();
    }

    function calculateDurationAndQuota() {
        const staffSelect = document.getElementById('leave_user_id');
        const staffId = staffSelect ? staffSelect.value : null;
        const leaveTypeSelect = document.getElementById('leave_type');
        const leaveType = leaveTypeSelect ? leaveTypeSelect.value : null;
        const startInput = document.getElementById('leave_start_date');
        const endInput = document.getElementById('leave_end_date');
        const durationBadge = document.getElementById('durationBadge');
        const quotaDisplayBox = document.getElementById('quotaDisplayBox');
        const quotaStatusText = document.getElementById('quotaStatusText');
        const quotaBadgeNumber = document.getElementById('quotaBadgeNumber');
        const alertBox = document.getElementById('quotaExceededAlert');
        const alertMsg = document.getElementById('quotaExceededMessage');
        const submitBtn = document.getElementById('submitLeaveBtn');

        if (!startInput.value || !endInput.value) {
            return;
        }

        // Calculate days
        let days = 1.0;
        if (leaveType === 'half_day') {
            days = 0.50;
        } else if (leaveType === 'early_leave') {
            days = 1.00;
        } else {
            const d1 = new Date(startInput.value);
            const d2 = new Date(endInput.value);
            if (d2 >= d1) {
                const diffTime = Math.abs(d2 - d1);
                days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            }
        }

        if (durationBadge) {
            durationBadge.textContent = days.toFixed(1) + ' Day(s)';
        }

        // Check staff quota
        if (staffId && staffPolicyMap[staffId] && leaveType && staffPolicyMap[staffId][leaveType]) {
            const quotaInfo = staffPolicyMap[staffId][leaveType];
            const remaining = parseFloat(quotaInfo.remaining || 0);

            quotaStatusText.textContent = quotaInfo.name + ' Quota:';
            quotaBadgeNumber.textContent = remaining.toFixed(1) + ' ' + (quotaInfo.unit || 'Days') + ' Left';
            quotaBadgeNumber.classList.remove('d-none');

            if (remaining > 0) {
                quotaBadgeNumber.className = 'badge bg-success rounded-pill px-3 py-1';
            } else {
                quotaBadgeNumber.className = 'badge bg-danger rounded-pill px-3 py-1';
            }

            // If requested days exceed remaining quota, show error immediately
            if (days > remaining) {
                if (alertBox) alertBox.classList.remove('d-none');
                if (alertMsg) alertMsg.textContent = 'Cannot be done: Requested ' + days.toFixed(1) + ' day(s), but only ' + remaining.toFixed(1) + ' day(s) unused leave available for ' + quotaInfo.name + '.';
                if (submitBtn) {
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-secondary');
                }
            } else {
                if (alertBox) alertBox.classList.add('d-none');
                if (submitBtn) {
                    submitBtn.classList.remove('btn-secondary');
                    submitBtn.classList.add('btn-primary');
                }
            }
        } else if (staffId) {
            quotaStatusText.textContent = 'Policy quota not configured for this leave type';
            quotaBadgeNumber.textContent = '0 Left';
            quotaBadgeNumber.className = 'badge bg-secondary rounded-pill px-3 py-1';
            quotaBadgeNumber.classList.remove('d-none');
            if (alertBox) alertBox.classList.remove('d-none');
            if (alertMsg) alertMsg.textContent = 'Cannot be done: No leave policy configured for this staff member and leave type. Please configure leave policy first.';
        } else {
            quotaStatusText.textContent = 'Select a staff member to view available quota';
            quotaBadgeNumber.classList.add('d-none');
            if (alertBox) alertBox.classList.add('d-none');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        calculateDurationAndQuota();

        // 1. Cancellation Modal Handler
        const cancelButtons = document.querySelectorAll('.btn-cancel-leave');
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelLeaveModal'));
        const cancelForm = document.getElementById('cancelLeaveForm');

        cancelButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const staffName = this.dataset.staff;
                const days = this.dataset.days;

                document.getElementById('modalStaffName').textContent = staffName;
                document.getElementById('modalLeaveDays').textContent = days;
                document.getElementById('cancellation_remarks').value = '';

                cancelForm.action = '{{ url("admin/leave-management/cancel") }}/' + id;
                cancelModal.show();
            });
        });

        // 2. Approve Modal Handler
        const approveButtons = document.querySelectorAll('.btn-approve-leave');
        const approveModal = new bootstrap.Modal(document.getElementById('approveLeaveModal'));
        const approveForm = document.getElementById('approveLeaveForm');

        approveButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const staffName = this.dataset.staff;
                const days = this.dataset.days;
                const typeName = this.dataset.type;

                document.getElementById('approveModalStaff').textContent = staffName;
                document.getElementById('approveModalDays').textContent = days;
                document.getElementById('approveModalType').textContent = typeName;
                document.getElementById('approve_admin_remarks').value = '';

                approveForm.action = '{{ url("admin/leave-management/approve") }}/' + id;
                approveModal.show();
            });
        });

        // 3. Reject Modal Handler
        const rejectButtons = document.querySelectorAll('.btn-reject-leave');
        const rejectModal = new bootstrap.Modal(document.getElementById('rejectLeaveModal'));
        const rejectForm = document.getElementById('rejectLeaveForm');

        rejectButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const staffName = this.dataset.staff;

                document.getElementById('rejectModalStaff').textContent = staffName;
                document.getElementById('reject_admin_remarks').value = '';

                rejectForm.action = '{{ url("admin/leave-management/reject") }}/' + id;
                rejectModal.show();
            });
        });

        // 4. Edit Modal Handler
        const editButtons = document.querySelectorAll('.btn-edit-leave');
        const editModal = new bootstrap.Modal(document.getElementById('editLeaveModal'));
        const editForm = document.getElementById('editLeaveForm');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                editForm.action = '{{ url("admin/leave-management/update") }}/' + id;

                fetch('{{ url("admin/leave-management/details") }}/' + id)
                    .then(res => res.json())
                    .then(response => {
                        if (response.success && response.data) {
                            const data = response.data;
                            document.getElementById('edit_staff_name').value = data.staff_name;
                            document.getElementById('edit_leave_type').value = data.leave_type;
                            document.getElementById('edit_start_date').value = data.start_date;
                            document.getElementById('edit_end_date').value = data.end_date;
                            document.getElementById('edit_status').value = data.status;
                            document.getElementById('edit_reason').value = data.reason || '';
                            document.getElementById('edit_admin_remarks').value = data.admin_remarks || '';
                            editModal.show();
                        } else {
                            alert('Unable to load leave details.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Error fetching leave details.');
                    });
            });
        });
    });
</script>
@endsection

