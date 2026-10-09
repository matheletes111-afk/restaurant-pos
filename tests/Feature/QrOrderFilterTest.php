<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\RestaurantMaster;
use App\Models\SubCategory;
use App\Models\TableManage;
use Tests\TestCase;

class QrOrderFilterTest extends TestCase
{
    protected function getOrCreateRestaurant(): RestaurantMaster
    {
        $restaurant = RestaurantMaster::first();
        if (!$restaurant) {
            $restaurant = new RestaurantMaster();
            $restaurant->name = 'Gourmet Bistro Test';
            $restaurant->address = '456 Flavors Way';
            $restaurant->pincode = '400001';
            $restaurant->gstin = '27ABCDE1234F1Z5';
            $restaurant->gst_percentage = 5;
            $restaurant->status = 'A';
            $restaurant->save();
        }
        return $restaurant;
    }

    protected function getOrCreateTable(int $restaurantId): TableManage
    {
        $table = TableManage::where('restaurant_id', $restaurantId)->first();
        if (!$table) {
            $table = new TableManage();
            $table->name = 'Table #1';
            $table->restaurant_id = $restaurantId;
            $table->status = 'A';
            $table->save();
        }
        return $table;
    }

    public function test_customer_order_page_renders_filters_and_deselect_elements()
    {
        $restaurant = $this->getOrCreateRestaurant();
        $table = $this->getOrCreateTable($restaurant->id);

        $category = Category::where('restaurant_id', $restaurant->id)->first();
        if (!$category) {
            $category = new Category();
            $category->restaurant_id = $restaurant->id;
            $category->name = 'Main Course';
            $category->description = 'Main courses';
            $category->save();
        }

        $vegItem = SubCategory::where('category_id', $category->id)->where('food_type', 'VEG')->first();
        if (!$vegItem) {
            $vegItem = new SubCategory();
            $vegItem->category_id = $category->id;
            $vegItem->name = 'Paneer Butter Masala';
            $vegItem->restaurant_id = $restaurant->id;
            $vegItem->price = 250;
            $vegItem->food_type = 'VEG';
            $vegItem->description = 'Rich cottage cheese in tomato gravy';
            $vegItem->save();
        }

        $nonVegItem = SubCategory::where('category_id', $category->id)->where('food_type', 'NON-VEG')->first();
        if (!$nonVegItem) {
            $nonVegItem = new SubCategory();
            $nonVegItem->category_id = $category->id;
            $nonVegItem->name = 'Chicken Tikka Masala';
            $nonVegItem->restaurant_id = $restaurant->id;
            $nonVegItem->price = 320;
            $nonVegItem->food_type = 'NON-VEG';
            $nonVegItem->description = 'Smoky tandoori chicken cooked in masala gravy';
            $nonVegItem->save();
        }

        $response = $this->get(route('temp.order.create', [
            'table_id' => $table->id,
            'restaurant_id' => $restaurant->id,
        ]));

        $response->assertStatus(200);

        // Assert dietary filter group and buttons exist
        $response->assertSee('dietary-filters-group', false);
        $response->assertSee('data-type=""', false);
        $response->assertSee('data-type="veg"', false);
        $response->assertSee('data-type="non-veg"', false);

        // Assert category tab navigation exists with single tab selection attributes
        $response->assertSee('category-nav-bar', false);
        $response->assertSee('cat-pill-tab', false);
        $response->assertSee('data-category="all"', false);
        $response->assertSee('data-category="' . $category->id . '"', false);

        // Ensure no confusing multi-select close/deselect icons exist
        $response->assertDontSee('cat-close-icon', false);

        // Assert dishes have normalized data-type and data-desc
        $response->assertSee('data-type="veg"', false);
        $response->assertSee('data-desc=', false);

        // Assert search and clear button exist
        $response->assertSee('id="searchBox"', false);
        $response->assertSee('id="clearSearchBtn"', false);
    }
}
