<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\StaffSalary;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use App\Models\StaffExtra;
use App\Models\StaffPayroll;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StaffPayrollTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $adminUser;
    protected $staffMember;
    protected $salary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::first();
        if (!$this->restaurant) {
            $this->restaurant = RestaurantMaster::create([
                'name' => 'Payroll Test Resto',
                'address' => 'Corporate Hub 101',
                'status' => 'A',
            ]);
        }

        $this->adminUser = User::firstOrCreate(
            ['email' => 'payroll_admin@example.com'],
            [
                'name' => 'Payroll Admin',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'RES',
                'role_type' => 'ADMIN',
                'status' => 'A',
            ]
        );

        $this->staffMember = User::firstOrCreate(
            ['email' => 'permanent_staff@example.com'],
            [
                'name' => 'Michael Scott',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'MANAGER',
                'status' => 'A',
            ]
        );

        // Configure permanent salary: ₹30,000 monthly, 10% PF, 2% ESI, 0% Other, 4 weekoffs
        $this->salary = StaffSalary::updateOrCreate(
            ['user_id' => $this->staffMember->id],
            [
                'restaurant_id' => $this->restaurant->id,
                'employment_type' => 'permanent',
                'monthly_salary' => 30000.00,
                'pf_percentage' => 10.00,
                'esi_percentage' => 2.00,
                'ptax_percentage' => 0.00,
                'other_deduction_percentage' => 0.00,
                'weekoff_count' => 4,
            ]
        );
    }

    public function test_unauthenticated_user_cannot_access_payroll()
    {
        $response = $this->get(route('restaurant.salary.index'));
        $this->assertContains($response->status(), [302, 401]);
    }

    public function test_admin_can_view_payroll_dashboard()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.salary.index', ['month' => 11, 'year' => 2026]));

        $response->assertStatus(200);
        $response->assertViewIs('restaurant.staff_payroll.index');
        $response->assertViewHas(['year', 'month', 'daysInMonth', 'staffList', 'stats']);
        $response->assertSee('Permanent Staff Payroll');
        $response->assertSee('Michael Scott');
        $response->assertSee('Actual Payable');
    }

    public function test_attendance_and_absent_salary_reduction()
    {
        // November has 30 days. Daily rate = 30,000 / 30 = 1,000/day.
        // Record 10 days present, 4 weekoffs = 14 paid duty days.
        // Absent = 30 - 14 = 16 days absent.
        // Absent reduction = 16 * 1,000 = ₹16,000.
        // Earned salary = 30,000 - 16,000 = ₹14,000.

        StaffAttendance::where('user_id', $this->staffMember->id)
            ->whereBetween('attendance_date', ['2026-11-01', '2026-11-30'])
            ->delete();

        for ($day = 1; $day <= 10; $day++) {
            StaffAttendance::create([
                'restaurant_id' => $this->restaurant->id,
                'user_id' => $this->staffMember->id,
                'attendance_date' => sprintf('2026-11-%02d', $day),
                'attendance_type' => 'P',
                'created_by' => $this->adminUser->id,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.salary.index', ['month' => 11, 'year' => 2026]));

        $response->assertStatus(200);
        $staffList = $response->viewData('staffList');
        $scottData = collect($staffList)->firstWhere('user_id', $this->staffMember->id);

        $this->assertNotNull($scottData);
        $this->assertEquals(10.0, (float)$scottData['present_days']);
        $this->assertEquals(4.0, (float)$scottData['weekoff_days']);
        $this->assertEquals(14.0, (float)$scottData['total_paid_days']);
        $this->assertEquals(16.0, (float)$scottData['absent_days']);
        $this->assertEquals(16000.00, (float)$scottData['absent_deduction']);
        $this->assertEquals(14000.00, (float)$scottData['earned_salary']);

        // Statutory deductions on ₹14,000: PF 10% = 1,400, ESI 2% = 280. Total = 1,680.
        $this->assertEquals(1400.00, (float)$scottData['pf_amount']);
        $this->assertEquals(280.00, (float)$scottData['esi_amount']);
        $this->assertEquals(1680.00, (float)$scottData['total_statutory']);
        // Net salary = 14,000 - 1,680 = ₹12,320.
        $this->assertEquals(12320.00, (float)$scottData['net_salary']);
    }

    public function test_leaves_and_extras_impact_on_final_salary()
    {
        // Add 2 days approved Paid Leave (PL) in Nov 2026
        StaffLeave::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'leave_type' => 'paid_leave',
            'start_date' => '2026-11-11',
            'end_date' => '2026-11-12',
            'days_count' => 2.0,
            'status' => 'approved',
            'created_by' => $this->adminUser->id,
        ]);

        // Add Extras in Nov 2026:
        // +₹2,000 Performance Bonus (Earning)
        // -₹500 Late Penalty (Deduction)
        // -₹3,000 Salary Advance (Advance)
        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Earning',
            'amount' => 2000.00,
            'entry_date' => '2026-11-15',
            'remarks' => 'Sales target incentive',
            'created_by' => $this->adminUser->id,
        ]);

        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Deduction',
            'amount' => 500.00,
            'entry_date' => '2026-11-18',
            'remarks' => 'Damaged equipment penalty',
            'created_by' => $this->adminUser->id,
        ]);

        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Advance',
            'amount' => 3000.00,
            'entry_date' => '2026-11-20',
            'remarks' => 'Salary advance payout',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.salary.index', ['month' => 11, 'year' => 2026]));

        $response->assertStatus(200);
        $staffList = $response->viewData('staffList');
        $scottData = collect($staffList)->firstWhere('user_id', $this->staffMember->id);

        $this->assertEquals(2.0, (float)$scottData['leave_days']);
        $this->assertEquals(2000.00, (float)$scottData['extra_earnings']);
        $this->assertEquals(500.00, (float)$scottData['extra_deductions']);
        $this->assertEquals(3000.00, (float)$scottData['advance_amount']);

        // Check formula: Net Salary + Extras Earnings - Penalties - Advances = Final Salary
        $expectedFinal = max(0, $scottData['net_salary'] + 2000 - 500 - 3000);
        $this->assertEquals($expectedFinal, (float)$scottData['final_salary']);
    }

    public function test_can_save_single_payroll_row_with_paid_checkbox_and_actual_payable()
    {
        $payload = [
            'user_id' => $this->staffMember->id,
            'year' => 2026,
            'month' => 11,
            'actual_payable' => 15500.00,
            'is_paid' => true,
            'paid_date' => '2026-11-30',
            'notes' => 'Disbursed via direct NEFT bank transfer',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.salary.save-single'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('staff_payrolls', [
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'year' => 2026,
            'month' => 11,
            'actual_payable' => 15500.00,
            'is_paid' => 1,
            'paid_date' => '2026-11-30',
            'notes' => 'Disbursed via direct NEFT bank transfer',
        ]);
    }

    public function test_can_batch_save_payroll_rows()
    {
        $payload = [
            'year' => 2026,
            'month' => 11,
            'payroll_rows' => [
                [
                    'user_id' => $this->staffMember->id,
                    'is_paid' => true,
                    'actual_payable' => 16000.00,
                    'paid_date' => '2026-11-30',
                    'notes' => 'Batch payroll paid',
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.salary.save-batch'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('staff_payrolls', [
            'user_id' => $this->staffMember->id,
            'year' => 2026,
            'month' => 11,
            'actual_payable' => 16000.00,
            'is_paid' => 1,
        ]);
    }

    public function test_can_export_payroll_to_csv()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.salary.export', ['year' => 2026, 'month' => 11]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('Michael Scott', $content);
        $this->assertStringContainsString('Base Monthly Salary (₹)', $content);
        $this->assertStringContainsString('Actual Payable (₹)', $content);
    }

    public function test_staff_without_preconfigured_salary_is_included_and_can_set_base_salary()
    {
        // Create staff without salary record
        $newStaff = User::create([
            'name' => 'Dwight Schrute',
            'email' => 'dwight_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'restaurant_id' => $this->restaurant->id,
            'role' => 'STAFF',
            'role_type' => 'WAITER',
            'status' => 'A',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.salary.index', ['month' => 11, 'year' => 2026]));

        $response->assertStatus(200);
        $staffList = $response->viewData('staffList');
        $dwightData = collect($staffList)->firstWhere('user_id', $newStaff->id);

        $this->assertNotNull($dwightData, 'Staff without salary profile must still be included in permanent payroll');
        $this->assertEquals(0.00, (float)$dwightData['base_salary']);

        // Set base salary inline via save-single
        $saveResponse = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.salary.save-single'), [
                'user_id' => $newStaff->id,
                'year' => 2026,
                'month' => 11,
                'base_salary' => 24000.00,
                'actual_payable' => 24000.00,
                'is_paid' => true,
                'paid_date' => '2026-11-30',
                'notes' => 'Inline base salary set',
            ]);

        $saveResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('staff_salaries', [
            'user_id' => $newStaff->id,
            'employment_type' => 'permanent',
            'monthly_salary' => 24000.00,
        ]);

        $this->assertDatabaseHas('staff_payrolls', [
            'user_id' => $newStaff->id,
            'year' => 2026,
            'month' => 11,
            'actual_payable' => 24000.00,
            'is_paid' => 1,
        ]);
    }

    public function test_hourly_tab_calculates_hours_from_login_logout_and_hourly_wage()
    {
        $hourlyStaff = User::firstOrCreate(
            ['email' => 'hourly_staff_test@example.com'],
            [
                'name' => 'Jim Halpert',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'WAITER',
                'status' => 'A',
            ]
        );

        // Hourly wage: ₹150/hr
        StaffSalary::updateOrCreate(
            ['user_id' => $hourlyStaff->id],
            [
                'restaurant_id' => $this->restaurant->id,
                'employment_type' => 'hourly',
                'hourly_wage' => 150.00,
            ]
        );

        // Attendance 1: 09:30 AM to 05:30 PM = 8.00 hrs -> ₹1,200
        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $hourlyStaff->id,
            'attendance_date' => '2026-11-10',
            'attendance_type' => 'P',
            'login_time' => '09:30:00',
            'logout_time' => '17:30:00',
        ]);

        // Attendance 2: 10:00 AM to 02:30 PM = 4.50 hrs -> ₹675
        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $hourlyStaff->id,
            'attendance_date' => '2026-11-11',
            'attendance_type' => 'P',
            'login_time' => '10:00:00',
            'logout_time' => '14:30:00',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.salary.index', [
                'tab' => 'hourly',
                'staff_id' => $hourlyStaff->id,
                'from_date' => '2026-11-01',
                'to_date' => '2026-11-30',
            ]));

        $response->assertStatus(200);
        $hourlyData = $response->viewData('hourlyData');

        $this->assertEquals(12.50, (float)$hourlyData['totalDutyHours']); // 8.00 + 4.50 = 12.50
        $this->assertEquals(1875.00, (float)$hourlyData['totalDutyEarnings']); // 12.50 * 150 = 1875
        $this->assertEquals(1875.00, (float)$hourlyData['grossDue']);
        $this->assertEquals(1875.00, (float)$hourlyData['restAmount']);
    }

    public function test_hourly_tab_includes_extras_and_payments_to_compute_rest_amount()
    {
        $hourlyStaff = User::firstOrCreate(
            ['email' => 'hourly_staff_calc@example.com'],
            [
                'name' => 'Pam Beesly',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'CASHIER',
                'status' => 'A',
            ]
        );

        // Hourly wage: ₹200/hr
        StaffSalary::updateOrCreate(
            ['user_id' => $hourlyStaff->id],
            [
                'restaurant_id' => $this->restaurant->id,
                'employment_type' => 'hourly',
                'hourly_wage' => 200.00,
            ]
        );

        // 10 hours duty = ₹2,000
        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $hourlyStaff->id,
            'attendance_date' => '2026-11-15',
            'attendance_type' => 'P',
            'login_time' => '08:00:00',
            'logout_time' => '18:00:00',
        ]);

        // Extras:
        // + Earning: ₹500
        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $hourlyStaff->id,
            'type' => StaffExtra::TYPE_EARNING,
            'amount' => 500.00,
            'entry_date' => '2026-11-16',
            'remarks' => 'Overtime Bonus',
        ]);

        // - Penalty/Deduction: ₹150
        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $hourlyStaff->id,
            'type' => StaffExtra::TYPE_DEDUCTION,
            'amount' => 150.00,
            'entry_date' => '2026-11-17',
            'remarks' => 'Late shift penalty',
        ]);

        // - Advance: ₹350
        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $hourlyStaff->id,
            'type' => StaffExtra::TYPE_ADVANCE,
            'amount' => 350.00,
            'entry_date' => '2026-11-18',
            'remarks' => 'Mid-month cash advance',
        ]);

        // Record a Payment of ₹1,000 (ONLINE)
        $paymentResponse = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.salary.record-hourly-payment'), [
                'user_id' => $hourlyStaff->id,
                'amount' => 1000.00,
                'payment_date' => '2026-11-19',
                'payment_method' => 'ONLINE',
                'period_from' => '2026-11-01',
                'period_to' => '2026-11-30',
                'remarks' => 'Interim transfer',
            ]);



        $paymentResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('staff_salary_payments', [
            'user_id' => $hourlyStaff->id,
            'amount' => 1000.00,
            'payment_method' => 'ONLINE',
        ]);

        // Gross Due = Duty (2000) + Extra Earning (500) - Penalty (150) - Advance (350) = 2000
        // Total Payments = 1000
        // Rest Amount Need to Pay = 2000 - 1000 = 1000
        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.salary.index', [
                'tab' => 'hourly',
                'staff_id' => $hourlyStaff->id,
                'from_date' => '2026-11-01',
                'to_date' => '2026-11-30',
            ]));

        $response->assertStatus(200);
        $hourlyData = $response->viewData('hourlyData');

        $this->assertEquals(2000.00, (float)$hourlyData['totalDutyEarnings']);
        $this->assertEquals(500.00, (float)$hourlyData['extraEarnings']);
        $this->assertEquals(150.00, (float)$hourlyData['extraPenalties']);
        $this->assertEquals(350.00, (float)$hourlyData['advanceAmount']);
        $this->assertEquals(0.00, (float)$hourlyData['netExtras']); // 500 - 150 - 350 = 0
        $this->assertEquals(2000.00, (float)$hourlyData['grossDue']);
        $this->assertEquals(1000.00, (float)$hourlyData['totalPayments']);
        $this->assertEquals(1000.00, (float)$hourlyData['restAmount']);
    }

    public function test_admin_can_delete_hourly_payment()
    {
        $hourlyStaff = User::firstOrCreate(
            ['email' => 'hourly_delete_pay@example.com'],
            [
                'name' => 'Stanley Hudson',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'WAITER',
                'status' => 'A',
            ]
        );

        $payment = \App\Models\StaffSalaryPayment::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $hourlyStaff->id,
            'payment_date' => '2026-11-20',
            'amount' => 450.00,
            'payment_method' => 'CASH',
            'period_from' => '2026-11-01',
            'period_to' => '2026-11-30',
            'created_by' => $this->adminUser->id,
        ]);

        $deleteResponse = $this->actingAs($this->adminUser)
            ->deleteJson(route('restaurant.salary.delete-hourly-payment', $payment->id));

        $deleteResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('staff_salary_payments', [
            'id' => $payment->id,
        ]);
    }

    public function test_admin_can_save_hourly_wage()
    {
        $hourlyStaff = User::firstOrCreate(
            ['email' => 'hourly_wage_update@example.com'],
            [
                'name' => 'Toby Flenderson',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'HR',
                'status' => 'A',
            ]
        );

        $wageResponse = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.salary.save-hourly-wage'), [
                'user_id' => $hourlyStaff->id,
                'hourly_wage' => 175.50,
            ]);

        $wageResponse->assertStatus(200)
            ->assertJson(['success' => true, 'hourly_wage' => 175.5]);

        $this->assertDatabaseHas('staff_salaries', [
            'user_id' => $hourlyStaff->id,
            'employment_type' => 'hourly',
            'hourly_wage' => 175.50,
        ]);
    }

    public function test_attendance_store_captures_employee_type()
    {
        $hourlyStaff = User::firstOrCreate(
            ['email' => 'attendance_type_hourly@example.com'],
            [
                'name' => 'Kevin Malone',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'COOK',
                'status' => 'A',
            ]
        );

        $response = $this->actingAs($this->adminUser)
            ->post(route('restaurant.attendance.store'), [
                'user_id' => $hourlyStaff->id,
                'date' => '2026-11-20',
                'attendance_type' => 'P',
                'employee_type' => 'hourly',
                'login_time' => '10:00',
                'logout_time' => '18:00',
                'notes' => 'Hourly shift worked',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('staff_attendances', [
            'user_id' => $hourlyStaff->id,
            'attendance_date' => '2026-11-20',
            'employee_type' => 'hourly',
            'notes' => 'Hourly shift worked',
        ]);
    }

    public function test_hourly_tab_only_shows_hourly_wage_staff_and_excludes_permanent_staff()
    {
        // 1. Purely permanent staff (monthly salary, no hourly wage)
        $permStaff = User::firstOrCreate(
            ['email' => 'strictly_permanent@example.com'],
            [
                'name' => 'Angela Martin',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'ACCOUNTANT',
                'status' => 'A',
            ]
        );

        StaffSalary::updateOrCreate(
            ['user_id' => $permStaff->id],
            [
                'restaurant_id' => $this->restaurant->id,
                'employment_type' => 'permanent',
                'monthly_salary' => 35000.00,
                'hourly_wage' => 0.00,
            ]
        );

        // Mark permanent attendance for Angela
        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $permStaff->id,
            'attendance_date' => '2026-11-15',
            'attendance_type' => 'P',
            'employee_type' => 'permanent',
            'login_time' => '09:00:00',
            'logout_time' => '17:00:00',
        ]);

        // 2. Hourly staff (duty based)
        $hourlyStaff = User::firstOrCreate(
            ['email' => 'strictly_hourly@example.com'],
            [
                'name' => 'Oscar Martinez',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'WAITER',
                'status' => 'A',
            ]
        );

        StaffSalary::updateOrCreate(
            ['user_id' => $hourlyStaff->id],
            [
                'restaurant_id' => $this->restaurant->id,
                'employment_type' => 'hourly',
                'hourly_wage' => 160.00,
            ]
        );

        StaffAttendance::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $hourlyStaff->id,
            'attendance_date' => '2026-11-15',
            'attendance_type' => 'P',
            'employee_type' => 'hourly',
            'login_time' => '10:00:00',
            'logout_time' => '16:00:00', // 6.00 hrs
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.salary.index', [
                'tab' => 'hourly',
                'from_date' => '2026-11-01',
                'to_date' => '2026-11-30',
            ]));

        $response->assertStatus(200);
        $hourlyData = $response->viewData('hourlyData');
        $staffIdsInDropdown = collect($hourlyData['staffMembers'])->pluck('id')->toArray();

        // Must include hourly staff Oscar
        $this->assertContains($hourlyStaff->id, $staffIdsInDropdown, 'Hourly worker Oscar must be present in hourly staff dropdown.');

        // Must strictly EXCLUDE permanent staff Angela
        $this->assertNotContains($permStaff->id, $staffIdsInDropdown, 'Pure permanent worker Angela must NOT appear in hourly staff dropdown.');
    }

    public function test_salary_configuration_supports_fixed_and_percentage_deductions_in_operations_and_payroll()
    {
        $testStaff = User::firstOrCreate(
            ['email' => 'fixed_deduct_staff@example.com'],
            [
                'name' => 'Stanley Hudson',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'WAITER',
                'status' => 'A',
            ]
        );

        // Save salary from operations form: P-Tax fixed ₹200, Other fixed ₹150
        $response = $this->actingAs($this->adminUser)
            ->post(route('restaurant.staff.save-salary'), [
                'user_id' => $testStaff->id,
                'employment_type' => 'permanent',
                'monthly_salary' => 30000.00,
                'pf_percentage' => 10.00,  // ₹3,000
                'esi_percentage' => 1.00,  // ₹300
                'ptax_type' => 'fixed',
                'ptax_value' => 200.00,    // ₹200 fixed
                'other_deduction_type' => 'fixed',
                'other_deduction_value' => 150.00, // ₹150 fixed
                'weekoff_count' => 4,
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('staff_salaries', [
            'user_id' => $testStaff->id,
            'employment_type' => 'permanent',
            'monthly_salary' => 30000.00,
            'ptax_type' => 'fixed',
            'ptax_amount' => 200.00,
            'other_deduction_type' => 'fixed',
            'other_deduction_amount' => 150.00,
        ]);

        // Check Salary Management view for this month (Nov 2026, 30 days)
        // Mark 26 present days so 0 absent days (26 present + 4 weekoffs = 30)
        for ($i = 1; $i <= 26; $i++) {
            StaffAttendance::create([
                'restaurant_id' => $this->restaurant->id,
                'user_id' => $testStaff->id,
                'attendance_date' => sprintf('2026-11-%02d', $i),
                'attendance_type' => 'P',
                'employee_type' => 'permanent',
            ]);
        }

        $res = $this->actingAs($this->adminUser)
            ->get(route('restaurant.salary.index', ['month' => 11, 'year' => 2026]));

        $res->assertStatus(200);
        $staffList = $res->viewData('staffList');
        $stanley = collect($staffList)->firstWhere('user_id', $testStaff->id);

        $this->assertNotNull($stanley);
        $this->assertEquals('fixed', $stanley['ptax_type']);
        $this->assertEquals('fixed', $stanley['other_type']);
        $this->assertEquals(200.00, (float)$stanley['ptax_amount']);
        $this->assertEquals(150.00, (float)$stanley['other_deduction_amount']);
        $this->assertEquals(350.00, (float)$stanley['other_deductions']); // 200 + 150 = 350
        $this->assertEquals(3000.00, (float)$stanley['pf_amount']);
        $this->assertEquals(300.00, (float)$stanley['esi_amount']);
        $this->assertEquals(3650.00, (float)$stanley['total_statutory']); // 3000 + 300 + 350 = 3650
        $this->assertEquals(26350.00, (float)$stanley['net_salary']);     // 30000 - 3650 = 26350
        $this->assertEquals(26350.00, (float)$stanley['final_salary']);
    }
}
