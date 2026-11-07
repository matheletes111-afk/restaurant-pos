<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\OrderItems;
use App\Models\OrderManage;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\BarItemVariation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BarKotStockDeductionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_bar_kot_item_deducts_ml_stock_when_served()
    {
        // 1. Create a Bar Category & SubCategory
        $category = Category::create([
            'restaurant_id' => 999,
            'name' => 'Whiskey Bar',
            'category_type' => 'BAR',
            'status' => 'A'
        ]);

        $barSubCat = SubCategory::create([
            'restaurant_id' => 999,
            'category_id' => $category->id,
            'name' => 'Glenfiddich 12',
            'food_type' => 'BAR',
            'bottle_size_ml' => 750,
            'current_stock_ml' => 1500, // 2 bottles
            'status' => 'A',
            'price' => 500
        ]);

        // 2. Create Variation: 60ml Peg
        $variation = BarItemVariation::create([
            'restaurant_id' => 999,
            'sub_category_id' => $barSubCat->id,
            'name' => 'Large (60ml)',
            'ml' => 60,
            'price' => 450,
            'status' => 'A'
        ]);

        // Create Order
        $order = OrderManage::create([
            'restaurant_id' => 999,
            'order_status' => 'PENDING',
            'payment_status' => 'PENDING',
            'order_type' => 'DINE_IN',
            'grand_total' => 900
        ]);

        // 3. Create OrderItem (2 x 60ml = 120ml)
        $orderItem = OrderItems::create([
            'restaurant_id' => 999,
            'order_id' => $order->id,
            'subcategory_id' => $barSubCat->id,
            'bar_variation_id' => $variation->id,
            'bar_variation_name' => $variation->name,
            'bar_variation_ml' => 60,
            'quantity' => 2,
            'order_status' => 'PENDING',
            'vat_rate' => 10.00,
            'total_amount' => 900
        ]);

        $this->assertEquals(0, $orderItem->is_bar_stock_deducted);

        // 4. Trigger deduction directly
        $result = $orderItem->deductBarStock();
        $this->assertTrue($result);

        // 5. Verify SubCategory current_stock_ml reduced by 120ml (1500 - 120 = 1380ml)
        $barSubCat->refresh();
        $this->assertEquals(1380, $barSubCat->current_stock_ml);

        // 6. Verify order item is marked deducted
        $orderItem->refresh();
        $this->assertEquals(1, $orderItem->is_bar_stock_deducted);

        // 7. Verify calling deductBarStock again returns false and doesn't deduct twice
        $secondResult = $orderItem->deductBarStock();
        $this->assertFalse($secondResult);
        $barSubCat->refresh();
        $this->assertEquals(1380, $barSubCat->current_stock_ml);
    }

    public function test_bar_kot_controller_update_status_serves_and_deducts_stock()
    {
        $category = Category::create([
            'restaurant_id' => 999,
            'name' => 'Vodka Bar',
            'category_type' => 'BAR',
            'status' => 'A'
        ]);

        $barSubCat = SubCategory::create([
            'restaurant_id' => 999,
            'category_id' => $category->id,
            'name' => 'Absolut Vodka',
            'food_type' => 'BAR',
            'bottle_size_ml' => 1000,
            'current_stock_ml' => 2000,
            'status' => 'A',
            'price' => 300
        ]);

        $variation = BarItemVariation::create([
            'restaurant_id' => 999,
            'sub_category_id' => $barSubCat->id,
            'name' => '30ml Shot',
            'ml' => 30,
            'price' => 200,
            'status' => 'A'
        ]);

        $order = OrderManage::create([
            'restaurant_id' => 999,
            'order_status' => 'PENDING',
            'payment_status' => 'PENDING',
            'order_type' => 'DINE_IN',
            'grand_total' => 600
        ]);

        $orderItem = OrderItems::create([
            'restaurant_id' => 999,
            'order_id' => $order->id,
            'kot_no' => 'KOT-BAR-001',
            'subcategory_id' => $barSubCat->id,
            'bar_variation_id' => $variation->id,
            'bar_variation_name' => $variation->name,
            'bar_variation_ml' => 30,
            'quantity' => 3, // 3 x 30 = 90ml
            'order_status' => 'PENDING',
            'vat_rate' => 10.00,
            'total_amount' => 600
        ]);

        // Mock authenticated user for restaurant 999
        $user = new User();
        $user->id = 999;
        $user->restaurant_id = 999;
        $this->actingAs($user);

        // Call BarKotController update-status
        $response = $this->postJson(route('bar.kot.status.update'), [
            'order_id' => $order->id,
            'kot_no' => 'KOT-BAR-001',
            'order_status' => 'SERVED'
        ]);

        $response->assertStatus(200);

        // Verify stock deducted by 90ml: 2000 - 90 = 1910ml
        $barSubCat->refresh();
        $this->assertEquals(1910, $barSubCat->current_stock_ml);

        // Verify order item status is SERVED and marked deducted
        $orderItem->refresh();
        $this->assertEquals('SERVED', $orderItem->order_status);
        $this->assertEquals(1, $orderItem->is_bar_stock_deducted);
    }
}
