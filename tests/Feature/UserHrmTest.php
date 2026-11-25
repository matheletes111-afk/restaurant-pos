<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use App\Models\StaffExtra;
use App\Models\StaffPayroll;
use App\Models\StaffSalary;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserHrmTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $staffUser1;
    protected $staffUser2;
    protected $payroll1;
    protected $payroll2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::first();
        if (!$this->restaurant) {
            $this->restaurant = RestaurantMaster::create([
                'name' => 'HRM Test Resto',
                'address' => '789 Gourmet Way',
                'status' => 'A',
            ]);
        }

        $this->staffUser1 = User::create([
            'name' => 'Alice Johnson',
            'email' => 'alice_' . uniqid() . '@example.com',
            'phone' => '9876543210',
            'password' => bcrypt('password123'),
            'restaurant_id' => $this->restaurant->id,
            'role' => 'STAFF',
            'role_type' => 'WAITER',
            'status' => 'A',
        ]);

        $this->staffUser2 = User::create([
            'name' => 'Bob Smith',
            'email' => 'bob_' . uniqid() . '@example.com',
            'phone' => '9876543211',
            'password' => bcrypt('password123'),
            'restaurant_id' => $this->restaurant->id,
            'role' => 'STAFF',
            'role_type' => 'CHEF',
            'status' => 'A',
        ]);

        // Create salary setup for Alice
        StaffSalary::create([
            'user_id' => $this->staffUser1->id,
            'restaurant_id' => $this->restaurant->id,
            'employment_type' => 'permanent',
            'monthly_salary' => 30000,
            'pf_percentage' => 12,
            'esi_percentage' => 0.75,
            'ptax_type' => 'fixed',
            'ptax_amount' => 200,
            'other_deduction_type' => 'fixed',
            'other_deduction_amount' => 100,
        ]);

        // Attendance records for Alice and Bob
        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffUser1->id,
            'attendance_date' => Carbon::now()->format('Y-m-d'),
            'attendance_status' => 'present',
            'employment_type' => 'permanent',
            'is_permanent' => 1,
            'login_time' => '09:00:00',
            'logout_time' => '17:00:00',
        ]);

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffUser2->id,
            'attendance_date' => Carbon::now()->format('Y-m-d'),
            'attendance_status' => 'present',
            'employment_type' => 'permanent',
            'is_permanent' => 1,
            'login_time' => '10:00:00',
            'logout_time' => '18:00:00',
        ]);

        // Extras for Alice
        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffUser1->id,
            'type' => 'earning',
            'amount' => 1500,
            'entry_date' => Carbon::now()->format('Y-m-d'),
            'remarks' => 'Sunday Festival Bonus',
        ]);

        // Payroll records
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        $this->payroll1 = StaffPayroll::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffUser1->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'total_days' => 30,
            'present_days' => 28,
            'absent_days' => 2,
            'leave_days' => 0,
            'base_salary' => 30000,
            'absent_deduction' => 2000,
            'earned_salary' => 28000,
            'pf_amount' => 3360,
            'esi_amount' => 210,
            'other_deductions' => 300,
            'extra_earnings' => 1500,
            'extra_deductions' => 0,
            'advance_amount' => 0,
            'final_salary' => 25630,
            'actual_payable' => 25630,
            'is_paid' => 1,
            'paid_date' => Carbon::now()->format('Y-m-d'),
            'notes' => 'Disbursed via bank transfer',
        ]);

        $this->payroll2 = StaffPayroll::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffUser2->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'total_days' => 30,
            'present_days' => 30,
            'absent_days' => 0,
            'leave_days' => 0,
            'base_salary' => 35000,
            'absent_deduction' => 0,
            'earned_salary' => 35000,
            'pf_amount' => 0,
            'esi_amount' => 0,
            'other_deductions' => 0,
            'extra_earnings' => 0,
            'extra_deductions' => 0,
            'advance_amount' => 0,
            'final_salary' => 35000,
            'actual_payable' => 35000,
            'is_paid' => 0,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_user_hrm()
    {
        $response = $this->get(route('user.hrm.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_staff_can_view_user_hrm_dashboard()
    {
        $response = $this->actingAs($this->staffUser1)->get(route('user.hrm.index'));
        $response->assertStatus(200);
        $response->assertSee('User HRM');
        $response->assertSee($this->staffUser1->name);
        $response->assertSee('My Attendance');
        $response->assertSee('My Leaves');
        $response->assertSee('My Extras');
        $response->assertSee('My Salary');
    }

    public function test_user_hrm_strict_isolation_cannot_see_other_staff_records()
    {
        // When Alice accesses HRM, she shouldn't see Bob's name or Bob's salary amounts
        $response = $this->actingAs($this->staffUser1)->get(route('user.hrm.index', ['tab' => 'salary']));
        $response->assertStatus(200);
        $response->assertSee('₹25,630.00'); // Alice's payroll
        $response->assertDontSee('₹35,000.00'); // Bob's payroll
    }

    public function test_authenticated_staff_can_export_attendance_excel()
    {
        $response = $this->actingAs($this->staffUser1)->get(route('user.hrm.attendance.export', [
            'month' => date('n'),
            'year' => date('Y'),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_authenticated_staff_can_apply_for_leave()
    {
        $startDate = Carbon::now()->addDays(5)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(6)->format('Y-m-d');

        $response = $this->actingAs($this->staffUser1)->post(route('user.hrm.leaves.apply'), [
            'leave_type' => 'sick_leave',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => 'Doctor appointment and rest',
        ]);

        $response->assertRedirect(route('user.hrm.index', ['tab' => 'leaves']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('staff_leaves', [
            'user_id' => $this->staffUser1->id,
            'restaurant_id' => $this->restaurant->id,
            'leave_type' => 'sick_leave',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days_count' => 2,
            'status' => 'pending',
        ]);

        // Verify status shows Pending Approval in staff's leave tab
        $viewResponse = $this->actingAs($this->staffUser1)->get(route('user.hrm.index', ['tab' => 'leaves']));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Pending Approval');
    }

    public function test_authenticated_staff_can_fetch_own_salary_slip_data()
    {
        $response = $this->actingAs($this->staffUser1)->get(route('user.hrm.salary.slip', ['id' => $this->payroll1->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $this->payroll1->id,
                'staff_name' => $this->staffUser1->name,
                'base_salary' => 30000,
                'actual_payable' => 25630,
                'is_paid' => true,
            ]
        ]);
    }

    public function test_authenticated_staff_cannot_fetch_other_staff_salary_slip()
    {
        // Alice tries to fetch Bob's payslip JSON
        $response = $this->actingAs($this->staffUser1)->get(route('user.hrm.salary.slip', ['id' => $this->payroll2->id]));
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Salary record not found.',
        ]);
    }

    public function test_authenticated_staff_can_view_printable_payslip()
    {
        $response = $this->actingAs($this->staffUser1)->get(route('user.hrm.salary.slip.print', ['id' => $this->payroll1->id]));

        $response->assertStatus(200);
        $response->assertSee('PAYSLIP');
        $response->assertSee($this->staffUser1->name);
        $response->assertSee('₹25,630.00');
    }

    public function test_authenticated_staff_cannot_view_other_staff_printable_payslip()
    {
        // Alice tries to open Bob's printable payslip
        $response = $this->actingAs($this->staffUser1)->get(route('user.hrm.salary.slip.print', ['id' => $this->payroll2->id]));
        $response->assertStatus(404);
    }

    public function test_admin_can_save_restaurant_geofence_location()
    {
        $admin = User::create([
            'name' => 'Admin Manager',
            'email' => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'restaurant_id' => $this->restaurant->id,
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'status' => 'A',
        ]);

        $response = $this->actingAs($admin)->post(route('restaurant.attendance.save-geofence'), [
            'restaurant_id' => $this->restaurant->id,
            'latitude' => 22.572646,
            'longitude' => 88.363895,
            'geofence_radius_meters' => 50,
            'geofence_enabled' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->restaurant->refresh();
        $this->assertEquals(22.572646, (float) $this->restaurant->latitude);
        $this->assertEquals(88.363895, (float) $this->restaurant->longitude);
        $this->assertEquals(50, $this->restaurant->geofence_radius_meters);
        $this->assertTrue((bool) $this->restaurant->geofence_enabled);
    }

    public function test_staff_cannot_clock_in_outside_50m_geofence()
    {
        $this->restaurant->update([
            'latitude' => 22.572646,
            'longitude' => 88.363895,
            'geofence_radius_meters' => 50,
            'geofence_enabled' => true,
        ]);

        // Staff is ~600m away
        $response = $this->actingAs($this->staffUser1)->post(route('user.hrm.attendance.clock-in'), [
            'latitude' => 22.578000,
            'longitude' => 88.363895,
        ]);

        $response->assertRedirect(route('user.hrm.index', ['tab' => 'attendance']));
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('staff_attendances', [
            'user_id' => $this->staffUser1->id,
            'attendance_date' => date('Y-m-d'),
            'checkin_lat' => 22.578000,
        ]);
    }

    public function test_staff_can_clock_in_within_50m_geofence_and_is_marked_present()
    {
        $this->restaurant->update([
            'latitude' => 22.572646,
            'longitude' => 88.363895,
            'geofence_radius_meters' => 50,
            'geofence_enabled' => true,
        ]);

        // Delete any today record for fresh test
        StaffAttendance::where('user_id', $this->staffUser1->id)
            ->where('attendance_date', date('Y-m-d'))
            ->delete();

        // Staff is ~6m away from restaurant
        $response = $this->actingAs($this->staffUser1)->post(route('user.hrm.attendance.clock-in'), [
            'latitude' => 22.572700,
            'longitude' => 88.363895,
            'notes' => 'Clock-in from front desk',
        ]);

        $response->assertRedirect(route('user.hrm.index', ['tab' => 'attendance']));
        $response->assertSessionHas('success');

        $attendance = StaffAttendance::where('user_id', $this->staffUser1->id)
            ->where('attendance_date', date('Y-m-d'))
            ->first();

        $this->assertNotNull($attendance);
        $this->assertEquals('P', $attendance->attendance_type);
        $this->assertEquals('permanent', $attendance->employee_type);
        $this->assertNotNull($attendance->login_time);
        $this->assertLessThanOrEqual(50, (float) $attendance->checkin_distance_meters);
        $this->assertEquals('verified', $attendance->geofence_status);
    }

    public function test_staff_can_clock_out_and_logout_time_is_captured()
    {
        $today = date('Y-m-d');
        StaffAttendance::where('user_id', $this->staffUser1->id)
            ->where('attendance_date', $today)
            ->delete();

        $attendance = StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffUser1->id,
            'attendance_date' => $today,
            'attendance_type' => 'P',
            'employee_type' => 'permanent',
            'login_time' => '09:00:00',
            'logout_time' => null,
        ]);

        $response = $this->actingAs($this->staffUser1)->post(route('user.hrm.attendance.clock-out'), [
            'latitude' => 22.572646,
            'longitude' => 88.363895,
        ]);

        $response->assertRedirect(route('user.hrm.index', ['tab' => 'attendance']));
        $response->assertSessionHas('success');

        $attendance->refresh();
        $this->assertNotNull($attendance->logout_time);
    }
}
