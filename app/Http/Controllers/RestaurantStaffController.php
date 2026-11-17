<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StaffSalary;
use App\Models\StaffLeavePolicy;
use App\Models\StaffDocument;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use Carbon\Carbon;
use Hash;

class RestaurantStaffController extends Controller
{
    // SHOW PAGE
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'SA') {
            $availableOutlets = \App\Models\RestaurantMaster::where('status', '!=', 'D')->orderBy('name', 'asc')->get();
            $reqRestaurantId = request('restaurant_id');
            if ($reqRestaurantId && $reqRestaurantId !== 'all') {
                $outletIds = [(int) $reqRestaurantId];
            } else {
                $outletIds = $availableOutlets->pluck('id')->toArray();
            }
        } else {
            $availableOutlets = $user->getAvailableOutlets();
            $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];
        }

        $data = User::whereIn('restaurant_id', $outletIds)
                ->where('role_type','!=','ADMIN')
                ->where('status','!=','D')
                ->with(['restaurant', 'salary'])
                ->orderBy('id', 'DESC')
                ->get();

        return view('staff', compact('data', 'availableOutlets'));
    }

    // INSERT
    public function insert(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required',
            'role_type'     => 'required',
            'password'      => 'required',
            'restaurant_id' => 'nullable|integer',
        ]);

        $user = auth()->user();
        if ($user->role === 'SA') {
            $availableOutlets = \App\Models\RestaurantMaster::where('status', '!=', 'D')->get();
            $targetRestaurantId = $request->restaurant_id ?: ($availableOutlets->first() ? $availableOutlets->first()->id : 1);
        } else {
            $targetRestaurantId = $request->restaurant_id ?: $user->restaurant_id;
            $availableOutlets = $user->getAvailableOutlets();
            if ($availableOutlets->isNotEmpty() && !$availableOutlets->contains('id', $targetRestaurantId)) {
                return back()->with('error', 'Invalid outlet selected.');
            }
        }

        $newUser = new User;
        $newUser->name          = $request->name;
        $newUser->email         = $request->email;
        $newUser->role          = 'RES';
        $newUser->phone         = $request->phone;
        $newUser->role_type     = $request->role_type;
        $newUser->restaurant_id = $targetRestaurantId;
        $newUser->address       = $request->address;
        $newUser->pincode       = $request->pincode;
        $newUser->status        = $request->status ?? 'A';
        $newUser->password      = Hash::make($request->password);

        $newUser->save();

        return back()->with('success','Staff added successfully!');
    }

    // UPDATE
    public function update(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'email'         => 'required|email',
            'phone'         => 'required',
            'role_type'     => 'required',
            'restaurant_id' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staffUser = User::where('id', $request->id)
            ->whereIn('restaurant_id', $outletIds)
            ->first();

        if(!$staffUser){ return back()->with('error','Staff not found!'); }

        $targetRestaurantId = $request->restaurant_id ?: $staffUser->restaurant_id;
        if ($availableOutlets->isNotEmpty() && !$availableOutlets->contains('id', $targetRestaurantId)) {
            return back()->with('error', 'Invalid outlet selected.');
        }

        $staffUser->name          = $request->name;
        $staffUser->email         = $request->email;
        $staffUser->phone         = $request->phone;
        $staffUser->role_type     = $request->role_type;
        $staffUser->restaurant_id = $targetRestaurantId;
        $staffUser->address       = $request->address;
        $staffUser->pincode       = $request->pincode;
        $staffUser->status        = $request->status;

        $staffUser->save();

        return back()->with('success','Staff updated successfully!');
    }

    // DELETE
    public function delete($id)
    {
        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        User::where('id', $id)
            ->whereIn('restaurant_id', $outletIds)
            ->update(['status'=>'D']);

        return back()->with('success','Staff deleted successfully!');
    }

    public function status($id)
    {
        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $check = User::where('id', $id)->whereIn('restaurant_id', $outletIds)->first();
        if ($check) {
            $newStatus = ($check->status == "A") ? "I" : "A";
            User::where('id', $id)->update(['status' => $newStatus]);
            return back()->with('success','Staff status changed successfully!');
        }
        return back()->with('error', 'Staff member not found.');
    }

    public function permissions($id)
    {
        if (auth()->user()->role_type !== 'ADMIN') {
            abort(403, 'Only restaurant administrators can manage staff permissions.');
        }

        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staff = User::where('id', $id)
            ->whereIn('restaurant_id', $outletIds)
            ->firstOrFail();

        $menus = [
            [
                'key' => 'menu_master',
                'title' => 'Menu Master',
                'description' => 'Manage food categories and add/edit/delete dishes.',
                'icon' => 'fas fa-concierge-bell'
            ],
            [
                'key' => 'menu_availability',
                'title' => 'Menu Availability',
                'description' => 'Toggle food availability status and manage discounts.',
                'icon' => 'fas fa-clipboard-list'
            ],
            [
                'key' => 'table_master',
                'title' => 'Table Management',
                'description' => 'Manage restaurant floor tables, table categories, dining zones, and QR standees.',
                'icon' => 'fas fa-chair'
            ],
            [
                'key' => 'order_master',
                'title' => 'Order Master',
                'description' => 'Access to creating, editing, and managing orders and processing payments.',
                'icon' => 'fas fa-receipt'
            ],
            [
                'key' => 'rapid_bill',
                'title' => 'Rapid Bill POS',
                'description' => 'Ultra-fast counter billing with keyword search, split Cash/UPI, and instant bill printing.',
                'icon' => 'fas fa-bolt'
            ],
            [
                'key' => 'kitchen_order',
                'title' => 'Kitchen Order',
                'description' => 'Access to the kitchen panel to view and process active food items.',
                'icon' => 'fas fa-users'
            ],
            [
                'key' => 'pending_order',
                'title' => 'Pending Order',
                'description' => 'Approve or reject customer-initiated QR orders.',
                'icon' => 'fas fa-clock'
            ],
            [
                'key' => 'dish_addon_master',
                'title' => 'Dish Addon Master',
                'description' => 'Manage dish add-ons, portion pricing, food types, and status toggles.',
                'icon' => 'fas fa-puzzle-piece'
            ],
            [
                'key' => 'billing_subscription',
                'title' => 'Billing & Subscription',
                'description' => 'View current active plan, billing history, and handle subscription renewals.',
                'icon' => 'fas fa-credit-card'
            ],
            [
                'key' => 'customer_support',
                'title' => 'Customer Support',
                'description' => 'Create support tickets and view responses from customer support.',
                'icon' => 'fas fa-headset'
            ],
            [
                'key' => 'staff',
                'title' => 'Staff Management',
                'description' => 'Add new staff members, toggle status, or delete staff records.',
                'icon' => 'fas fa-users'
            ],
            [
                'key' => 'cash_drawer',
                'title' => 'Cash Drawer Management',
                'description' => 'Access cash drawer ledger, record cash in / cash out, and monitor live balance.',
                'icon' => 'fas fa-cash-register'
            ],
            [
                'key' => 'expense_management',
                'title' => 'Expense Management',
                'description' => 'Record daily restaurant expenses with payment modes and track spending.',
                'icon' => 'fas fa-wallet'
            ],
            [
                'key' => 'inventory_setting',
                'title' => 'Inventory Setting',
                'description' => 'Access to units, products, suppliers, purchases, stockouts, and debit notes.',
                'icon' => 'fas fa-boxes'
            ],
            [
                'key' => 'master_report',
                'title' => 'Master Report',
                'description' => 'Comprehensive executive overview, orders, item-level sales, stock, purchases, expenses & BI analytics.',
                'icon' => 'fas fa-chart-pie'
            ],
            [
                'key' => 'reports',
                'title' => 'Reports & Analytics',
                'description' => 'Access top dish/customer analysis, order reports, live stock, and order graphs.',
                'icon' => 'ti ti-report-analytics'
            ]
        ];

        $selectedPermissions = $staff->permissions ?: [];

        return view('restaurant.permissions', compact('staff', 'menus', 'selectedPermissions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        if (auth()->user()->role_type !== 'ADMIN') {
            abort(403, 'Only restaurant administrators can manage staff permissions.');
        }

        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staff = User::where('id', $id)
            ->whereIn('restaurant_id', $outletIds)
            ->firstOrFail();

        $staff->permissions = $request->input('permissions', []);
        $staff->save();

        return redirect()->route('restaurant.staff.index')->with('success', 'Staff permissions updated successfully!');
    }

    /**
     * Helper to retrieve authorized staff member.
     */
    protected function getAuthorizedStaff($id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'view') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized access to staff operations.');
        }

        if ($user->role === 'SA') {
            return User::where('id', $id)
                ->with(['restaurant', 'salary'])
                ->firstOrFail();
        }

        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        return User::where('id', $id)
            ->whereIn('restaurant_id', $outletIds)
            ->with(['restaurant', 'salary'])
            ->firstOrFail();
    }

    /**
     * Tab 1: Dedicated Staff Salary Management page (/salary/{id}).
     */
    public function salaryPage(Request $request, $id)
    {
        $staff = $this->getAuthorizedStaff($id);
        $salary = $staff->salary;
        $documents = StaffDocument::where('user_id', $staff->id)->orderBy('id', 'DESC')->get();
        $activeTab = 'salary';

        return view('restaurant.staff_operations', compact('staff', 'salary', 'documents', 'activeTab'));
    }

    /**
     * Tab 2: Dedicated Staff Leave Policy Management page (/leave-policy/{id}).
     */
    public function leavePolicyPage(Request $request, $id)
    {
        $staff = $this->getAuthorizedStaff($id);
        $year = (int) $request->input('year', date('Y'));
        $leaveDefinitions = StaffLeavePolicy::getLeaveTypeDefinitions();

        $existingPolicies = StaffLeavePolicy::where('user_id', $staff->id)
            ->where('year', $year)
            ->get()
            ->keyBy('leave_type');

        $leavePolicies = [];
        foreach ($leaveDefinitions as $typeKey => $def) {
            if ($existingPolicies->has($typeKey)) {
                $leavePolicies[$typeKey] = $existingPolicies->get($typeKey);
            } else {
                $leavePolicies[$typeKey] = new StaffLeavePolicy([
                    'user_id' => $staff->id,
                    'restaurant_id' => $staff->restaurant_id,
                    'year' => $year,
                    'leave_type' => $typeKey,
                    'annual_quota' => 0.00,
                    'carried_forward' => 0.00,
                    'used_count' => 0.00,
                    'is_carry_forward' => false,
                    'notes' => ''
                ]);
            }
        }

        $salary = $staff->salary;
        $documents = StaffDocument::where('user_id', $staff->id)->orderBy('id', 'DESC')->get();
        $activeTab = 'leave_policy';

        return view('restaurant.staff_operations', compact('staff', 'salary', 'leavePolicies', 'leaveDefinitions', 'year', 'documents', 'activeTab'));
    }

    /**
     * Tab 3: Dedicated Staff Document Management page (/documents/{id}).
     */
    public function documentsPage(Request $request, $id)
    {
        $staff = $this->getAuthorizedStaff($id);
        $salary = $staff->salary;
        $documents = StaffDocument::where('user_id', $staff->id)->orderBy('id', 'DESC')->get();
        $activeTab = 'documents';

        return view('restaurant.staff_operations', compact('staff', 'salary', 'documents', 'activeTab'));
    }

    /**
     * Show dedicated Staff Operations & Salary Management page (backwards-compatible router).
     */
    public function operations(Request $request, $id)
    {
        $tab = $request->input('tab', 'salary');
        if ($tab === 'leave_policy') {
            return $this->leavePolicyPage($request, $id);
        } elseif ($tab === 'documents') {
            return $this->documentsPage($request, $id);
        } elseif ($tab === 'calendar') {
            return $this->calendarPage($request, $id);
        }
        return $this->salaryPage($request, $id);
    }

    /**
     * Tab 4: Dedicated Staff Calendar & Monthly Activity page (/calendar/{id}).
     */
    public function calendarPage(Request $request, $id)
    {
        $staff = $this->getAuthorizedStaff($id);
        $salary = $staff->salary;
        $documents = StaffDocument::where('user_id', $staff->id)->orderBy('id', 'DESC')->get();
        $activeTab = 'calendar';

        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('m'));
        if ($month < 1 || $month > 12) {
            $month = (int) date('m');
        }
        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }

        $activeView = $request->input('view', 'calendar'); // 'calendar' or 'list'

        $startDateStr = sprintf('%04d-%02d-01', $year, $month);
        $daysInMonth = Carbon::parse($startDateStr)->daysInMonth;
        $endDateStr = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

        // Fetch attendances for this staff in this month
        $attendances = StaffAttendance::where('user_id', $staff->id)
            ->whereBetween('attendance_date', [$startDateStr, $endDateStr])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->attendance_date)->format('Y-m-d');
            });

        // Fetch approved leaves overlapping this month
        $leaves = StaffLeave::where('user_id', $staff->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDateStr, $endDateStr) {
                $q->whereBetween('start_date', [$startDateStr, $endDateStr])
                  ->orWhereBetween('end_date', [$startDateStr, $endDateStr])
                  ->orWhere(function ($sub) use ($startDateStr, $endDateStr) {
                      $sub->where('start_date', '<=', $startDateStr)
                          ->where('end_date', '>=', $endDateStr);
                  });
            })
            ->get();

        // Build day-by-day activity
        $calendarData = [];
        $stats = [
            'total_days' => $daysInMonth,
            'present' => 0,
            'half_day' => 0,
            'early_leave' => 0,
            'weekoff' => 0,
            'leave' => 0,
            'absent' => 0,
            'upcoming' => 0,
        ];

        $today = date('Y-m-d');

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateFormatted = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $cDate = Carbon::parse($dateFormatted);
            $dayOfWeek = $cDate->format('D'); // Mon, Tue...
            $isPastOrToday = ($dateFormatted <= $today);

            $att = $attendances->get($dateFormatted);
            $matchingLeave = $leaves->first(function ($l) use ($dateFormatted) {
                $start = Carbon::parse($l->start_date)->format('Y-m-d');
                $end = Carbon::parse($l->end_date)->format('Y-m-d');
                return ($dateFormatted >= $start && $dateFormatted <= $end);
            });

            if ($att) {
                if ($att->attendance_type === 'P') {
                    $status = 'present';
                    $statusLabel = 'Full Day Present (P)';
                    $badgeClass = 'badge-attendance-present';
                    $stats['present']++;
                } elseif ($att->attendance_type === 'H') {
                    $status = 'half_day';
                    $statusLabel = 'Half Day (H)';
                    $badgeClass = 'badge-attendance-halfday';
                    $stats['half_day']++;
                } elseif ($att->attendance_type === 'EL') {
                    $status = 'early_leave';
                    $statusLabel = 'Early Leave (EL)';
                    $badgeClass = 'badge-attendance-earlyleave';
                    $stats['early_leave']++;
                } elseif ($att->attendance_type === 'WO') {
                    $status = 'weekoff';
                    $statusLabel = 'Weekoff (WO)';
                    $badgeClass = 'badge-attendance-weekoff';
                    $stats['weekoff']++;
                } else {
                    $status = 'present';
                    $statusLabel = 'Present';
                    $badgeClass = 'badge-attendance-present';
                    $stats['present']++;
                }
            } elseif ($matchingLeave) {
                $status = 'leave';
                $statusLabel = 'On Leave (' . $matchingLeave->leave_type_name . ')';
                $badgeClass = 'badge-calendar-leave';
                $stats['leave']++;
            } elseif ($isPastOrToday) {
                $status = 'absent';
                $statusLabel = 'Absent / Unmarked';
                $badgeClass = 'badge-calendar-absent';
                $stats['absent']++;
            } else {
                $status = 'upcoming';
                $statusLabel = 'Upcoming';
                $badgeClass = 'badge-calendar-upcoming';
                $stats['upcoming']++;
            }

            $calendarData[$dateFormatted] = [
                'day' => $d,
                'date' => $dateFormatted,
                'day_of_week' => $dayOfWeek,
                'day_of_week_full' => $cDate->format('l'),
                'is_today' => ($dateFormatted === $today),
                'is_past' => ($dateFormatted < $today),
                'status' => $status,
                'status_label' => $statusLabel,
                'badge_class' => $badgeClass,
                'attendance' => $att,
                'leave' => $matchingLeave,
            ];
        }

        // Calendar grid: determine first day of month (0 = Sun, 1 = Mon, ..., 6 = Sat)
        $firstDayOfWeek = Carbon::parse($startDateStr)->dayOfWeek;
        $monthName = Carbon::parse($startDateStr)->format('F');
        $prevMonth = Carbon::parse($startDateStr)->subMonth();
        $nextMonth = Carbon::parse($startDateStr)->addMonth();

        return view('restaurant.staff_operations', compact(
            'staff',
            'salary',
            'documents',
            'activeTab',
            'calendarData',
            'stats',
            'year',
            'month',
            'monthName',
            'daysInMonth',
            'firstDayOfWeek',
            'prevMonth',
            'nextMonth',
            'activeView'
        ));
    }

    /**
     * Download staff monthly activity calendar as CSV (compatible with Excel).
     */
    public function exportCalendar(Request $request, $id)
    {
        $staff = $this->getAuthorizedStaff($id);
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('m'));
        if ($month < 1 || $month > 12) $month = (int) date('m');
        if ($year < 2000 || $year > 2100) $year = (int) date('Y');

        $startDateStr = sprintf('%04d-%02d-01', $year, $month);
        $daysInMonth = Carbon::parse($startDateStr)->daysInMonth;
        $endDateStr = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);
        $monthName = Carbon::parse($startDateStr)->format('F');

        $attendances = StaffAttendance::where('user_id', $staff->id)
            ->whereBetween('attendance_date', [$startDateStr, $endDateStr])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->attendance_date)->format('Y-m-d');
            });

        $leaves = StaffLeave::where('user_id', $staff->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDateStr, $endDateStr) {
                $q->whereBetween('start_date', [$startDateStr, $endDateStr])
                  ->orWhereBetween('end_date', [$startDateStr, $endDateStr])
                  ->orWhere(function ($sub) use ($startDateStr, $endDateStr) {
                      $sub->where('start_date', '<=', $startDateStr)
                          ->where('end_date', '>=', $endDateStr);
                  });
            })
            ->get();

        $filename = "staff_activity_" . str_replace(' ', '_', strtolower($staff->name)) . "_{$monthName}_{$year}.csv";

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
            'Day',
            'Status',
            'Login Time',
            'Logout Time',
            'Work Duration',
            'Leave Details / Remarks',
            'Staff Name',
            'Staff Email',
            'Outlet'
        ];

        $today = date('Y-m-d');

        $callback = function () use ($daysInMonth, $year, $month, $attendances, $leaves, $staff, $today, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            $index = 1;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateFormatted = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $cDate = Carbon::parse($dateFormatted);
                $dayName = $cDate->format('l');

                $att = $attendances->get($dateFormatted);
                $matchingLeave = $leaves->first(function ($l) use ($dateFormatted) {
                    $start = Carbon::parse($l->start_date)->format('Y-m-d');
                    $end = Carbon::parse($l->end_date)->format('Y-m-d');
                    return ($dateFormatted >= $start && $dateFormatted <= $end);
                });

                $status = 'Upcoming';
                $loginTime = '-';
                $logoutTime = '-';
                $duration = '-';
                $remarks = '-';

                if ($att) {
                    $status = $att->type_label . ' (' . $att->attendance_type . ')';
                    $loginTime = $att->formatted_login_time;
                    $logoutTime = $att->formatted_logout_time;
                    $duration = $att->work_duration ?: '-';
                    $remarks = $att->notes ?: '-';
                } elseif ($matchingLeave) {
                    $status = 'On Leave (' . $matchingLeave->leave_type_name . ')';
                    $remarks = $matchingLeave->reason ?: 'Approved Leave';
                } elseif ($dateFormatted <= $today) {
                    $status = 'Absent / Unmarked';
                    $remarks = 'No attendance or leave recorded for this day';
                }

                fputcsv($file, [
                    $index++,
                    date('d M Y', strtotime($dateFormatted)),
                    $dayName,
                    $status,
                    $loginTime,
                    $logoutTime,
                    $duration,
                    $remarks,
                    $staff->name,
                    $staff->email,
                    $staff->restaurant ? $staff->restaurant->name : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get staff salary details as JSON.
     */
    public function getSalary($id)
    {
        $user = auth()->user();
        if (!$user->hasPermission('staff', 'view') && $user->role_type !== 'ADMIN') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staff = User::where('id', $id)
            ->whereIn('restaurant_id', $outletIds)
            ->with(['restaurant', 'salary'])
            ->first();

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff member not found.'], 404);
        }

        $salary = $staff->salary;

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'role_type' => $staff->role_type,
                'restaurant_name' => $staff->restaurant ? $staff->restaurant->name : 'N/A',
            ],
            'salary' => $salary ? [
                'id' => $salary->id,
                'employment_type' => $salary->employment_type,
                'monthly_salary' => (float) $salary->monthly_salary,
                'pf_percentage' => (float) $salary->pf_percentage,
                'esi_percentage' => (float) $salary->esi_percentage,
                'ptax_percentage' => (float) $salary->ptax_percentage,
                'other_deduction_percentage' => (float) $salary->other_deduction_percentage,
                'weekoff_count' => (int) $salary->weekoff_count,
                'hourly_wage' => (float) $salary->hourly_wage,
                'notes' => $salary->notes,
                'breakdown' => $salary->salary_breakdown,
            ] : [
                'id' => null,
                'employment_type' => 'permanent',
                'monthly_salary' => '',
                'pf_percentage' => 0,
                'esi_percentage' => 0,
                'ptax_percentage' => 0,
                'other_deduction_percentage' => 0,
                'weekoff_count' => 4,
                'hourly_wage' => '',
                'notes' => '',
                'breakdown' => null,
            ]
        ]);
    }

    /**
     * Save staff salary configuration.
     */
    public function saveSalary(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'employment_type' => 'required|in:permanent,hourly',
            'monthly_salary' => 'nullable|numeric|min:0',
            'pf_percentage' => 'nullable|numeric|min:0|max:100',
            'esi_percentage' => 'nullable|numeric|min:0|max:100',
            'ptax_percentage' => 'nullable|numeric|min:0|max:100',
            'other_deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'weekoff_count' => 'nullable|integer|min:0|max:31',
            'hourly_wage' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staff = User::where('id', $request->user_id)
            ->whereIn('restaurant_id', $outletIds)
            ->first();

        if (!$staff) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Staff member not found or does not belong to your outlets.'], 404);
            }
            return back()->with('error', 'Staff member not found.');
        }

        $employmentType = $request->employment_type;

        $salaryData = [
            'restaurant_id' => $staff->restaurant_id,
            'employment_type' => $employmentType,
            'notes' => $request->notes,
        ];

        if ($employmentType === 'permanent') {
            $salaryData['monthly_salary'] = $request->filled('monthly_salary') ? (float) $request->monthly_salary : 0;
            $salaryData['pf_percentage'] = $request->filled('pf_percentage') ? (float) $request->pf_percentage : 0;
            $salaryData['esi_percentage'] = $request->filled('esi_percentage') ? (float) $request->esi_percentage : 0;
            $salaryData['ptax_percentage'] = $request->filled('ptax_percentage') ? (float) $request->ptax_percentage : 0;
            $salaryData['other_deduction_percentage'] = $request->filled('other_deduction_percentage') ? (float) $request->other_deduction_percentage : 0;
            $salaryData['weekoff_count'] = $request->filled('weekoff_count') ? (int) $request->weekoff_count : 4;
            $salaryData['hourly_wage'] = 0;
        } else {
            $salaryData['hourly_wage'] = $request->filled('hourly_wage') ? (float) $request->hourly_wage : 0;
            $salaryData['monthly_salary'] = 0;
            $salaryData['pf_percentage'] = 0;
            $salaryData['esi_percentage'] = 0;
            $salaryData['ptax_percentage'] = 0;
            $salaryData['other_deduction_percentage'] = 0;
            $salaryData['weekoff_count'] = 0;
        }

        $salary = StaffSalary::updateOrCreate(
            ['user_id' => $staff->id],
            $salaryData
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Salary configuration saved successfully!',
                'salary' => [
                    'id' => $salary->id,
                    'employment_type' => $salary->employment_type,
                    'monthly_salary' => (float) $salary->monthly_salary,
                    'pf_percentage' => (float) $salary->pf_percentage,
                    'esi_percentage' => (float) $salary->esi_percentage,
                    'ptax_percentage' => (float) $salary->ptax_percentage,
                    'other_deduction_percentage' => (float) $salary->other_deduction_percentage,
                    'weekoff_count' => (int) $salary->weekoff_count,
                    'hourly_wage' => (float) $salary->hourly_wage,
                    'notes' => $salary->notes,
                    'breakdown' => $salary->salary_breakdown,
                ]
            ]);
        }

        if ($request->input('action') === 'save_and_close') {
            return redirect()->route('restaurant.staff.index')->with('success', 'Staff salary configuration saved successfully!');
        }

        return redirect()->route('restaurant.staff.salary', $staff->id)->with('success', 'Staff salary configuration saved successfully!');
    }

    /**
     * Save staff leave policy configuration.
     */
    public function saveLeavePolicy(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020|max:2099',
            'policies' => 'required|array',
            'policies.*.annual_quota' => 'nullable|numeric|min:0|max:365',
            'policies.*.carried_forward' => 'nullable|numeric|min:0|max:365',
            'policies.*.notes' => 'nullable|string|max:500',
        ]);

        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staff = User::where('id', $request->user_id)
            ->whereIn('restaurant_id', $outletIds)
            ->firstOrFail();

        $year = (int) $request->year;
        $definitions = StaffLeavePolicy::getLeaveTypeDefinitions();

        foreach ($request->input('policies', []) as $leaveType => $pData) {
            if (!array_key_exists($leaveType, $definitions)) {
                continue;
            }

            $isCarry = isset($pData['is_carry_forward']) && ($pData['is_carry_forward'] == '1' || $pData['is_carry_forward'] == 'on');

            StaffLeavePolicy::updateOrCreate(
                [
                    'user_id' => $staff->id,
                    'leave_type' => $leaveType,
                    'year' => $year,
                ],
                [
                    'restaurant_id' => $staff->restaurant_id,
                    'annual_quota' => isset($pData['annual_quota']) && $pData['annual_quota'] !== '' ? (float) $pData['annual_quota'] : 0,
                    'carried_forward' => isset($pData['carried_forward']) && $pData['carried_forward'] !== '' ? (float) $pData['carried_forward'] : 0,
                    'is_carry_forward' => $isCarry,
                    'notes' => $pData['notes'] ?? null,
                ]
            );
        }

        if ($request->input('action') === 'save_and_close') {
            return redirect()->route('restaurant.staff.index')->with('success', 'Staff leave policies saved successfully!');
        }

        return redirect()->route('restaurant.staff.leave-policy', ['id' => $staff->id, 'year' => $year])
            ->with('success', 'Staff leave policies saved successfully!');
    }

    /**
     * Upload staff document (max 5 files per staff, max 5MB each).
     */
    public function uploadDocument(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'document_file' => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png,webp,doc,docx',
        ], [
            'document_file.max' => 'The document file may not be greater than 5 MB (5120 KB).',
            'document_file.mimes' => 'The document file must be a file of type: pdf, jpg, jpeg, png, webp, doc, docx.',
        ]);

        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staff = User::where('id', $request->user_id)
            ->whereIn('restaurant_id', $outletIds)
            ->firstOrFail();

        // Enforce maximum 5 documents per staff member
        $currentCount = StaffDocument::where('user_id', $staff->id)->count();
        if ($currentCount >= 5) {
            return redirect()->route('restaurant.staff.documents', $staff->id)
                ->with('error', 'Maximum limit of 5 documents reached for this staff member. Please delete an existing document before uploading a new one.');
        }

        $file = $request->file('document_file');
        $originalFileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileExt = strtolower($file->getClientOriginalExtension());

        $fileName = 'staff_' . $staff->id . '_' . time() . '_' . uniqid() . '.' . $fileExt;
        $destinationPath = public_path('uploads/staff_documents');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);
        $filePath = 'uploads/staff_documents/' . $fileName;

        StaffDocument::create([
            'user_id' => $staff->id,
            'restaurant_id' => $staff->restaurant_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $originalFileName,
            'file_path' => $filePath,
            'file_type' => $fileExt,
            'file_size' => $fileSize,
        ]);

        return redirect()->route('restaurant.staff.documents', $staff->id)
            ->with('success', 'Document uploaded successfully!');
    }

    /**
     * Download staff document.
     */
    public function downloadDocument($document_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'view') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized access.');
        }

        $document = StaffDocument::where('id', $document_id)->firstOrFail();

        // Verify staff belongs to user's restaurant
        if ($user->role !== 'SA') {
            $availableOutlets = $user->getAvailableOutlets();
            $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];
            $staff = User::where('id', $document->user_id)
                ->whereIn('restaurant_id', $outletIds)
                ->firstOrFail();
        }

        $fullPath = public_path($document->file_path);
        if (!file_exists($fullPath)) {
            return back()->with('error', 'File not found on server.');
        }

        return response()->download($fullPath, $document->file_name);
    }

    /**
     * Delete staff document.
     */
    public function deleteDocument($document_id)
    {
        $user = auth()->user();
        if ($user->role !== 'SA' && !$user->hasPermission('staff', 'edit') && $user->role_type !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $document = StaffDocument::where('id', $document_id)->firstOrFail();

        // Verify staff belongs to user's restaurant
        if ($user->role !== 'SA') {
            $availableOutlets = $user->getAvailableOutlets();
            $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];
            $staff = User::where('id', $document->user_id)
                ->whereIn('restaurant_id', $outletIds)
                ->firstOrFail();
        }

        $fullPath = public_path($document->file_path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $staffId = $document->user_id;
        $document->delete();

        return redirect()->route('restaurant.staff.documents', $staffId)
            ->with('success', 'Document deleted successfully!');
    }
}
