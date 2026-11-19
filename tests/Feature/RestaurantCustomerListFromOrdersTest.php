<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\OrderManage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantCustomerListFromOrdersTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndUser()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Biryani Palace ' . uniqid(),
            'restaurant_id_unique' => 'REST-BP-' . rand(100, 999),
            'parent_id' => null,
            'address' => '12 Food Street',
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

    public function test_customers_page_renders_successfully()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        $response = $this->actingAs($user)->get('/customers');
        $response->assertStatus(200);
        $response->assertSee('Customer Directory');
        $response->assertSee('Quick Actions');
    }

    public function test_customers_page_extracts_unique_mobile_numbers_with_call_and_whatsapp_actions()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        // Create 2 orders for the same phone number (should appear only ONCE)
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Amit Kumar',
            'customer_phone' => '9876543210',
            'grand_total' => 500,
            'amount_paid' => 500,
            'order_status' => 'COMPLETED'
        ]);

        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Amit Kumar',
            'customer_phone' => '9876543210',
            'grand_total' => 750,
            'amount_paid' => 750,
            'order_status' => 'COMPLETED'
        ]);

        // Create an order for a second customer
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Pooja Sharma',
            'customer_phone' => '9123456789',
            'grand_total' => 300,
            'amount_paid' => 300,
            'order_status' => 'COMPLETED'
        ]);

        // Create a walk-in order without a mobile number (should NOT appear)
        OrderManage::create([
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Walk-in Guest',
            'customer_phone' => null,
            'grand_total' => 200,
            'order_status' => 'COMPLETED'
        ]);

        $response = $this->actingAs($user)->get('/customers');

        $response->assertStatus(200);
        $response->assertSee('Amit Kumar');
        $response->assertSee('9876543210');
        $response->assertSee('Pooja Sharma');
        $response->assertSee('9123456789');

        // Verify Call and WhatsApp action buttons
        $response->assertSee('tel:9876543210');
        $response->assertSee('api.whatsapp.com/send?phone=919876543210');
        $response->assertSee('tel:9123456789');
        $response->assertSee('api.whatsapp.com/send?phone=919123456789');

        // Check that Amit's total spent is aggregated (500 + 750 = 1,250) and repeat badge is present
        $response->assertSee('1,250.00');
        $response->assertSee('Repeat');
    }
}
