<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\OrderManage;
use App\Models\FranchiseRestaurantMapping;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MasterReportCustomersTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndUser($role = 'RES')
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Royal Spice ' . uniqid(),
            'restaurant_id_unique' => 'REST-RS-' . rand(100, 999),
            'parent_id' => null,
            'address' => '78 Heritage Boulevard',
            'status' => 'A'
        ]);

        $plan = \App\Models\Plan::create([
            'name' => 'Enterprise Plan ' . uniqid(),
            'price' => 4999,
            'duration_days' => 365,
            'billing_cycle' => 'monthly',
            'inventory_checkbox' => 'Y',
            'plan_status' => 'A'
        ]);

        \App\Models\Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(300),
            'amount' => 4999
        ]);

        $user = User::create([
            'name' => 'Admin ' . uniqid(),
            'email' => 'admin_' . uniqid() . '@example.com',
            'phone' => '98123' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => $role,
            'role_type' => 'ADMIN',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        return [$restaurant, $user];
    }

    public function test_master_report_customers_tab_exists_and_renders_successfully()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        $response = $this->actingAs($user)->get(route('admin.reports.master.customers', ['outlet_id' => $restaurant->id]));
        $response->assertStatus(200);
        $response->assertSeeText('Customer Directory');
        $response->assertSeeText('Unique Customers');
        $response->assertSee(route('admin.reports.master.customers', ['outlet_id' => $restaurant->id]));
    }

    public function test_master_report_deduplicates_by_phone_removes_walkin_and_shows_not_found()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        // 1. Order 1 for Rohit (Phone: 9876543210)
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Rohit Sharma',
            'customer_phone' => '9876543210',
            'customer_email' => 'rohit@example.com',
            'total_amount' => 1200,
            'grand_total' => 1200,
            'amount_paid' => 1200,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // 2. Order 2 for SAME Rohit with same phone 9876543210
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Rohit Sharma',
            'customer_phone' => '9876543210',
            'customer_email' => null,
            'total_amount' => 1500,
            'grand_total' => 1500,
            'amount_paid' => 1500,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // 3. Walk-in Customer (MUST BE EXCLUDED)
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Walk-in Customer',
            'customer_phone' => null,
            'customer_email' => null,
            'total_amount' => 350,
            'grand_total' => 350,
            'amount_paid' => 350,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // 4. Customer with name but NO phone and NO email
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Suman Roy',
            'customer_phone' => null,
            'customer_email' => null,
            'total_amount' => 450,
            'grand_total' => 450,
            'amount_paid' => 450,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        $response = $this->actingAs($user)->get(route('admin.reports.master.customers', ['outlet_id' => $restaurant->id]));
        $response->assertStatus(200);

        // Deduplication: Rohit Sharma should only appear once in customer list (1 row)
        $response->assertSee('Rohit Sharma');
        $this->assertEquals(1, substr_count($response->getContent(), 'href="tel:9876543210"'));
        $this->assertEquals(1, substr_count($response->getContent(), 'href="mailto:rohit@example.com"'));

        // Suman Roy without phone/email shows "Not Found"
        $response->assertSee('Suman Roy');
        $response->assertSee('Not Found');

        // Walk-in Customer must not appear
        $response->assertDontSeeText('Walk-in Customer');
    }

    public function test_master_report_customers_keyword_search()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Vikram Patel',
            'customer_phone' => '9111223344',
            'customer_email' => 'vikram@example.com',
            'total_amount' => 700,
            'grand_total' => 700,
            'amount_paid' => 700,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Sneha Kulkarni',
            'customer_phone' => '9555667788',
            'customer_email' => 'sneha@example.com',
            'total_amount' => 950,
            'grand_total' => 950,
            'amount_paid' => 950,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // Search for Vikram
        $response = $this->actingAs($user)->get(route('admin.reports.master.customers', [
            'outlet_id' => $restaurant->id,
            'keyword' => 'Vikram'
        ]));
        $response->assertStatus(200);
        $response->assertSee('Vikram Patel');
        $response->assertDontSee('Sneha Kulkarni');
    }

    public function test_master_report_customers_excel_export()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Kavita Joshi',
            'customer_phone' => '9777888999',
            'customer_email' => 'kavita@example.com',
            'total_amount' => 850,
            'grand_total' => 850,
            'amount_paid' => 850,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        $response = $this->actingAs($user)->get(route('admin.reports.master.customers.export', [
            'outlet_id' => $restaurant->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_franchise_performance_report_customers_and_export()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser('FO');

        FranchiseRestaurantMapping::create([
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Aakash Verma',
            'customer_phone' => '9444332211',
            'customer_email' => 'aakash@example.com',
            'total_amount' => 1100,
            'grand_total' => 1100,
            'amount_paid' => 1100,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        $response = $this->actingAs($user)->get(route('franchise.performance.report.customers', [
            'restaurantId' => $restaurant->id
        ]));
        $response->assertStatus(200);
        $response->assertSee('Aakash Verma');
        $response->assertSee('9444332211');

        $exportResponse = $this->actingAs($user)->get(route('franchise.performance.report.customers.export', [
            'restaurantId' => $restaurant->id
        ]));
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
