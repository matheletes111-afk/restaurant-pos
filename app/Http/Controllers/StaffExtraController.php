<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StaffExtra;
use App\Models\RestaurantMaster;
use Carbon\Carbon;

class StaffExtraController extends Controller
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
            abort(403, 'Unauthorized access to staff extras.');
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
     * Display one-pager extras dashboard and records.
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

        $staffId = $request->input('staff_id');
        $type = $request->input('type');
        $search = trim($request->input('search', ''));

        // Query active staff list for dropdowns
        $staffList = User::whereIn('restaurant_id', $outletIds)
            ->where('role_type', '!=', 'ADMIN')
            ->where('status', '!=', 'D')
            ->with('restaurant')
            ->orderBy('name', 'asc')
            ->get();

        // Build base query for date range and outlet
        $query = StaffExtra::whereIn('restaurant_id', $outletIds)
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->with(['user.restaurant', 'creator']);

        if ($staffId && $staffId !== 'all') {
            $query->where('user_id', $staffId);
        }

        if ($type && in_array($type, StaffExtra::getValidTypes())) {
            $query->where('type', $type);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('remarks', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $extras = $query->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Compute summary statistics for the filtered result
        $totalEarnings = (float) $extras->where('type', StaffExtra::TYPE_EARNING)->sum('amount');
        $totalDeductions = (float) $extras->where('type', StaffExtra::TYPE_DEDUCTION)->sum('amount');
        $totalAdvances = (float) $extras->where('type', StaffExtra::TYPE_ADVANCE)->sum('amount');
        $netImpact = $totalEarnings - $totalDeductions - $totalAdvances;

        $stats = [
            'total_count' => $extras->count(),
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'total_advances' => $totalAdvances,
            'net_impact' => $netImpact,
            'distinct_staff' => $extras->pluck('user_id')->unique()->count(),
        ];

        return view('restaurant.staff_extras.index', compact(
            'availableOutlets',
            'outletIds',
            'selectedOutletId',
            'isSuperAdmin',
            'fromDate',
            'toDate',
            'staffList',
            'extras',
            'stats',
            'staffId',
            'type',
            'search'
        ));
    }

    /**
     * Store a new staff extra entry.
     */
    public function store(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $outletIds = $context['outletIds'];

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:Earning,Deduction,Advance',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $staff = User::whereIn('restaurant_id', $outletIds)
            ->where('id', $validated['user_id'])
            ->first();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid staff member selected or staff does not belong to active outlet.',
            ], 422);
        }

        $extra = StaffExtra::create([
            'restaurant_id' => $staff->restaurant_id,
            'user_id' => $staff->id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'entry_date' => $validated['entry_date'],
            'remarks' => $validated['remarks'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$validated['type']} entry of ₹" . number_format($validated['amount'], 2) . " recorded successfully for {$staff->name}.",
            'extra' => $extra->load(['user', 'creator']),
        ]);
    }

    /**
     * Get a specific extra entry for editing.
     */
    public function show($id)
    {
        $context = $this->resolveOutletContext();
        $outletIds = $context['outletIds'];

        $extra = StaffExtra::whereIn('restaurant_id', $outletIds)
            ->where('id', $id)
            ->with('user')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $extra->id,
                'user_id' => $extra->user_id,
                'staff_name' => $extra->user->name ?? 'Staff',
                'type' => $extra->type,
                'amount' => $extra->amount,
                'entry_date' => Carbon::parse($extra->entry_date)->format('Y-m-d'),
                'remarks' => $extra->remarks,
            ],
        ]);
    }

    /**
     * Update an existing extra entry.
     */
    public function update(Request $request, $id)
    {
        $context = $this->resolveOutletContext($request);
        $outletIds = $context['outletIds'];

        $extra = StaffExtra::whereIn('restaurant_id', $outletIds)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:Earning,Deduction,Advance',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $staff = User::whereIn('restaurant_id', $outletIds)
            ->where('id', $validated['user_id'])
            ->first();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid staff member selected.',
            ], 422);
        }

        $extra->update([
            'restaurant_id' => $staff->restaurant_id,
            'user_id' => $staff->id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'entry_date' => $validated['entry_date'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Staff extra entry updated successfully.',
            'extra' => $extra->fresh()->load(['user', 'creator']),
        ]);
    }

    /**
     * Delete an extra entry.
     */
    public function destroy($id)
    {
        $context = $this->resolveOutletContext();
        $outletIds = $context['outletIds'];

        $extra = StaffExtra::whereIn('restaurant_id', $outletIds)
            ->where('id', $id)
            ->firstOrFail();

        $type = $extra->type;
        $amount = $extra->amount;
        $staffName = $extra->user->name ?? 'Staff';

        $extra->delete();

        return response()->json([
            'success' => true,
            'message' => "{$type} of ₹" . number_format($amount, 2) . " for {$staffName} deleted successfully.",
        ]);
    }

    /**
     * Export staff extras data to CSV.
     */
    public function export(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $outletIds = $context['outletIds'];

        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-t'));
        $staffId = $request->input('staff_id');
        $type = $request->input('type');
        $search = trim($request->input('search', ''));

        $query = StaffExtra::whereIn('restaurant_id', $outletIds)
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->with(['user.restaurant', 'creator']);

        if ($staffId && $staffId !== 'all') {
            $query->where('user_id', $staffId);
        }

        if ($type && in_array($type, StaffExtra::getValidTypes())) {
            $query->where('type', $type);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('remarks', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $extras = $query->orderBy('entry_date', 'asc')->get();

        $filename = "staff_extras_{$fromDate}_to_{$toDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($extras) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Outlet',
                'Staff Name',
                'Staff Phone',
                'Type',
                'Amount (INR)',
                'Entry Date',
                'Remarks',
                'Created By',
                'Created At',
            ]);

            foreach ($extras as $extra) {
                fputcsv($file, [
                    $extra->id,
                    $extra->restaurant->name ?? '-',
                    $extra->user->name ?? '-',
                    $extra->user->phone ?? '-',
                    $extra->type,
                    number_format($extra->amount, 2, '.', ''),
                    Carbon::parse($extra->entry_date)->format('Y-m-d'),
                    $extra->remarks ?? '-',
                    $extra->creator->name ?? 'System',
                    $extra->created_at ? $extra->created_at->format('Y-m-d H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
