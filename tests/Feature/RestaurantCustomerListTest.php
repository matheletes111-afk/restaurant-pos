<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\OrderManage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantCustomerListTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndUser()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Biryani Paradise ' . uniqid(),
            'restaurant_id_unique' => 'REST-BP-' . rand(100, 999),
            'parent_id' => null,
            'address' => '42 Food Street',
            'status' => 'A'
        ]);

        $user = User::create([
            'name' => 'Owner ' . uniqid(),
            'email' => 'owner_' . uniqid() . '@example.com',
            'phone' => '98765' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        return [$restaurant, $user];
    }

    public function test_sidebar_displays_customer_list_menu_for_restaurant_user()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        $response = $this->actingAs($user)->get('/customers');
        $response->assertStatus(200);
        $response->assertSeeText('Customer List');
        $response->assertSee(route('restaurant.customers.index'));
    }

    public function test_customer_list_groups_by_phone_removes_walkin_and_shows_not_found()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        // 1. Order 1 for Customer A (Phone: 9988776655)
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Ananya Sharma',
            'customer_phone' => '9988776655',
            'customer_email' => 'ananya@example.com',
            'total_amount' => 500,
            'grand_total' => 500,
            'amount_paid' => 500,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // 2. Order 2 for SAME Customer A (Same Phone: 9988776655)
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Ananya Sharma',
            'customer_phone' => '9988776655',
            'customer_email' => null,
            'total_amount' => 800,
            'grand_total' => 800,
            'amount_paid' => 800,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // 3. Order 3: Walk-in customer (MUST BE REMOVED)
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Walk-in Customer',
            'customer_phone' => null,
            'customer_email' => null,
            'total_amount' => 300,
            'grand_total' => 300,
            'amount_paid' => 300,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // 4. Order 4: Customer B who gave name but NO phone and NO email
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Devendra Verma',
            'customer_phone' => null,
            'customer_email' => null,
            'total_amount' => 450,
            'grand_total' => 450,
            'amount_paid' => 450,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // Request Customer List Page
        $response = $this->actingAs($user)->get('/customers');
        $response->assertStatus(200);

        // Check Customer A is listed
        $response->assertSeeText('Ananya Sharma');
        $response->assertSeeText('9988776655');
        // Check orders combined for Customer A (total spent 1300)
        $response->assertSeeText('1,300.00');

        // Check Walk-in Customer is NOT listed
        $response->assertDontSeeText('Walk-in Customer');

        // Check Customer B is listed with "Not Found" for phone and email
        $response->assertSeeText('Devendra Verma');
        $response->assertSeeText('Not Found');

        // Verify Customer A appears only once (deduplicated by phone number)
        $customersData = $response->viewData('customers');
        $this->assertEquals(2, $customersData->count());
        $this->assertEquals(1, $customersData->where('phone', '9988776655')->count());
        $this->assertEquals(1300.0, $customersData->where('phone', '9988776655')->first()->total_spent);
        $this->assertEquals(2, $customersData->where('phone', '9988776655')->first()->total_orders);
    }

    public function test_keyword_search_filters_customer_list()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        // Customer 1
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Vikramaditya Roy',
            'customer_phone' => '9123456789',
            'customer_email' => 'vikram@example.com',
            'total_amount' => 600,
            'grand_total' => 600,
            'amount_paid' => 600,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // Customer 2
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Sneha Patel',
            'customer_phone' => '9876543210',
            'customer_email' => 'sneha@testdomain.com',
            'total_amount' => 950,
            'grand_total' => 950,
            'amount_paid' => 950,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        // Search by keyword "Vikram"
        $resSearch = $this->actingAs($user)->get('/customers?keyword=Vikram');
        $resSearch->assertStatus(200);
        $resSearch->assertSeeText('Vikramaditya Roy');
        $resSearch->assertDontSeeText('Sneha Patel');

        // Search by phone "9876543210"
        $resPhone = $this->actingAs($user)->get('/customers?keyword=9876543210');
        $resPhone->assertStatus(200);
        $resPhone->assertSeeText('Sneha Patel');
        $resPhone->assertDontSeeText('Vikramaditya Roy');
    }

    public function test_excel_export_downloads_spreadsheet()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Pooja Hegde',
            'customer_phone' => '9000011111',
            'customer_email' => 'pooja@example.com',
            'total_amount' => 1200,
            'grand_total' => 1200,
            'amount_paid' => 1200,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED'
        ]);

        $response = $this->actingAs($user)->get('/customers/export');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), '.xlsx'));
    }
}
