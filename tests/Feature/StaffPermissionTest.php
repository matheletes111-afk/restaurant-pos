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
    }
}
