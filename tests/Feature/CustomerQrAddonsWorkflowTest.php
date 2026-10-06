<?php

namespace Tests\Feature;

use App\Models\DishAddon;
use App\Models\OrderItems;
use App\Models\OrderManage;
use App\Models\RestaurantMaster;
use App\Models\SubCategory;
use App\Models\TableManage;
use App\Models\TempOrder;
use App\Models\TempOrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerQrAddonsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $restaurant;
    protected $table;
    protected $dish;
    protected $addonCheese;
    protected $addonDip;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::create([
            'name' => 'Addons Test Cafe ' . uniqid(),
            'email' => 'cafe_' . uniqid() . '@example.com',
            'phone_number' => '9876543210',
            'address' => 'Food Street 1',
            'status' => 'A',
            'is_gst_registered' => 'YES',
            'gstin' => '27ABCDE1234F1Z5',
            'gst_percentage' => 5,
        ]);

        $plan = \App\Models\Plan::first() ?? \App\Models\Plan::create([
            'name' => 'Gold Plan',
            'price' => 999,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'plan_status' => 'A',
        ]);

        \App\Models\Subscription::create([
            'user_id' => $this->restaurant->id,
            'plan_id' => $plan->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addYear(),
            'status' => 'active'
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $this->restaurant->id,
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'status' => 'A',
        ]);

        $this->table = TableManage::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 10',
            'seating_capacity' => 4,
            'table_status' => 'AVAILABLE',
            'status' => 'A',
        ]);

        $category = \App\Models\Category::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Pizzas',
            'status' => 'A',
        ]);

        $this->dish = SubCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $category->id,
            'name' => 'Gourmet Cheese Pizza',
            'price' => 200.00,
            'discount_percentage' => 10.00, // discounted price: 180.00
            'food_type' => 'Veg',
            'status' => 'A',
        ]);

        $this->addonCheese = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Extra Mozzarella',
            'price' => 40.00,
            'food_type' => 'Veg',
            'status' => 'A',
        ]);

        $this->addonDip = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Garlic Dip',
            'price' => 25.00,
            'food_type' => 'Veg',
            'status' => 'A',
        ]);

        $this->dish->addons()->sync([$this->addonCheese->id, $this->addonDip->id]);
    }

    public function test_customer_can_view_menu_with_customisable_addons()
    {
        $response = $this->get(route('temp.order.create', [$this->table->id, $this->restaurant->id]));

        $response->assertStatus(200);
        $response->assertSee('Gourmet Cheese Pizza');
        $response->assertSee('Customisable');
        $response->assertSee('Extra Mozzarella', false);
    }

    public function test_customer_can_place_order_with_addons_and_approve_to_active_order()
    {
        $orderItemsPayload = [
            [
                'id' => $this->dish->id,
                'name' => 'Gourmet Cheese Pizza',
                'price' => 200.00,
                'qty' => 2,
                'item_discount' => 10,
                'addons' => [
                    [
                        'id' => $this->addonCheese->id,
                        'name' => 'Extra Mozzarella',
                        'price' => 40.00,
                        'qty' => 1,
                        'food_type' => 'Veg',
                    ],
                    [
                        'id' => $this->addonDip->id,
                        'name' => 'Garlic Dip',
                        'price' => 25.00,
                        'qty' => 2,
                        'food_type' => 'Veg',
                    ]
                ]
            ]
        ];

        // 1. Submit QR temporary order
        $response = $this->postJson(route('temp.order.store'), [
            'customer_name' => 'Aditya Sharma',
            'customer_phone' => '9876543210',
            'table_id' => $this->table->id,
            'restaurant_id' => $this->restaurant->id,
            'order_items' => $orderItemsPayload,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $tempOrder = TempOrder::where('table_id', $this->table->id)->latest()->first();
        $this->assertNotNull($tempOrder);
        $this->assertEquals('PENDING', $tempOrder->order_status);

        $tempItem = TempOrderItem::where('temp_order_id', $tempOrder->id)->first();
        $this->assertNotNull($tempItem);
        $this->assertNotEmpty($tempItem->addons);
        $this->assertCount(2, $tempItem->addons_list);

        // Unit price = (200 + 40 + (25*2)) = 290
        // Discount 10% = 290 - 29 = 261
        // Qty 2 -> Taxable = 261 * 2 = 522
        // GST 5% = 26.10
        // Total = 548.10
        $this->assertEquals(290.00, floatval($tempItem->price));
        $this->assertEquals(261.00, floatval($tempItem->discounted_price));
        $this->assertEquals(522.00, floatval($tempItem->taxable_amount));
        $this->assertEquals(26.10, floatval($tempItem->gst_amount));
        $this->assertEquals(548.10, floatval($tempItem->total_amount));

        // 2. Admin approves temporary order
        $this->actingAs($this->adminUser);
        $approveResponse = $this->get(route('admin.temporder.approve', $tempOrder->id));
        $approveResponse->assertRedirect();

        $mainOrder = OrderManage::where('table_id', $this->table->id)->latest()->first();
        $this->assertNotNull($mainOrder);

        $mainOrderItem = OrderItems::where('order_id', $mainOrder->id)->first();
        $this->assertNotNull($mainOrderItem);
        $this->assertNotEmpty($mainOrderItem->addons);
        $this->assertCount(2, $mainOrderItem->addons_list);
        $this->assertEquals('Extra Mozzarella', $mainOrderItem->addons_list[0]['name']);
        $this->assertEquals(548.10, floatval($mainOrderItem->total_amount));

        // 3. Customer views active order details
        session(['customer_qr_allowed_orders' => [(int) $mainOrder->id]]);
        $detailsResponse = $this->get(route('order.details', $mainOrder->id));
        $detailsResponse->assertStatus(200);
        $detailsResponse->assertSee('Extra Mozzarella');
        $detailsResponse->assertSee('Garlic Dip');
    }

    public function test_customer_can_add_more_items_with_addons_to_existing_dining_order()
    {
        $mainOrder = OrderManage::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Aditya Sharma',
            'phone' => '9876543210',
            'order_status' => 'ACCEPTED',
            'order_type' => 'DINEIN',
            'order_complete' => 'PROGRESS',
            'payment_status' => 'PENDING',
            'total_amount' => 100.00,
            'taxable_amount' => 100.00,
            'gst_amount' => 5.00,
            'grand_total' => 105.00,
        ]);

        session(['customer_qr_allowed_orders' => [(int) $mainOrder->id]]);

        $addPayload = [
            'order_id' => $mainOrder->id,
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Aditya Sharma',
            'customer_phone' => '9876543210',
            'order_items' => [
                [
                    'id' => $this->dish->id,
                    'name' => 'Gourmet Cheese Pizza',
                    'price' => 200.00,
                    'qty' => 1,
                    'item_discount' => 0,
                    'addons' => [
                        [
                            'id' => $this->addonCheese->id,
                            'name' => 'Extra Mozzarella',
                            'price' => 40.00,
                            'qty' => 1,
                            'food_type' => 'Veg',
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson(route('temp.order.add_items'), $addPayload);
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $addedItem = OrderItems::where('order_id', $mainOrder->id)->latest('id')->first();
        $this->assertNotNull($addedItem);
        $this->assertEquals(240.00, floatval($addedItem->price));
        $this->assertNotEmpty($addedItem->addons);
        $this->assertEquals('Extra Mozzarella', $addedItem->addons_list[0]['name']);
    }
}
