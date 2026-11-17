<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\StaffLeavePolicy;
use App\Models\StaffAttendance;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantStaffAttendanceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndAdmin()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Att Restro ' . uniqid(),
            'restaurant_id_unique' => 'REST-ATT-' . rand(100, 999),
            'parent_id' => null,
            'address' => '700 Attendance Road',
            'status' => 'A'
        ]);

        $admin = User::create([
            'name' => 'Restro Admin ' . uniqid(),
            'email' => 'admin_' . uniqid() . '@example.com',
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
            'name' => 'Staff Member ' . uniqid(),
            'email' => 'staff_' . uniqid() . '@example.com',
            'phone' => '91234' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'STAFF',
            'role_type' => $role,
            'restaurant_id' => $restaurant->id,
            'status' => 'A',
            'permissions' => json_encode(['staff' => ['view', 'create', 'edit', 'delete']])
        ]);
    }

    public function test_can_view_attendance_page_with_current_date_by_default()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        $response = $this->actingAs($admin)
            ->get(route('restaurant.attendance.index'));

        $response->assertStatus(200);
        $response->assertSee('Staff Attendance Management');
        $response->assertSee('Full Day Present (P)');
        $response->assertSee('Half Day (H)');
        $response->assertSee('Early Leave (EL)');
        $response->assertSee($staff->name);
        $response->assertSee(date('d-m-Y'));
    }

    public function test_can_change_date_to_view_other_date_attendance()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $targetDate = '2026-11-10';

        $response = $this->actingAs($admin)
            ->get(route('restaurant.attendance.index', ['date' => $targetDate]));

        $response->assertStatus(200);
        $response->assertSee('10-11-2026');
        $response->assertSee('10 Nov 2026');
    }

    public function test_can_record_full_day_present_with_optional_logout_time()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);
        $date = '2026-11-16';

        $response = $this->actingAs($admin)
            ->post(route('restaurant.attendance.store'), [
                'date' => $date,
                'user_id' => $staff->id,
                'attendance_type' => 'P',
                'login_time' => '09:30',
                'logout_time' => '', // optional empty logout time
                'notes' => 'On time morning shift',
            ]);

        $response->assertRedirect(route('restaurant.attendance.index', ['date' => $date]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('staff_attendances', [
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => $date,
            'attendance_type' => 'P',
            'login_time' => '09:30',
            'logout_time' => null,
            'notes' => 'On time morning shift',
        ]);
    }

    public function test_cannot_mark_same_staff_twice_for_same_date()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);
        $date = '2026-11-16';

        // 1st entry
        $this->actingAs($admin)
            ->post(route('restaurant.attendance.store'), [
                'date' => $date,
                'user_id' => $staff->id,
                'attendance_type' => 'P',
                'login_time' => '09:00',
            ]);

        $this->assertEquals(1, StaffAttendance::where('user_id', $staff->id)->where('attendance_date', $date)->count());

        // 2nd entry attempt for same staff on same date
        $response = $this->actingAs($admin)
            ->post(route('restaurant.attendance.store'), [
                'date' => $date,
                'user_id' => $staff->id,
                'attendance_type' => 'P',
                'login_time' => '10:00',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(1, StaffAttendance::where('user_id', $staff->id)->where('attendance_date', $date)->count());
    }

    public function test_half_day_attendance_deducts_from_leave_policy()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);
        $date = '2026-11-16';
        $year = 2026;

        // Set up leave policy for Half Day with 4 annual quota
        $policy = StaffLeavePolicy::create([
            'user_id' => $staff->id,
            'restaurant_id' => $restaurant->id,
            'leave_type' => 'half_day',
            'year' => $year,
            'annual_quota' => 4.00,
            'carried_forward' => 0.00,
            'used_count' => 0.00,
            'is_carry_forward' => false,
        ]);

        $this->assertEquals(4.00, $policy->remaining_balance);

        $response = $this->actingAs($admin)
            ->post(route('restaurant.attendance.store'), [
                'date' => $date,
                'user_id' => $staff->id,
                'attendance_type' => 'H',
                'login_time' => '09:00',
                'logout_time' => '13:00',
                'notes' => 'Half day afternoon leave',
            ]);

        $response->assertRedirect(route('restaurant.attendance.index', ['date' => $date]));
        $response->assertSessionHas('success');

        // Verify leave policy used_count incremented to 1
        $policy->refresh();
        $this->assertEquals(1.00, $policy->used_count);
        $this->assertEquals(3.00, $policy->remaining_balance);
    }

    public function test_half_day_fails_when_leave_policy_has_no_balance_remaining()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);
        $date = '2026-11-16';
        $year = 2026;

        // Set up leave policy with 0 remaining balance
        StaffLeavePolicy::create([
            'user_id' => $staff->id,
            'restaurant_id' => $restaurant->id,
            'leave_type' => 'half_day',
            'year' => $year,
            'annual_quota' => 2.00,
            'carried_forward' => 0.00,
            'used_count' => 2.00, // already used 2, remaining = 0
            'is_carry_forward' => false,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('restaurant.attendance.store'), [
                'date' => $date,
                'user_id' => $staff->id,
                'attendance_type' => 'H',
                'login_time' => '09:00',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Already used: No half day leave quota remaining', session('error'));

        // Verify no attendance was created
        $this->assertEquals(0, StaffAttendance::where('user_id', $staff->id)->count());
    }

    public function test_early_leave_attendance_deducts_early_leave_quota_and_rejects_when_depleted()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);
        $date = '2026-11-16';
        $year = 2026;

        // Set up leave policy with 1 early leave quota
        $policy = StaffLeavePolicy::create([
            'user_id' => $staff->id,
            'restaurant_id' => $restaurant->id,
            'leave_type' => 'early_leave',
            'year' => $year,
            'annual_quota' => 1.00,
            'carried_forward' => 0.00,
            'used_count' => 0.00,
            'is_carry_forward' => false,
        ]);

        // 1st early leave: succeeds
        $response = $this->actingAs($admin)
            ->post(route('restaurant.attendance.store'), [
                'date' => $date,
                'user_id' => $staff->id,
                'attendance_type' => 'EL',
                'login_time' => '09:00',
                'logout_time' => '15:00',
            ]);

        $response->assertRedirect(route('restaurant.attendance.index', ['date' => $date]));
        $policy->refresh();
        $this->assertEquals(1.00, $policy->used_count);
        $this->assertEquals(0.00, $policy->remaining_balance);

        // 2nd early leave on another date: fails because remaining balance is 0
        $staff2 = $this->createStaffMember($restaurant);
        // staff2 has 0 quota
        $response2 = $this->actingAs($admin)
            ->post(route('restaurant.attendance.store'), [
                'date' => '2026-11-17',
                'user_id' => $staff2->id,
                'attendance_type' => 'EL',
                'login_time' => '09:00',
            ]);

        $response2->assertSessionHas('error');
        $this->assertStringContainsString('Already used: No early leave quota remaining', session('error'));
    }

    public function test_can_edit_logout_time_from_attendance_list()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);
        $date = '2026-11-16';

        $att = StaffAttendance::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => $date,
            'attendance_type' => 'P',
            'login_time' => '09:00',
            'logout_time' => null,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('restaurant.attendance.update', $att->id), [
                'attendance_type' => 'P',
                'login_time' => '09:00',
                'logout_time' => '18:00',
                'notes' => 'Shift ended on time',
            ]);

        $response->assertRedirect(route('restaurant.attendance.index', ['date' => $date]));
        $response->assertSessionHas('success');

        $att->refresh();
        $this->assertEquals('18:00', $att->logout_time);
        $this->assertEquals('Shift ended on time', $att->notes);
        $this->assertEquals('9h', $att->work_duration);
    }

    public function test_delete_attendance_removes_record_and_restores_deducted_leave()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);
        $date = '2026-11-16';
        $year = 2026;

        $policy = StaffLeavePolicy::create([
            'user_id' => $staff->id,
            'restaurant_id' => $restaurant->id,
            'leave_type' => 'half_day',
            'year' => $year,
            'annual_quota' => 5.00,
            'carried_forward' => 0.00,
            'used_count' => 1.00,
            'is_carry_forward' => false,
        ]);

        $att = StaffAttendance::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => $date,
            'attendance_type' => 'H',
            'login_time' => '09:00',
            'logout_time' => '13:00',
            'created_by' => $admin->id,
        ]);

        $deleteResponse = $this->actingAs($admin)
            ->get(route('restaurant.attendance.delete', $att->id));

        $deleteResponse->assertRedirect(route('restaurant.attendance.index', ['date' => $date]));
        $deleteResponse->assertSessionHas('success');

        // Check attendance removed
        $this->assertDatabaseMissing('staff_attendances', ['id' => $att->id]);

        // Check leave quota restored
        $policy->refresh();
        $this->assertEquals(0.00, $policy->used_count);
        $this->assertEquals(5.00, $policy->remaining_balance);
    }

    public function test_can_export_daily_attendance_excel_csv()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);
        $date = '2026-11-16';

        StaffAttendance::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => $date,
            'attendance_type' => 'P',
            'login_time' => '09:00',
            'logout_time' => '17:30',
            'notes' => 'Export test log',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('restaurant.attendance.export', ['date' => $date]));

        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('content-disposition'));
        $this->assertStringContainsString("attendance_{$date}.csv", $response->headers->get('content-disposition'));
    }

    public function test_super_admin_can_view_attendance_page_with_staff_dropdown_and_switch_restaurant()
    {
        [$restaurant1, ] = $this->setupRestaurantAndAdmin();
        [$restaurant2, ] = $this->setupRestaurantAndAdmin();

        $staff1 = $this->createStaffMember($restaurant1, 'Cashier');
        $staff2 = $this->createStaffMember($restaurant2, 'Chef');

        $superAdmin = User::create([
            'name' => 'Super Admin Test ' . uniqid(),
            'email' => 'superadmin_' . uniqid() . '@example.com',
            'phone' => '90000' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'SA',
            'role_type' => 'SUPER_ADMIN',
            'restaurant_id' => null,
            'status' => 'A'
        ]);

        // 1. Visit attendance specifying restaurant 1
        $response1 = $this->actingAs($superAdmin)
            ->get(route('restaurant.attendance.index', ['restaurant_id' => $restaurant1->id]));

        $response1->assertStatus(200);
        $response1->assertSee($staff1->name);
        $response1->assertDontSee($staff2->name);

        // 2. Switch to restaurant 2
        $response2 = $this->actingAs($superAdmin)
            ->get(route('restaurant.attendance.index', ['restaurant_id' => $restaurant2->id]));

        $response2->assertStatus(200);
        $response2->assertSee($staff2->name);
        $response2->assertDontSee($staff1->name);

        // 3. Super Admin records attendance for staff2
        $date = '2026-11-16';
        $postResponse = $this->actingAs($superAdmin)
            ->post(route('restaurant.attendance.store'), [
                'date' => $date,
                'user_id' => $staff2->id,
                'attendance_type' => 'P',
                'login_time' => '10:00',
                'logout_time' => '19:00',
                'notes' => 'Marked by Super Admin',
                'restaurant_id' => $restaurant2->id,
            ]);

        $postResponse->assertRedirect(route('restaurant.attendance.index', ['date' => $date, 'restaurant_id' => $restaurant2->id]));
        $this->assertDatabaseHas('staff_attendances', [
            'restaurant_id' => $restaurant2->id,
            'user_id' => $staff2->id,
            'attendance_date' => $date,
            'attendance_type' => 'P',
            'created_by' => $superAdmin->id,
        ]);
    }

    public function test_can_record_weekoff_attendance_without_leave_deduction_and_optional_login_time()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);
        $date = '2026-11-16';

        // Set up leave policy
        StaffLeavePolicy::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'year' => 2026,
            'leave_type' => 'sick_leave',
            'annual_quota' => 10.00,
            'used_count' => 0.00,
            'carried_forward' => 0.00,
            'is_carry_forward' => false
        ]);

        // Submit Weekoff attendance without login/logout times
        $response = $this->actingAs($admin)
            ->post(route('restaurant.attendance.store'), [
                'date' => $date,
                'user_id' => $staff->id,
                'attendance_type' => 'WO',
                'login_time' => '',
                'logout_time' => '',
                'notes' => 'Scheduled Sunday week-off',
            ]);

        $response->assertRedirect(route('restaurant.attendance.index', ['date' => $date]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('staff_attendances', [
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'attendance_date' => $date,
            'attendance_type' => 'WO',
            'login_time' => null,
            'notes' => 'Scheduled Sunday week-off',
        ]);

        // Verify viewing page shows Weekoff badge & count
        $viewResponse = $this->actingAs($admin)
            ->get(route('restaurant.attendance.index', ['date' => $date]));

        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Weekoff (WO)');
        $viewResponse->assertSee('badge-attendance-weekoff');
    }
}

