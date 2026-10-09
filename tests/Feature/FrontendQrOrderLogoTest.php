<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\RestaurantMaster;
use App\Models\SubCategory;
use App\Models\TableManage;
use App\Models\TempOrder;
use App\Models\TempOrderItem;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FrontendQrOrderLogoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $storageDir = storage_path('app/public/restaurant');
        if (!File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }
    }

    protected function getOrCreateRestaurant(): RestaurantMaster
    {
        $restaurant = RestaurantMaster::first();
        if (!$restaurant) {
            $restaurant = RestaurantMaster::create([
                'name' => 'Gourmet Bistro Test',
                'address' => '456 Flavors Way',
                'pincode' => '400001',
                'gstin' => '27ABCDE1234F1Z5',
                'gst_percentage' => 5,
                'status' => 'A',
            ]);
        }
        return $restaurant;
    }

    protected function getOrCreateTable(int $restaurantId): TableManage
    {
        $table = TableManage::where('restaurant_id', $restaurantId)->first();
        if (!$table) {
            $table = TableManage::create([
                'name' => 'Table #7',
                'restaurant_id' => $restaurantId,
                'status' => 'A',
            ]);
        }
        return $table;
    }

    public function test_temp_order_page_renders_with_restaurant_logo_when_logo_exists()
    {
        $restaurant = $this->getOrCreateRestaurant();
        $table = $this->getOrCreateTable($restaurant->id);

        // Create sample category & dish if none exists
        $category = Category::where('restaurant_id', $restaurant->id)->first();
        if (!$category) {
            $category = new Category();
            $category->restaurant_id = $restaurant->id;
            $category->name = 'Starters';
            $category->description = 'Delicious starters';
            $category->save();
        }

        $subCategory = SubCategory::where('category_id', $category->id)->first();
        if (!$subCategory) {
            $subCategory = new SubCategory();
            $subCategory->category_id = $category->id;
            $subCategory->name = 'Crispy Corn';
            $subCategory->price = 180;
            $subCategory->food_type = 'veg';
            $subCategory->restaurant_id = $restaurant->id;
            $subCategory->save();
        }

        // Create a dummy logo file
        $logoFilename = 'logo_test_' . time() . '.png';
        $logoPath = storage_path('app/public/restaurant/' . $logoFilename);
        File::put($logoPath, 'fake_png_data');

        $restaurant->logo = $logoFilename;
        $restaurant->save();

        $response = $this->get(route('temp.order.create', [
            'table_id' => $table->id,
            'restaurant_id' => $restaurant->id
        ]));

        $response->assertStatus(200);
        $response->assertSee('restaurant-logo-card', false);
        $response->assertSee('storage/restaurant/' . $logoFilename, false);
        $response->assertSee(htmlspecialchars($restaurant->name, ENT_QUOTES, 'UTF-8'), false);
        $response->assertSee($table->name);

        // Cleanup dummy file
        if (File::exists($logoPath)) {
            File::delete($logoPath);
        }
    }

    public function test_temp_order_page_renders_fallback_when_no_logo()
    {
        $restaurant = $this->getOrCreateRestaurant();
        $table = $this->getOrCreateTable($restaurant->id);

        $restaurant->logo = null;
        $restaurant->save();

        $response = $this->get(route('temp.order.create', [
            'table_id' => $table->id,
            'restaurant_id' => $restaurant->id
        ]));

        $response->assertStatus(200);
        $response->assertSee('header-icon-ring', false);
        $response->assertSee(htmlspecialchars($restaurant->name, ENT_QUOTES, 'UTF-8'), false);
    }

    public function test_order_success_page_renders_with_restaurant_logo_and_details()
    {
        $restaurant = $this->getOrCreateRestaurant();
        $table = $this->getOrCreateTable($restaurant->id);

        // Create dummy logo
        $logoFilename = 'logo_success_test_' . time() . '.png';
        $logoPath = storage_path('app/public/restaurant/' . $logoFilename);
        File::put($logoPath, 'fake_png_data');

        $restaurant->logo = $logoFilename;
        $restaurant->save();

        $tempOrder = TempOrder::create([
            'table_id' => $table->id,
            'order_id' => 'ORD-TEST-001',
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '9876543210',
            'order_type' => 'DINE_IN',
            'total_amount' => 360,
            'taxable_amount' => 360,
            'gst_amount' => 18,
            'grand_total' => 378,
            'order_status' => 'PENDING',
            'payment_status' => 'PENDING',
        ]);

        $response = $this->get(route('order.success', $tempOrder->id));

        $response->assertStatus(200);
        $response->assertSee('storage/restaurant/' . $logoFilename, false);
        $response->assertSee($restaurant->name);
        $response->assertSee('ORD-TEST-001');
        $response->assertSee('John Doe');

        // Cleanup
        if (File::exists($logoPath)) {
            File::delete($logoPath);
        }
    }
}
