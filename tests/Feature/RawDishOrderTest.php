<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\TempOrder;
use App\Models\TempOrderItem;
use App\Models\TableManage;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RawDishOrderTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $user;
    protected $category;
    protected $rawDish;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::first() ?? RestaurantMaster::create([
            'name' => 'Raw Dish Test Rest',
            'email' => 'raw_' . uniqid() . '@example.com',
            'phone' => '9876543210',
            'address' => 'Test Street',
            'prefix' => 'RAW',
            'gst_percentage' => 5,
            'is_gst_enable' => 1,
        ]);

        $plan = Plan::first() ?? Plan::create([
            'name' => 'Raw Plan',
            'price' => 999,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        Subscription::firstOrCreate(
            ['user_id' => $this->restaurant->id, 'status' => 'active'],
            ['plan_id' => $plan->id, 'start_date' => now()->subDay(), 'end_date' => now()->addYear()]
        );

        $this->user = User::where('restaurant_id', $this->restaurant->id)->first() ?? User::create([
            'name' => 'Raw Rest Owner',
            'email' => 'raw_owner_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $this->restaurant->id,
            'role' => 'RES',
            'role_type' => 'ADMIN',
        ]);

        $this->category = Category::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Butchery & Fish',
            'status' => 'ACTIVE',
        ]);

        $this->rawDish = SubCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $this->category->id,
            'name' => 'Fresh Raw Salmon',
            'price' => 800.00, // Rs. 800 per KG
            'is_raw_dish' => true,
            'food_type' => 'non-veg',
            'status' => 'ACTIVE',
        ]);
    }

    public function test_formatted_weight_attribute_on_order_items()
    {
        $item1 = new OrderItems([
            'is_raw_dish' => true,
            'weight_in_kg' => 1.500,
            'weight_gram' => 1500,
            'per_kg_price' => 800.00,
        ]);
        $this->assertEquals('1 kg 500 gm', $item1->formatted_weight);

        $item2 = new OrderItems([
            'is_raw_dish' => true,
            'weight_in_kg' => 0.750,
            'weight_gram' => 750,
            'per_kg_price' => 800.00,
        ]);
        $this->assertEquals('750 gm', $item2->formatted_weight);

        $item3 = new OrderItems([
            'is_raw_dish' => true,
            'weight_in_kg' => 2.000,
            'weight_gram' => 2000,
            'per_kg_price' => 800.00,
        ]);
        $this->assertEquals('2 kg', $item3->formatted_weight);
    }

    public function test_rapid_bill_with_raw_dish_weight()
    {
        $this->actingAs($this->user);

        // 1.5 KG @ Rs 800/KG = 1200.00 line price
        $payload = [
            'customer_name' => 'John Raw',
            'customer_phone' => '9999888877',
            'order_type' => 'TAKEAWAY',
            'items' => [
                [
                    'id' => $this->rawDish->id,
                    'dish_id' => $this->rawDish->id,
                    'name' => $this->rawDish->name,
                    'price' => 1200.00,
                    'quantity' => 1,
                    'is_raw_dish' => 1,
                    'weight_in_kg' => 1.5,
                    'weight_gram' => 1500,
                    'per_kg_price' => 800.00,
                ]
            ],
            'cash_amount' => 1260, // 1200 + 5% GST = 1260
            'order_discount' => 0,
            'order_discount_type' => 'FIXED',
        ];

        $response = $this->postJson(route('rapid.bill.store'), $payload);
        $response->assertStatus(200);

        $savedOrder = OrderManage::where('restaurant_id', $this->restaurant->id)
            ->where('customer_name', 'John Raw')
            ->latest()
            ->first();

        $this->assertNotNull($savedOrder);
        $orderItem = OrderItems::where('order_id', $savedOrder->id)->first();
        $this->assertNotNull($orderItem);
        $this->assertTrue((bool)$orderItem->is_raw_dish);
        $this->assertEquals(1.5, (float)$orderItem->weight_in_kg);
        $this->assertEquals(1500, $orderItem->weight_gram);
        $this->assertEquals(800.00, (float)$orderItem->per_kg_price);
        $this->assertEquals('1 kg 500 gm', $orderItem->formatted_weight);
    }

    public function test_customer_qr_order_with_raw_dish_persists_and_approves()
    {
        $table = TableManage::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table RAW 1',
            'table_status' => 'AVAILABLE',
            'capacity' => 4,
        ]);

        // Place QR order with raw dish (0.5 kg @ Rs. 800/KG = Rs. 400)
        $qrPayload = [
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $table->id,
            'customer_name' => 'Sarah QR',
            'customer_phone' => '9888776655',
            'order_items' => [
                [
                    'id' => $this->rawDish->id,
                    'name' => $this->rawDish->name,
                    'price' => 400.00,
                    'qty' => 1,
                    'is_raw_dish' => 1,
                    'weight_in_kg' => 0.5,
                    'weight_gram' => 500,
                    'per_kg_price' => 800.00,
                ]
            ]
        ];

        $qrResponse = $this->postJson(route('temp.order.store'), $qrPayload);
        $qrResponse->assertStatus(200);
        $qrResponse->assertJson(['status' => true]);

        $tempOrder = TempOrder::where('restaurant_id', $this->restaurant->id)
            ->where('customer_name', 'Sarah QR')
            ->latest()
            ->first();

        $this->assertNotNull($tempOrder);
        $tempItem = TempOrderItem::where('temp_order_id', $tempOrder->id)->first();
        $this->assertNotNull($tempItem);
        $this->assertTrue((bool)$tempItem->is_raw_dish);
        $this->assertEquals(0.5, (float)$tempItem->weight_in_kg);
        $this->assertEquals(500, $tempItem->weight_gram);
        $this->assertEquals('500 gm', $tempItem->formatted_weight);

        // Now approve the order as staff
        $this->actingAs($this->user);
        $approveResponse = $this->get(route('admin.temporder.approve', $tempOrder->id));
        $approveResponse->assertRedirect();

        $activeOrder = OrderManage::where('restaurant_id', $this->restaurant->id)
            ->where('customer_name', 'Sarah QR')
            ->latest()
            ->first();

        $this->assertNotNull($activeOrder);
        $activeItem = OrderItems::where('order_id', $activeOrder->id)->first();
        $this->assertNotNull($activeItem);
        $this->assertTrue((bool)$activeItem->is_raw_dish);
        $this->assertEquals(0.5, (float)$activeItem->weight_in_kg);
        $this->assertEquals(500, $activeItem->weight_gram);
        $this->assertEquals('500 gm', $activeItem->formatted_weight);
    }
}
