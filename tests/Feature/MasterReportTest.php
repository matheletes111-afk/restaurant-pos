<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterReportTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $branch;
    protected $owner;
    protected $staffWithPerm;
    protected $staffWithoutPerm;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Main Restaurant & Branch
        $this->restaurant = RestaurantMaster::create([
            'restaurant_id_unique' => 'BILL-BITE-' . rand(1000, 9999),
            'name' => 'Main HQ Restaurant',
            'status' => 'A',
            'gstin' => '27ABCDE1234F1Z5'
        ]);

        $this->branch = RestaurantMaster::create([
            'restaurant_id_unique' => 'BILL-BITE-' . rand(1000, 9999),
            'name' => 'Downtown Branch',
            'parent_id' => $this->restaurant->id,
            'status' => 'A'
        ]);

        // Create Active Subscription for Main Restaurant
        $plan = Plan::create([
            'name' => 'Enterprise Plan',
            'price' => 4999,
            'duration_days' => 365,
            'billing_cycle' => 'monthly',
            'inventory_checkbox' => 'Y',
            'plan_status' => 'A'
        ]);

        Subscription::create([
            'user_id' => $this->restaurant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(300),
            'amount' => 4999
        ]);

        // 2. Users
        $this->owner = User::create([
            'name' => 'Restaurant Owner',
            'email' => 'owner@restro.com',
            'phone' => '9999999999',
            'password' => Hash::make('password'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $this->restaurant->id,
            'status' => 'A'
        ]);

        $this->staffWithPerm = User::create([
            'name' => 'Staff With Report',
            'email' => 'staff1@restro.com',
            'phone' => '8888888888',
            'password' => Hash::make('password'),
            'role' => 'RES',
            'role_type' => 'STAFF',
            'restaurant_id' => $this->restaurant->id,
            'permissions' => ['master_report'],
            'status' => 'A'
        ]);

        $this->staffWithoutPerm = User::create([
            'name' => 'Staff Without Report',
            'email' => 'staff2@restro.com',
            'phone' => '7777777777',
            'password' => Hash::make('password'),
            'role' => 'RES',
            'role_type' => 'STAFF',
            'restaurant_id' => $this->restaurant->id,
            'permissions' => ['order_master'],
            'status' => 'A'
        ]);

        // 3. Create Sample Categories, Dishes, Orders, Expenses, Purchases
        $category = Category::create([
            'name' => 'Beverages',
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->owner->id
        ]);

        $dish = SubCategory::create([
            'name' => 'Cold Coffee',
            'price' => 150,
            'food_type' => 'veg',
            'category_id' => $category->id,
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->owner->id,
            'status' => 'A'
        ]);

        $order = OrderManage::create([
            'customer_name' => 'Alice VIP',
            'customer_phone' => '9876543210',
            'order_type' => 'dine_in',
            'total_amount' => 300,
            'taxable_amount' => 300,
            'gst_amount' => 15,
            'grand_total' => 315,
            'amount_paid' => 315,
            'payment_status' => 'PAID',
            'payment_method' => 'UPI',
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->owner->id
        ]);

        OrderItems::create([
            'order_id' => $order->id,
            'subcategory_id' => $dish->id,
            'quantity' => 2,
            'price' => 150,
            'taxable_amount' => 300,
            'gst_rate' => 5,
            'gst_amount' => 15,
            'total_amount' => 315,
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->owner->id
        ]);

        Expense::create([
            'title' => 'Milk & Coffee Beans',
            'amount' => 500,
            'expense_date' => now(),
            'payment_method' => 'CASH',
            'restaurant_id' => $this->restaurant->id,
            'created_by' => $this->owner->id
        ]);
    }

    /** @test */
    public function restaurant_owner_can_access_all_seven_master_report_tabs()
    {
        $tabs = [
            'admin.reports.master.overview',
            'admin.reports.master.orders',
            'admin.reports.master.order-items',
            'admin.reports.master.purchases',
            'admin.reports.master.stock',
            'admin.reports.master.expenses',
            'admin.reports.master.analytics',
        ];

        foreach ($tabs as $route) {
            $response = $this->actingAs($this->owner)->get(route($route));
            $response->assertStatus(200);
            $response->assertSee('Master Executive Report');
        }
    }

    /** @test */
    public function staff_with_master_report_permission_can_access_while_unauthorized_staff_is_denied()
    {
        // Staff with permission -> Allowed
        $response = $this->actingAs($this->staffWithPerm)->get(route('admin.reports.master.overview'));
        $response->assertStatus(200);

        // Staff without permission -> Forbidden 403
        $deniedResponse = $this->actingAs($this->staffWithoutPerm)->get(route('admin.reports.master.overview'));
        $deniedResponse->assertStatus(403);
    }

    /** @test */
    public function multi_branch_outlet_switcher_works_and_strictly_isolates_data()
    {
        // Create an unrelated 3rd party restaurant
        $unrelatedRest = RestaurantMaster::create([
            'restaurant_id_unique' => 'BILL-BITE-999',
            'name' => 'Foreign Cafe',
            'status' => 'A'
        ]);

        // 1. Owner views HQ
        $responseHq = $this->actingAs($this->owner)->get(route('admin.reports.master.overview', ['outlet_id' => $this->restaurant->id]));
        $responseHq->assertStatus(200);
        $responseHq->assertSee('Main HQ Restaurant');
        $responseHq->assertSee('Alice VIP');

        // 2. Owner switches to Downtown Branch
        $responseBranch = $this->actingAs($this->owner)->get(route('admin.reports.master.overview', ['outlet_id' => $this->branch->id]));
        $responseBranch->assertStatus(200);
        $responseBranch->assertSee('Downtown Branch');

        // 3. Unauthorized outlet query falls back safely to owner's restaurant
        $responseForeign = $this->actingAs($this->owner)->get(route('admin.reports.master.overview', ['outlet_id' => $unrelatedRest->id]));
        $responseForeign->assertStatus(200);
        $responseForeign->assertDontSee('Foreign Cafe');
    }

    /** @test */
    public function master_orders_and_analytics_report_renders_customer_and_financial_data()
    {
        $responseOrders = $this->actingAs($this->owner)->get(route('admin.reports.master.orders'));
        $responseOrders->assertStatus(200);
        $responseOrders->assertSee('Alice VIP');
        $responseOrders->assertSee('9876543210');
        $responseOrders->assertSee('315.00');

        $responseAnalytics = $this->actingAs($this->owner)->get(route('admin.reports.master.analytics'));
        $responseAnalytics->assertStatus(200);
        $responseAnalytics->assertSee('Cold Coffee');
        $responseAnalytics->assertSee('Alice VIP');
    }
}
