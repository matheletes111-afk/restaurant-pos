<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\OrderToPayment;
use App\Models\CashDrawerTransaction;
use App\Models\TableManage;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderCheckoutSplitPaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndUser()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Split Pay Rest ' . uniqid(),
            'email' => 'split_pay_' . uniqid() . '@example.com',
            'phone' => '9876543210',
            'address' => 'Express Street 10',
            'prefix' => 'SPLIT',
            'gstin' => '27AABCU9603R1ZM',
            'gst_percentage' => 5,
            'is_gst_enable' => 1,
        ]);

        $plan = Plan::first() ?? Plan::create([
            'name' => 'Pro Plan',
            'price' => 999,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addYear(),
            'status' => 'active'
        ]);

        $user = User::create([
            'name' => 'Checkout Cashier',
            'email' => 'cashier_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $restaurant->id,
            'role' => 'RES',
            'role_type' => 'ADMIN',
        ]);

        $category = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Main Kitchen',
            'status' => 'ACTIVE',
        ]);

        $dish1 = SubCategory::create([
            'restaurant_id' => $restaurant->id,
            'category_id' => $category->id,
            'name' => 'Paneer Butter Masala',
            'price' => 200,
            'food_type' => 'veg',
            'status' => 'ACTIVE',
        ]);

        $dish2 = SubCategory::create([
            'restaurant_id' => $restaurant->id,
            'category_id' => $category->id,
            'name' => 'Butter Naan',
            'price' => 50,
            'food_type' => 'veg',
            'status' => 'ACTIVE',
        ]);

        $table = TableManage::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'T-SPLIT-' . uniqid(),
            'capacity' => 4,
            'table_status' => 'AVAILABLE',
            'status' => 'A'
        ]);

        return [$restaurant, $user, $dish1, $dish2, $table];
    }

    public function test_create_takeaway_with_split_cash_and_upi_payment()
    {
        list($restaurant, $user, $dish1, $dish2, $table) = $this->setupRestaurantAndUser();

        // 1 Paneer (200) + 2 Naan (100) = 300. With 5% GST = 315.
        $payload = [
            'customer_name' => 'John Doe',
            'customer_phone' => '9876543210',
            'table_id' => null, // TAKEAWAY
            'order_complete' => 'DONE',
            'cash_amount' => 150.00,
            'upi_amount' => 165.00, // Total 315 settles full 315
            'order_items' => [
                ['id' => $dish1->id, 'name' => $dish1->name, 'price' => $dish1->price, 'qty' => 1, 'item_discount' => 0],
                ['id' => $dish2->id, 'name' => $dish2->name, 'price' => $dish2->price, 'qty' => 2, 'item_discount' => 0],
            ]
        ];

        $response = $this->actingAs($user)->postJson(route('order.save'), $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'final_total', 'order_id', 'redirect_url', 'invoice_url']);
        $this->assertTrue($response->json('success'));
        $this->assertEquals(315, $response->json('final_total'));
        $this->assertStringContainsString('autoprint=1', $response->json('redirect_url'));

        $orderId = $response->json('order_id');
        $order = OrderManage::findOrFail($orderId);

        $this->assertEquals('DONE', $order->order_complete);
        $this->assertEquals('COMPLETED', $order->order_status);
        $this->assertEquals('PAID', $order->payment_status);
        $this->assertEquals('SPLIT', $order->payment_method);

        // Verify OrderToPayment logs
        $payments = OrderToPayment::where('order_id', $orderId)->get();
        $this->assertCount(2, $payments);
        
        $cashPmt = $payments->where('payment_method', 'CASH')->first();
        $upiPmt = $payments->where('payment_method', 'UPI')->first();
        
        $this->assertNotNull($cashPmt);
        $this->assertEquals(150.00, (float)$cashPmt->amount);
        $this->assertNotNull($upiPmt);
        $this->assertEquals(165.00, (float)$upiPmt->amount);

        // Verify CashDrawer transaction
        $cashDrawerTx = CashDrawerTransaction::where('restaurant_id', $restaurant->id)
            ->where('reference_id', $cashPmt->id)
            ->first();
        $this->assertNotNull($cashDrawerTx);
        $this->assertEquals(150.00, (float)$cashDrawerTx->amount);
    }

    public function test_edit_order_mark_done_and_checkout_with_split_payment()
    {
        list($restaurant, $user, $dish1, $dish2, $table) = $this->setupRestaurantAndUser();

        // Create an active running order on Table
        $order = OrderManage::create([
            'order_id' => 'ORD-TEST-' . uniqid(),
            'customer_name' => 'Table Guest',
            'customer_phone' => '9888877777',
            'table_id' => $table->id,
            'order_type' => 'DINE_IN',
            'total_amount' => 200,
            'taxable_amount' => 200,
            'gst_amount' => 0,
            'grand_total' => 200,
            'discount' => 0,
            'discount_percentage' => 0,
            'order_status' => 'PENDING',
            'order_complete' => 'PENDING',
            'payment_status' => 'PENDING',
            'amount_paid' => 0,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        OrderItems::create([
            'order_id' => $order->id,
            'subcategory_id' => $dish1->id,
            'quantity' => 1,
            'price' => 200,
            'taxable_amount' => 200,
            'gst_rate' => 5,
            'gst_amount' => 10,
            'total_amount' => 210,
            'order_status' => 'PENDING',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $table->update(['table_status' => 'OCCUPIED', 'order_id' => $order->id]);

        // Edit order: Mark Done & Checkout Bill with ₹105 Cash + ₹105 UPI (Grand total with 5% GST is 210)
        $payload = [
            'order_complete' => 'DONE',
            'cash_amount' => 105.00,
            'upi_amount' => 105.00,
            'customer_phone' => '9888877777'
        ];

        $response = $this->actingAs($user)->postJson(route('order.update', $order->id), $payload);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertStringContainsString('autoprint=1', $response->json('redirect_url'));

        $order->refresh();
        $this->assertEquals('DONE', $order->order_complete);
        $this->assertEquals('COMPLETED', $order->order_status);
        $this->assertEquals('PAID', $order->payment_status);
        $this->assertEquals(210.00, (float)$order->amount_paid);

        // Verify OrderToPayment logs
        $payments = OrderToPayment::where('order_id', $order->id)->get();
        $this->assertCount(2, $payments);

        // Verify Table freed
        $table->refresh();
        $this->assertEquals('AVAILABLE', $table->table_status);
    }

    public function test_create_order_with_addons_and_update_with_addons()
    {
        list($restaurant, $user, $dish1, $dish2, $table) = $this->setupRestaurantAndUser();

        $addon1 = \App\Models\DishAddon::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Extra Butter',
            'price' => 30.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $addon2 = \App\Models\DishAddon::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Extra Cheese Slice',
            'price' => 20.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        // Create an order via Order Management with addons
        // Base dish1 = 200, Addon1 = 30 * 2 = 60, Addon2 = 20 * 1 = 20. Unit price = 280.
        // Qty = 1. Taxable = 280. GST 5% = 14. Grand total = 294.
        $payload = [
            'customer_name' => 'Addon Foodie',
            'customer_phone' => '9123456789',
            'table_id' => $table->id,
            'order_complete' => 'PENDING',
            'order_items' => [
                [
                    'id' => $dish1->id,
                    'name' => $dish1->name,
                    'price' => $dish1->price,
                    'qty' => 1,
                    'item_discount' => 0,
                    'addons' => [
                        ['id' => $addon1->id, 'name' => 'Extra Butter', 'price' => 30, 'qty' => 2, 'food_type' => 'VEG'],
                        ['id' => $addon2->id, 'name' => 'Extra Cheese Slice', 'price' => 20, 'qty' => 1, 'food_type' => 'VEG']
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($user)->postJson(route('order.save'), $payload);
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertEquals(294, $response->json('final_total'));

        $orderId = $response->json('order_id');
        $orderItem = OrderItems::where('order_id', $orderId)->first();
        $this->assertNotNull($orderItem);
        $this->assertNotNull($orderItem->addons);
        $this->assertCount(2, $orderItem->addons_list);
        $this->assertEquals(280.00, (float)$orderItem->price);

        // Test editing order to add new item with addons
        // Add dish2 = 50 + Extra Butter (30 * 1) = 80. Taxable = 80. GST 5% = 4. Total = 84.
        $updatePayload = [
            'order_complete' => 'PENDING',
            'order_items' => [
                [
                    'id' => $dish2->id,
                    'name' => $dish2->name,
                    'price' => $dish2->price,
                    'qty' => 1,
                    'item_discount' => 0,
                    'addons' => [
                        ['id' => $addon1->id, 'name' => 'Extra Butter', 'price' => 30, 'qty' => 1, 'food_type' => 'VEG']
                    ]
                ]
            ]
        ];

        $updateResponse = $this->actingAs($user)->postJson(route('order.update', $orderId), $updatePayload);
        $updateResponse->assertStatus(200);
        $this->assertTrue($updateResponse->json('success'));

        $newItem = OrderItems::where('order_id', $orderId)->where('subcategory_id', $dish2->id)->first();
        $this->assertNotNull($newItem);
        $this->assertNotNull($newItem->addons);
        $this->assertEquals('Extra Butter', $newItem->addons_list[0]['name']);
        $this->assertEquals(80.00, (float)$newItem->price);

        // Verify edit view renders
        $editViewResponse = $this->actingAs($user)->get(route('order.edit', $orderId));
        $editViewResponse->assertStatus(200);
        $editViewResponse->assertSee('Extra Butter');
        $editViewResponse->assertSee('Extra Cheese Slice');
    }
}
