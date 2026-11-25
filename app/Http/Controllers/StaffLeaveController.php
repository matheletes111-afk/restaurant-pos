<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StaffLeave;
use App\Models\StaffLeavePolicy;
use App\Models\RestaurantMaster;
use Carbon\Carbon;

class StaffLeaveController extends Controller
{
    /**
     * Resolve outlet context (supports Super Admin, multi-outlet owners, and single outlet admins).
     */
    protected function resolveOutletContext(Request $request = null): array
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'view') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized access to leave management.');
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
     * Display leave management dashboard and list.
     */
    public function index(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $availableOutlets = $context['availableOutlets'];
        $outletIds = $context['outletIds'];
        $selectedOutletId = $context['selectedOutletId'];
        $isSuperAdmin = $context['isSuperAdmin'];

        // Default to current month from_date and to_date
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-t'));

        if (!strtotime($fromDate)) {
            $fromDate = date('Y-m-01');
        }
        if (!strtotime($toDate)) {
            $toDate = date('Y-m-t');
        }

        // Active staff list
        $staffList = User::whereIn('restaurant_id', $outletIds)
            ->where('role_type', '!=', 'ADMIN')
            ->where('status', '!=', 'D')
            ->with(['restaurant', 'leavePolicies'])
            ->orderBy('name', 'asc')
            ->get();

        // Leave applications overlapping the date range
        $allLeaves = StaffLeave::whereIn('restaurant_id', $outletIds)
            ->where(function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('start_date', [$fromDate, $toDate])
                  ->orWhereBetween('end_date', [$fromDate, $toDate])
                  ->orWhere(function ($sub) use ($fromDate, $toDate) {
                      $sub->where('start_date', '<=', $fromDate)
                          ->where('end_date', '>=', $toDate);
                  });
            })
            ->with(['user.restaurant', 'creator', 'canceller', 'approver'])
            ->orderBy('id', 'desc')
            ->get();

        // Statistics for the date range
        $stats = [
            'total_applications' => $allLeaves->count(),
            'pending_count' => $allLeaves->where('status', 'pending')->count(),
            'approved_count' => $allLeaves->where('status', 'approved')->count(),
            'rejected_count' => $allLeaves->where('status', 'rejected')->count(),
            'total_days_taken' => (float) $allLeaves->where('status', 'approved')->sum('days_count'),
            'cancelled_count' => $allLeaves->where('status', 'cancelled')->count(),
            'distinct_staff' => $allLeaves->where('status', 'approved')->pluck('user_id')->unique()->count(),
        ];

        $statusFilter = $request->input('status', 'all');
        if ($statusFilter !== 'all' && in_array($statusFilter, ['pending', 'approved', 'rejected', 'cancelled'])) {
            $leaves = $allLeaves->where('status', $statusFilter);
        } else {
            $leaves = $allLeaves;
        }

        $leaveDefinitions = StaffLeavePolicy::getLeaveTypeDefinitions();

        // Build policy map for staff (used for live front-end balance display)
        $currentYear = (int) date('Y', strtotime($fromDate));
        $staffPolicyMap = [];
        foreach ($staffList as $staff) {
            $staffPolicyMap[$staff->id] = [];
            $policies = StaffLeavePolicy::where('user_id', $staff->id)
                ->where('year', $currentYear)
                ->get()
                ->keyBy('leave_type');

            foreach ($leaveDefinitions as $typeKey => $def) {
                $p = $policies->get($typeKey);
                $staffPolicyMap[$staff->id][$typeKey] = [
                    'name' => $def['name'],
                    'remaining' => $p ? (float) $p->remaining_balance : 0.00,
                    'total' => $p ? (float) $p->total_entitled : 0.00,
                    'used' => $p ? (float) $p->used_count : 0.00,
                    'unit' => $def['unit'] ?? 'Days',
                ];
            }
        }

        return view('restaurant.leave_management', compact(
            'leaves',
            'fromDate',
            'toDate',
            'statusFilter',
            'staffList',
            'stats',
            'availableOutlets',
            'selectedOutletId',
            'isSuperAdmin',
            'leaveDefinitions',
            'staffPolicyMap'
        ));
    }

    /**
     * Record a new leave application.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000',
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

        // Calculate leave days
        if ($request->leave_type === 'half_day') {
            // Half day counts as 0.50
            $days = 0.50;
        } elseif ($request->leave_type === 'early_leave') {
            // Early leave occasion
            $days = 1.00;
        } else {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $days = (float) ($start->diffInDays($end) + 1);
        }

        $year = (int) date('Y', strtotime($request->start_date));
        $defs = StaffLeavePolicy::getLeaveTypeDefinitions();
        $typeName = $defs[$request->leave_type]['name'] ?? ucwords(str_replace('_', ' ', $request->leave_type));

        // Retrieve leave policy for this staff, type, and year
        $policy = StaffLeavePolicy::where('user_id', $staff->id)
            ->where('leave_type', $request->leave_type)
            ->where('year', $year)
            ->first();

        $unused = $policy ? (float) $policy->remaining_balance : 0.00;

        // Check quota availability
        if ($days > $unused) {
            return back()->withInput()->with('error', "Cannot be done: Requested {$days} day(s), but only {$unused} day(s) unused leave available for {$typeName}.");
        }

        // Deduct from policy
        if ($policy) {
            $policy->increment('used_count', $days);
        }

        $restaurantId = $staff->restaurant_id ?: ($context['selectedOutletId'] !== 'all' ? $context['selectedOutletId'] : 1);

        StaffLeave::create([
            'restaurant_id' => $restaurantId,
            'user_id' => $staff->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $days,
            'reason' => $request->reason,
            'status' => 'approved',
            'created_by' => $user->id,
        ]);

        $redirectParams = [
            'from_date' => $request->input('from_date', date('Y-m-01', strtotime($request->start_date))),
            'to_date' => $request->input('to_date', date('Y-m-t', strtotime($request->start_date))),
        ];
        if ($request->filled('restaurant_id')) {
            $redirectParams['restaurant_id'] = $request->restaurant_id;
        }

        return redirect()->route('restaurant.leaves.index', $redirectParams)
            ->with('success', "Leave of {$days} day(s) ({$typeName}) successfully recorded for {$staff->name}. Remaining balance: " . max(0, $unused - $days) . " day(s).");
    }

    /**
     * Cancel / Delete leave application with remarks and restore quota while keeping data in the list.
     */
    public function cancel(Request $request, $leave_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'cancellation_remarks' => 'required|string|max:500',
        ]);

        $context = $this->resolveOutletContext($request);
        $query = StaffLeave::where('id', $leave_id);
        if (!$context['isSuperAdmin']) {
            $allowed = $context['availableOutlets']->isNotEmpty()
                ? $context['availableOutlets']->pluck('id')->toArray()
                : $context['outletIds'];
            $query->whereIn('restaurant_id', $allowed);
        }
        $leave = $query->firstOrFail();

        if ($leave->status === 'cancelled') {
            return back()->with('error', 'This leave record is already cancelled.');
        }

        // Restore deducted leave quota to policy only if it was approved
        if ($leave->status === 'approved') {
            $year = (int) date('Y', strtotime($leave->start_date));
            $policy = StaffLeavePolicy::where('user_id', $leave->user_id)
                ->where('leave_type', $leave->leave_type)
                ->where('year', $year)
                ->first();

            if ($policy && $policy->used_count > 0) {
                $deduction = min((float) $policy->used_count, (float) $leave->days_count);
                $policy->decrement('used_count', $deduction);
            }
        }

        // Mark as cancelled and store cancellation remarks
        $leave->status = 'cancelled';
        $leave->cancellation_remarks = $request->cancellation_remarks;
        $leave->cancelled_by = $user->id;
        $leave->cancelled_at = now();
        $leave->save();

        $staffName = $leave->user ? $leave->user->name : 'Staff';

        $redirectParams = [
            'from_date' => $request->input('from_date', date('Y-m-01', strtotime($leave->start_date))),
            'to_date' => $request->input('to_date', date('Y-m-t', strtotime($leave->start_date))),
        ];
        if ($request->filled('restaurant_id')) {
            $redirectParams['restaurant_id'] = $request->restaurant_id;
        }

        return redirect()->route('restaurant.leaves.index', $redirectParams)
            ->with('success', "Leave for {$staffName} has been cancelled" . ($leave->status === 'approved' ? " and {$leave->days_count} day(s) restored to their leave balance." : "."));
    }

    /**
     * Approve a pending leave request and adjust quota.
     */
    public function approve(Request $request, $leave_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $context = $this->resolveOutletContext($request);
        $query = StaffLeave::where('id', $leave_id);
        if (!$context['isSuperAdmin']) {
            $allowed = $context['availableOutlets']->isNotEmpty()
                ? $context['availableOutlets']->pluck('id')->toArray()
                : $context['outletIds'];
            $query->whereIn('restaurant_id', $allowed);
        }
        $leave = $query->with('user')->firstOrFail();

        if ($leave->status === 'approved') {
            return back()->with('error', 'This leave request is already approved.');
        }

        $year = (int) date('Y', strtotime($leave->start_date));
        $policy = StaffLeavePolicy::where('user_id', $leave->user_id)
            ->where('leave_type', $leave->leave_type)
            ->where('year', $year)
            ->first();

        $unused = $policy ? (float) $policy->remaining_balance : 0.00;
        $defs = StaffLeavePolicy::getLeaveTypeDefinitions();
        $typeName = $defs[$leave->leave_type]['name'] ?? ucwords(str_replace('_', ' ', $leave->leave_type));

        if ($policy && $leave->days_count > $unused) {
            return back()->with('error', "Cannot approve: Requested {$leave->days_count} day(s), but only {$unused} day(s) unused balance available for {$typeName}.");
        }

        // Deduct from policy quota
        if ($policy) {
            $policy->increment('used_count', $leave->days_count);
        }

        $leave->status = 'approved';
        $leave->approved_by = $user->id;
        $leave->approved_at = now();
        if ($request->filled('admin_remarks')) {
            $leave->admin_remarks = $request->admin_remarks;
        }
        $leave->save();

        $staffName = $leave->user ? $leave->user->name : 'Staff';
        $remaining = $policy ? max(0, $unused - $leave->days_count) : 0;

        return back()->with('success', "Leave request of {$leave->days_count} day(s) ({$typeName}) for {$staffName} approved and adjusted from quota. Remaining balance: {$remaining} day(s).");
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, $leave_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $context = $this->resolveOutletContext($request);
        $query = StaffLeave::where('id', $leave_id);
        if (!$context['isSuperAdmin']) {
            $allowed = $context['availableOutlets']->isNotEmpty()
                ? $context['availableOutlets']->pluck('id')->toArray()
                : $context['outletIds'];
            $query->whereIn('restaurant_id', $allowed);
        }
        $leave = $query->with('user')->firstOrFail();

        // If previously approved, restore quota
        if ($leave->status === 'approved') {
            $year = (int) date('Y', strtotime($leave->start_date));
            $policy = StaffLeavePolicy::where('user_id', $leave->user_id)
                ->where('leave_type', $leave->leave_type)
                ->where('year', $year)
                ->first();

            if ($policy && $policy->used_count > 0) {
                $deduction = min((float) $policy->used_count, (float) $leave->days_count);
                $policy->decrement('used_count', $deduction);
            }
        }

        $leave->status = 'rejected';
        $leave->admin_remarks = $request->input('admin_remarks', 'Rejected by administrator');
        $leave->save();

        $staffName = $leave->user ? $leave->user->name : 'Staff';
        return back()->with('success', "Leave request for {$staffName} has been rejected.");
    }

    /**
     * Fetch leave details for Edit Modal via AJAX.
     */
    public function details(Request $request, $leave_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'view') && $user->role_type !== 'ADMIN') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $context = $this->resolveOutletContext($request);
        $query = StaffLeave::where('id', $leave_id);
        if (!$context['isSuperAdmin']) {
            $allowed = $context['availableOutlets']->isNotEmpty()
                ? $context['availableOutlets']->pluck('id')->toArray()
                : $context['outletIds'];
            $query->whereIn('restaurant_id', $allowed);
        }
        $leave = $query->with('user')->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $leave->id,
                'user_id' => $leave->user_id,
                'staff_name' => $leave->user ? $leave->user->name : 'Staff',
                'leave_type' => $leave->leave_type,
                'start_date' => Carbon::parse($leave->start_date)->format('Y-m-d'),
                'end_date' => Carbon::parse($leave->end_date)->format('Y-m-d'),
                'days_count' => (float) $leave->days_count,
                'reason' => $leave->reason,
                'status' => $leave->status,
                'admin_remarks' => $leave->admin_remarks ?: $leave->cancellation_remarks,
            ]
        ]);
    }

    /**
     * Update leave request (Edit functionality for Admin).
     */
    public function update(Request $request, $leave_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,approved,rejected,cancelled',
            'reason' => 'nullable|string|max:1000',
            'admin_remarks' => 'nullable|string|max:1000',
        ]);

        $context = $this->resolveOutletContext($request);
        $query = StaffLeave::where('id', $leave_id);
        if (!$context['isSuperAdmin']) {
            $allowed = $context['availableOutlets']->isNotEmpty()
                ? $context['availableOutlets']->pluck('id')->toArray()
                : $context['outletIds'];
            $query->whereIn('restaurant_id', $allowed);
        }
        $leave = $query->with('user')->firstOrFail();

        // Calculate new days
        if ($request->leave_type === 'half_day') {
            $newDays = 0.50;
        } elseif ($request->leave_type === 'early_leave') {
            $newDays = 1.00;
        } else {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $newDays = (float) ($start->diffInDays($end) + 1);
        }

        $oldStatus = $leave->status;
        $newStatus = $request->status;
        $oldDays = (float) $leave->days_count;
        $oldType = $leave->leave_type;
        $newType = $request->leave_type;

        $oldYear = (int) date('Y', strtotime($leave->start_date));
        $newYear = (int) date('Y', strtotime($request->start_date));

        // Step 1: If previously approved, restore old days to old policy
        if ($oldStatus === 'approved') {
            $oldPolicy = StaffLeavePolicy::where('user_id', $leave->user_id)
                ->where('leave_type', $oldType)
                ->where('year', $oldYear)
                ->first();
            if ($oldPolicy && $oldPolicy->used_count > 0) {
                $restore = min((float) $oldPolicy->used_count, $oldDays);
                $oldPolicy->decrement('used_count', $restore);
            }
        }

        // Step 2: If new status is approved, deduct new days from new policy
        if ($newStatus === 'approved') {
            $newPolicy = StaffLeavePolicy::where('user_id', $leave->user_id)
                ->where('leave_type', $newType)
                ->where('year', $newYear)
                ->first();

            $unused = $newPolicy ? (float) $newPolicy->remaining_balance : 0.00;
            $defs = StaffLeavePolicy::getLeaveTypeDefinitions();
            $typeName = $defs[$newType]['name'] ?? $newType;

            if ($newPolicy && $newDays > $unused) {
                // If fails quota check, rollback old policy if it was approved
                if ($oldStatus === 'approved' && isset($oldPolicy)) {
                    $oldPolicy->increment('used_count', min((float)$oldPolicy->remaining_balance, $oldDays));
                }
                return back()->withInput()->with('error', "Cannot approve leave: Requested {$newDays} day(s), but only {$unused} day(s) unused balance available for {$typeName}.");
            }

            if ($newPolicy) {
                $newPolicy->increment('used_count', $newDays);
            }
            $leave->approved_by = $user->id;
            $leave->approved_at = now();
        }

        if ($newStatus === 'cancelled') {
            $leave->cancelled_by = $user->id;
            $leave->cancelled_at = now();
            if ($request->filled('admin_remarks')) {
                $leave->cancellation_remarks = $request->admin_remarks;
            }
        }

        $leave->leave_type = $newType;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->days_count = $newDays;
        $leave->reason = $request->reason;
        $leave->status = $newStatus;
        if ($request->filled('admin_remarks')) {
            $leave->admin_remarks = $request->admin_remarks;
        }
        $leave->save();

        $staffName = $leave->user ? $leave->user->name : 'Staff';
        return back()->with('success', "Leave request for {$staffName} has been updated successfully.");
    }

    /**
     * AJAX endpoint to get staff remaining leave quotas.
     */
    public function getStaffQuota(Request $request, $user_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'view') && $user->role_type !== 'ADMIN') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $year = (int) $request->input('year', date('Y'));
        $defs = StaffLeavePolicy::getLeaveTypeDefinitions();

        $policies = StaffLeavePolicy::where('user_id', $user_id)
            ->where('year', $year)
            ->get()
            ->keyBy('leave_type');

        $result = [];
        foreach ($defs as $key => $def) {
            $p = $policies->get($key);
            $result[$key] = [
                'name' => $def['name'],
                'code' => $def['code'],
                'color' => $def['color'],
                'remaining' => $p ? (float) $p->remaining_balance : 0.00,
                'total' => $p ? (float) $p->total_entitled : 0.00,
                'used' => $p ? (float) $p->used_count : 0.00,
                'unit' => $def['unit'] ?? 'Days',
            ];
        }

        return response()->json([
            'user_id' => $user_id,
            'year' => $year,
            'quotas' => $result,
        ]);
    }

    /**
     * Export leaves report as CSV / Excel.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'view') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized access.');
        }

        $context = $this->resolveOutletContext($request);
        $outletIds = $context['outletIds'];

        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-t'));

        $leaves = StaffLeave::whereIn('restaurant_id', $outletIds)
            ->where(function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('start_date', [$fromDate, $toDate])
                  ->orWhereBetween('end_date', [$fromDate, $toDate])
                  ->orWhere(function ($sub) use ($fromDate, $toDate) {
                      $sub->where('start_date', '<=', $fromDate)
                          ->where('end_date', '>=', $toDate);
                  });
            })
            ->with(['user.restaurant', 'creator', 'canceller'])
            ->orderBy('id', 'desc')
            ->get();

        $filename = "leave_management_{$fromDate}_to_{$toDate}.csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'S.No',
            'Staff Name',
            'Staff Email',
            'Phone',
            'Role',
            'Outlet',
            'Leave Type',
            'Start Date',
            'End Date',
            'Days Count',
            'Reason',
            'Status',
            'Cancellation Remarks',
            'Cancelled By',
            'Cancelled At',
            'Recorded By',
            'Recorded At'
        ];

        $callback = function () use ($leaves, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            $index = 1;
            foreach ($leaves as $leave) {
                $staff = $leave->user;
                fputcsv($file, [
                    $index++,
                    $staff ? $staff->name : 'N/A',
                    $staff ? $staff->email : 'N/A',
                    $staff ? $staff->phone : 'N/A',
                    $staff ? $staff->role_type : 'N/A',
                    $staff && $staff->restaurant ? $staff->restaurant->name : 'N/A',
                    $leave->leave_type_name,
                    Carbon::parse($leave->start_date)->format('d M Y'),
                    Carbon::parse($leave->end_date)->format('d M Y'),
                    $leave->days_count,
                    $leave->reason ?: '-',
                    strtoupper($leave->status),
                    $leave->cancellation_remarks ?: '-',
                    $leave->canceller ? $leave->canceller->name : '-',
                    $leave->cancelled_at ? $leave->cancelled_at->format('d M Y h:i A') : '-',
                    $leave->creator ? $leave->creator->name : 'Admin',
                    $leave->created_at ? $leave->created_at->format('d M Y h:i A') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

