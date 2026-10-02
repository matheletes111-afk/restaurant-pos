<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\TableManage;
use App\Models\TempOrder;
use App\Models\TempOrderItem;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

class CustomerQrOrderingWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_full_qr_ordering_workflow_with_progressive_kot_and_status_restrictions()
    {
        // 1. Setup test restaurant, user, category, table, items
        $restaurant = RestaurantMaster::first();
        if (!$restaurant) {
            $restaurant = RestaurantMaster::create([
                'name' => 'QR Test Bistro',
                'email' => 'qr_test@example.com',
                'phone' => '9876543210',
                'address' => 'Test Street',
                'prefix' => 'QBISTRO',
                'gstin' => '27AAAAA0000A1Z5',
                'gst_percentage' => 5,
            ]);
        }

        $plan = Plan::first() ?? Plan::create(['name' => 'Diamond', 'price' => 2999, 'duration_days' => 365, 'billing_cycle' => 'monthly', 'inventory_checkbox' => 'Y', 'plan_status' => 'A', 'is_delete' => 'N']);
        Subscription::firstOrCreate(
            ['user_id' => $restaurant->id, 'status' => 'active'],
            ['plan_id' => $plan->id, 'start_date' => now()->subDay(), 'end_date' => now()->addYear()]
        );

        $adminUser = User::where('restaurant_id', $restaurant->id)->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Restro Admin',
                'email' => 'admin_qr_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'restaurant_id' => $restaurant->id,
            ]);
        }

        $table = TableManage::where('restaurant_id', $restaurant->id)->first();
        if (!$table) {
            $table = TableManage::create([
                'name' => 'T10',
                'description' => 'Window Table 10',
                'restaurant_id' => $restaurant->id,
                'table_status' => 'AVAILABLE',
                'status' => 'A'
            ]);
        }

        $category = Category::where('restaurant_id', $restaurant->id)->first();
        if (!$category) {
            $category = Category::create([
                'restaurant_id' => $restaurant->id,
                'name' => 'Main Dishes',
                'status' => 'ACTIVE'
            ]);
        }

        $dish1 = new SubCategory();
        $dish1->restaurant_id = $restaurant->id;
        $dish1->category_id = $category->id;
        $dish1->name = 'Butter Paneer ' . uniqid();
        $dish1->price = 250;
        $dish1->food_type = 'Veg';
        $dish1->status = 'ACTIVE';
        $dish1->gst_rate = 5;
        $dish1->save();

        $dish2 = new SubCategory();
        $dish2->restaurant_id = $restaurant->id;
        $dish2->category_id = $category->id;
        $dish2->name = 'Garlic Naan ' . uniqid();
        $dish2->price = 50;
        $dish2->food_type = 'Veg';
        $dish2->status = 'ACTIVE';
        $dish2->gst_rate = 5;
        $dish2->save();

        $dish3 = new SubCategory();
        $dish3->restaurant_id = $restaurant->id;
        $dish3->category_id = $category->id;
        $dish3->name = 'Gulab Jamun ' . uniqid();
        $dish3->price = 100;
        $dish3->food_type = 'Veg';
        $dish3->status = 'ACTIVE';
        $dish3->gst_rate = 5;
        $dish3->save();

        $dish4 = new SubCategory();
        $dish4->restaurant_id = $restaurant->id;
        $dish4->category_id = $category->id;
        $dish4->name = 'Mango Lassi ' . uniqid();
        $dish4->price = 80;
        $dish4->food_type = 'Veg';
        $dish4->status = 'ACTIVE';
        $dish4->gst_rate = 5;
        $dish4->save();

        // 2. Initial Customer Order via QR code (Table scan)
        $storeRes = $this->postJson(route('temp.order.store'), [
            'customer_name' => 'Alice Wonder',
            'customer_phone' => '9988776655',
            'table_id' => $table->id,
            'restaurant_id' => $restaurant->id,
            'order_items' => [
                ['id' => $dish1->id, 'name' => $dish1->name, 'price' => 250, 'qty' => 1, 'item_discount' => 0],
                ['id' => $dish2->id, 'name' => $dish2->name, 'price' => 50, 'qty' => 2, 'item_discount' => 0],
            ]
        ]);

        $storeRes->assertStatus(200);
        $this->assertTrue($storeRes->json('status'));
        $tempOrder = TempOrder::where('customer_phone', '9988776655')->latest('id')->first();
        $this->assertNotNull($tempOrder);
        $this->assertEquals('PENDING', $tempOrder->order_status);

        // 3. Admin accepts / approves the order from pending QR orders
        $this->actingAs($adminUser);
        $approveRes = $this->get(route('admin.temporder.approve', $tempOrder->id));
        $approveRes->assertRedirect(route('temp.orders'));

        $tempOrder->refresh();
        $this->assertEquals('APPROVED', $tempOrder->order_status);
        $this->assertNotNull($tempOrder->order_id);

        // Check main active order
        $activeOrder = OrderManage::with('orderItems')->find($tempOrder->order_id);
        $this->assertNotNull($activeOrder);
        $this->assertEquals($table->id, $activeOrder->table_id);
        $this->assertCount(2, $activeOrder->orderItems);

        // Verify initial KOT number: all initial items share the same KOT number
        $firstKot = $activeOrder->orderItems[0]->kot_no;
        $this->assertNotNull($firstKot);
        $this->assertEquals($firstKot, $activeOrder->orderItems[1]->kot_no);

        // Verify table status is OCCUPIED
        $table->refresh();
        $this->assertEquals('OCCUPIED', $table->table_status);
        $this->assertEquals($activeOrder->id, $table->order_id);

        // 4. Customer adds more items later from table to this active order (Save & Order)
        $addRes = $this->postJson(route('temp.order.add_items'), [
            'order_id' => $activeOrder->id,
            'table_id' => $table->id,
            'restaurant_id' => $restaurant->id,
            'order_items' => [
                ['id' => $dish3->id, 'name' => $dish3->name, 'price' => 100, 'qty' => 2, 'item_discount' => 0],
            ]
        ]);

        $addRes->assertStatus(200);
        $this->assertTrue($addRes->json('status'));
        $secondKot = $addRes->json('kot_no');
        $this->assertNotNull($secondKot);
        $this->assertNotEquals($firstKot, $secondKot, 'New items batch must get a progressive KOT number');

        $activeOrder->refresh();
        $this->assertCount(3, $activeOrder->orderItems);
        $addedItem = $activeOrder->orderItems->firstWhere('subcategory_id', $dish3->id);
        $this->assertEquals($secondKot, $addedItem->kot_no);
        $this->assertEquals('PENDING', $addedItem->order_status);

        // Verify KOT numbers increment sequentially
        preg_match('/-(\d+)$/', $firstKot, $m1);
        preg_match('/-(\d+)$/', $secondKot, $m2);
        $this->assertEquals(intval($m1[1]) + 1, intval($m2[1]));

        // 5. Check order status API check: items statuses returned
        $statusCheckRes = $this->getJson(route('order.status.check', $activeOrder->id));
        $statusCheckRes->assertStatus(200);
        $this->assertTrue($statusCheckRes->json('status'));
        $itemsData = $statusCheckRes->json('items');
        $this->assertCount(3, $itemsData);

        // 6. Test deleting a PENDING item by customer: allowed
        $pendingItemId = $addedItem->id;
        $deleteRes = $this->postJson(route('temp.order.delete_item', $pendingItemId));
        $deleteRes->assertStatus(200);
        $this->assertTrue($deleteRes->json('status'));

        $activeOrder->refresh();
        $this->assertCount(2, $activeOrder->orderItems);

        // 7. Kitchen updates one item to COOKING, and another to DONE
        $itemToCook = $activeOrder->orderItems[0];
        $itemToCook->order_status = 'COOKING';
        $itemToCook->save();

        $itemToDone = $activeOrder->orderItems[1];
        $itemToDone->order_status = 'DONE';
        $itemToDone->save();

        // 8. Customer attempts to delete a COOKING item -> must be REJECTED!
        $deleteCookingRes = $this->postJson(route('temp.order.delete_item', $itemToCook->id));
        $deleteCookingRes->assertStatus(400);
        $this->assertFalse($deleteCookingRes->json('status'));
        $this->assertStringContainsString('cannot be deleted', strtolower($deleteCookingRes->json('message')));

        // 9. Customer attempts to delete a DONE item -> must be REJECTED!
        $deleteDoneRes = $this->postJson(route('temp.order.delete_item', $itemToDone->id));
        $deleteDoneRes->assertStatus(400);
        $this->assertFalse($deleteDoneRes->json('status'));
        $this->assertStringContainsString('cannot be deleted', strtolower($deleteDoneRes->json('message')));

        // 10. Customer attempts to increment/decrement quantity of COOKING item -> must be REJECTED!
        $updateCookingRes = $this->postJson(route('temp.order.update_item_qty', $itemToCook->id), ['qty' => 5]);
        $updateCookingRes->assertStatus(400);
        $this->assertFalse($updateCookingRes->json('status'));
        $this->assertStringContainsString('cannot be modified', strtolower($updateCookingRes->json('message')));

        // 11. Customer attempts to increment/decrement quantity of DONE item -> must be REJECTED!
        $updateDoneRes = $this->postJson(route('temp.order.update_item_qty', $itemToDone->id), ['qty' => 5]);
        $updateDoneRes->assertStatus(400);
        $this->assertFalse($updateDoneRes->json('status'));
        $this->assertStringContainsString('cannot be modified', strtolower($updateDoneRes->json('message')));

        // 12. Customer adds another lot of dishes later (Batch 3)
        $addRes3 = $this->postJson(route('temp.order.add_items'), [
            'order_id' => $activeOrder->id,
            'table_id' => $table->id,
            'restaurant_id' => $restaurant->id,
            'order_items' => [
                ['id' => $dish4->id, 'name' => $dish4->name, 'price' => 80, 'qty' => 1, 'item_discount' => 0],
            ]
        ]);

        $addRes3->assertStatus(200);
        $thirdKot = $addRes3->json('kot_no');
        $this->assertNotNull($thirdKot);
        preg_match('/-(\d+)$/', $thirdKot, $m3);
        $this->assertEquals(intval($m2[1]) + 1, intval($m3[1]), '3rd batch must increment sequence by 1');

        $activeOrder->refresh();
        $this->assertCount(3, $activeOrder->orderItems);
    }
}
