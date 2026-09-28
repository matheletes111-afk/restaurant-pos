<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\TableManage;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\TempOrder;
use App\Models\TempOrderItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerQrOrderCancellationTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $table;
    protected $dish1;
    protected $dish2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->flushSession();

        $this->restaurant = RestaurantMaster::create([
            'name' => 'Cancel Test Cafe',
            'phone' => '9998887771',
            'email' => 'canceltest@example.com',
            'address' => '789 Test Road',
            'status' => 'ACTIVE',
        ]);

        $this->table = new TableManage();
        $this->table->restaurant_id = $this->restaurant->id;
        $this->table->name = '12';
        $this->table->status = 'AVAILABLE';
        $this->table->save();

        $category = new Category();
        $category->restaurant_id = $this->restaurant->id;
        $category->name = 'Starters';
        $category->status = 'active';
        $category->save();

        $this->dish1 = new SubCategory();
        $this->dish1->restaurant_id = $this->restaurant->id;
        $this->dish1->category_id = $category->id;
        $this->dish1->name = 'Paneer Tikka';
        $this->dish1->price = 200.00;
        $this->dish1->status = 'active';
        $this->dish1->save();

        $this->dish2 = new SubCategory();
        $this->dish2->restaurant_id = $this->restaurant->id;
        $this->dish2->category_id = $category->id;
        $this->dish2->name = 'Crispy Corn';
        $this->dish2->price = 150.00;
        $this->dish2->status = 'active';
        $this->dish2->save();
    }

    protected function tearDown(): void
    {
        $this->flushSession();
        parent::tearDown();
    }

    public function test_can_cancel_single_item_from_pending_temp_order()
    {
        $tempOrder = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Ayan Das',
            'customer_phone' => '9876543210',
            'order_type' => 'DINE_IN',
            'total_amount' => 350.00,
            'grand_total' => 350.00,
            'order_status' => 'PENDING',
        ]);

        $item1 = TempOrderItem::create([
            'temp_order_id' => $tempOrder->id,
            'subcategory_id' => $this->dish1->id,
            'quantity' => 1,
            'price' => 200.00,
            'total_amount' => 200.00,
            'order_status' => 'PENDING',
            'restaurant_id' => $this->restaurant->id,
        ]);

        $item2 = TempOrderItem::create([
            'temp_order_id' => $tempOrder->id,
            'subcategory_id' => $this->dish2->id,
            'quantity' => 1,
            'price' => 150.00,
            'total_amount' => 150.00,
            'order_status' => 'PENDING',
            'restaurant_id' => $this->restaurant->id,
        ]);

        // Customer session viewing order-success
        $session = [
            'customer_qr_order_id' => $tempOrder->id,
            'customer_qr_allowed_orders' => [$tempOrder->id],
            'customer_phone' => '9876543210',
        ];

        // Cancel item 1
        $response = $this->withSession($session)
            ->postJson("/order-customer/item/delete/{$item1->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'order_cancelled' => false,
        ]);

        $this->assertDatabaseMissing('temp_order_items', ['id' => $item1->id]);
        $this->assertDatabaseHas('temp_order_items', ['id' => $item2->id]);

        $tempOrder->refresh();
        $this->assertEquals(150.00, floatval($tempOrder->total_amount));
    }

    public function test_cancelling_last_item_cancels_entire_pending_order()
    {
        $tempOrder = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Ayan Das',
            'customer_phone' => '9876543210',
            'order_type' => 'DINE_IN',
            'total_amount' => 200.00,
            'grand_total' => 200.00,
            'order_status' => 'PENDING',
        ]);

        $item1 = TempOrderItem::create([
            'temp_order_id' => $tempOrder->id,
            'subcategory_id' => $this->dish1->id,
            'quantity' => 1,
            'price' => 200.00,
            'total_amount' => 200.00,
            'order_status' => 'PENDING',
            'restaurant_id' => $this->restaurant->id,
        ]);

        $session = [
            'customer_qr_order_id' => $tempOrder->id,
            'customer_qr_allowed_orders' => [$tempOrder->id],
            'customer_phone' => '9876543210',
        ];

        // Delete the only item
        $response = $this->withSession($session)
            ->postJson("/order-customer/item/delete/{$item1->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'order_cancelled' => true,
        ]);

        $tempOrder->refresh();
        $this->assertEquals('REJECTED', $tempOrder->order_status);
        $this->assertEquals(0, floatval($tempOrder->total_amount));
    }

    public function test_can_cancel_entire_pending_order_via_endpoint()
    {
        $tempOrder = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Ayan Das',
            'customer_phone' => '9876543210',
            'order_type' => 'DINE_IN',
            'total_amount' => 200.00,
            'grand_total' => 200.00,
            'order_status' => 'PENDING',
        ]);

        $item1 = TempOrderItem::create([
            'temp_order_id' => $tempOrder->id,
            'subcategory_id' => $this->dish1->id,
            'quantity' => 1,
            'price' => 200.00,
            'total_amount' => 200.00,
            'order_status' => 'PENDING',
            'restaurant_id' => $this->restaurant->id,
        ]);

        $session = [
            'customer_qr_order_id' => $tempOrder->id,
            'customer_qr_allowed_orders' => [$tempOrder->id],
            'customer_phone' => '9876543210',
        ];

        $response = $this->withSession($session)
            ->postJson("/order-customer/cancel/{$tempOrder->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $tempOrder->refresh();
        $this->assertEquals('REJECTED', $tempOrder->order_status);
    }
}
