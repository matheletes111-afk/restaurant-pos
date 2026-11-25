<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThermalPrintSetupTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::create([
            'name' => 'Print Test Bistro',
            'phone' => '9876543210',
            'address' => '123 Market Street',
            'pincode' => '700001',
            'thermal_print_size' => '80mm',
            'status' => 1
        ]);

        $this->user = User::create([
            'name' => 'Owner Test',
            'email' => 'printowner_' . uniqid() . '@example.com',
            'phone' => '9876543210',
            'password' => bcrypt('password123'),
            'restaurant_id' => $this->restaurant->id,
            'role_id' => 1
        ]);

        $this->restaurant->owner_id = $this->user->id;
        $this->restaurant->save();
    }

    public function test_restaurant_profile_displays_thermal_print_setup_options()
    {
        $response = $this->actingAs($this->user)
            ->get(route('restaurant.profile.index'));

        $response->assertStatus(200);
        $response->assertSee('Thermal Print Setup');
        $response->assertSee('80mm');
        $response->assertSee('58mm');
    }

    public function test_can_switch_thermal_print_size_to_58mm_and_back()
    {
        // 1. Switch to 58mm
        $updateResp = $this->actingAs($this->user)
            ->post(route('restaurant.profile.update'), [
                'name' => 'Owner Test',
                'restaurant_name' => 'Print Test Bistro 58mm',
                'phone' => '9876543210',
                'address' => '123 Market Street',
                'pincode' => '700001',
                'thermal_print_size' => '58mm',
            ]);

        $updateResp->assertRedirect();
        $this->restaurant->refresh();
        $this->assertEquals('58mm', $this->restaurant->thermal_print_size);

        // 2. Switch back to 80mm
        $updateResp2 = $this->actingAs($this->user)
            ->post(route('restaurant.profile.update'), [
                'name' => 'Owner Test',
                'restaurant_name' => 'Print Test Bistro 80mm',
                'phone' => '9876543210',
                'address' => '123 Market Street',
                'pincode' => '700001',
                'thermal_print_size' => '80mm',
            ]);

        $updateResp2->assertRedirect();
        $this->restaurant->refresh();
        $this->assertEquals('80mm', $this->restaurant->thermal_print_size);
    }

    public function test_receipt_and_kot_render_with_58mm_size()
    {
        $this->restaurant->update(['thermal_print_size' => '58mm']);

        $category = Category::create([
            'name' => 'Main Course',
            'restaurant_id' => $this->restaurant->id
        ]);

        $dish = SubCategory::create([
            'category_id' => $category->id,
            'name' => 'Butter Chicken',
            'price' => 250,
            'restaurant_id' => $this->restaurant->id
        ]);

        $order = OrderManage::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->user->id,
            'order_id' => 'ORD-PRNT-' . rand(100, 999),
            'subtotal' => 250,
            'grand_total' => 250,
            'order_status' => 'PENDING'
        ]);

        $orderItem = OrderItems::create([
            'order_id' => $order->id,
            'restaurant_id' => $this->restaurant->id,
            'subcategory_id' => $dish->id,
            'name' => 'Butter Chicken',
            'price' => 250,
            'quantity' => 1,
            'kot_no' => 'KOT-101',
            'order_status' => 'PENDING',
            'user_id' => $this->user->id
        ]);

        // Test Receipt PDF stream
        $receiptResp = $this->actingAs($this->user)
            ->get(route('order.receipt.pdf', $order->id));
        $receiptResp->assertStatus(200);

        // Test KOT PDF stream
        $kotResp = $this->actingAs($this->user)
            ->get(route('kitchen.kot.pdf', $orderItem->id));
        $kotResp->assertStatus(200);
    }
}
