<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderOverallDiscountCalculationTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $user;
    protected $category;
    protected $dish;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::create([
            'name' => 'Discount Test Rest',
            'email' => 'discount_' . uniqid() . '@example.com',
            'phone' => '9988776655',
            'address' => 'Discount Test St',
            'prefix' => 'DISC',
            'gst_percentage' => 5,
            'is_gst_enable' => 1,
            'gstin' => '27AABCU9603R1ZM',
        ]);

        $plan = Plan::first() ?? Plan::create([
            'name' => 'Basic Plan',
            'price' => 500,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        Subscription::firstOrCreate(
            ['user_id' => $this->restaurant->id, 'status' => 'active'],
            ['plan_id' => $plan->id, 'start_date' => now()->subDay(), 'end_date' => now()->addYear()]
        );

        $this->user = User::create([
            'name' => 'Discount Tester',
            'email' => 'tester_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $this->restaurant->id,
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'status' => 'A'
        ]);

        $this->category = Category::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Fast Food',
            'status' => 'A',
        ]);

        $this->dish = SubCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $this->category->id,
            'name' => 'Classic Burger',
            'price' => 100,
            'status' => 'A',
            'food_type' => 'VEG',
        ]);
    }

    /**
     * Test Rapid Bill with overall percentage discount
     */
    public function test_rapid_bill_overall_percentage_discount()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('rapid.bill.store'), [
            'customer_name' => 'Jane Percent',
            'customer_phone' => '9876543212',
            'order_type' => 'TAKEAWAY',
            'order_discount' => 10,
            'order_discount_type' => 'PERCENT',
            'cash_amount' => 95,
            'upi_amount' => 0,
            'items' => [
                [
                    'dish_id' => $this->dish->id,
                    'name' => $this->dish->name,
                    'quantity' => 1,
                    'price' => 100,
                    'discount_percentage' => 0,
                    'vat_rate' => 0,
                ]
            ]
        ]);

        $response->assertStatus(200);
        $orderId = $response->json('order_id');
        $order = OrderManage::find($orderId);

        $this->assertEquals(100.0, (float)$order->total_amount);
        $this->assertEquals(10.0, (float)$order->discount);
        $this->assertEquals(10.0, (float)$order->discount_percentage);
        $this->assertEquals('PERCENT', $order->discount_type);
        $this->assertEquals(90.0, (float)$order->taxable_amount);
        $this->assertEquals(4.50, (float)$order->gst_amount);
        $this->assertEquals(95.0, (float)$order->grand_total);
    }

    /**
     * Test that item-wise discount is preserved and overall discount applies on top
     * Dish price: 200, item discount: 50% => line taxable = 100
     * Overall fixed discount = 10 => net taxable = 90
     * GST 5% on 90 = 4.50 => grand total = 95
     */
    public function test_item_discount_preserved_with_overall_order_discount()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('rapid.bill.store'), [
            'customer_name' => 'Bob BothDiscounts',
            'customer_phone' => '9876543213',
            'order_type' => 'TAKEAWAY',
            'order_discount' => 10,
            'order_discount_type' => 'FIXED',
            'cash_amount' => 95,
            'upi_amount' => 0,
            'items' => [
                [
                    'dish_id' => $this->dish->id,
                    'name' => $this->dish->name,
                    'quantity' => 1,
                    'price' => 200,
                    'discount_percentage' => 50,
                    'vat_rate' => 0,
                ]
            ]
        ]);

        $response->assertStatus(200);
        $orderId = $response->json('order_id');
        $order = OrderManage::find($orderId);

        $this->assertEquals(200.0, (float)$order->total_amount);
        $this->assertEquals(10.0, (float)$order->discount);
        $this->assertEquals('FIXED', $order->discount_type);
        $this->assertEquals(90.0, (float)$order->taxable_amount);
        $this->assertEquals(4.50, (float)$order->gst_amount);
        $this->assertEquals(95.0, (float)$order->grand_total);

        // Check order item retains its individual 50% discount and 100 discounted price
        $item = OrderItems::where('order_id', $orderId)->first();
        $this->assertEquals(200.0, (float)$item->price);
        $this->assertEquals(50.0, (float)$item->item_discount_percentage);
        $this->assertEquals(100.0, (float)$item->discounted_price);
        $this->assertEquals(100.0, (float)$item->taxable_amount);
    }

    /**
     * Price without GST = 100
     * Fixed discount = 10
     * Net taxable = 90
     * GST 5% on 90 = 4.50
     * Final Grand Total = 95 (with round_off +0.50)
     */
    public function test_rapid_bill_overall_fixed_discount()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('rapid.bill.store'), [
            'customer_name' => 'John Fixed',
            'customer_phone' => '9876543210',
            'order_type' => 'TAKEAWAY',
            'order_discount' => 10,
            'order_discount_type' => 'FIXED',
            'cash_amount' => 95,
            'upi_amount' => 0,
            'items' => [
                [
                    'dish_id' => $this->dish->id,
                    'name' => $this->dish->name,
                    'quantity' => 1,
                    'price' => 100,
                    'discount_percentage' => 0,
                    'vat_rate' => 0,
                ]
            ]
        ]);

        if ($response->status() !== 200) {
            dump($response->json());
        }

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $orderId = $response->json('order_id');
        $order = OrderManage::find($orderId);

        $this->assertNotNull($order);
        // Original subtotal = 100
        $this->assertEquals(100.0, (float)$order->total_amount);
        // Overall discount = 10
        $this->assertEquals(10.0, (float)$order->discount);
        $this->assertEquals('FIXED', $order->discount_type);
        // Post-discount taxable amount = 90
        $this->assertEquals(90.0, (float)$order->taxable_amount);
        // GST (5% on 90) = 4.50
        $this->assertEquals(4.50, (float)$order->gst_amount);
        // Final total = 95
        $this->assertEquals(95.0, (float)$order->grand_total);
        $this->assertEquals(0.50, (float)$order->round_off);

        // Individual item unit price and taxable amount remain unchanged
        $item = OrderItems::where('order_id', $orderId)->first();
        $this->assertEquals(100.0, (float)$item->price);
        $this->assertEquals(100.0, (float)$item->taxable_amount);
        $this->assertEquals(0.0, (float)$item->item_discount_percentage);
    }

    /**
     * Test Order Management Create with overall percentage discount:
     * Price without GST = 100
     * Percentage discount = 10%
     * Net taxable = 90
     * GST 5% on 90 = 4.50
     * Final Grand Total = 95
     */
    public function test_order_management_store_and_update_with_discount()
    {
        $this->actingAs($this->user);

        // 1. Store order with 10% discount
        $response = $this->postJson(route('order.save'), [
            'customer_name' => 'Alice Percent',
            'customer_phone' => '9876543211',
            'discount' => 10,
            'discount_type' => 'PERCENT',
            'order_complete' => 'DONE',
            'cash_amount' => 95,
            'upi_amount' => 0,
            'order_items' => [
                [
                    'id' => $this->dish->id,
                    'name' => $this->dish->name,
                    'price' => 100,
                    'qty' => 1,
                    'item_discount' => 0,
                    'vat_rate' => 0,
                ]
            ]
        ]);

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $orderId = $response->json('order_id');
        $order = OrderManage::find($orderId);

        $this->assertNotNull($order);
        $this->assertEquals(100.0, (float)$order->total_amount);
        $this->assertEquals(10.0, (float)$order->discount);
        $this->assertEquals(10.0, (float)$order->discount_percentage);
        $this->assertEquals('PERCENT', $order->discount_type);
        $this->assertEquals(90.0, (float)$order->taxable_amount);
        $this->assertEquals(4.50, (float)$order->gst_amount);
        $this->assertEquals(95.0, (float)$order->grand_total);

        // 2. Update order to fixed discount of 20
        $updateResponse = $this->postJson(route('order.update', $order->id), [
            'discount' => 20,
            'discount_type' => 'FIXED',
            'order_complete' => 'PENDING',
        ]);

        if ($updateResponse->status() !== 200) {
            dump($updateResponse->json());
        }

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals(20.0, (float)$order->discount);
        $this->assertEquals('FIXED', $order->discount_type);
        // Post-discount taxable = 100 - 20 = 80
        $this->assertEquals(80.0, (float)$order->taxable_amount);
        // GST (5% on 80) = 4.00
        $this->assertEquals(4.00, (float)$order->gst_amount);
        // Final total = 84
        $this->assertEquals(84.0, (float)$order->grand_total);
    }
}
