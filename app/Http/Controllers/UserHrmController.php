<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use App\Models\StaffLeavePolicy;
use App\Models\StaffExtra;
use App\Models\StaffPayroll;
use App\Models\StaffSalary;
use App\Models\RestaurantMaster;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UserHrmController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * User HRM Main Dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $tab = $request->input('tab', 'attendance');
        $validTabs = ['attendance', 'leaves', 'extras', 'salary'];
        if (!in_array($tab, $validTabs)) {
            $tab = 'attendance';
        }

        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));

        if ($year < 2020 || $year > 2050) {
            $year = (int) date('Y');
        }
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $salary = StaffSalary::where('user_id', $user->id)->first();
        $restaurant = RestaurantMaster::find($user->restaurant_id);
        $todayAttendance = StaffAttendance::where('user_id', $user->id)
            ->where('attendance_date', date('Y-m-d'))
            ->first();

        // 1. ATTENDANCE DATA (Strictly for auth user)
        $attendances = StaffAttendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('attendance_date', 'asc')
            ->get();

        $attSummary = [
            'total_days_in_month' => $daysInMonth,
            'total_logged' => $attendances->count(),
            'present_count' => $attendances->where('attendance_type', 'P')->count(),
            'half_day_count' => $attendances->where('attendance_type', 'H')->count(),
            'early_leave_count' => $attendances->where('attendance_type', 'EL')->count(),
            'weekoff_count' => $attendances->where('attendance_type', 'WO')->count(),
            'total_hours' => round($attendances->sum(function ($a) {
                return (float) $a->worked_hours_decimal;
            }), 2),
        ];

        // 2. LEAVE DATA (Strictly for auth user)
        $leaveStatusFilter = $request->input('leave_status', 'all');
        $leavesQuery = StaffLeave::where('user_id', $user->id)
            ->whereYear('start_date', $year);

        if (!empty($leaveStatusFilter) && $leaveStatusFilter !== 'all') {
            $leavesQuery->where('status', $leaveStatusFilter);
        }

        $leaves = $leavesQuery->orderBy('start_date', 'desc')->get();
        $leavePolicies = StaffLeavePolicy::where('user_id', $user->id)
            ->where('year', $year)
            ->get();

        $totalLeaveQuota = (float) $leavePolicies->sum('annual_quota') + (float) $leavePolicies->sum('carried_forward');
        $approvedLeavesUsed = (float) StaffLeave::where('user_id', $user->id)
            ->whereYear('start_date', $year)
            ->where('status', 'approved')
            ->sum('days_count');

        $leaveDefinitions = StaffLeavePolicy::getLeaveTypeDefinitions();
        $policiesKeyed = $leavePolicies->keyBy('leave_type');
        $staffPolicyMap = [];
        foreach ($leaveDefinitions as $typeKey => $def) {
            $p = $policiesKeyed->get($typeKey);
            $staffPolicyMap[$typeKey] = [
                'name' => $def['name'],
                'code' => $def['code'] ?? '',
                'remaining' => $p ? (float) $p->remaining_balance : 0.00,
                'total' => $p ? (float) $p->total_entitled : 0.00,
                'used' => $p ? (float) $p->used_count : 0.00,
                'unit' => $def['unit'] ?? 'Days',
            ];
        }

        $leaveSummary = [
            'total_quota' => $totalLeaveQuota,
            'used_days' => $approvedLeavesUsed,
            'remaining_days' => max(0, $totalLeaveQuota - $approvedLeavesUsed),
            'pending_count' => $leaves->where('status', 'pending')->count(),
            'approved_count' => $leaves->where('status', 'approved')->count(),
            'rejected_count' => $leaves->where('status', 'rejected')->count(),
        ];

        // 3. EXTRAS DATA (Strictly for auth user)
        $extraTypeFilter = $request->input('extra_type', 'all');
        $extrasQuery = StaffExtra::where('user_id', $user->id)
            ->whereBetween('entry_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if (!empty($extraTypeFilter) && $extraTypeFilter !== 'all') {
            $extrasQuery->where('type', $extraTypeFilter);
        }

        $extras = $extrasQuery->orderBy('entry_date', 'desc')->get();

        $extraEarnings = (float) StaffExtra::where('user_id', $user->id)
            ->whereBetween('entry_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('type', StaffExtra::TYPE_EARNING)
            ->sum('amount');

        $extraDeductions = (float) StaffExtra::where('user_id', $user->id)
            ->whereBetween('entry_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('type', StaffExtra::TYPE_DEDUCTION)
            ->sum('amount');

        $extraAdvances = (float) StaffExtra::where('user_id', $user->id)
            ->whereBetween('entry_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('type', StaffExtra::TYPE_ADVANCE)
            ->sum('amount');

        $extrasSummary = [
            'total_earnings' => $extraEarnings,
            'total_deductions' => $extraDeductions,
            'total_advances' => $extraAdvances,
            'net_balance' => round($extraEarnings - $extraDeductions - $extraAdvances, 2),
        ];

        // 4. SALARY DATA (Strictly for auth user)
        $payrolls = StaffPayroll::where('user_id', $user->id)
            ->where('year', $year)
            ->orderBy('month', 'desc')
            ->get();

        return view('restaurant.user_hrm.index', compact(
            'user',
            'tab',
            'year',
            'month',
            'startDate',
            'endDate',
            'todayAttendance',
            'salary',
            'restaurant',
            'attendances',
            'attSummary',
            'leaves',
            'leavePolicies',
            'leaveSummary',
            'leaveStatusFilter',
            'leaveDefinitions',
            'staffPolicyMap',
            'extras',
            'extrasSummary',
            'extraTypeFilter',
            'payrolls'
        ));
    }

    /**
     * Download Attendance Sheet as Excel for Auth Staff
     */
    public function exportAttendance(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        $restaurant = RestaurantMaster::find($user->restaurant_id);

        $attendances = StaffAttendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('attendance_date', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('My Attendance');

        // Title & metadata banner
        $monthName = $startDate->format('F Y');
        $sheet->setCellValue('A1', ($restaurant->name ?? 'Restaurant') . " — My Attendance Sheet ({$user->name})");
        $sheet->setCellValue('A2', "Employee: {$user->name} ({$user->role_type}) | Period: {$monthName} | Generated: " . now()->format('d M Y h:i A'));
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getFont()->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        // Headers
        $headers = [
            'A4' => '#',
            'B4' => 'Date',
            'C4' => 'Day',
            'D4' => 'Wage Type',
            'E4' => 'Attendance Status',
            'F4' => 'In / Login Time',
            'G4' => 'Out / Logout Time',
            'H4' => 'Work Duration',
            'I4' => 'Hours (Decimal)',
            'J4' => 'Remarks / Notes'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E293B']
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FFFF5E14']]
            ]
        ];
        $sheet->getStyle('A4:J4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(26);

        $row = 5;
        $totalHours = 0;
        foreach ($attendances as $idx => $att) {
            $attDate = $att->attendance_date ? Carbon::parse($att->attendance_date) : null;
            $hrs = (float) $att->worked_hours_decimal;
            $totalHours += $hrs;

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $attDate ? $attDate->format('d M Y') : '—');
            $sheet->setCellValue('C' . $row, $attDate ? $attDate->format('l') : '—');
            $sheet->setCellValue('D' . $row, $att->employee_type === 'hourly' ? 'Hourly Wage' : 'Permanent');
            $sheet->setCellValue('E' . $row, $att->type_label);
            $sheet->setCellValue('F' . $row, $att->formatted_login_time);
            $sheet->setCellValue('G' . $row, $att->formatted_logout_time);
            $sheet->setCellValue('H' . $row, $att->work_duration ?: ($hrs > 0 ? "{$hrs} hrs" : '—'));
            $sheet->setCellValue('I' . $row, round($hrs, 2));
            $sheet->setCellValue('J' . $row, $att->notes ?: '—');

            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }
            $row++;
        }

        // Totals Row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, $attendances->count() . ' Days Logged');
        $sheet->setCellValue('I' . $row, round($totalHours, 2) . ' hrs');

        $totalStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFE4D6']
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFF5E14']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FFFF5E14']]
            ]
        ];
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($totalStyle);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtolower($user->name));
        $filename = "my_attendance_{$safeName}_{$year}_{$month}.xlsx";

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Get JSON data for Salary Slip modal (Strictly for auth user)
     */
    public function getSalarySlipData($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $payroll = StaffPayroll::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['restaurant', 'user'])
            ->first();

        if (!$payroll) {
            return response()->json(['success' => false, 'message' => 'Salary record not found.'], 404);
        }

        $monthName = Carbon::create($payroll->year, $payroll->month, 1)->format('F Y');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payroll->id,
                'staff_name' => $user->name,
                'role_type' => $user->role_type,
                'phone' => $user->phone ?: '—',
                'restaurant_name' => $payroll->restaurant ? $payroll->restaurant->name : 'Restaurant POS',
                'month_name' => $monthName,
                'total_days' => $payroll->total_days,
                'present_days' => (float) $payroll->present_days,
                'absent_days' => (float) $payroll->absent_days,
                'leave_days' => (float) $payroll->leave_days,
                'base_salary' => (float) $payroll->base_salary,
                'absent_deduction' => (float) $payroll->absent_deduction,
                'earned_salary' => (float) $payroll->earned_salary,
                'pf_amount' => (float) $payroll->pf_amount,
                'esi_amount' => (float) $payroll->esi_amount,
                'other_deductions' => (float) $payroll->other_deductions,
                'total_statutory' => (float) ($payroll->pf_amount + $payroll->esi_amount + $payroll->other_deductions),
                'extra_earnings' => (float) $payroll->extra_earnings,
                'extra_deductions' => (float) $payroll->extra_deductions,
                'advance_amount' => (float) $payroll->advance_amount,
                'final_salary' => (float) $payroll->final_salary,
                'actual_payable' => (float) $payroll->actual_payable,
                'is_paid' => (bool) $payroll->is_paid,
                'paid_date' => $payroll->paid_date ? Carbon::parse($payroll->paid_date)->format('d M Y') : '—',
                'notes' => $payroll->notes ?: '—',
            ]
        ]);
    }

    /**
     * Dedicated printable Salary Slip view (Strictly for auth user)
     */
    public function printSalarySlip($id)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $payroll = StaffPayroll::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['restaurant', 'user'])
            ->firstOrFail();

        $monthName = Carbon::create($payroll->year, $payroll->month, 1)->format('F Y');
        $restaurant = $payroll->restaurant ?: RestaurantMaster::find($user->restaurant_id);

        return view('restaurant.user_hrm.payslip', compact('payroll', 'user', 'monthName', 'restaurant'));
    }

    /**
     * Submit Leave Application (Strictly for auth user)
     */
    public function applyLeave(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $validated = $request->validate([
            'leave_type' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000',
        ]);

        if ($validated['leave_type'] === 'half_day') {
            $daysCount = 0.50;
        } elseif ($validated['leave_type'] === 'early_leave') {
            $daysCount = 1.00;
        } else {
            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);
            $daysCount = (float) ($start->diffInDays($end) + 1);
        }

        $year = (int) date('Y', strtotime($validated['start_date']));
        $defs = StaffLeavePolicy::getLeaveTypeDefinitions();
        $typeName = $defs[$validated['leave_type']]['name'] ?? ucwords(str_replace('_', ' ', $validated['leave_type']));

        // Check quota availability if policy configured
        $policy = StaffLeavePolicy::where('user_id', $user->id)
            ->where('leave_type', $validated['leave_type'])
            ->where('year', $year)
            ->first();

        $unused = $policy ? (float) $policy->remaining_balance : 0.00;
        if ($policy && $daysCount > $unused) {
            return redirect()->route('user.hrm.index', ['tab' => 'leaves'])
                ->with('error', "Cannot submit request: Requested {$daysCount} day(s), but only {$unused} day(s) unused balance available for {$typeName}.");
        }

        // Store leave request with 'pending' status - quota will only be deducted when admin approves
        StaffLeave::create([
            'restaurant_id' => $user->restaurant_id ?: 1,
            'user_id' => $user->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_count' => $daysCount,
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        return redirect()->route('user.hrm.index', ['tab' => 'leaves'])
            ->with('success', "Leave application for {$daysCount} day(s) ({$typeName}) submitted successfully. Status: Pending Manager Approval.");
    }

    /**
     * Calculate geodesic distance in meters using the Haversine formula (pure math, no external API).
     */
    public static function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // Earth radius in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }

    /**
     * Self Clock-In with 50-meter Geofence Verification.
     */
    public function clockIn(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $restaurant = RestaurantMaster::find($user->restaurant_id);
        $distance = null;

        // 1. Geofence verification if enabled on restaurant
        if ($restaurant && $restaurant->geofence_enabled && !is_null($restaurant->latitude) && !is_null($restaurant->longitude)) {
            $userLat = $request->input('latitude');
            $userLng = $request->input('longitude');

            if (is_null($userLat) || is_null($userLng)) {
                return redirect()->route('user.hrm.index', ['tab' => 'attendance'])
                    ->with('error', 'Location coordinates are required to mark attendance. Please enable device GPS.');
            }

            $distance = self::calculateHaversineDistance(
                (float) $restaurant->latitude,
                (float) $restaurant->longitude,
                (float) $userLat,
                (float) $userLng
            );

            $radius = (int) ($restaurant->geofence_radius_meters ?: 50);

            if ($distance > $radius) {
                return redirect()->route('user.hrm.index', ['tab' => 'attendance'])
                    ->with('error', "Attendance rejected: You are {$distance}m away from {$restaurant->name}. Attendance must be marked within {$radius} meters of the outlet.");
            }
        }

        $today = date('Y-m-d');
        $attendance = StaffAttendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->first();

        if ($attendance && !empty($attendance->login_time)) {
            return redirect()->route('user.hrm.index', ['tab' => 'attendance'])
                ->with('error', 'You have already clocked in today at ' . Carbon::parse($attendance->login_time)->format('h:i A') . '.');
        }

        $salary = StaffSalary::where('user_id', $user->id)->first();
        $empType = ($salary && ($salary->employment_type === 'hourly' || (float)$salary->hourly_wage > 0)) ? 'hourly' : 'permanent';

        if ($attendance) {
            $attendance->update([
                'attendance_type' => 'P',
                'employee_type' => $empType,
                'login_time' => Carbon::now()->format('H:i:s'),
                'checkin_lat' => $request->input('latitude'),
                'checkin_lng' => $request->input('longitude'),
                'checkin_distance_meters' => $distance,
                'geofence_status' => 'verified',
            ]);
        } else {
            StaffAttendance::create([
                'restaurant_id' => $user->restaurant_id ?: 1,
                'user_id' => $user->id,
                'attendance_date' => $today,
                'attendance_type' => 'P', // Default full day Present
                'employee_type' => $empType,
                'login_time' => Carbon::now()->format('H:i:s'),
                'checkin_lat' => $request->input('latitude'),
                'checkin_lng' => $request->input('longitude'),
                'checkin_distance_meters' => $distance,
                'geofence_status' => 'verified',
                'notes' => $request->input('notes', 'Self Clock-In via User HRM'),
                'created_by' => $user->id,
            ]);
        }

        $distNote = !is_null($distance) ? " ({$distance}m from outlet)" : "";
        return redirect()->route('user.hrm.index', ['tab' => 'attendance'])
            ->with('success', "Clocked in successfully at " . Carbon::now()->format('h:i A') . "{$distNote}. Attendance recorded as Full Day Present.");
    }

    /**
     * Self Clock-Out capturing logout time and shift duration.
     */
    public function clockOut(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $today = date('Y-m-d');
        $attendance = StaffAttendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->first();

        if (!$attendance || empty($attendance->login_time)) {
            return redirect()->route('user.hrm.index', ['tab' => 'attendance'])
                ->with('error', 'No active clock-in found for today. Please clock in first.');
        }

        if (!empty($attendance->logout_time)) {
            return redirect()->route('user.hrm.index', ['tab' => 'attendance'])
                ->with('error', 'You have already clocked out today at ' . Carbon::parse($attendance->logout_time)->format('h:i A') . '.');
        }

        $restaurant = RestaurantMaster::find($user->restaurant_id);
        $distance = null;
        if ($restaurant && !is_null($restaurant->latitude) && !is_null($restaurant->longitude) && $request->filled('latitude') && $request->filled('longitude')) {
            $distance = self::calculateHaversineDistance(
                (float) $restaurant->latitude,
                (float) $restaurant->longitude,
                (float) $request->input('latitude'),
                (float) $request->input('longitude')
            );
        }

        $attendance->update([
            'logout_time' => Carbon::now()->format('H:i:s'),
            'checkout_lat' => $request->input('latitude'),
            'checkout_lng' => $request->input('longitude'),
            'checkout_distance_meters' => $distance,
        ]);

        return redirect()->route('user.hrm.index', ['tab' => 'attendance'])
            ->with('success', "Clocked out successfully at " . Carbon::now()->format('h:i A') . ". Duty completed for today.");
    }
}
