<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StaffAttendanceReportTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $adminUser;
    protected $staffPermanent;
    protected $staffHourly;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::first();
        if (!$this->restaurant) {
            $this->restaurant = RestaurantMaster::create([
                'name' => 'Report Test Kitchen',
                'address' => 'Test Avenue 10',
                'status' => 'A',
            ]);
        }

        $this->adminUser = User::firstOrCreate(
            ['email' => 'attendance_report_admin@example.com'],
            [
                'name' => 'Report Admin',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'RES',
                'role_type' => 'ADMIN',
                'status' => 'A',
            ]
        );

        $this->staffPermanent = User::firstOrCreate(
            ['email' => 'att_perm_staff@example.com'],
            [
                'name' => 'Permanent Chef',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'CHEF',
                'status' => 'A',
            ]
        );

        $this->staffHourly = User::firstOrCreate(
            ['email' => 'att_hourly_staff@example.com'],
            [
                'name' => 'Hourly Waiter',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'WAITER',
                'status' => 'A',
            ]
        );
    }

    public function test_unauthenticated_user_cannot_access_staff_attendance_report()
    {
        $response = $this->get(route('report.staff.attendance'));
        $this->assertContains($response->status(), [302, 401]);
    }

    public function test_admin_can_view_attendance_report_with_default_month_range()
    {
        $currentMonthDay = Carbon::now()->startOfMonth()->addDays(2)->format('Y-m-d');

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffPermanent->id,
            'attendance_date' => $currentMonthDay,
            'attendance_type' => 'P',
            'employee_type' => 'permanent',
            'login_time' => '09:00:00',
            'logout_time' => '17:00:00',
            'notes' => 'On time duty',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('report.staff.attendance'));

        $response->assertStatus(200);
        $response->assertViewIs('report.staff-attendance');
        $response->assertViewHas('attendances');
        $response->assertViewHas('summary');
        $response->assertSee('Staff Attendance Report');
        $response->assertSee('Permanent Chef');
        $response->assertSee('On time duty');
    }

    public function test_can_filter_attendance_report_by_date_range()
    {
        $targetDate = '2026-05-15';
        $outsideDate = '2026-04-10';

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffPermanent->id,
            'attendance_date' => $targetDate,
            'attendance_type' => 'P',
            'employee_type' => 'permanent',
            'login_time' => '09:00:00',
            'logout_time' => '18:00:00',
            'notes' => 'May Duty Record',
            'created_by' => $this->adminUser->id,
        ]);

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffPermanent->id,
            'attendance_date' => $outsideDate,
            'attendance_type' => 'P',
            'employee_type' => 'permanent',
            'login_time' => '09:00:00',
            'logout_time' => '18:00:00',
            'notes' => 'April Duty Record',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('report.staff.attendance', [
                'from_date' => '2026-05-01',
                'to_date' => '2026-05-31',
            ]));

        $response->assertStatus(200);
        $attendances = $response->viewData('attendances');
        $this->assertTrue($attendances->contains('notes', 'May Duty Record'));
        $this->assertFalse($attendances->contains('notes', 'April Duty Record'));
    }

    public function test_can_filter_attendance_report_by_staff_member()
    {
        $testDate = Carbon::now()->startOfMonth()->addDays(5)->format('Y-m-d');

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffPermanent->id,
            'attendance_date' => $testDate,
            'attendance_type' => 'P',
            'employee_type' => 'permanent',
            'notes' => 'Permanent Only Log',
            'created_by' => $this->adminUser->id,
        ]);

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffHourly->id,
            'attendance_date' => $testDate,
            'attendance_type' => 'P',
            'employee_type' => 'hourly',
            'notes' => 'Hourly Only Log',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('report.staff.attendance', [
                'from_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'to_date' => Carbon::now()->format('Y-m-d'),
                'staff_id' => $this->staffHourly->id,
            ]));

        $response->assertStatus(200);
        $attendances = $response->viewData('attendances');
        $this->assertTrue($attendances->contains('notes', 'Hourly Only Log'));
        $this->assertFalse($attendances->contains('notes', 'Permanent Only Log'));
    }

    public function test_can_filter_attendance_report_by_attendance_status()
    {
        $testDate = Carbon::now()->startOfMonth()->addDays(3)->format('Y-m-d');

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffPermanent->id,
            'attendance_date' => $testDate,
            'attendance_type' => 'H', // Half Day
            'employee_type' => 'permanent',
            'notes' => 'Half Day Record',
            'created_by' => $this->adminUser->id,
        ]);

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffPermanent->id,
            'attendance_date' => Carbon::parse($testDate)->addDay()->format('Y-m-d'),
            'attendance_type' => 'P', // Full Day
            'employee_type' => 'permanent',
            'notes' => 'Full Day Record',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('report.staff.attendance', [
                'from_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'to_date' => Carbon::now()->format('Y-m-d'),
                'attendance_type' => 'H',
            ]));

        $response->assertStatus(200);
        $attendances = $response->viewData('attendances');
        $this->assertTrue($attendances->contains('notes', 'Half Day Record'));
        $this->assertFalse($attendances->contains('notes', 'Full Day Record'));
    }

    public function test_can_filter_attendance_report_by_wage_type()
    {
        $testDate = Carbon::now()->startOfMonth()->addDays(4)->format('Y-m-d');

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffPermanent->id,
            'attendance_date' => $testDate,
            'attendance_type' => 'P',
            'employee_type' => 'permanent',
            'notes' => 'Perm Wage Record',
            'created_by' => $this->adminUser->id,
        ]);

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffHourly->id,
            'attendance_date' => $testDate,
            'attendance_type' => 'P',
            'employee_type' => 'hourly',
            'notes' => 'Hourly Wage Record',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('report.staff.attendance', [
                'from_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'to_date' => Carbon::now()->format('Y-m-d'),
                'employee_type' => 'hourly',
            ]));

        $response->assertStatus(200);
        $attendances = $response->viewData('attendances');
        $this->assertTrue($attendances->contains('notes', 'Hourly Wage Record'));
        $this->assertFalse($attendances->contains('notes', 'Perm Wage Record'));
    }

    public function test_admin_can_export_staff_attendance_to_excel()
    {
        $testDate = Carbon::now()->startOfMonth()->addDays(1)->format('Y-m-d');

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffPermanent->id,
            'attendance_date' => $testDate,
            'attendance_type' => 'P',
            'employee_type' => 'permanent',
            'login_time' => '10:00:00',
            'logout_time' => '19:00:00',
            'notes' => 'Export Check Record',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('report.staff.attendance.export', [
                'from_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'to_date' => Carbon::now()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'spreadsheet') ||
            str_contains($response->headers->get('content-type'), 'vnd.openxmlformats')
        );
        $this->assertStringContainsString('attachment; filename=staff_attendance_report_', $response->headers->get('content-disposition'));
    }
}
