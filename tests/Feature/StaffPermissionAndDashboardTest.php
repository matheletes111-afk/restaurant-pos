<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StaffPermissionAndDashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $adminUser;
    protected $plan;
    protected $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::create([
            'name' => 'Permission Test Bistro ' . uniqid(),
            'restaurant_id_unique' => 'REST-PERM-' . rand(100, 999),
            'status' => 'A',
            'phone' => '9999999999',
            'email' => 'permtest_' . uniqid() . '@bistro.com'
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin Owner',
            'email' => 'admin_perm_' . uniqid() . '@bistro.com',
            'phone' => '98765' . rand(10000, 99999),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $this->restaurant->id,
            'password' => bcrypt('password123'),
            'status' => 'A'
        ]);

        $this->plan = Plan::create([
            'name' => 'Staff Test Plan ' . uniqid(),
            'price' => 999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        $this->subscription = Subscription::create([
            'user_id' => $this->restaurant->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(29),
            'amount' => 999.00,
            'currency' => 'INR',
        ]);
    }

    public function test_adding_staff_redirects_directly_to_permissions_page()
    {
        $this->actingAs($this->adminUser);

        $email = 'waiter_' . uniqid() . '@bistro.com';
        $response = $this->post(route('restaurant.staff.insert'), [
            'name' => 'John Waiter',
            'email' => $email,
            'phone' => '9876543210',
            'role_type' => 'Waiter',
            'password' => 'secret123',
            'restaurant_id' => $this->restaurant->id,
            'status' => 'A'
        ]);

        $newStaff = User::where('email', $email)->first();
        $this->assertNotNull($newStaff);
        $this->assertTrue($newStaff->isStaff());
        $this->assertFalse($newStaff->hasAnyPermission());

        $response->assertRedirect(route('restaurant.staff.permissions', $newStaff->id));
        $response->assertSessionHas('success');
    }

    public function test_staff_with_no_permissions_sees_welcome_and_only_user_hrm_in_sidebar()
    {
        $staffNoPerm = User::create([
            'name' => 'Alex ZeroPerm',
            'email' => 'alex_noperm_' . uniqid() . '@bistro.com',
            'phone' => '9123456789',
            'role' => 'RES',
            'role_type' => 'Captain',
            'restaurant_id' => $this->restaurant->id,
            'password' => bcrypt('password123'),
            'status' => 'A',
            'permissions' => []
        ]);

        $this->assertTrue($staffNoPerm->isStaff());
        $this->assertFalse($staffNoPerm->hasAnyPermission());

        $this->actingAs($staffNoPerm);

        // 1. Check Dashboard
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        // Should have welcome message and HRM action links
        $response->assertSee('Welcome, Alex ZeroPerm!');
        $response->assertSee('Permissions Pending Configuration');
        $response->assertSee('Daily Attendance');
        $response->assertSee('Leave Requests');
        $response->assertSee('Salary & Payslips');

        // Should NOT have financial/revenue statistics or management grids
        $response->assertDontSee("Today&#039;s Revenue", false);
        $response->assertDontSee("Today's Revenue");
        $response->assertDontSee("This Month Sale");
        $response->assertDontSee("Top Selling Menu Items");

        // 2. Check Sidebar contents for zero-permission staff
        // Should see Dashboard and User HRM
        $response->assertSee('Dashboard');
        $response->assertSee('User HRM');

        // Should NOT see unauthorized menus
        $response->assertDontSee('Menu Master');
        $response->assertDontSee('Dish Addon Master');
        $response->assertDontSee('Table Management');
        $response->assertDontSee('Order Master');
        $response->assertDontSee('Cash Drawer');
        $response->assertDontSee('Expenses');
        $response->assertDontSee('Inventory Setting');
        $response->assertDontSee('Customer List');
        $response->assertDontSee('Restaurant Profile');
    }

    public function test_staff_with_assigned_permissions_sees_corresponding_menus_and_dashboard()
    {
        $staffWithPerm = User::create([
            'name' => 'Sam OrderStaff',
            'email' => 'sam_perm_' . uniqid() . '@bistro.com',
            'phone' => '9123456788',
            'role' => 'RES',
            'role_type' => 'Billing Staff',
            'restaurant_id' => $this->restaurant->id,
            'password' => bcrypt('password123'),
            'status' => 'A',
            'permissions' => ['order_master']
        ]);

        $this->assertTrue($staffWithPerm->isStaff());
        $this->assertTrue($staffWithPerm->hasAnyPermission());
        $this->assertTrue($staffWithPerm->hasPermission('order_master'));
        // order_master inherits rapid_bill
        $this->assertTrue($staffWithPerm->hasPermission('rapid_bill'));
        $this->assertFalse($staffWithPerm->hasPermission('reports'));
        $this->assertFalse($staffWithPerm->hasPermission('menu_master'));

        $this->actingAs($staffWithPerm);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        // Should see staff dashboard header
        $response->assertSee('Billing Staff Dashboard');
        $response->assertSee('New POS Order');
        $response->assertSee('Rapid Bill');
        $response->assertSee('Orders Today');

        // Should NOT see revenue (since no reports permission)
        $response->assertDontSee("Today's Revenue");
        $response->assertDontSee("This Month Sale");

        // Should see Order Master in sidebar
        $response->assertSee('Order Master');
        $response->assertSee('Rapid Bill');
        $response->assertSee('User HRM');

        // Should NOT see Menu Master or Reports in sidebar
        $response->assertDontSee('Menu Master');
        $response->assertDontSee('Master Report');
        $response->assertDontSee('Cash Drawer');
    }
}
