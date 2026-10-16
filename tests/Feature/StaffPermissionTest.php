<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class StaffPermissionTest extends TestCase
{
    public function test_super_admin_has_all_permissions()
    {
        $superAdmin = new User();
        $superAdmin->role = 'SA';
        $superAdmin->role_type = null;

        $this->assertTrue($superAdmin->hasPermission('dashboard'));
        $this->assertTrue($superAdmin->hasPermission('menu_master'));
        $this->assertTrue($superAdmin->hasPermission('non_existent_permission'));
    }

    public function test_restaurant_admin_has_all_permissions()
    {
        $restaurantAdmin = new User();
        $restaurantAdmin->role = 'RES';
        $restaurantAdmin->role_type = 'ADMIN';

        $this->assertTrue($restaurantAdmin->hasPermission('dashboard'));
        $this->assertTrue($restaurantAdmin->hasPermission('menu_master'));
        $this->assertTrue($restaurantAdmin->hasPermission('non_existent_permission'));
    }

    public function test_restaurant_staff_has_only_assigned_permissions()
    {
        $staff = new User();
        $staff->role = 'RES';
        $staff->role_type = 'Manager';
        $staff->permissions = ['dashboard', 'menu_master'];

        $this->assertTrue($staff->hasPermission('dashboard'));
        $this->assertTrue($staff->hasPermission('menu_master'));
        
        $this->assertFalse($staff->hasPermission('table_master'));
        $this->assertFalse($staff->hasPermission('order_master'));
    }

    public function test_restaurant_staff_has_granular_permissions()
    {
        $staff = new User();
        $staff->role = 'RES';
        $staff->role_type = 'Manager';
        $staff->permissions = ['menu_master.view', 'menu_master.add', 'inventory_setting.view', 'inventory_setting.delete'];

        // menu_master checks
        $this->assertTrue($staff->hasPermission('menu_master', 'view'));
        $this->assertTrue($staff->hasPermission('menu_master', 'add'));
        $this->assertFalse($staff->hasPermission('menu_master', 'edit'));
        $this->assertFalse($staff->hasPermission('menu_master', 'delete'));

        // inventory_setting checks
        $this->assertTrue($staff->hasPermission('inventory_setting', 'view'));
        $this->assertFalse($staff->hasPermission('inventory_setting', 'add'));
        $this->assertFalse($staff->hasPermission('inventory_setting', 'edit'));
        $this->assertTrue($staff->hasPermission('inventory_setting', 'delete'));

        // Legacy compatibility check: should still support checking for simple module key without suffix
        $legacyStaff = new User();
        $legacyStaff->role = 'RES';
        $legacyStaff->role_type = 'Manager';
        $legacyStaff->permissions = ['menu_master'];

        $this->assertTrue($legacyStaff->hasPermission('menu_master', 'view'));
        $this->assertTrue($legacyStaff->hasPermission('menu_master', 'add'));
        $this->assertTrue($legacyStaff->hasPermission('menu_master', 'edit'));
        $this->assertTrue($legacyStaff->hasPermission('menu_master', 'delete'));

        // dish_addon_master granular check
        $addonStaff = new User();
        $addonStaff->role = 'RES';
        $addonStaff->role_type = 'Staff';
        $addonStaff->permissions = ['dish_addon_master.view', 'dish_addon_master.edit'];

        $this->assertTrue($addonStaff->hasPermission('dish_addon_master', 'view'));
        $this->assertFalse($addonStaff->hasPermission('dish_addon_master', 'add'));
        $this->assertTrue($addonStaff->hasPermission('dish_addon_master', 'edit'));
        $this->assertFalse($addonStaff->hasPermission('dish_addon_master', 'delete'));
    }

    public function test_order_master_permission_grants_rapid_bill_access()
    {
        $staff = new User();
        $staff->role = 'RES';
        $staff->role_type = 'Staff';
        $staff->permissions = ['order_master'];

        $this->assertTrue($staff->hasPermission('order_master'));
        $this->assertTrue($staff->hasPermission('rapid_bill'));

        $staffGranular = new User();
        $staffGranular->role = 'RES';
        $staffGranular->role_type = 'Staff';
        $staffGranular->permissions = ['order_master.view'];

        $this->assertTrue($staffGranular->hasPermission('rapid_bill'));
    }

    public function test_staff_permissions_page_renders_dish_addon_master_provision()
    {
        $restaurant = \App\Models\RestaurantMaster::create([
            'name' => 'Perm Test Cafe ' . uniqid(),
            'email' => 'perm_' . uniqid() . '@example.com',
            'phone_number' => '9876543299',
            'status' => 'A'
        ]);

        $plan = \App\Models\Plan::first() ?? \App\Models\Plan::create([
            'name' => 'Pro Plan',
            'price' => 1999,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        \App\Models\Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addYear(),
            'status' => 'active'
        ]);

        $admin = User::create([
            'name' => 'Owner Admin',
            'email' => 'owner_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $restaurant->id,
            'role' => 'RES',
            'role_type' => 'ADMIN',
        ]);

        $staff = User::create([
            'name' => 'Kitchen Staff',
            'email' => 'staff_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $restaurant->id,
            'role' => 'RES',
            'role_type' => 'Waiter',
            'permissions' => []
        ]);

        // 1. Check permissions page loads and shows Dish Addon Master matrix
        $response = $this->actingAs($admin)->get(route('restaurant.staff.permissions', $staff->id));
        $response->assertStatus(200);
        $response->assertSee('Dish Addon Master');
        $response->assertSee('dish_addon_master.view');
        $response->assertSee('dish_addon_master.add');
        $response->assertSee('dish_addon_master.edit');
        $response->assertSee('dish_addon_master.delete');

        // 2. Update permissions to grant Dish Addon Master View and Add
        $updateResponse = $this->actingAs($admin)->post(route('restaurant.staff.update-permissions', $staff->id), [
            'permissions' => [
                'dish_addon_master.view',
                'dish_addon_master.add',
            ]
        ]);
        $updateResponse->assertRedirect(route('restaurant.staff.index'));

        $staff->refresh();
        $this->assertTrue($staff->hasPermission('dish_addon_master', 'view'));
        $this->assertTrue($staff->hasPermission('dish_addon_master', 'add'));
        $this->assertFalse($staff->hasPermission('dish_addon_master', 'edit'));
        $this->assertFalse($staff->hasPermission('dish_addon_master', 'delete'));
    }

    public function test_staff_with_dish_addon_master_permission_can_access_dish_addons()
    {
        $restaurant = \App\Models\RestaurantMaster::create([
            'name' => 'Addon Route Cafe ' . uniqid(),
            'email' => 'route_' . uniqid() . '@example.com',
            'phone_number' => '9876543288',
            'status' => 'A'
        ]);

        $plan = \App\Models\Plan::first() ?? \App\Models\Plan::create([
            'name' => 'Pro Plan',
            'price' => 1999,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        \App\Models\Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addYear(),
            'status' => 'active'
        ]);

        $staffWithoutPerm = User::create([
            'name' => 'Staff Without Addon Perm',
            'email' => 'no_addon_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $restaurant->id,
            'role' => 'RES',
            'role_type' => 'Waiter',
            'permissions' => ['table_master.view']
        ]);

        $staffWithPerm = User::create([
            'name' => 'Staff With Addon Perm',
            'email' => 'has_addon_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $restaurant->id,
            'role' => 'RES',
            'role_type' => 'Manager',
            'permissions' => ['dish_addon_master.view']
        ]);

        // Unauthorized staff should get 403
        $deniedResponse = $this->actingAs($staffWithoutPerm)->get(route('addon.index'));
        $deniedResponse->assertStatus(403);

        // Authorized staff should get 200
        $allowedResponse = $this->actingAs($staffWithPerm)->get(route('addon.index'));
        $allowedResponse->assertStatus(200);
        $allowedResponse->assertSee('Dish Addon Master');
    }
}
