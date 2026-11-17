<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\FranchiseRestaurantMapping;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FranchiseDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fso_user_redirects_to_franchise_dashboard_upon_login()
    {
        $fso = User::create([
            'name' => 'FSO Redirect Test',
            'email' => 'fso_redirect_' . uniqid() . '@example.com',
            'phone' => '9876500001',
            'password' => bcrypt('password123'),
            'role' => 'FSO',
            'status' => 'A'
        ]);

        $this->assertEquals(route('franchise.dashboard'), $fso->getDashboardRedirectUrl());
    }

    public function test_franchise_dashboard_renders_metrics_and_hierarchy()
    {
        // 1. Create FSO user
        $fso = User::create([
            'name' => 'Sponsor Executive Owner',
            'email' => 'fso_dash_' . uniqid() . '@example.com',
            'phone' => '9876500002',
            'password' => bcrypt('password123'),
            'role' => 'FSO',
            'status' => 'A'
        ]);

        // 2. Create Parent Restaurant 1
        $parent1 = RestaurantMaster::create([
            'name' => 'Bite Kingdom HQ ' . uniqid(),
            'restaurant_id_unique' => 'REST-HQ-01',
            'parent_id' => null,
            'address' => 'HQ Central Mall',
            'status' => 'A'
        ]);

        // 3. Create Sub-Restaurant under Parent 1
        $sub1 = RestaurantMaster::create([
            'name' => 'Bite Kingdom Outlet A ' . uniqid(),
            'restaurant_id_unique' => 'REST-OUT-01',
            'parent_id' => $parent1->id,
            'address' => 'City Center Branch',
            'status' => 'A'
        ]);

        // 4. Map Parent 1 to FSO
        FranchiseRestaurantMapping::create([
            'user_id' => $fso->id,
            'restaurant_id' => $parent1->id,
        ]);

        // 5. Create category & dish
        $cat = Category::create([
            'restaurant_id' => $parent1->id,
            'name' => 'Signature Burgers',
            'category_type' => 'FOOD',
            'status' => 'A'
        ]);

        $dish = SubCategory::create([
            'restaurant_id' => $parent1->id,
            'category_id' => $cat->id,
            'name' => 'Smoky BBQ Burger ' . uniqid(),
            'price' => 250,
            'food_type' => 'NON-VEG',
            'status' => 'A'
        ]);

        // 6. Create Today's Order at Parent
        $orderParent = OrderManage::create([
            'restaurant_id' => $parent1->id,
            'user_id' => $fso->id,
            'customer_name' => 'Alice Parent',
            'order_type' => 'DINE_IN',
            'total_amount' => 500,
            'grand_total' => 500,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED',
        ]);

        OrderItems::create([
            'order_id' => $orderParent->id,
            'restaurant_id' => $parent1->id,
            'subcategory_id' => $dish->id,
            'quantity' => 2,
            'price' => 250,
            'total_amount' => 500,
            'order_status' => 'SERVED'
        ]);

        // 7. Create Today's Order at Sub-Restaurant
        $orderSub = OrderManage::create([
            'restaurant_id' => $sub1->id,
            'user_id' => $fso->id,
            'customer_name' => 'Bob Sub',
            'order_type' => 'TAKEAWAY',
            'total_amount' => 750,
            'grand_total' => 750,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED',
        ]);

        OrderItems::create([
            'order_id' => $orderSub->id,
            'restaurant_id' => $sub1->id,
            'subcategory_id' => $dish->id,
            'quantity' => 3,
            'price' => 250,
            'total_amount' => 750,
            'order_status' => 'SERVED'
        ]);

        // 8. Act as FSO and visit /franchise-owners-dashboard
        $response = $this->actingAs($fso)->get('/franchise-owners-dashboard');
        $response->assertStatus(200);

        // Assert Mapped Sponsors Count
        $response->assertSeeText('Mapped Sponsors', false);
        $response->assertSeeText('1 Parent HQs', false);

        // Assert Today's Orders (2 orders total: 1 parent + 1 sub)
        $response->assertSeeText("Today's Orders", false);
        $response->assertSeeText('5 Items Sold', false); // 2 + 3 items

        // Assert Total Invoice Value (₹1,250.00)
        $response->assertSeeText('Total Invoice Value', false);
        $response->assertSeeText('1,250.00', false);

        // Assert Top 10 Best Dishes
        $response->assertSeeText('Top 10 Best Performing Dishes', false);
        $response->assertSeeText($dish->name, false);

        // Assert Parent & Sub-Restaurants Performance Hierarchy
        $response->assertSeeText($parent1->name, false);
        $response->assertSeeText($sub1->name, false);
        $response->assertSeeText('Sub-Restaurant Branches', false);
    }
}
