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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

class KotNumberProgressionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_kot_number_progression_on_create_and_edit_orders()
    {
        // Find or create test restaurant
        $restaurant = RestaurantMaster::first();
        if (!$restaurant) {
            $restaurant = RestaurantMaster::create([
                'name' => 'Test Restaurant',
                'email' => 'test_rest@example.com',
                'phone' => '1234567890',
                'address' => 'Test Address',
                'prefix' => 'REST',
            ]);
        }

        // Find or create user
        $user = User::where('restaurant_id', $restaurant->id)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test Staff',
                'email' => 'test_staff_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'restaurant_id' => $restaurant->id,
            ]);
        }

        // Create category and subcategories (items)
        $category = Category::where('restaurant_id', $restaurant->id)->first();
        if (!$category) {
            $category = new Category();
            $category->restaurant_id = $restaurant->id;
            $category->name = 'Main Course';
            $category->status = 'ACTIVE';
            $category->save();
        }

        $items = [];
        for ($i = 1; $i <= 6; $i++) {
            $item = SubCategory::where('restaurant_id', $restaurant->id)->where('name', "Dish Item $i")->first();
            if (!$item) {
                $item = new SubCategory();
                $item->restaurant_id = $restaurant->id;
                $item->category_id = $category->id;
                $item->name = "Dish Item $i";
                $item->price = 100 * $i;
                $item->food_type = 'Veg';
                $item->status = 'ACTIVE';
                $item->gst_rate = 5;
                $item->save();
            }
            $items[$i] = $item;
        }

        $this->actingAs($user);

        // 1. Initial Order: 3 items (Item 1, 2, 3)
        $response1 = $this->post(route('order.save'), [
            'customer_name' => 'John Doe',
            'customer_phone' => '9876543210',
            'order_items' => [
                ['id' => $items[1]->id, 'price' => 100, 'qty' => 1, 'item_discount' => 0],
                ['id' => $items[2]->id, 'price' => 200, 'qty' => 1, 'item_discount' => 0],
                ['id' => $items[3]->id, 'price' => 300, 'qty' => 1, 'item_discount' => 0],
            ],
            'discount' => 0,
        ]);

        $response1->assertStatus(200);
        $orderId = $response1->json('order_id');
        $this->assertNotNull($orderId);

        $order = OrderManage::with('orderItems')->findOrFail($orderId);
        $this->assertCount(3, $order->orderItems);

        $firstKot = $order->orderItems[0]->kot_no;
        $this->assertNotNull($firstKot);

        // Verify that all 3 initial items share the EXACT same KOT number
        foreach ($order->orderItems as $item) {
            $this->assertEquals($firstKot, $item->kot_no, 'All initial items must share the same KOT number');
        }

        // 2. Customer adds 2 more items from edit: (Item 4, 5)
        $response2 = $this->postJson(route('order.update', $orderId), [
            'customer_phone' => '9876543210',
            'order_items' => [
                ['id' => $items[4]->id, 'name' => $items[4]->name, 'price' => 400, 'qty' => 1, 'item_discount' => 0],
                ['id' => $items[5]->id, 'name' => $items[5]->name, 'price' => 500, 'qty' => 1, 'item_discount' => 0],
            ],
        ]);

        $response2->assertStatus(200);

        $order->refresh();
        $this->assertCount(5, $order->orderItems);

        // The new items added in this edit
        $newItemsBatch1 = $order->orderItems->whereIn('subcategory_id', [$items[4]->id, $items[5]->id])->values();
        $this->assertCount(2, $newItemsBatch1);

        $secondKot = $newItemsBatch1[0]->kot_no;
        $this->assertNotNull($secondKot);
        $this->assertNotEquals($firstKot, $secondKot, 'Second batch must have a new progressive KOT number');

        // Verify both added items share the exact same second KOT number
        $this->assertEquals($secondKot, $newItemsBatch1[0]->kot_no);
        $this->assertEquals($secondKot, $newItemsBatch1[1]->kot_no);

        // 3. Customer adds 1 more item later from edit: (Item 6)
        $response3 = $this->postJson(route('order.update', $orderId), [
            'customer_phone' => '9876543210',
            'order_items' => [
                ['id' => $items[6]->id, 'name' => $items[6]->name, 'price' => 600, 'qty' => 1, 'item_discount' => 0],
            ],
        ]);

        $response3->assertStatus(200);

        $order->refresh();
        $this->assertCount(6, $order->orderItems);

        $thirdKotItem = $order->orderItems->firstWhere('subcategory_id', $items[6]->id);
        $this->assertNotNull($thirdKotItem);
        $thirdKot = $thirdKotItem->kot_no;

        $this->assertNotEquals($firstKot, $thirdKot);
        $this->assertNotEquals($secondKot, $thirdKot);

        // Verify sequential numbering
        preg_match('/-(\d+)$/', $firstKot, $m1);
        preg_match('/-(\d+)$/', $secondKot, $m2);
        preg_match('/-(\d+)$/', $thirdKot, $m3);

        $seq1 = intval($m1[1]);
        $seq2 = intval($m2[1]);
        $seq3 = intval($m3[1]);

        $this->assertEquals($seq1 + 1, $seq2, 'KOT sequence should increment by 1 for the 2nd batch');
        $this->assertEquals($seq2 + 1, $seq3, 'KOT sequence should increment by 1 for the 3rd batch');

        // 4. Test KOT PDF generation for an item - it should load without error
        $itemToPrint = $order->orderItems->first();
        $pdfResponse = $this->get(route('kitchen.kot.pdf', $itemToPrint->id));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));
    }
}
