<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StaffAttendance;
use App\Models\StaffLeavePolicy;
use App\Models\RestaurantMaster;
use Carbon\Carbon;

class StaffAttendanceController extends Controller
{
    /**
     * Resolve outlet context for attendance (supports Super Admin, multi-outlet owners, and single outlet admins).
     */
    protected function resolveOutletContext(Request $request = null): array
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'view') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized access to staff attendance.');
        }

        $reqRestaurantId = $request ? ($request->input('restaurant_id') ?: $request->query('restaurant_id')) : null;

        // 1. Super Admin access
        if ($user->role === 'SA') {
            $availableOutlets = RestaurantMaster::where('status', '!=', 'D')
                ->orderBy('name', 'asc')
                ->get();

            if ($reqRestaurantId) {
                if ($reqRestaurantId === 'all') {
                    $selectedOutletId = 'all';
                    $outletIds = $availableOutlets->pluck('id')->toArray();
                } else {
                    $selectedOutletId = (int) $reqRestaurantId;
                    $outletIds = [$selectedOutletId];
                    session(['active_restaurant_id' => $selectedOutletId]);
                }
            } elseif (session('active_restaurant_id') && $availableOutlets->contains('id', session('active_restaurant_id'))) {
                $selectedOutletId = (int) session('active_restaurant_id');
                $outletIds = [$selectedOutletId];
            } elseif (session('restaurant_id') && $availableOutlets->contains('id', session('restaurant_id'))) {
                $selectedOutletId = (int) session('restaurant_id');
                $outletIds = [$selectedOutletId];
            } else {
                $first = $availableOutlets->first();
                $selectedOutletId = $first ? $first->id : null;
                $outletIds = $selectedOutletId ? [$selectedOutletId] : [];
                if ($selectedOutletId) {
                    session(['active_restaurant_id' => $selectedOutletId]);
                }
            }

            return [
                'availableOutlets' => $availableOutlets,
                'outletIds' => $outletIds,
                'selectedOutletId' => $selectedOutletId,
                'isSuperAdmin' => true,
            ];
        }

        // 2. Restaurant Owner / Staff access
        $availableOutlets = $user->getAvailableOutlets();

        if ($availableOutlets->isNotEmpty()) {
            if ($reqRestaurantId) {
                if ($reqRestaurantId === 'all') {
                    $selectedOutletId = 'all';
                    $outletIds = $availableOutlets->pluck('id')->toArray();
                } else {
                    $selectedOutletId = (int) $reqRestaurantId;
                    $outletIds = in_array($selectedOutletId, $availableOutlets->pluck('id')->toArray())
                        ? [$selectedOutletId]
                        : $availableOutlets->pluck('id')->toArray();
                    session(['active_restaurant_id' => $selectedOutletId]);
                }
            } elseif (session('active_restaurant_id') && $availableOutlets->contains('id', session('active_restaurant_id'))) {
                $selectedOutletId = (int) session('active_restaurant_id');
                $outletIds = [$selectedOutletId];
            } elseif ($user->restaurant_id) {
                $selectedOutletId = (int) $user->restaurant_id;
                $outletIds = in_array($selectedOutletId, $availableOutlets->pluck('id')->toArray())
                    ? [$selectedOutletId]
                    : $availableOutlets->pluck('id')->toArray();
            } else {
                $first = $availableOutlets->first();
                $selectedOutletId = $first ? $first->id : null;
                $outletIds = $availableOutlets->pluck('id')->toArray();
            }
        } else {
            $selectedOutletId = $user->restaurant_id ?: (session('active_restaurant_id') ?: session('restaurant_id'));
            $outletIds = $selectedOutletId ? [(int) $selectedOutletId] : [];
        }

        return [
            'availableOutlets' => $availableOutlets,
            'outletIds' => $outletIds,
            'selectedOutletId' => $selectedOutletId,
            'isSuperAdmin' => false,
        ];
    }

    /**
     * Backward-compatible helper for authorized outlet IDs.
     */
    protected function getAuthorizedOutletIds(Request $request = null): array
    {
        $context = $this->resolveOutletContext($request);
        return $context['outletIds'];
    }

    /**
     * Display daily attendance management page.
     */
    public function index(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $availableOutlets = $context['availableOutlets'];
        $outletIds = $context['outletIds'];
        $selectedOutletId = $context['selectedOutletId'];
        $isSuperAdmin = $context['isSuperAdmin'];

        // Selected date (defaults to today)
        $date = $request->input('date', date('Y-m-d'));
        if (!strtotime($date)) {
            $date = date('Y-m-d');
        }

        // Active staff list belonging to user's outlets
        $staffList = User::whereIn('restaurant_id', $outletIds)
            ->where('role_type', '!=', 'ADMIN')
            ->where('status', '!=', 'D')
            ->with(['restaurant', 'salary'])
            ->orderBy('name', 'asc')
            ->get();

        // Attendances recorded for this date
        $attendances = StaffAttendance::whereIn('restaurant_id', $outletIds)
            ->where('attendance_date', $date)
            ->with(['user.restaurant', 'creator'])
            ->orderBy('id', 'desc')
            ->get();

        $markedUserIds = $attendances->pluck('user_id')->toArray();

        // Statistics for the date
        $stats = [
            'total_staff' => $staffList->count(),
            'total_recorded' => $attendances->count(),
            'present_count' => $attendances->where('attendance_type', 'P')->count(),
            'half_day_count' => $attendances->where('attendance_type', 'H')->count(),
            'early_leave_count' => $attendances->where('attendance_type', 'EL')->count(),
            'weekoff_count' => $attendances->where('attendance_type', 'WO')->count(),
            'unmarked_count' => max(0, $staffList->count() - $attendances->count()),
        ];

        return view('restaurant.attendance', compact(
            'attendances',
            'date',
            'staffList',
            'markedUserIds',
            'stats',
            'availableOutlets',
            'selectedOutletId',
            'isSuperAdmin'
        ));
    }

    /**
     * Save daily attendance record.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'attendance_type' => 'required|in:P,H,EL,WO',
            'employee_type' => 'nullable|in:hourly,permanent',
            'login_time' => 'required_unless:attendance_type,WO|nullable|string',
            'logout_time' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
            'restaurant_id' => 'nullable',
        ]);

        $context = $this->resolveOutletContext($request);

        if ($context['isSuperAdmin']) {
            $staff = User::where('id', $request->user_id)->firstOrFail();
        } else {
            $allowed = $context['availableOutlets']->isNotEmpty()
                ? $context['availableOutlets']->pluck('id')->toArray()
                : $context['outletIds'];
            $staff = User::where('id', $request->user_id)
                ->whereIn('restaurant_id', $allowed)
                ->firstOrFail();
        }

        // Check if attendance already exists for this staff on this date
        $exists = StaffAttendance::where('user_id', $staff->id)
            ->where('attendance_date', $request->date)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Attendance has already been marked for ' . $staff->name . ' on ' . date('d M Y', strtotime($request->date)) . '. Staff member cannot be added twice for the same date.');
        }

        // Leave Policy Check & Deduction for Half Day (H) and Early Leave (EL)
        $year = (int) date('Y', strtotime($request->date));

        if ($request->attendance_type === 'H') {
            $policy = StaffLeavePolicy::where('user_id', $staff->id)
                ->where('leave_type', 'half_day')
                ->where('year', $year)
                ->first();

            if (!$policy || $policy->remaining_balance < 1) {
                return back()->withInput()->with('error', 'Already used: No half day leave quota remaining for ' . $staff->name . ' in year ' . $year . '. Current available balance is 0.');
            }

            $policy->increment('used_count', 1);
        } elseif ($request->attendance_type === 'EL') {
            $policy = StaffLeavePolicy::where('user_id', $staff->id)
                ->where('leave_type', 'early_leave')
                ->where('year', $year)
                ->first();

            if (!$policy || $policy->remaining_balance < 1) {
                return back()->withInput()->with('error', 'Already used: No early leave quota remaining for ' . $staff->name . ' in year ' . $year . '. Current available balance is 0.');
            }

            $policy->increment('used_count', 1);
        }

        $restaurantId = $staff->restaurant_id ?: ($context['selectedOutletId'] !== 'all' ? $context['selectedOutletId'] : 1);

        $employeeType = $request->input('employee_type');
        if (empty($employeeType)) {
            $employeeType = ($staff->salary && ($staff->salary->employment_type === 'hourly' || (float)$staff->salary->hourly_wage > 0))
                ? 'hourly'
                : 'permanent';
        }

        StaffAttendance::create([
            'restaurant_id' => $restaurantId,
            'user_id' => $staff->id,
            'attendance_date' => $request->date,
            'attendance_type' => $request->attendance_type,
            'employee_type' => $employeeType,
            'login_time' => $request->filled('login_time') ? $request->login_time : null,
            'logout_time' => $request->filled('logout_time') ? $request->logout_time : null,
            'notes' => $request->notes,
            'created_by' => $user->id,
        ]);

        $redirectParams = ['date' => $request->date];
        if ($request->filled('restaurant_id')) {
            $redirectParams['restaurant_id'] = $request->restaurant_id;
        }

        $typeLabel = match ($request->attendance_type) {
            'P' => 'Full Day Present',
            'H' => 'Half Day',
            'EL' => 'Early Leave',
            'WO' => 'Weekoff',
            default => 'Attendance',
        };

        return redirect()->route('restaurant.attendance.index', $redirectParams)
            ->with('success', 'Attendance marked successfully for ' . $staff->name . ' (' . $typeLabel . ').');
    }

    /**
     * Update attendance record (e.g. set or edit logout time, notes, or type).
     */
    public function update(Request $request, $attendance_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'login_time' => 'required_unless:attendance_type,WO|nullable|string',
            'logout_time' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
            'attendance_type' => 'nullable|in:P,H,EL,WO',
            'employee_type' => 'nullable|in:hourly,permanent',
        ]);

        $context = $this->resolveOutletContext($request);
        $query = StaffAttendance::where('id', $attendance_id);
        if (!$context['isSuperAdmin']) {
            $allowed = $context['availableOutlets']->isNotEmpty()
                ? $context['availableOutlets']->pluck('id')->toArray()
                : $context['outletIds'];
            $query->whereIn('restaurant_id', $allowed);
        }
        $attendance = $query->firstOrFail();

        $staff = $attendance->user;
        $year = (int) date('Y', strtotime($attendance->attendance_date));

        // If attendance type is modified, adjust leave policy usage accordingly
        if ($request->filled('attendance_type') && $request->attendance_type !== $attendance->attendance_type) {
            $oldType = $attendance->attendance_type;
            $newType = $request->attendance_type;

            // Revert old deduction if it was H or EL
            if ($oldType === 'H') {
                $oldPolicy = StaffLeavePolicy::where('user_id', $staff->id)->where('leave_type', 'half_day')->where('year', $year)->first();
                if ($oldPolicy && $oldPolicy->used_count > 0) {
                    $oldPolicy->decrement('used_count', 1);
                }
            } elseif ($oldType === 'EL') {
                $oldPolicy = StaffLeavePolicy::where('user_id', $staff->id)->where('leave_type', 'early_leave')->where('year', $year)->first();
                if ($oldPolicy && $oldPolicy->used_count > 0) {
                    $oldPolicy->decrement('used_count', 1);
                }
            }

            // Apply new deduction if new is H or EL
            if ($newType === 'H') {
                $newPolicy = StaffLeavePolicy::where('user_id', $staff->id)->where('leave_type', 'half_day')->where('year', $year)->first();
                if (!$newPolicy || $newPolicy->remaining_balance < 1) {
                    // Rollback old reversion
                    if ($oldType === 'H' && isset($oldPolicy)) $oldPolicy->increment('used_count', 1);
                    if ($oldType === 'EL' && isset($oldPolicy)) $oldPolicy->increment('used_count', 1);
                    return back()->with('error', 'Already used: No half day leave quota remaining for ' . $staff->name . '.');
                }
                $newPolicy->increment('used_count', 1);
            } elseif ($newType === 'EL') {
                $newPolicy = StaffLeavePolicy::where('user_id', $staff->id)->where('leave_type', 'early_leave')->where('year', $year)->first();
                if (!$newPolicy || $newPolicy->remaining_balance < 1) {
                    // Rollback old reversion
                    if ($oldType === 'H' && isset($oldPolicy)) $oldPolicy->increment('used_count', 1);
                    if ($oldType === 'EL' && isset($oldPolicy)) $oldPolicy->increment('used_count', 1);
                    return back()->with('error', 'Already used: No early leave quota remaining for ' . $staff->name . '.');
                }
                $newPolicy->increment('used_count', 1);
            }

            $attendance->attendance_type = $newType;
        }

        $attendance->login_time = $request->filled('login_time') ? $request->login_time : null;
        $attendance->logout_time = $request->filled('logout_time') ? $request->logout_time : null;
        if ($request->filled('employee_type')) {
            $attendance->employee_type = $request->employee_type;
        }
        if ($request->has('notes')) {
            $attendance->notes = $request->notes;
        }
        $attendance->save();

        $redirectParams = ['date' => Carbon::parse($attendance->attendance_date)->format('Y-m-d')];
        if ($request->filled('restaurant_id')) {
            $redirectParams['restaurant_id'] = $request->restaurant_id;
        }

        return redirect()->route('restaurant.attendance.index', $redirectParams)
            ->with('success', 'Attendance updated successfully for ' . $staff->name . '.');
    }

    /**
     * Delete attendance record and restore leave policy if applicable.
     */
    public function destroy($attendance_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $context = $this->resolveOutletContext(request());
        $query = StaffAttendance::where('id', $attendance_id);
        if (!$context['isSuperAdmin']) {
            $allowed = $context['availableOutlets']->isNotEmpty()
                ? $context['availableOutlets']->pluck('id')->toArray()
                : $context['outletIds'];
            $query->whereIn('restaurant_id', $allowed);
        }
        $attendance = $query->firstOrFail();

        $staffName = $attendance->user ? $attendance->user->name : 'Staff';
        $dateStr = Carbon::parse($attendance->attendance_date)->format('Y-m-d');
        $year = (int) date('Y', strtotime($attendance->attendance_date));

        // Restore leave policy quota if it was Half Day or Early Leave
        if ($attendance->attendance_type === 'H') {
            $policy = StaffLeavePolicy::where('user_id', $attendance->user_id)
                ->where('leave_type', 'half_day')
                ->where('year', $year)
                ->first();
            if ($policy && $policy->used_count > 0) {
                $policy->decrement('used_count', 1);
            }
        } elseif ($attendance->attendance_type === 'EL') {
            $policy = StaffLeavePolicy::where('user_id', $attendance->user_id)
                ->where('leave_type', 'early_leave')
                ->where('year', $year)
                ->first();
            if ($policy && $policy->used_count > 0) {
                $policy->decrement('used_count', 1);
            }
        }

        $attendance->delete();

        $redirectParams = ['date' => $dateStr];
        if (request()->filled('restaurant_id')) {
            $redirectParams['restaurant_id'] = request('restaurant_id');
        }

        return redirect()->route('restaurant.attendance.index', $redirectParams)
            ->with('success', 'Attendance record deleted successfully for ' . $staffName . '. Any deducted leave quota has been restored.');
    }

    /**
     * Download daily attendance report as CSV (compatible with Excel).
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'view') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized access.');
        }

        $context = $this->resolveOutletContext($request);
        $outletIds = $context['outletIds'];
        $date = $request->input('date', date('Y-m-d'));
        if (!strtotime($date)) {
            $date = date('Y-m-d');
        }

        $attendances = StaffAttendance::whereIn('restaurant_id', $outletIds)
            ->where('attendance_date', $date)
            ->with(['user.restaurant', 'creator'])
            ->orderBy('login_time', 'asc')
            ->get();

        $filename = "attendance_" . $date . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'S.No',
            'Date',
            'Staff Name',
            'Staff Email',
            'Phone',
            'Role',
            'Outlet',
            'Attendance Type',
            'Login Time',
            'Logout Time',
            'Work Duration',
            'Notes',
            'Marked By',
            'Recorded At'
        ];

        $callback = function () use ($attendances, $columns, $date) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Microsoft Excel proper character rendering
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            $index = 1;
            foreach ($attendances as $att) {
                $staff = $att->user;
                fputcsv($file, [
                    $index++,
                    date('d M Y', strtotime($date)),
                    $staff ? $staff->name : 'N/A',
                    $staff ? $staff->email : 'N/A',
                    $staff ? $staff->phone : 'N/A',
                    $staff ? $staff->role_type : 'N/A',
                    $staff && $staff->restaurant ? $staff->restaurant->name : 'N/A',
                    $att->type_label . ' (' . $att->attendance_type . ')',
                    $att->formatted_login_time,
                    $att->formatted_logout_time,
                    $att->work_duration ?: '-',
                    $att->notes ?: '-',
                    $att->creator ? $att->creator->name : 'Admin',
                    $att->created_at ? $att->created_at->format('d M Y h:i A') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
