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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerQrAddonsWorkflowTest extends TestCase
{
    use DatabaseTransactions;

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
        $response->assertSee('customerAddonModal', false);
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

        // Base price: 200.00
        // Discount 10% on dish: 180.00
        // Qty 2 -> Dish taxable: 180 * 2 = 360.00
        // Addons: 40 + (25*2) = 90.00
        // Taxable = 360 + 90 = 450.00
        // GST 5% = 22.50
        // Total = 472.50
        $this->assertEquals(200.00, floatval($tempItem->price));
        $this->assertEquals(180.00, floatval($tempItem->discounted_price));
        $this->assertEquals(450.00, floatval($tempItem->taxable_amount));
        $this->assertEquals(22.50, floatval($tempItem->gst_amount));
        $this->assertEquals(472.50, floatval($tempItem->total_amount));

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
        $this->assertEquals(472.50, floatval($mainOrderItem->total_amount));

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
            'order_status' => 'PENDING',
            'order_type' => 'DINE_IN',
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
        $this->assertEquals(200.00, floatval($addedItem->price));
        $this->assertEquals(240.00, floatval($addedItem->taxable_amount));
        $this->assertNotEmpty($addedItem->addons);
        $this->assertEquals('Extra Mozzarella', $addedItem->addons_list[0]['name']);
    }

    public function test_customer_can_order_standalone_addons_separately()
    {
        $orderItemsPayload = [
            [
                'id' => 'addon_' . $this->addonDip->id,
                'name' => 'Garlic Dip',
                'price' => 25.00,
                'qty' => 2,
                'is_addon' => true,
                'addons' => [
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

        $response = $this->postJson(route('temp.order.store'), [
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Standalone Addon Customer',
            'customer_phone' => '9998887776',
            'order_items' => $orderItemsPayload,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $tempOrder = TempOrder::where('table_id', $this->table->id)->latest('id')->first();
        $this->assertNotNull($tempOrder);

        $tempItem = TempOrderItem::where('temp_order_id', $tempOrder->id)->first();
        $this->assertNotNull($tempItem);
        $this->assertNull($tempItem->subcategory_id);
        $this->assertEquals('Garlic Dip', $tempItem->subcategory->name);
        $this->assertEquals(25.00, floatval($tempItem->price));
        $this->assertEquals(2, $tempItem->quantity);
        $this->assertEquals(50.00, floatval($tempItem->taxable_amount));

        // Admin approves temporary order
        $this->actingAs($this->adminUser);
        $approveResponse = $this->get(route('admin.temporder.approve', $tempOrder->id));
        $approveResponse->assertRedirect();

        $mainOrder = OrderManage::where('table_id', $this->table->id)->latest('id')->first();
        $this->assertNotNull($mainOrder);

        $mainOrderItem = OrderItems::where('order_id', $mainOrder->id)->latest('id')->first();
        $this->assertNotNull($mainOrderItem);
        $this->assertNull($mainOrderItem->subcategory_id);
        $this->assertEquals('Garlic Dip', $mainOrderItem->subcategory->name);
        $this->assertEquals(2, $mainOrderItem->quantity);
    }

    public function test_order_success_page_renders_attached_and_standalone_addons()
    {
        $tempOrder = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Addon Viewer',
            'customer_phone' => '9876543210',
            'order_status' => 'PENDING',
            'total_amount' => 240.00,
            'grand_total' => 240.00,
        ]);

        // Item 1: Dish with attached addon
        TempOrderItem::create([
            'restaurant_id' => $this->restaurant->id,
            'temp_order_id' => $tempOrder->id,
            'subcategory_id' => $this->dish->id,
            'quantity' => 1,
            'price' => 240.00,
            'discounted_price' => 240.00,
            'total_amount' => 240.00,
            'order_status' => 'PENDING',
            'addons' => [
                [
                    'id' => $this->addonCheese->id,
                    'name' => 'Extra Mozzarella',
                    'price' => 40.00,
                    'qty' => 1,
                    'food_type' => 'Veg',
                ]
            ],
        ]);

        // Item 2: Standalone addon
        TempOrderItem::create([
            'restaurant_id' => $this->restaurant->id,
            'temp_order_id' => $tempOrder->id,
            'subcategory_id' => null,
            'quantity' => 2,
            'price' => 25.00,
            'discounted_price' => 25.00,
            'total_amount' => 50.00,
            'order_status' => 'PENDING',
            'addons' => [
                [
                    'id' => $this->addonDip->id,
                    'name' => 'Garlic Dip',
                    'price' => 25.00,
                    'qty' => 2,
                    'food_type' => 'Veg',
                ]
            ],
        ]);

        session([
            'customer_qr_allowed_orders' => [(int) $tempOrder->id],
            'customer_qr_order_id' => $tempOrder->id,
            'customer_qr_order_type' => 'temp',
        ]);

        $response = $this->get(route('order.success', $tempOrder->id));

        $response->assertStatus(200);
        $response->assertSee('Gourmet Cheese Pizza');
        $response->assertSee('Extra Mozzarella');
        $response->assertSee('Garlic Dip');
    }

    public function test_dish_ordered_without_addons_does_not_attach_or_show_addons()
    {
        $payload = [
            'customer_name' => 'Rahul Sharma',
            'customer_phone' => '9876543210',
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'order_items' => [
                [
                    'id' => $this->dish->id,
                    'name' => 'Gourmet Cheese Pizza',
                    'price' => 200.00,
                    'qty' => 1,
                    'item_discount' => 0,
                    'addons' => []
                ]
            ]
        ];

        $postResponse = $this->post(route('temp.order.store'), $payload);
        $postResponse->assertStatus(200);
        $postResponse->assertJson(['status' => true]);

        $createdTempOrder = TempOrder::where('customer_phone', '9876543210')->latest('id')->first();
        $this->assertNotNull($createdTempOrder);

        $item = $createdTempOrder->items->first();
        $this->assertEquals($this->dish->id, $item->subcategory_id);
        $this->assertEmpty($item->addons_list);
        $this->assertEquals(200.00, floatval($item->price));
        $this->assertEquals(200.00, floatval($item->taxable_amount));
        $this->assertEquals(210.00, floatval($item->total_amount));

        // View order success page
        session([
            'customer_qr_allowed_orders' => [(int) $createdTempOrder->id],
            'customer_qr_order_id' => $createdTempOrder->id,
            'customer_qr_order_type' => 'temp',
        ]);
        $successResponse = $this->get(route('order.success', $createdTempOrder->id));
        $successResponse->assertStatus(200);
        $successResponse->assertSee('Gourmet Cheese Pizza');
        $successResponse->assertDontSee('+ Gourmet Cheese Pizza');
        $successResponse->assertDontSee('+ Extra Mozzarella');
    }
}

