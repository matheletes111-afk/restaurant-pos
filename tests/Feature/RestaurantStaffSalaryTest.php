<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\StaffSalary;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantStaffSalaryTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndAdmin()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Salary Restro ' . uniqid(),
            'restaurant_id_unique' => 'REST-SAL-' . rand(100, 999),
            'parent_id' => null,
            'address' => '100 Salary Ave',
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

    public function test_staff_management_view_renders_operation_type_link_to_dedicated_page()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $staff = User::create([
            'name' => 'John Waiter ' . uniqid(),
            'email' => 'waiter_' . uniqid() . '@example.com',
            'phone' => '98123' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Waiter',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $response = $this->actingAs($admin)->get('/admin/restaurant-staff');
        $response->assertStatus(200);

        // Check staff name is visible
        $response->assertSee($staff->name);

        // Check Operation Type link to dedicated separate page is present
        $response->assertSee(route('restaurant.staff.operations', $staff->id));
        $response->assertSee('Operation Type (Salary Management)');
        $response->assertSee('fa-sliders-h');

        // Confirm modal is NOT present in staff index
        $response->assertDontSee('id="operationModal"', false);
    }

    public function test_dedicated_staff_operations_page_renders_salary_management_tab_and_fields()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $staff = User::create([
            'name' => 'Sara Hostess ' . uniqid(),
            'email' => 'hostess_' . uniqid() . '@example.com',
            'phone' => '98999' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Cashier',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $response = $this->actingAs($admin)->get('/admin/restaurant-staff/operations/' . $staff->id);
        $response->assertStatus(200);

        // Staff profile banner
        $response->assertSee($staff->name);
        $response->assertSee($staff->email);
        $response->assertSee($staff->phone);
        $response->assertSee('Back to Staff List');

        // Tab bar with Salary Management as first tab
        $response->assertSee('Salary Management');
        $response->assertSee('tab-salary');

        // Employment type options
        $response->assertSee('Permanent / Full-Time');
        $response->assertSee('Hourly / Shift-Based');

        // Permanent salary fields
        $response->assertSee('Monthly Gross Salary (₹)');
        $response->assertSee('Weekoff Count (Days / Month)');
        $response->assertSee('PF (Provident Fund)');
        $response->assertSee('ESI');
        $response->assertSee('P-Tax (Professional Tax)');
        $response->assertSee('Other Deduction');
        $response->assertSee('Estimated Net In-Hand Salary');

        // Hourly wage fields
        $response->assertSee('Hourly Wage Rate (₹ / Hour)');
        $response->assertSee('Standard 8-hour shift:');

        // Notes & Save buttons
        $response->assertSee('Internal Notes', false);
        $response->assertSee('Save & Return to Staff List', false);
    }

    public function test_get_salary_data_endpoint_returns_json()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $staff = User::create([
            'name' => 'Jane Chef ' . uniqid(),
            'email' => 'chef_' . uniqid() . '@example.com',
            'phone' => '98222' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Kitchen Staff',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        // Default endpoint response when not configured yet
        $res1 = $this->actingAs($admin)->getJson('/admin/restaurant-staff/salary-data/' . $staff->id);
        $res1->assertStatus(200);
        $res1->assertJson([
            'success' => true,
            'salary' => [
                'employment_type' => 'permanent',
                'pf_percentage' => 0,
                'esi_percentage' => 0,
                'ptax_percentage' => 0,
                'other_deduction_percentage' => 0,
                'weekoff_count' => 4,
            ]
        ]);

        // Create salary record
        StaffSalary::create([
            'user_id' => $staff->id,
            'restaurant_id' => $restaurant->id,
            'employment_type' => 'permanent',
            'monthly_salary' => 30000.00,
            'pf_percentage' => 12.00,
            'esi_percentage' => 1.75,
            'ptax_percentage' => 200.00,
            'other_deduction_percentage' => 1.00,
            'weekoff_count' => 4,
            'notes' => 'Senior Chef package'
        ]);

        $res2 = $this->actingAs($admin)->getJson('/admin/restaurant-staff/salary-data/' . $staff->id);
        $res2->assertStatus(200);
        $res2->assertJson([
            'success' => true,
            'salary' => [
                'employment_type' => 'permanent',
                'monthly_salary' => 30000,
                'pf_percentage' => 12,
                'esi_percentage' => 1.75,
                'weekoff_count' => 4,
                'notes' => 'Senior Chef package'
            ]
        ]);
    }

    public function test_save_salary_permanent_configuration_and_redirect_to_staff()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $staff = User::create([
            'name' => 'Sam Manager ' . uniqid(),
            'email' => 'manager_' . uniqid() . '@example.com',
            'phone' => '98333' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Manager',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $postData = [
            'user_id' => $staff->id,
            'employment_type' => 'permanent',
            'monthly_salary' => 35000,
            'pf_percentage' => 12,
            'esi_percentage' => 1.75,
            'ptax_percentage' => 2,
            'other_deduction_percentage' => 1,
            'weekoff_count' => 5,
            'notes' => 'General Manager terms',
            'action' => 'save_and_close',
        ];

        $response = $this->actingAs($admin)->post('/admin/restaurant-staff/salary/save', $postData);
        $response->assertRedirect(route('restaurant.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('staff_salaries', [
            'user_id' => $staff->id,
            'employment_type' => 'permanent',
            'monthly_salary' => 35000.00,
            'pf_percentage' => 12.00,
            'esi_percentage' => 1.75,
            'ptax_percentage' => 2.00,
            'other_deduction_percentage' => 1.00,
            'weekoff_count' => 5,
        ]);
    }

    public function test_save_salary_hourly_configuration()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $staff = User::create([
            'name' => 'Alex PartTime ' . uniqid(),
            'email' => 'parttime_' . uniqid() . '@example.com',
            'phone' => '98444' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Waiter',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $postData = [
            'user_id' => $staff->id,
            'employment_type' => 'hourly',
            'hourly_wage' => 180.50,
            'notes' => 'Weekend shift only',
            'action' => 'save',
        ];

        $response = $this->actingAs($admin)->post('/admin/restaurant-staff/salary/save', $postData);
        $response->assertRedirect(route('restaurant.staff.salary', $staff->id));
        $response->assertSessionHas('success');

        // Test GET on dedicated separate salary route
        $getPage = $this->actingAs($admin)->get(route('restaurant.staff.salary', $staff->id));
        $getPage->assertStatus(200);
        $getPage->assertSee('Salary Management');

        $this->assertDatabaseHas('staff_salaries', [
            'user_id' => $staff->id,
            'employment_type' => 'hourly',
            'hourly_wage' => 180.50,
            'monthly_salary' => 0.00,
        ]);
    }
}
