<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\OrderManage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

class AdminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected function createSuperAdmin()
    {
        return User::create([
            'name' => 'Super Admin Test',
            'email' => 'sa_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'SA',
            'role_type' => 'ADMIN',
        ]);
    }

    public function test_guest_is_redirected_from_admin_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect();
    }

    public function test_non_sa_user_without_subscription_is_redirected()
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertRedirect(route('select.plan.page'));
    }

    public function test_non_sa_user_with_subscription_cannot_access_admin_dashboard()
    {
        $owner = User::create([
            'name' => 'Subscribed Non SA User',
            'email' => 'sub_user_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => 8888,
        ]);

        $plan = Plan::create([
            'name' => 'Test Plan',
            'price' => 100.00,
            'billing_cycle' => 'monthly',
            'duration_days' => 30,
            'plan_status' => 'A',
        ]);

        Subscription::create([
            'user_id' => 8888,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(30),
            'auto_renew' => 1,
        ]);

        $response = $this->actingAs($owner)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_dashboard_with_all_metrics()
    {
        $admin = $this->createSuperAdmin();

        // 1. Create a restaurant
        $owner = User::create([
            'name' => 'Owner Test',
            'email' => 'owner_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
        ]);

        $restaurant = RestaurantMaster::create([
            'name' => 'Chef Delight Bistro',
            'owner_id' => $owner->id,
            'status' => 'A',
            'address' => '123 Food Street',
        ]);

        $owner->update(['restaurant_id' => $restaurant->id]);

        // 2. Create Plan
        $plan = Plan::create([
            'name' => 'Executive Gold Plan',
            'price' => 1999.00,
            'billing_cycle' => 'monthly',
            'duration_days' => 30,
            'plan_status' => 'A',
        ]);

        // 3. Create Subscription expiring in 15 days
        $subscription = Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(15),
            'end_date' => Carbon::now()->addDays(15),
            'auto_renew' => 1,
        ]);

        // 4. Create Order today
        $order = OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'John Doe',
            'order_type' => 'DINE_IN',
            'total_amount' => 500.00,
            'taxable_amount' => 500.00,
            'grand_total' => 500.00,
            'order_status' => 'COMPLETED',
            'created_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Chef Delight Bistro');
        $response->assertSee('Executive Gold Plan');
        $response->assertSee('Current Restaurants');
        $response->assertSee('With Active Plans');
        $response->assertSee('30-Day Expiry Watch');
        $response->assertSee('Total Orders Today');
        $response->assertSee('Most Popular Package');
        $response->assertSee('Best Performing Restaurant');
        $response->assertSee('Yearly Platform Growth');
    }

    public function test_yearly_chart_ajax_endpoint_returns_json()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->getJson(route('admin.dashboard', ['year' => Carbon::now()->year]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'year',
            'labels',
            'registrations',
            'subscriptions',
            'totalRegistrationsYear',
            'totalSubscriptionsYear',
        ]);

        $this->assertCount(12, $response->json('labels'));
        $this->assertCount(12, $response->json('registrations'));
        $this->assertCount(12, $response->json('subscriptions'));
    }

    public function test_dashboard_renders_crm_leads_status_breakdown()
    {
        $admin = $this->createSuperAdmin();

        \App\Models\DemoLead::create([
            'full_name' => 'Alice Walker',
            'restaurant_name' => 'Alice Cafe',
            'email_address' => 'alice_' . uniqid() . '@example.com',
            'phone_number' => '9876543210',
            'status' => 'Contacted',
        ]);

        \App\Models\DemoLead::create([
            'full_name' => 'Bob Baker',
            'restaurant_name' => 'Bob Bakery',
            'email_address' => 'bob_' . uniqid() . '@example.com',
            'phone_number' => '9876543211',
            'status' => 'Qualified',
        ]);

        \App\Models\DemoLead::create([
            'full_name' => 'Charlie Chef',
            'restaurant_name' => 'Charlie Grill',
            'email_address' => 'charlie_' . uniqid() . '@example.com',
            'phone_number' => '9876543212',
            'status' => 'Nurturing',
        ]);

        \App\Models\DemoLead::create([
            'full_name' => 'David Diner',
            'restaurant_name' => 'David Diner',
            'email_address' => 'david_' . uniqid() . '@example.com',
            'phone_number' => '9876543213',
            'status' => 'Converted',
        ]);

        \App\Models\DemoLead::create([
            'full_name' => 'Evan Eatery',
            'restaurant_name' => 'Evan Eatery',
            'email_address' => 'evan_' . uniqid() . '@example.com',
            'phone_number' => '9876543214',
            'status' => 'Lost',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('CRM Lead Pipeline', false);
        $response->assertSee('Contacted');
        $response->assertSee('Qualified');
        $response->assertSee('Nurturing');
        $response->assertSee('Converted');
        $response->assertSee('Lost');
    }
}
