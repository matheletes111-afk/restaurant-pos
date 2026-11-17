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
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FranchisePerformanceTest extends TestCase
{
    use DatabaseTransactions;

    protected function createFsoUser()
    {
        return User::create([
            'name' => 'FSO Performance Tester',
            'email' => 'fso_perf_' . uniqid() . '@example.com',
            'phone' => '98765' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'FSO',
            'status' => 'A'
        ]);
    }

    public function test_sidebar_displays_performance_menu_for_fso_user()
    {
        $fso = $this->createFsoUser();

        $response = $this->actingAs($fso)->get('/franchise-owners-dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Performance');
        $response->assertSee(route('franchise.performance.index'));
    }

    public function test_performance_index_displays_mapped_parents_and_correct_counts_and_links()
    {
        $fso = $this->createFsoUser();

        // 1. Parent 1: HAS 0 ASSOCIATED OUTLETS
        $standaloneParent = RestaurantMaster::create([
            'name' => 'Standalone Grill ' . uniqid(),
            'restaurant_id_unique' => 'REST-SOLO-' . rand(100, 999),
            'parent_id' => null,
            'address' => '77 Beach Road, City Center',
            'pincode' => '560001',
            'status' => 'A'
        ]);

        FranchiseRestaurantMapping::create([
            'user_id' => $fso->id,
            'restaurant_id' => $standaloneParent->id,
        ]);

        // 2. Parent 2: HAS ASSOCIATED OUTLETS (1 branch)
        $chainParent = RestaurantMaster::create([
            'name' => 'Royal Curry Hub ' . uniqid(),
            'restaurant_id_unique' => 'REST-CHAIN-' . rand(100, 999),
            'parent_id' => null,
            'address' => '101 Metro Tower, North Block',
            'pincode' => '560002',
            'status' => 'A'
        ]);

        $outlet1 = RestaurantMaster::create([
            'name' => 'Royal Curry Branch A ' . uniqid(),
            'restaurant_id_unique' => 'REST-OUT-A-' . rand(100, 999),
            'parent_id' => $chainParent->id,
            'address' => 'Branch Street 5',
            'pincode' => '560003',
            'status' => 'A'
        ]);

        FranchiseRestaurantMapping::create([
            'user_id' => $fso->id,
            'restaurant_id' => $chainParent->id,
        ]);

        // 3. Visit Performance Index
        $response = $this->actingAs($fso)->get('/franchise/performance');
        $response->assertStatus(200);

        // Check Standalone Parent
        $response->assertSeeText($standaloneParent->name);
        $response->assertSeeText('77 Beach Road');
        $response->assertSeeText('0 Associated Restaurants');
        // Clicking directly enters that restaurant master report
        $directReportUrl = route('franchise.performance.report', ['restaurantId' => $standaloneParent->id]);
        $response->assertSee($directReportUrl);

        // Check Chain Parent
        $response->assertSeeText($chainParent->name);
        $response->assertSeeText('101 Metro Tower');
        $response->assertSeeText('1 Associated Restaurant');
        // Clicking opens the associated restaurants page
        $branchesUrl = route('franchise.performance.parent', ['parentId' => $chainParent->id]);
        $response->assertSee($branchesUrl);
    }

    public function test_associated_restaurants_page_shows_parent_and_outlets()
    {
        $fso = $this->createFsoUser();

        $chainParent = RestaurantMaster::create([
            'name' => 'Burger Empire HQ ' . uniqid(),
            'restaurant_id_unique' => 'REST-EMP-01',
            'parent_id' => null,
            'address' => 'Central Plaza, Floor 1',
            'status' => 'A'
        ]);

        $sub1 = RestaurantMaster::create([
            'name' => 'Burger Empire Downtown ' . uniqid(),
            'restaurant_id_unique' => 'REST-EMP-02',
            'parent_id' => $chainParent->id,
            'address' => 'Downtown Boulevard',
            'status' => 'A'
        ]);

        FranchiseRestaurantMapping::create([
            'user_id' => $fso->id,
            'restaurant_id' => $chainParent->id,
        ]);

        // Visit associated restaurants page for parent
        $response = $this->actingAs($fso)->get(route('franchise.performance.parent', ['parentId' => $chainParent->id]));
        $response->assertStatus(200);

        // Assert parent is listed as HQ
        $response->assertSeeText($chainParent->name);
        $response->assertSeeText('HQ / Parent Restaurant');
        $response->assertSee(route('franchise.performance.report', ['restaurantId' => $chainParent->id]));

        // Assert outlet is listed as Sub-Outlet
        $response->assertSeeText($sub1->name);
        $response->assertSeeText('Associated Sub-Outlet');
        $response->assertSee(route('franchise.performance.report', ['restaurantId' => $sub1->id]));
    }

    public function test_restaurant_master_report_pages_render_for_performance_tracking()
    {
        $fso = $this->createFsoUser();

        $parent = RestaurantMaster::create([
            'name' => 'Spice Symphony ' . uniqid(),
            'restaurant_id_unique' => 'REST-SPICE-01',
            'parent_id' => null,
            'address' => 'Gourmet Street',
            'status' => 'A'
        ]);

        FranchiseRestaurantMapping::create([
            'user_id' => $fso->id,
            'restaurant_id' => $parent->id,
        ]);

        // Create sample dish & order
        $cat = Category::create([
            'restaurant_id' => $parent->id,
            'name' => 'Starters',
            'category_type' => 'FOOD',
            'status' => 'A'
        ]);

        $dish = SubCategory::create([
            'restaurant_id' => $parent->id,
            'category_id' => $cat->id,
            'name' => 'Paneer Tikka Platter',
            'price' => 320,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $order = OrderManage::create([
            'restaurant_id' => $parent->id,
            'user_id' => $fso->id,
            'customer_name' => 'John Gourmet',
            'order_type' => 'DINE_IN',
            'total_amount' => 640,
            'grand_total' => 640,
            'amount_paid' => 640,
            'payment_status' => 'PAID',
            'payment_method' => 'UPI',
            'order_status' => 'COMPLETED',
        ]);

        OrderItems::create([
            'order_id' => $order->id,
            'restaurant_id' => $parent->id,
            'subcategory_id' => $dish->id,
            'quantity' => 2,
            'price' => 320,
            'total_amount' => 640,
            'order_status' => 'SERVED'
        ]);

        // 1. Dashboard Tab (Before Executive Overview)
        $resDashboard = $this->actingAs($fso)->get(route('franchise.performance.report.dashboard', ['restaurantId' => $parent->id]));
        $resDashboard->assertStatus(200);
        $resDashboard->assertSeeText('Spice Symphony');
        $resDashboard->assertSeeText('Total Sales Sum Till Now');
        $resDashboard->assertSeeText('Top 10 Best Selling Dishes');
        $resDashboard->assertSeeText('Paneer Tikka Platter');
        $resDashboard->assertSeeText('640.00');

        // 2. Executive Overview Tab
        $resOverview = $this->actingAs($fso)->get(route('franchise.performance.report.overview', ['restaurantId' => $parent->id]));
        $resOverview->assertStatus(200);
        $resOverview->assertSeeText('Spice Symphony');
        $resOverview->assertSeeText('Master Report');
        $resOverview->assertSeeText('640.00');

        // 3. Orders Tab
        $resOrders = $this->actingAs($fso)->get(route('franchise.performance.report.orders', ['restaurantId' => $parent->id]));
        $resOrders->assertStatus(200);
        $resOrders->assertSeeText('Detailed Orders Report');
        $resOrders->assertSeeText('John Gourmet');

        // 4. Order Items Tab
        $resItems = $this->actingAs($fso)->get(route('franchise.performance.report.order-items', ['restaurantId' => $parent->id]));
        $resItems->assertStatus(200);
        $resItems->assertSeeText('Itemized Sales Ledger');
        $resItems->assertSeeText('Paneer Tikka Platter');

        // 5. Live Stock Tab
        $resStock = $this->actingAs($fso)->get(route('franchise.performance.report.stock', ['restaurantId' => $parent->id]));
        $resStock->assertStatus(200);
        $resStock->assertSeeText('Live Inventory Stock Summary');

        // 6. Analytics Tab
        $resAnalytics = $this->actingAs($fso)->get(route('franchise.performance.report.analytics', ['restaurantId' => $parent->id]));
        $resAnalytics->assertStatus(200);
        $resAnalytics->assertSeeText('Total Period Sales');
    }

    public function test_unauthorized_fso_cannot_access_unmapped_restaurant_report()
    {
        $fso = $this->createFsoUser();

        // Restaurant NOT mapped to this FSO
        $otherRestaurant = RestaurantMaster::create([
            'name' => 'Foreign Kitchen ' . uniqid(),
            'restaurant_id_unique' => 'REST-FOR-' . rand(100, 999),
            'parent_id' => null,
            'address' => 'Secret Alley',
            'status' => 'A'
        ]);

        $response = $this->actingAs($fso)->get(route('franchise.performance.report', ['restaurantId' => $otherRestaurant->id]));
        $response->assertStatus(403);
    }
}
