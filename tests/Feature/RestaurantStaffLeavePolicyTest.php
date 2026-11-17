<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\StaffLeavePolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantStaffLeavePolicyTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndAdmin()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Leave Restro ' . uniqid(),
            'restaurant_id_unique' => 'REST-LVE-' . rand(100, 999),
            'parent_id' => null,
            'address' => '200 Leave Blvd',
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
            'name' => 'Leave Staff Plan ' . uniqid(),
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

    public function test_operations_page_renders_leave_policy_management_tab_with_all_categories()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $staff = User::create([
            'name' => 'Ravi Kumar ' . uniqid(),
            'email' => 'ravi_' . uniqid() . '@example.com',
            'phone' => '98101' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Waiter',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $response = $this->actingAs($admin)->get('/admin/restaurant-staff/operations/' . $staff->id . '?tab=leave_policy');
        $response->assertStatus(200);

        // Verify Staff Details
        $response->assertSee($staff->name);

        // Verify Tab & Headings
        $response->assertSee('Leave Policy Management');
        $response->assertSee('Staff Leave Policy & Annual Entitlements', false);

        // Verify Categories
        $response->assertSee('Sick Leave');
        $response->assertSee('Paid Leave');
        $response->assertSee('Early Leave');
        $response->assertSee('Half Day');

        // Verify Carry Forward toggle & labels
        $response->assertSee('Carry Forward to Next Year');
        $response->assertSee('Annual Quota');
        $response->assertSee('Carried Forward');

        // Verify default quotas are 0
        $response->assertSee('name="policies[sick_leave][annual_quota]"', false);
        $response->assertSee('name="policies[paid_leave][annual_quota]"', false);
        $response->assertSee('name="policies[early_leave][annual_quota]"', false);
        $response->assertSee('name="policies[half_day][annual_quota]"', false);
        $response->assertSee('value="0"', false);
    }

    public function test_save_leave_policy_persists_custom_quotas_and_carry_forward_flags()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $staff = User::create([
            'name' => 'Anita Sharma ' . uniqid(),
            'email' => 'anita_' . uniqid() . '@example.com',
            'phone' => '98202' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Cashier',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $postData = [
            'user_id' => $staff->id,
            'year' => 2026,
            'policies' => [
                'sick_leave' => [
                    'annual_quota' => 12,
                    'carried_forward' => 2,
                    'is_carry_forward' => '1',
                    'notes' => 'Medical certificate required for > 2 days',
                ],
                'paid_leave' => [
                    'annual_quota' => 18,
                    'carried_forward' => 3,
                    'is_carry_forward' => '1',
                    'notes' => 'Advance 7 days notice required',
                ],
                'early_leave' => [
                    'annual_quota' => 6,
                    'carried_forward' => 0,
                    'is_carry_forward' => '0',
                    'notes' => 'Max 2 hours early per occasion',
                ],
                'half_day' => [
                    'annual_quota' => 8,
                    'carried_forward' => 0,
                    // unchecked checkbox sends no is_carry_forward
                    'notes' => 'Morning or afternoon shift',
                ],
            ],
            'action' => 'save_and_close',
        ];

        $response = $this->actingAs($admin)->post('/admin/restaurant-staff/leave-policy/save', $postData);
        $response->assertRedirect(route('restaurant.staff.index'));
        $response->assertSessionHas('success');

        // Check Sick Leave persisted with carry forward = true
        $this->assertDatabaseHas('staff_leave_policies', [
            'user_id' => $staff->id,
            'leave_type' => 'sick_leave',
            'year' => 2026,
            'annual_quota' => 12.00,
            'carried_forward' => 2.00,
            'is_carry_forward' => 1,
            'notes' => 'Medical certificate required for > 2 days',
        ]);

        // Check Paid Leave persisted with carry forward = true
        $this->assertDatabaseHas('staff_leave_policies', [
            'user_id' => $staff->id,
            'leave_type' => 'paid_leave',
            'year' => 2026,
            'annual_quota' => 18.00,
            'carried_forward' => 3.00,
            'is_carry_forward' => 1,
        ]);

        // Check Early Leave persisted with carry forward = false
        $this->assertDatabaseHas('staff_leave_policies', [
            'user_id' => $staff->id,
            'leave_type' => 'early_leave',
            'year' => 2026,
            'annual_quota' => 6.00,
            'carried_forward' => 0.00,
            'is_carry_forward' => 0,
        ]);

        // Check Half Day persisted with carry forward = false
        $this->assertDatabaseHas('staff_leave_policies', [
            'user_id' => $staff->id,
            'leave_type' => 'half_day',
            'year' => 2026,
            'annual_quota' => 8.00,
            'carried_forward' => 0.00,
            'is_carry_forward' => 0,
        ]);
    }

    public function test_save_leave_policy_redirects_back_to_dedicated_leave_policy_page()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $staff = User::create([
            'name' => 'Leave Staff ' . uniqid(),
            'email' => 'leave_' . uniqid() . '@example.com',
            'phone' => '98404' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Waiter',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $postData = [
            'user_id' => $staff->id,
            'year' => 2026,
            'policies' => [
                'sick_leave' => [
                    'annual_quota' => 10,
                    'carried_forward' => 1,
                    'is_carry_forward' => '1',
                ],
            ],
            'action' => 'save',
        ];

        $response = $this->actingAs($admin)->post('/admin/restaurant-staff/leave-policy/save', $postData);
        $response->assertRedirect(route('restaurant.staff.leave-policy', ['id' => $staff->id, 'year' => 2026]));
        $response->assertSessionHas('success');

        // Test GET on dedicated separate leave-policy route
        $getPage = $this->actingAs($admin)->get(route('restaurant.staff.leave-policy', ['id' => $staff->id, 'year' => 2026]));
        $getPage->assertStatus(200);
        $getPage->assertSee('Staff Leave Policy & Annual Entitlements', false);
    }

    public function test_staff_to_staff_leave_quotas_are_completely_independent()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        // Staff 1: Senior Manager
        $manager = User::create([
            'name' => 'Manager Vikram ' . uniqid(),
            'email' => 'mgr_' . uniqid() . '@example.com',
            'phone' => '98303' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Manager',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        // Staff 2: Trainee Waiter
        $trainee = User::create([
            'name' => 'Trainee Deepa ' . uniqid(),
            'email' => 'deepa_' . uniqid() . '@example.com',
            'phone' => '98404' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'Waiter',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        // Assign Manager Quota (20 Sick, Carry Forward = ON)
        StaffLeavePolicy::create([
            'user_id' => $manager->id,
            'restaurant_id' => $restaurant->id,
            'year' => 2026,
            'leave_type' => 'sick_leave',
            'annual_quota' => 20.00,
            'carried_forward' => 5.00,
            'is_carry_forward' => true,
        ]);

        // Assign Trainee Quota (5 Sick, Carry Forward = OFF)
        StaffLeavePolicy::create([
            'user_id' => $trainee->id,
            'restaurant_id' => $restaurant->id,
            'year' => 2026,
            'leave_type' => 'sick_leave',
            'annual_quota' => 5.00,
            'carried_forward' => 0.00,
            'is_carry_forward' => false,
        ]);

        // Verify independent database values
        $mgrPolicy = StaffLeavePolicy::where('user_id', $manager->id)->where('leave_type', 'sick_leave')->first();
        $this->assertEquals(20.00, (float) $mgrPolicy->annual_quota);
        $this->assertEquals(5.00, (float) $mgrPolicy->carried_forward);
        $this->assertTrue((bool) $mgrPolicy->is_carry_forward);
        $this->assertEquals(25.00, $mgrPolicy->total_entitled);

        $traineePolicy = StaffLeavePolicy::where('user_id', $trainee->id)->where('leave_type', 'sick_leave')->first();
        $this->assertEquals(5.00, (float) $traineePolicy->annual_quota);
        $this->assertEquals(0.00, (float) $traineePolicy->carried_forward);
        $this->assertFalse((bool) $traineePolicy->is_carry_forward);
        $this->assertEquals(5.00, $traineePolicy->total_entitled);
    }
}

