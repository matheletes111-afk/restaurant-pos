<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthRedirectTest extends TestCase
{
    public function test_guest_can_see_login_page()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In');
    }

    public function test_super_admin_dashboard_url()
    {
        $superAdmin = new User();
        $superAdmin->role = 'SA';

        $this->assertEquals(route('admin.dashboard'), $superAdmin->getDashboardRedirectUrl());
    }

    public function test_authenticated_super_admin_redirected_away_from_login()
    {
        $superAdmin = new User();
        $superAdmin->id = 1;
        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 'admin@example.com';
        $superAdmin->role = 'SA';

        $response = $this->actingAs($superAdmin)->get('/login');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_restaurant_user_without_subscription_redirected_to_select_plans()
    {
        $user = new User();
        $user->id = 999;
        $user->restaurant_id = 999999; // Non-existent restaurant ID -> no subscription
        $user->role = 'RES';
        $user->role_type = 'ADMIN';

        $this->assertEquals(route('select.plan.page'), $user->getDashboardRedirectUrl());

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect(route('select.plan.page'));
    }

    public function test_authenticated_user_redirected_away_from_login_verify()
    {
        $superAdmin = new User();
        $superAdmin->id = 1;
        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 'admin@example.com';
        $superAdmin->role = 'SA';

        $response = $this->actingAs($superAdmin)->get('/login/verify');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_user_redirected_away_from_register_restaurant()
    {
        $superAdmin = new User();
        $superAdmin->id = 1;
        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 'admin@example.com';
        $superAdmin->role = 'SA';

        $response = $this->actingAs($superAdmin)->get('/register-restaurant');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_user_redirected_away_from_forget_password()
    {
        $superAdmin = new User();
        $superAdmin->id = 1;
        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 'admin@example.com';
        $superAdmin->role = 'SA';

        $response = $this->actingAs($superAdmin)->get('/forget-password-user');
        $response->assertRedirect(route('admin.dashboard'));
    }
}
