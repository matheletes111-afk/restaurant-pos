<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\StaffAttendance;
use App\Models\StaffLeavePolicy;
use App\Models\StaffLeave;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantStaffCalendarTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndAdmin()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Cal Restro ' . uniqid(),
            'restaurant_id_unique' => 'REST-CAL-' . rand(100, 999),
            'parent_id' => null,
            'address' => '300 Calendar Lane',
            'status' => 'A'
        ]);

        $admin = User::create([
            'name' => 'Cal Admin ' . uniqid(),
            'email' => 'cal_admin_' . uniqid() . '@example.com',
            'phone' => '98765' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $plan = Plan::create([
            'name' => 'Staff Plan ' . uniqid(),
            'price' => 999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(29),
            'amount' => 999.00,
            'currency' => 'INR',
        ]);

        return [$restaurant, $admin];
    }

    protected function createStaffMember($restaurant, $role = 'Waiter')
    {
        return User::create([
            'name' => 'Cal Staff ' . uniqid(),
            'email' => 'staff_cal_' . uniqid() . '@example.com',
            'phone' => '91234' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'STAFF',
            'role_type' => $role,
            'restaurant_id' => $restaurant->id,
            'status' => 'A',
            'permissions' => json_encode(['staff' => ['view', 'create', 'edit', 'delete']])
        ]);
    }

    public function test_can_access_staff_calendar_tab_after_document_management()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        $response = $this->actingAs($admin)
            ->get(route('restaurant.staff.calendar', $staff->id));

        $response->assertStatus(200);
        $response->assertSee('Calendar');
        $response->assertSee('Activity &amp; Attendance Calendar', false);
        $response->assertSee($staff->name);
        $response->assertSee('Full Day (P)');
        $response->assertSee('Half Day (H)');
        $response->assertSee('Early Leave');
        $response->assertSee('Weekoff (WO)');
        $response->assertSee('On Leave');
        $response->assertSee('Absent / Unmarked');
    }

    public function test_calendar_displays_present_halfday_earlyleave_weekoff_leave_and_absent()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        $year = 2026;
        $month = 11;

        // 1. Day 1: Present (P)
        StaffAttendance::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => '2026-11-01',
            'attendance_type' => 'P',
            'login_time' => '09:00',
            'logout_time' => '18:00',
            'created_by' => $admin->id,
        ]);

        // 2. Day 2: Half Day (H)
        StaffAttendance::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => '2026-11-02',
            'attendance_type' => 'H',
            'login_time' => '09:30',
            'logout_time' => '13:30',
            'created_by' => $admin->id,
        ]);

        // 3. Day 3: Early Leave (EL)
        StaffAttendance::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => '2026-11-03',
            'attendance_type' => 'EL',
            'login_time' => '09:00',
            'logout_time' => '15:00',
            'created_by' => $admin->id,
        ]);

        // 4. Day 4: Weekoff (WO)
        StaffAttendance::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => '2026-11-04',
            'attendance_type' => 'WO',
            'created_by' => $admin->id,
        ]);

        // 5. Day 5: Approved Leave (Sick Leave)
        StaffLeave::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'leave_type' => 'sick_leave',
            'start_date' => '2026-11-05',
            'end_date' => '2026-11-05',
            'days_count' => 1.00,
            'reason' => 'Doctor appointment',
            'status' => 'approved',
            'created_by' => $admin->id,
        ]);

        // Visit calendar
        $response = $this->actingAs($admin)
            ->get(route('restaurant.staff.calendar', [
                'id' => $staff->id,
                'month' => $month,
                'year' => $year,
                'view' => 'calendar'
            ]));

        $response->assertStatus(200);
        $response->assertSee('Present (P)');
        $response->assertSee('Half Day (H)');
        $response->assertSee('Early Leave');
        $response->assertSee('Weekoff (WO)');
        $response->assertSee('Sick Leave');
        $response->assertSee('Absent');
    }

    public function test_can_switch_to_list_view_subtab()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        // Day 1: Present
        StaffAttendance::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => '2026-11-01',
            'attendance_type' => 'P',
            'login_time' => '09:00',
            'logout_time' => '18:00',
            'notes' => 'Great shift',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('restaurant.staff.calendar', [
                'id' => $staff->id,
                'month' => 11,
                'year' => 2026,
                'view' => 'list'
            ]));

        $response->assertStatus(200);
        $response->assertSee('Day-by-Day Activity List');
        $response->assertSee('01 Nov 2026');
        $response->assertSee('Full Day Present (P)');
        $response->assertSee('Great shift');
    }

    public function test_can_switch_month_and_year()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        $response = $this->actingAs($admin)
            ->get(route('restaurant.staff.calendar', [
                'id' => $staff->id,
                'month' => 10,
                'year' => 2026,
            ]));

        $response->assertStatus(200);
        $response->assertSee('October 2026');
    }

    public function test_operations_router_handles_calendar_tab()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        $response = $this->actingAs($admin)
            ->get(route('restaurant.staff.operations', [
                'id' => $staff->id,
                'tab' => 'calendar'
            ]));

        $response->assertStatus(200);
        $response->assertSee('Calendar');
        $response->assertSee('tab-calendar-btn');
    }

    public function test_can_export_staff_monthly_calendar_to_csv()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        StaffAttendance::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => '2026-11-10',
            'attendance_type' => 'P',
            'login_time' => '09:00',
            'logout_time' => '18:00',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('restaurant.staff.calendar.export', [
                'id' => $staff->id,
                'month' => 11,
                'year' => 2026,
            ]));

        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('content-disposition'));
        $this->assertStringContainsString('staff_activity_', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition'));
    }
}

