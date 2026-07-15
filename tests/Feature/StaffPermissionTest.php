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
}
