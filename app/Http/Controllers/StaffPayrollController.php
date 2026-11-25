<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StaffSalary;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use App\Models\StaffExtra;
use App\Models\StaffPayroll;
use App\Models\StaffSalaryPayment;
use App\Models\RestaurantMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffPayrollController extends Controller
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
            abort(403, 'Unauthorized access to staff payroll.');
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
                $selectedOutletId = 'all';
                $outletIds = $availableOutlets->pluck('id')->toArray();
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
     * Permanent Payroll Dashboard & Monthly Processing.
     */
    public function index(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $availableOutlets = $context['availableOutlets'];
        $outletIds = $context['outletIds'];
        $selectedOutletId = $context['selectedOutletId'];
        $isSuperAdmin = $context['isSuperAdmin'];

        $activeTab = $request->input('tab', 'permanent');
        if (!in_array($activeTab, ['permanent', 'hourly'])) {
            $activeTab = 'permanent';
        }

        // Month & Year selection (defaults to current month and year)
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }
        if ($year < 2020 || $year > 2050) {
            $year = (int) date('Y');
        }

        $search = trim($request->input('search', ''));
        $paymentFilter = $request->input('payment_status', 'all'); // 'all', 'paid', 'unpaid'

        $payrollData = $this->computePayrollData($outletIds, $year, $month, $search, $paymentFilter);

        $staffList = $payrollData['staffList'];
        $stats = $payrollData['stats'];
        $daysInMonth = $payrollData['daysInMonth'];
        $monthName = Carbon::create($year, $month, 1)->format('F');

        // Hourly Base Staff data computation
        $hourlyData = $this->computeHourlyData($outletIds, $request);

        return view('restaurant.staff_payroll.index', compact(
            'availableOutlets',
            'outletIds',
            'selectedOutletId',
            'isSuperAdmin',
            'activeTab',
            'year',
            'month',
            'monthName',
            'daysInMonth',
            'staffList',
            'stats',
            'search',
            'paymentFilter',
            'hourlyData'
        ));
    }

    /**
     * Compute full corporate payroll breakdown for permanent staff.
     */
    protected function computePayrollData(array $outletIds, int $year, int $month, string $search = '', string $paymentFilter = 'all'): array
    {
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

        // Fetch permanent staff in outlet(s)
        $query = User::whereIn('restaurant_id', $outletIds)
            ->where('role_type', '!=', 'ADMIN')
            ->where('status', '!=', 'D')
            ->where(function ($q) {
                // Default to permanent: include staff who have no salary profile yet OR have non-hourly salary
                $q->whereDoesntHave('salary')
                  ->orWhereHas('salary', function ($sq) {
                      $sq->where('employment_type', '!=', 'hourly');
                  });
            })
            ->with(['restaurant', 'salary']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staffMembers = $query->orderBy('name', 'asc')->get();

        // Fetch saved payroll records for this month
        $savedPayrolls = StaffPayroll::whereIn('restaurant_id', $outletIds)
            ->where('year', $year)
            ->where('month', $month)
            ->get()
            ->keyBy('user_id');

        // Fetch all attendances for the month (exclude hourly duty attendances)
        $attendances = StaffAttendance::whereIn('restaurant_id', $outletIds)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('employee_type', '!=', 'hourly')
                  ->orWhereNull('employee_type');
            })
            ->get()
            ->groupBy('user_id');

        // Fetch all approved leaves for the month
        $leaves = StaffLeave::whereIn('restaurant_id', $outletIds)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                  });
            })
            ->get()
            ->groupBy('user_id');

        // Fetch all extras for the month
        $extras = StaffExtra::whereIn('restaurant_id', $outletIds)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->get()
            ->groupBy('user_id');

        $computedStaff = [];
        $totalBaseCommitted = 0;
        $totalAbsentDeduction = 0;
        $totalEarnedGross = 0;
        $totalStatutoryDeduction = 0;
        $totalExtraEarnings = 0;
        $totalExtraPenalties = 0;
        $totalAdvances = 0;
        $totalFinalSalary = 0;
        $totalActualPayable = 0;
        $paidCount = 0;
        $unpaidCount = 0;

        foreach ($staffMembers as $staff) {
            $saved = $savedPayrolls->get($staff->id);
            $salary = $staff->salary;
            $baseSalary = (float) ($salary ? $salary->monthly_salary : ($saved ? $saved->base_salary : 0));
            $perDayRate = $daysInMonth > 0 ? ($baseSalary / $daysInMonth) : 0;

            // 1. Process Attendances
            $userAttendances = $attendances->get($staff->id, collect());
            $fullPresent = $userAttendances->where('attendance_type', 'P')->count();
            $halfDayAtt = $userAttendances->where('attendance_type', 'H')->count();
            $earlyLeaveAtt = $userAttendances->where('attendance_type', 'EL')->count();
            $woAtt = $userAttendances->where('attendance_type', 'WO')->count();

            // Configured weekoffs
            $configuredWeekoffs = (int) ($salary->weekoff_count ?? 4);
            $weekoffDays = $woAtt > 0 ? $woAtt : $configuredWeekoffs;

            // Duty days from attendance
            $presentDutyDays = ($fullPresent * 1.0) + ($halfDayAtt * 0.5) + ($earlyLeaveAtt * 1.0);

            // 2. Process Leaves
            $userLeaves = $leaves->get($staff->id, collect());
            $slCount = 0.0;
            $plCount = 0.0;
            $hdCount = 0.0;
            $elCount = 0.0;

            foreach ($userLeaves as $l) {
                // Calculate overlapping days with month
                $lStart = Carbon::parse($l->start_date)->max(Carbon::parse($startDate));
                $lEnd = Carbon::parse($l->end_date)->min(Carbon::parse($endDate));
                $overlapDays = $lStart->diffInDays($lEnd) + 1;
                $leaveRatio = (float) $l->days_count > 0 ? min(1.0, (float) $l->days_count / (Carbon::parse($l->start_date)->diffInDays(Carbon::parse($l->end_date)) + 1)) : 1.0;
                $effectiveDays = round($overlapDays * $leaveRatio, 2);

                if ($l->leave_type === 'sick_leave') {
                    $slCount += $effectiveDays;
                } elseif ($l->leave_type === 'paid_leave') {
                    $plCount += $effectiveDays;
                } elseif ($l->leave_type === 'half_day') {
                    $hdCount += $effectiveDays;
                } elseif ($l->leave_type === 'early_leave') {
                    $elCount += $effectiveDays;
                }
            }

            $totalLeaveDays = round($slCount + $plCount + $hdCount + $elCount, 2);

            // Total Paid Duty = Present + Weekoffs + Approved Leaves
            $totalPaidDays = min($daysInMonth, round($presentDutyDays + $weekoffDays + $totalLeaveDays, 2));

            // Absent Days = Days in month - Paid days
            $absentDays = max(0, round($daysInMonth - $totalPaidDays, 2));

            // Absent salary deduction
            $absentDeduction = round($absentDays * $perDayRate, 2);
            $earnedGross = max(0, round($baseSalary - $absentDeduction, 2));

            // 3. Statutory Deductions
            $pfPct = (float) ($salary->pf_percentage ?? 0);
            $esiPct = (float) ($salary->esi_percentage ?? 0);

            $pfAmount = round(($earnedGross * $pfPct) / 100, 2);
            $esiAmount = round(($earnedGross * $esiPct) / 100, 2);

            // P-Tax (Percentage or Fixed)
            $ptaxType = $salary ? ($salary->ptax_type ?? 'percentage') : 'percentage';
            if ($ptaxType === 'fixed') {
                $ptaxVal = (float) ($salary->ptax_amount ?? 0);
                $ptaxAmount = round($ptaxVal, 2);
            } else {
                $ptaxVal = (float) ($salary->ptax_percentage ?? 0);
                $ptaxAmount = round(($earnedGross * $ptaxVal) / 100, 2);
            }

            // Other Deduction (Percentage or Fixed)
            $otherType = $salary ? ($salary->other_deduction_type ?? 'percentage') : 'percentage';
            if ($otherType === 'fixed') {
                $otherVal = (float) ($salary->other_deduction_amount ?? 0);
                $otherDedAmount = round($otherVal, 2);
            } else {
                $otherVal = (float) ($salary->other_deduction_percentage ?? 0);
                $otherDedAmount = round(($earnedGross * $otherVal) / 100, 2);
            }

            $otherDeductions = round($ptaxAmount + $otherDedAmount, 2);
            $otherPct = (float) (($ptaxType === 'percentage' ? $ptaxVal : 0) + ($otherType === 'percentage' ? $otherVal : 0));
            $totalStatutory = round($pfAmount + $esiAmount + $otherDeductions, 2);

            // Net Salary before extras
            $netSalary = max(0, round($earnedGross - $totalStatutory, 2));

            // 4. Extras
            $userExtras = $extras->get($staff->id, collect());
            $extraEarnings = (float) $userExtras->where('type', StaffExtra::TYPE_EARNING)->sum('amount');
            $extraPenalties = (float) $userExtras->where('type', StaffExtra::TYPE_DEDUCTION)->sum('amount');
            $advances = (float) $userExtras->where('type', StaffExtra::TYPE_ADVANCE)->sum('amount');

            // 5. Final Salary: Net Salary + Extra Earnings - Penalties - Advances
            $finalSalary = max(0, round($netSalary + $extraEarnings - $extraPenalties - $advances, 2));

            // 6. Saved record check
            $saved = $savedPayrolls->get($staff->id);
            $actualPayable = $saved ? (float) $saved->actual_payable : $finalSalary;
            $isPaid = $saved ? (bool) $saved->is_paid : false;
            $paidDate = $saved && $saved->paid_date ? Carbon::parse($saved->paid_date)->format('Y-m-d') : date('Y-m-d');
            $notes = $saved ? ($saved->notes ?? '') : '';

            // Filter by payment status if requested
            if ($paymentFilter === 'paid' && !$isPaid) {
                continue;
            }
            if ($paymentFilter === 'unpaid' && $isPaid) {
                continue;
            }

            if ($isPaid) {
                $paidCount++;
            } else {
                $unpaidCount++;
            }

            $totalBaseCommitted += $baseSalary;
            $totalAbsentDeduction += $absentDeduction;
            $totalEarnedGross += $earnedGross;
            $totalStatutoryDeduction += $totalStatutory;
            $totalExtraEarnings += $extraEarnings;
            $totalExtraPenalties += $extraPenalties;
            $totalAdvances += $advances;
            $totalFinalSalary += $finalSalary;
            $totalActualPayable += $actualPayable;

            $computedStaff[] = [
                'staff' => $staff,
                'user_id' => $staff->id,
                'restaurant_id' => $staff->restaurant_id,
                'base_salary' => $baseSalary,
                'per_day_rate' => $perDayRate,
                'present_days' => $presentDutyDays,
                'weekoff_days' => $weekoffDays,
                'leave_breakdown' => [
                    'sl' => $slCount,
                    'pl' => $plCount,
                    'hd' => $hdCount,
                    'el' => $elCount,
                    'wo' => $weekoffDays,
                ],
                'leave_days' => $totalLeaveDays,
                'total_paid_days' => $totalPaidDays,
                'absent_days' => $absentDays,
                'absent_deduction' => $absentDeduction,
                'earned_salary' => $earnedGross,
                'pf_percentage' => $pfPct,
                'pf_amount' => $pfAmount,
                'esi_percentage' => $esiPct,
                'esi_amount' => $esiAmount,
                'ptax_type' => $ptaxType,
                'ptax_val' => $ptaxVal,
                'ptax_amount' => $ptaxAmount,
                'other_type' => $otherType,
                'other_val' => $otherVal,
                'other_deduction_amount' => $otherDedAmount,
                'other_deductions_percentage' => $otherPct,
                'other_deductions' => $otherDeductions,
                'total_statutory' => $totalStatutory,
                'net_salary' => $netSalary,
                'extra_earnings' => $extraEarnings,
                'extra_deductions' => $extraPenalties,
                'advance_amount' => $advances,
                'final_salary' => $finalSalary,
                'actual_payable' => $actualPayable,
                'is_paid' => $isPaid,
                'paid_date' => $paidDate,
                'notes' => $notes,
                'saved_id' => $saved ? $saved->id : null,
            ];
        }

        $stats = [
            'staff_count' => count($computedStaff),
            'total_base_committed' => $totalBaseCommitted,
            'total_absent_deduction' => $totalAbsentDeduction,
            'total_earned_gross' => $totalEarnedGross,
            'total_statutory_deduction' => $totalStatutoryDeduction,
            'total_extra_earnings' => $totalExtraEarnings,
            'total_extra_penalties' => $totalExtraPenalties,
            'total_advances' => $totalAdvances,
            'total_final_salary' => $totalFinalSalary,
            'total_actual_payable' => $totalActualPayable,
            'paid_count' => $paidCount,
            'unpaid_count' => $unpaidCount,
        ];

        return [
            'staffList' => $computedStaff,
            'stats' => $stats,
            'daysInMonth' => $daysInMonth,
        ];
    }

    /**
     * Save single staff payroll record.
     */
    public function saveSingle(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $outletIds = $context['outletIds'];

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020|max:2050',
            'month' => 'required|integer|min:1|max:12',
            'base_salary' => 'nullable|numeric|min:0',
            'actual_payable' => 'required|numeric|min:0',
            'is_paid' => 'nullable|boolean',
            'paid_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $staff = User::whereIn('restaurant_id', $outletIds)
            ->where('id', $validated['user_id'])
            ->firstOrFail();

        // If base salary was entered/updated, save to StaffSalary profile
        if ($request->filled('base_salary') && is_numeric($request->base_salary)) {
            $baseSalaryVal = (float) $request->base_salary;
            StaffSalary::updateOrCreate(
                ['user_id' => $staff->id],
                [
                    'restaurant_id' => $staff->restaurant_id,
                    'employment_type' => 'permanent',
                    'monthly_salary' => $baseSalaryVal,
                ]
            );
        }

        // Recompute latest numbers
        $computed = $this->computePayrollData([$staff->restaurant_id], $validated['year'], $validated['month']);
        $staffData = collect($computed['staffList'])->firstWhere('user_id', $staff->id);

        if (!$staffData) {
            return response()->json([
                'success' => false,
                'message' => 'Staff record or permanent salary profile not found.',
            ], 422);
        }

        $isPaid = (bool) ($request->input('is_paid', false));
        $paidDate = $isPaid ? ($request->input('paid_date') ?: date('Y-m-d')) : null;

        $payroll = StaffPayroll::updateOrCreate(
            [
                'restaurant_id' => $staff->restaurant_id,
                'user_id' => $staff->id,
                'year' => $validated['year'],
                'month' => $validated['month'],
            ],
            [
                'total_days' => $computed['daysInMonth'],
                'present_days' => $staffData['present_days'],
                'leave_days' => $staffData['leave_days'],
                'leave_breakdown' => $staffData['leave_breakdown'],
                'absent_days' => $staffData['absent_days'],
                'base_salary' => $staffData['base_salary'],
                'absent_deduction' => $staffData['absent_deduction'],
                'earned_salary' => $staffData['earned_salary'],
                'pf_amount' => $staffData['pf_amount'],
                'esi_amount' => $staffData['esi_amount'],
                'other_deductions' => $staffData['other_deductions'],
                'net_salary' => $staffData['net_salary'],
                'extra_earnings' => $staffData['extra_earnings'],
                'extra_deductions' => $staffData['extra_deductions'],
                'advance_amount' => $staffData['advance_amount'],
                'final_salary' => $staffData['final_salary'],
                'actual_payable' => $validated['actual_payable'],
                'is_paid' => $isPaid,
                'paid_date' => $paidDate,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Payroll updated for {$staff->name}.",
            'data' => $payroll,
        ]);
    }

    /**
     * Batch save all payroll changes.
     */
    public function saveBatch(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $outletIds = $context['outletIds'];

        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2050',
            'month' => 'required|integer|min:1|max:12',
            'payroll_rows' => 'required|array',
            'payroll_rows.*.user_id' => 'required|exists:users,id',
            'payroll_rows.*.base_salary' => 'nullable|numeric|min:0',
            'payroll_rows.*.actual_payable' => 'required|numeric|min:0',
            'payroll_rows.*.is_paid' => 'nullable',
            'payroll_rows.*.paid_date' => 'nullable|date',
            'payroll_rows.*.notes' => 'nullable|string|max:1000',
        ]);

        $year = $validated['year'];
        $month = $validated['month'];

        // If base salaries were passed, update StaffSalary profiles first
        foreach ($validated['payroll_rows'] as $row) {
            if (isset($row['base_salary']) && is_numeric($row['base_salary'])) {
                $baseSalaryVal = (float) $row['base_salary'];
                $staffUser = User::find($row['user_id']);
                if ($staffUser) {
                    StaffSalary::updateOrCreate(
                        ['user_id' => $row['user_id']],
                        [
                            'restaurant_id' => $staffUser->restaurant_id,
                            'employment_type' => 'permanent',
                            'monthly_salary' => $baseSalaryVal,
                        ]
                    );
                }
            }
        }

        // Recompute latest baseline
        $computed = $this->computePayrollData($outletIds, $year, $month);
        $computedKeyed = collect($computed['staffList'])->keyBy('user_id');

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            foreach ($validated['payroll_rows'] as $row) {
                $userId = $row['user_id'];
                $staffData = $computedKeyed->get($userId);
                if (!$staffData) {
                    continue;
                }

                $isPaid = filter_var($row['is_paid'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $paidDate = $isPaid ? ($row['paid_date'] ?? date('Y-m-d')) : null;

                StaffPayroll::updateOrCreate(
                    [
                        'restaurant_id' => $staffData['restaurant_id'],
                        'user_id' => $userId,
                        'year' => $year,
                        'month' => $month,
                    ],
                    [
                        'total_days' => $computed['daysInMonth'],
                        'present_days' => $staffData['present_days'],
                        'leave_days' => $staffData['leave_days'],
                        'leave_breakdown' => $staffData['leave_breakdown'],
                        'absent_days' => $staffData['absent_days'],
                        'base_salary' => $staffData['base_salary'],
                        'absent_deduction' => $staffData['absent_deduction'],
                        'earned_salary' => $staffData['earned_salary'],
                        'pf_amount' => $staffData['pf_amount'],
                        'esi_amount' => $staffData['esi_amount'],
                        'other_deductions' => $staffData['other_deductions'],
                        'net_salary' => $staffData['net_salary'],
                        'extra_earnings' => $staffData['extra_earnings'],
                        'extra_deductions' => $staffData['extra_deductions'],
                        'advance_amount' => $staffData['advance_amount'],
                        'final_salary' => $staffData['final_salary'],
                        'actual_payable' => (float) $row['actual_payable'],
                        'is_paid' => $isPaid,
                        'paid_date' => $paidDate,
                        'notes' => $row['notes'] ?? null,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]
                );
                $updatedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Permanent payroll changes successfully saved for {$updatedCount} staff member(s).",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save payroll changes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export Corporate Payroll Data to CSV.
     */
    public function export(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $outletIds = $context['outletIds'];

        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $search = trim($request->input('search', ''));
        $paymentFilter = $request->input('payment_status', 'all');

        $payrollData = $this->computePayrollData($outletIds, $year, $month, $search, $paymentFilter);
        $staffList = $payrollData['staffList'];
        $monthName = Carbon::create($year, $month, 1)->format('F');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"payroll_{$monthName}_{$year}.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($staffList, $monthName, $year) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header Row
            fputcsv($handle, [
                '#',
                'Staff Name',
                'Role',
                'Phone',
                'Outlet',
                'Month & Year',
                'Total Days',
                'Present Days',
                'Weekoffs',
                'Sick Leave (SL)',
                'Paid Leave (PL)',
                'Half Day (HD)',
                'Early Leave (EL)',
                'Total Paid Days',
                'Absent Days',
                'Base Monthly Salary (₹)',
                'Absent Deduction (₹)',
                'Earned Salary (₹)',
                'PF Deduction (₹)',
                'ESI Deduction (₹)',
                'Other Deductions (₹)',
                'Net Salary (₹)',
                'Extra Earnings (+) (₹)',
                'Extra Penalties (-) (₹)',
                'Advance Payouts (-) (₹)',
                'Final Calculated Salary (₹)',
                'Actual Payable (₹)',
                'Payment Status',
                'Payment Date',
                'Notes / Remarks'
            ]);

            foreach ($staffList as $idx => $row) {
                $staff = $row['staff'];
                fputcsv($handle, [
                    $idx + 1,
                    $staff->name,
                    $staff->role_type,
                    $staff->phone ?? '—',
                    $staff->restaurant ? $staff->restaurant->name : 'N/A',
                    "{$monthName} {$year}",
                    $row['leave_breakdown']['wo'] ? (int) (Carbon::create($year, Carbon::parse($monthName)->month, 1)->daysInMonth) : 30,
                    number_format($row['present_days'], 2),
                    number_format($row['weekoff_days'], 2),
                    number_format($row['leave_breakdown']['sl'], 2),
                    number_format($row['leave_breakdown']['pl'], 2),
                    number_format($row['leave_breakdown']['hd'], 2),
                    number_format($row['leave_breakdown']['el'], 2),
                    number_format($row['total_paid_days'], 2),
                    number_format($row['absent_days'], 2),
                    number_format($row['base_salary'], 2),
                    number_format($row['absent_deduction'], 2),
                    number_format($row['earned_salary'], 2),
                    number_format($row['pf_amount'], 2),
                    number_format($row['esi_amount'], 2),
                    number_format($row['other_deductions'], 2),
                    number_format($row['net_salary'], 2),
                    number_format($row['extra_earnings'], 2),
                    number_format($row['extra_deductions'], 2),
                    number_format($row['advance_amount'], 2),
                    number_format($row['final_salary'], 2),
                    number_format($row['actual_payable'], 2),
                    $row['is_paid'] ? 'PAID' : 'UNPAID',
                    $row['is_paid'] && $row['paid_date'] ? $row['paid_date'] : '—',
                    $row['notes'] ?: '—'
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Compute duty breakdown, extras, and payments for Hourly Base Staff.
     */
    protected function computeHourlyData(array $outletIds, Request $request): array
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));

        // Filter ONLY staff who do duty as hourly wage (NOT permanent)
        $staffMembers = User::whereIn('restaurant_id', $outletIds)
            ->where('role_type', '!=', 'ADMIN')
            ->where('status', '!=', 'D')
            ->where(function ($q) {
                // 1. Has attendances captured as hourly duty
                $q->whereHas('attendances', function ($aq) {
                    $aq->where('employee_type', 'hourly');
                })
                // 2. OR configured in salary as hourly worker
                ->orWhereHas('salary', function ($sq) {
                    $sq->where('employment_type', 'hourly')
                      ->orWhere(function ($sub) {
                          $sub->where('hourly_wage', '>', 0)
                              ->where('employment_type', '!=', 'permanent');
                      });
                });
            })
            ->with(['restaurant', 'salary'])
            ->orderBy('name', 'asc')
            ->get();

        $selectedStaffId = $request->input('staff_id');

        // If no staff_id explicitly requested, auto-select first hourly worker or first staff
        if (empty($selectedStaffId) && $staffMembers->isNotEmpty()) {
            $hourlyPreferred = $staffMembers->first(function ($s) {
                return ($s->salary && (float)$s->salary->hourly_wage > 0) || ($s->salary && $s->salary->employment_type === 'hourly');
            });
            $selectedStaffId = $hourlyPreferred ? $hourlyPreferred->id : $staffMembers->first()->id;
        }

        $selectedStaff = $selectedStaffId ? $staffMembers->firstWhere('id', (int) $selectedStaffId) : null;
        $hourlyWage = (float) ($selectedStaff && $selectedStaff->salary ? $selectedStaff->salary->hourly_wage : 0);

        $dutyRecords = [];
        $totalDutyHours = 0.00;
        $totalDutyEarnings = 0.00;

        $extras = collect();
        $extraEarnings = 0.00;
        $extraPenalties = 0.00;
        $advanceAmount = 0.00;
        $netExtras = 0.00;

        $payments = collect();
        $totalPayments = 0.00;

        $grossDue = 0.00;
        $restAmount = 0.00;

        if ($selectedStaff) {
            // 1. Attendances & Duty Breakdown (Only calculate hourly duty attendances)
            $attendances = StaffAttendance::where('user_id', $selectedStaff->id)
                ->whereBetween('attendance_date', [$fromDate, $toDate])
                ->where(function ($q) use ($selectedStaff) {
                    $q->where('employee_type', 'hourly');
                    if ($selectedStaff->salary && $selectedStaff->salary->employment_type === 'hourly') {
                        $q->orWhereNull('employee_type');
                    }
                })
                ->orderBy('attendance_date', 'asc')
                ->get();

            foreach ($attendances as $att) {
                $hrs = (float) $att->worked_hours_decimal;
                $dayEarning = round($hrs * $hourlyWage, 2);
                $totalDutyHours += $hrs;
                $totalDutyEarnings += $dayEarning;

                $dutyRecords[] = [
                    'id' => $att->id,
                    'date' => $att->attendance_date ? Carbon::parse($att->attendance_date)->format('Y-m-d') : '',
                    'formatted_date' => $att->attendance_date ? Carbon::parse($att->attendance_date)->format('d M Y') : '',
                    'day_name' => $att->attendance_date ? Carbon::parse($att->attendance_date)->format('D') : '',
                    'attendance_type' => $att->attendance_type,
                    'type_label' => $att->type_label,
                    'badge_class' => $att->type_badge_class,
                    'login_time' => $att->formatted_login_time,
                    'logout_time' => $att->formatted_logout_time,
                    'raw_login_time' => $att->login_time,
                    'raw_logout_time' => $att->logout_time,
                    'work_duration' => $att->work_duration ?: ($hrs > 0 ? "{$hrs} hrs" : '--'),
                    'hours' => $hrs,
                    'hourly_wage' => $hourlyWage,
                    'earning' => $dayEarning,
                    'notes' => $att->notes,
                ];
            }

            // 2. Extras (Earnings, Deductions/Penalties, Advances)
            $extras = StaffExtra::where('user_id', $selectedStaff->id)
                ->whereBetween('entry_date', [$fromDate, $toDate])
                ->orderBy('entry_date', 'desc')
                ->get();

            $extraEarnings = (float) $extras->where('type', StaffExtra::TYPE_EARNING)->sum('amount');
            $extraPenalties = (float) $extras->where('type', StaffExtra::TYPE_DEDUCTION)->sum('amount');
            $advanceAmount = (float) $extras->where('type', StaffExtra::TYPE_ADVANCE)->sum('amount');
            $netExtras = round($extraEarnings - $extraPenalties - $advanceAmount, 2);

            // 3. Payments made against this period
            $payments = StaffSalaryPayment::where('user_id', $selectedStaff->id)
                ->where(function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('payment_date', [$fromDate, $toDate])
                      ->orWhere(function ($sub) use ($fromDate, $toDate) {
                          $sub->whereNotNull('period_from')
                              ->where('period_from', '<=', $toDate)
                              ->where('period_to', '>=', $fromDate);
                      });
                })
                ->with('creator')
                ->orderBy('payment_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $totalPayments = (float) $payments->sum('amount');

            // 4. Estimation Rest Payment
            // Calculation: Duty Earning + Extra Earning - Penalty - Advance - Total Payments
            $grossDue = round($totalDutyEarnings + $extraEarnings - $extraPenalties - $advanceAmount, 2);
            $restAmount = max(0, round($grossDue - $totalPayments, 2));
        }

        return [
            'staffMembers' => $staffMembers,
            'selectedStaff' => $selectedStaff,
            'selectedStaffId' => $selectedStaffId,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'hourlyWage' => $hourlyWage,
            'dutyRecords' => $dutyRecords,
            'totalDutyHours' => round($totalDutyHours, 2),
            'totalDutyEarnings' => round($totalDutyEarnings, 2),
            'extras' => $extras,
            'extraEarnings' => round($extraEarnings, 2),
            'extraPenalties' => round($extraPenalties, 2),
            'advanceAmount' => round($advanceAmount, 2),
            'netExtras' => $netExtras,
            'payments' => $payments,
            'totalPayments' => round($totalPayments, 2),
            'grossDue' => $grossDue,
            'restAmount' => $restAmount,
        ];
    }

    /**
     * Record a new hourly salary payment.
     */
    public function recordHourlyPayment(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:CASH,ONLINE',
            'period_from' => 'nullable|date',
            'period_to' => 'nullable|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        $staff = User::findOrFail($validated['user_id']);
        $user = auth()->user();

        if ($user->role !== 'SA' && (int)$staff->restaurant_id !== (int)$user->restaurant_id) {
            if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized for this staff outlet.'], 403);
            }
            return back()->with('error', 'Unauthorized for this staff outlet.');
        }

        $payment = StaffSalaryPayment::create([
            'restaurant_id' => $staff->restaurant_id,
            'user_id' => $staff->id,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'period_from' => $validated['period_from'] ?? null,
            'period_to' => $validated['period_to'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'created_by' => $user->id,
        ]);

        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment of ₹' . number_format($payment->amount, 2) . ' (' . $payment->payment_method . ') recorded successfully.',
                'payment' => $payment,
            ]);
        }

        return back()->with('success', 'Payment of ₹' . number_format($payment->amount, 2) . ' (' . $payment->payment_method . ') recorded successfully.');
    }

    /**
     * Delete an hourly salary payment.
     */
    public function deleteHourlyPayment(Request $request, $id)
    {
        $payment = StaffSalaryPayment::findOrFail($id);
        $user = auth()->user();

        if ($user->role !== 'SA' && (int)$payment->restaurant_id !== (int)$user->restaurant_id) {
            if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            return back()->with('error', 'Unauthorized action.');
        }

        $amount = $payment->amount;
        $payment->delete();

        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment record of ₹' . number_format($amount, 2) . ' deleted successfully.',
            ]);
        }

        return back()->with('success', 'Payment record of ₹' . number_format($amount, 2) . ' deleted successfully.');
    }

    /**
     * Save / Update hourly wage for a staff member.
     */
    public function saveHourlyWage(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hourly_wage' => 'required|numeric|min:0',
        ]);

        $staff = User::findOrFail($validated['user_id']);
        $user = auth()->user();

        if ($user->role !== 'SA' && (int)$staff->restaurant_id !== (int)$user->restaurant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $salary = StaffSalary::updateOrCreate(
            ['user_id' => $staff->id],
            [
                'restaurant_id' => $staff->restaurant_id,
                'hourly_wage' => $validated['hourly_wage'],
                'employment_type' => 'hourly',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Hourly wage updated successfully to ₹' . number_format($salary->hourly_wage, 2) . '/hr.',
            'hourly_wage' => (float) $salary->hourly_wage,
        ]);
    }
}
