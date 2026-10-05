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

class RapidBillTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndUser()
    {
        $restaurant = RestaurantMaster::first();
        if (!$restaurant) {
            $restaurant = RestaurantMaster::create([
                'name' => 'Rapid Test Rest',
                'email' => 'rapid_test@example.com',
                'phone' => '9876543210',
                'address' => 'Fast Lane 101',
                'prefix' => 'RAPID',
                'gst_percentage' => 5,
                'is_gst_enable' => 1,
            ]);
        }

        $plan = Plan::first() ?? Plan::create([
            'name' => 'Fast Plan',
            'price' => 999,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        Subscription::firstOrCreate(
            ['user_id' => $restaurant->id, 'status' => 'active'],
            ['plan_id' => $plan->id, 'start_date' => now()->subDay(), 'end_date' => now()->addYear()]
        );

        $user = User::where('restaurant_id', $restaurant->id)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Rapid Cashier',
                'email' => 'rapid_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'restaurant_id' => $restaurant->id,
                'role' => 'RES',
                'role_type' => 'ADMIN',
            ]);
        }

        $category = Category::where('restaurant_id', $restaurant->id)->first();
        if (!$category) {
            $category = Category::create([
                'restaurant_id' => $restaurant->id,
                'name' => 'Fast Bites',
                'status' => 'ACTIVE',
            ]);
        }

        $dish1 = SubCategory::where('restaurant_id', $restaurant->id)->where('category_id', $category->id)->first();
        if (!$dish1) {
            $dish1 = SubCategory::create([
                'restaurant_id' => $restaurant->id,
                'category_id' => $category->id,
                'name' => 'Crispy Burger',
                'price' => 120,
                'food_type' => 'veg',
                'status' => 'ACTIVE',
            ]);
        }

        $dish2 = SubCategory::where('restaurant_id', $restaurant->id)->where('id', '!=', $dish1->id)->first();
        if (!$dish2) {
            $dish2 = SubCategory::create([
                'restaurant_id' => $restaurant->id,
                'category_id' => $category->id,
                'name' => 'Iced Cold Coffee',
                'price' => 80,
                'food_type' => 'veg',
                'status' => 'ACTIVE',
            ]);
        }

        $table = TableManage::where('restaurant_id', $restaurant->id)->first();
        if (!$table) {
            $table = TableManage::create([
                'restaurant_id' => $restaurant->id,
                'name' => 'T1',
                'seating_capacity' => 4,
                'status' => 'ACTIVE',
            ]);
        }

        return compact('restaurant', 'user', 'category', 'dish1', 'dish2', 'table');
    }

    public function test_rapid_bill_page_renders_successfully()
    {
        $data = $this->setupRestaurantAndUser();

        $response = $this->actingAs($data['user'])->get(route('rapid.bill'));
        $response->assertStatus(200);
        $response->assertSee('Rapid Bill Terminal');
        $response->assertSee($data['dish1']->name);
    }

    public function test_create_rapid_bill_with_split_cash_and_upi_payment()
    {
        $data = $this->setupRestaurantAndUser();

        $payload = [
            'customer_name' => 'Rahul Sharma',
            'customer_phone' => '9988776655',
            'order_type' => 'takeaway',
            'cash_amount' => 100,
            'upi_amount' => 100,
            'print_bill' => 1,
            'items' => [
                [
                    'dish_id' => $data['dish1']->id,
                    'quantity' => 1,
                    'price' => 120,
                    'discount_percentage' => 10,
                ],
                [
                    'dish_id' => $data['dish2']->id,
                    'quantity' => 1,
                    'price' => 80,
                    'discount_percentage' => 0,
                ]
            ]
        ];

        $response = $this->actingAs($data['user'])
            ->postJson(route('rapid.bill.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $orderId = $response->json('order_id');
        $this->assertNotEmpty($orderId);

        $order = OrderManage::find($orderId);
        $this->assertNotNull($order);
        $this->assertTrue(in_array(strtoupper($order->order_type), ['TAKEAWAY', 'EXPRESS']));
        $this->assertEquals(12, $order->discount); // 10% of 120 = 12

        // Check OrderItems and KOT
        $orderItems = OrderItems::where('order_id', $order->id)->get();
        $this->assertCount(2, $orderItems);
        $this->assertNotNull($orderItems->first()->kot_no);

        // Check Split Payments in OrderToPayment
        $payments = OrderToPayment::where('order_id', $order->id)->get();
        $this->assertCount(2, $payments);
        
        $cashPayment = $payments->where('payment_method', 'CASH')->first();
        $upiPayment = $payments->where('payment_method', 'UPI')->first();

        $this->assertNotNull($cashPayment);
        $this->assertNotNull($upiPayment);
        $this->assertEquals(100, $cashPayment->amount);
        $this->assertEquals(100, $upiPayment->amount);

        // Check Cash Drawer Transaction for CASH part
        $drawerTx = CashDrawerTransaction::where('reference_id', $cashPayment->id)
            ->where(function ($q) {
                $q->where('reference_type', OrderToPayment::class)
                  ->orWhere('reference_type', 'OrderToPayment');
            })
            ->first();
        $this->assertNotNull($drawerTx);
        $this->assertEquals(100, $drawerTx->amount);
        $this->assertEquals(CashDrawerTransaction::ENTRY_CREDIT, $drawerTx->entry_type);
    }

    public function test_create_rapid_bill_with_optional_customer_and_full_cash()
    {
        $data = $this->setupRestaurantAndUser();

        // Customer name and phone are left empty (optional)
        $payload = [
            'customer_name' => '',
            'customer_phone' => '',
            'order_type' => 'dine_in',
            'table_id' => $data['table']->id,
            'discount_percentage' => 0,
            'cash_amount' => 120,
            'upi_amount' => 0,
            'print_bill' => 0,
            'items' => [
                [
                    'dish_id' => $data['dish1']->id,
                    'quantity' => 1,
                    'price' => 120,
                    'discount_percentage' => 0,
                ]
            ]
        ];

        $response = $this->actingAs($data['user'])
            ->postJson(route('rapid.bill.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $order = OrderManage::find($response->json('order_id'));
        $this->assertEquals('Walk-in Customer', $order->customer_name);
        $this->assertTrue(in_array(strtoupper($order->order_type), ['DINE_IN', 'DINEIN']));
        $this->assertEquals($data['table']->id, $order->table_id);

        $payments = OrderToPayment::where('order_id', $order->id)->get();
        $this->assertCount(1, $payments);
        $this->assertEquals('CASH', $payments->first()->payment_method);
    }

    public function test_create_rapid_bill_with_full_upi_payment()
    {
        $data = $this->setupRestaurantAndUser();

        $payload = [
            'customer_name' => 'Aarav Patel',
            'customer_phone' => '9123456780',
            'order_type' => 'takeaway',
            'discount_percentage' => 0,
            'cash_amount' => 0,
            'upi_amount' => 80,
            'print_bill' => 1,
            'items' => [
                [
                    'dish_id' => $data['dish2']->id,
                    'quantity' => 1,
                    'price' => 80,
                    'discount_percentage' => 0,
                ]
            ]
        ];

        $response = $this->actingAs($data['user'])
            ->postJson(route('rapid.bill.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $order = OrderManage::find($response->json('order_id'));
        $payments = OrderToPayment::where('order_id', $order->id)->get();
        $this->assertCount(1, $payments);
        $this->assertEquals('UPI', $payments->first()->payment_method);
        $this->assertEquals(80, $payments->first()->amount);

        // No cash drawer transaction for UPI
        $upiPayment = $payments->first();
        $drawerTx = CashDrawerTransaction::where('reference_id', $upiPayment->id)->first();
        $this->assertNull($drawerTx);
    }

    public function test_create_rapid_bill_with_mapped_addons()
    {
        $data = $this->setupRestaurantAndUser();

        $addon1 = \App\Models\DishAddon::create([
            'restaurant_id' => $data['restaurant']->id,
            'name' => 'Extra Cheese Slice',
            'price' => 25.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $addon2 = \App\Models\DishAddon::create([
            'restaurant_id' => $data['restaurant']->id,
            'name' => 'Smoked Bacon',
            'price' => 50.00,
            'food_type' => 'NON-VEG',
            'status' => 'A'
        ]);

        $data['dish1']->addons()->sync([$addon1->id, $addon2->id]);

        $payload = [
            'customer_name' => 'Karan Malhotra',
            'customer_phone' => '9811223344',
            'order_type' => 'takeaway',
            'cash_amount' => 195,
            'upi_amount' => 0,
            'print_bill' => 1,
            'items' => [
                [
                    'dish_id' => $data['dish1']->id,
                    'quantity' => 1,
                    'price' => 120, // Base price
                    'discount_percentage' => 0,
                    'addons' => [
                        [
                            'id' => $addon1->id,
                            'name' => 'Extra Cheese Slice',
                            'price' => 25.00,
                        ],
                        [
                            'id' => $addon2->id,
                            'name' => 'Smoked Bacon',
                            'price' => 50.00,
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($data['user'])
            ->postJson(route('rapid.bill.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $order = OrderManage::find($response->json('order_id'));
        $this->assertNotNull($order);
        // Base 120 + 25 + 50 = 195 (plus GST if enabled)
        $this->assertGreaterThanOrEqual(195, $order->grand_total);

        $orderItem = OrderItems::where('order_id', $order->id)->first();
        $this->assertEquals(195, $orderItem->price);
    }

    public function test_create_rapid_bill_with_multiple_quantity_addons()
    {
        $data = $this->setupRestaurantAndUser();

        $addon1 = \App\Models\DishAddon::create([
            'restaurant_id' => $data['restaurant']->id,
            'name' => 'Extra Cheese Dip',
            'price' => 30.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $data['dish1']->addons()->sync([$addon1->id]);

        $payload = [
            'customer_name' => 'Aditya Roy',
            'customer_phone' => '9877665544',
            'order_type' => 'takeaway',
            'cash_amount' => 180,
            'upi_amount' => 0,
            'print_bill' => 1,
            'items' => [
                [
                    'dish_id' => $data['dish1']->id,
                    'quantity' => 1,
                    'price' => 120, // Base price
                    'discount_percentage' => 0,
                    'addons' => [
                        [
                            'id' => $addon1->id,
                            'name' => 'Extra Cheese Dip',
                            'price' => 30.00,
                            'qty' => 2, // 2x Cheese Dip = 60
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($data['user'])
            ->postJson(route('rapid.bill.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $order = OrderManage::find($response->json('order_id'));
        $this->assertNotNull($order);
        // Base 120 + (30 * 2) = 180 (plus GST if enabled)
        $this->assertGreaterThanOrEqual(180, $order->grand_total);

        $orderItem = OrderItems::where('order_id', $order->id)->first();
        $this->assertEquals(180, $orderItem->price);
        $this->assertNotEmpty($orderItem->addons_list);
        $this->assertEquals('Extra Cheese Dip', $orderItem->addons_list[0]['name']);
        $this->assertEquals(2, $orderItem->addons_list[0]['qty']);
    }

    public function test_order_invoice_page_renders_addons()
    {
        $data = $this->setupRestaurantAndUser();

        $addon = \App\Models\DishAddon::create([
            'restaurant_id' => $data['restaurant']->id,
            'name' => 'Garlic Mayo Dip',
            'price' => 35.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $order = OrderManage::create([
            'restaurant_id' => $data['restaurant']->id,
            'user_id' => $data['user']->id,
            'customer_name' => 'Invoice Customer',
            'customer_phone' => '9888877777',
            'order_id' => 'ORD-INV-001',
            'order_type' => 'TAKEAWAY',
            'total_amount' => 155.00,
            'taxable_amount' => 155.00,
            'gst_amount' => 0.00,
            'grand_total' => 155.00,
            'amount_paid' => 155.00,
            'payment_status' => 'PAID',
            'payment_method' => 'CASH',
            'order_status' => 'PENDING',
        ]);

        OrderItems::create([
            'order_id' => $order->id,
            'subcategory_id' => $data['dish1']->id,
            'quantity' => 1,
            'price' => 155.00,
            'addons' => [
                [
                    'id' => $addon->id,
                    'name' => 'Garlic Mayo Dip',
                    'price' => 35.00,
                    'qty' => 1,
                    'quantity' => 1,
                    'total' => 35.00,
                    'food_type' => 'VEG'
                ]
            ],
            'discounted_price' => 155.00,
            'taxable_amount' => 155.00,
            'total_amount' => 155.00,
            'restaurant_id' => $data['restaurant']->id,
            'user_id' => $data['user']->id,
        ]);

        $response = $this->actingAs($data['user'])
            ->get('/admin/order/' . $order->id . '/invoice');

        $response->assertStatus(200);
        $response->assertSee('Garlic Mayo Dip');
        $response->assertSee('Mapped Add-ons');
        $response->assertSee('35.00');
    }

    public function test_kitchen_panel_renders_addon_items()
    {
        $data = $this->setupRestaurantAndUser();

        $addon = \App\Models\DishAddon::create([
            'restaurant_id' => $data['restaurant']->id,
            'name' => 'Spicy Peri Peri Dip',
            'price' => 25.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $order = OrderManage::create([
            'restaurant_id' => $data['restaurant']->id,
            'user_id' => $data['user']->id,
            'customer_name' => 'Kitchen Customer',
            'customer_phone' => '9888877777',
            'order_id' => 'ORD-KDS-001',
            'order_type' => 'TAKEAWAY',
            'total_amount' => 145.00,
            'taxable_amount' => 145.00,
            'gst_amount' => 0.00,
            'grand_total' => 145.00,
            'amount_paid' => 145.00,
            'payment_status' => 'PAID',
            'payment_method' => 'CASH',
            'order_status' => 'PENDING',
        ]);

        $orderItem = OrderItems::create([
            'order_id' => $order->id,
            'subcategory_id' => $data['dish1']->id,
            'quantity' => 1,
            'price' => 145.00,
            'kot_no' => 'KOT-261005-001',
            'addons' => [
                [
                    'id' => $addon->id,
                    'name' => 'Spicy Peri Peri Dip',
                    'price' => 25.00,
                    'qty' => 2,
                    'quantity' => 2,
                    'total' => 50.00,
                    'food_type' => 'VEG'
                ]
            ],
            'discounted_price' => 145.00,
            'taxable_amount' => 145.00,
            'total_amount' => 145.00,
            'order_status' => 'PENDING',
            'restaurant_id' => $data['restaurant']->id,
            'user_id' => $data['user']->id,
        ]);

        $response = $this->actingAs($data['user'])
            ->get(route('manage.kitchen-panel'));

        $response->assertStatus(200);
        $response->assertSee('Spicy Peri Peri Dip');
        $response->assertSee('ADD-ON');
        $response->assertSee('x2');

        // Verify KOT PDF ticket generation works seamlessly with addons
        $kotPdfResponse = $this->actingAs($data['user'])
            ->get(route('kitchen.kot.pdf', $orderItem->id));

        $kotPdfResponse->assertStatus(200);
    }
}
