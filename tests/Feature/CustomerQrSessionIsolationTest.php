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

class CustomerQrSessionIsolationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_customer_order_session_isolation_and_completion_removal()
    {
        // 1. Setup restaurant, admin, table, dish
        $restaurant = RestaurantMaster::first();
        if (!$restaurant) {
            $restaurant = RestaurantMaster::create([
                'name' => 'Isolation Bistro',
                'email' => 'isolation@example.com',
                'phone' => '9876543210',
                'address' => 'Test Street',
                'prefix' => 'ISOL',
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
                'email' => 'admin_iso_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'restaurant_id' => $restaurant->id,
            ]);
        }

        $table = TableManage::where('restaurant_id', $restaurant->id)->first();
        if (!$table) {
            $table = TableManage::create([
                'restaurant_id' => $restaurant->id,
                'name' => 'T10',
                'table_type' => 'Dine In',
                'seating_capacity' => 4,
                'table_status' => 'AVAILABLE',
            ]);
        }

        $category = Category::where('restaurant_id', $restaurant->id)->first();
        if (!$category) {
            $category = Category::create([
                'restaurant_id' => $restaurant->id,
                'name' => 'Test Mains',
            ]);
        }

        $dish1 = new SubCategory();
        $dish1->restaurant_id = $restaurant->id;
        $dish1->category_id = $category->id;
        $dish1->name = 'Butter Chicken ' . uniqid();
        $dish1->price = 300;
        $dish1->food_type = 'Non-Veg';
        $dish1->status = 'ACTIVE';
        $dish1->save();

        $dish2 = new SubCategory();
        $dish2->restaurant_id = $restaurant->id;
        $dish2->category_id = $category->id;
        $dish2->name = 'Veg Biryani ' . uniqid();
        $dish2->price = 200;
        $dish2->food_type = 'Veg';
        $dish2->status = 'ACTIVE';
        $dish2->save();

        // 2. Sayan Ghosh visits table menu (Session A)
        $sayanVisit1 = $this->withSession([])->get(route('temp.order.create', [$table->id, $restaurant->id]));
        $sayanVisit1->assertStatus(200);
        $sayanVisit1->assertViewHas('activeOrder', null);
        $sayanVisit1->assertViewHas('pendingTempOrder', null);

        // 3. Sayan places an order from Table T10
        $sayanOrderRes = $this->withSession([])->postJson(route('temp.order.store'), [
            'customer_name' => 'Sayan Ghosh',
            'customer_phone' => '9876543210',
            'table_id' => $table->id,
            'restaurant_id' => $restaurant->id,
            'order_items' => [
                ['id' => $dish1->id, 'name' => $dish1->name, 'price' => 300, 'qty' => 1, 'item_discount' => 0],
            ]
        ]);
        $sayanOrderRes->assertStatus(200);
        $sayanOrderRes->assertJson(['status' => true]);

        $sayanTempOrder = TempOrder::where('customer_phone', '9876543210')->latest('id')->first();
        $this->assertNotNull($sayanTempOrder);
        $this->assertEquals('Sayan Ghosh', $sayanTempOrder->customer_name);

        // Admin approves Sayan's order
        $this->actingAs($adminUser);
        $this->get(route('admin.temporder.approve', $sayanTempOrder->id));
        $this->app['auth']->guard()->logout();
        $this->app['auth']->forgetGuards();
        $sayanTempOrder->refresh();
        $this->assertEquals('APPROVED', $sayanTempOrder->order_status);
        $sayanActiveOrderId = $sayanTempOrder->order_id;
        $this->assertNotNull($sayanActiveOrderId);

        // 4. Sayan visits menu with Sayan's Session: he sees his active order
        $sayanSession = [
            'customer_qr_order_id' => $sayanActiveOrderId,
            'customer_qr_order_type' => 'main',
            "customer_qr_order_{$restaurant->id}_{$table->id}" => [
                'id' => $sayanActiveOrderId,
                'type' => 'main'
            ],
            'customer_qr_allowed_orders' => [$sayanTempOrder->id, $sayanActiveOrderId],
            'customer_name' => 'Sayan Ghosh',
            'customer_phone' => '9876543210',
        ];

        $sayanVisit2 = $this->withSession($sayanSession)->get(route('temp.order.create', [$table->id, $restaurant->id]));
        $sayanVisit2->assertStatus(200);
        $sayanActiveOrder = $sayanVisit2->viewData('activeOrder');
        $this->assertNotNull($sayanActiveOrder);
        $this->assertEquals($sayanActiveOrderId, $sayanActiveOrder->id);
        $this->assertEquals('Sayan Ghosh', $sayanActiveOrder->customer_name);

        // 5. Rohi visits Table T10 with a completely FRESH session (different phone/device)
        // Rohi MUST NOT see Sayan's order!
        $this->flushSession();
        $rohiVisit1 = $this->get(route('temp.order.create', [$table->id, $restaurant->id]));
        $rohiVisit1->assertStatus(200);
        $rohiActiveOrder = $rohiVisit1->viewData('activeOrder');
        $this->assertNull($rohiActiveOrder, "Rohi must NOT see Sayan's active order!");
        $this->assertNull($rohiVisit1->viewData('pendingTempOrder'));

        // 6. Rohi places an order for himself on Table T10
        $rohiOrderRes = $this->postJson(route('temp.order.store'), [
            'customer_name' => 'Rohi',
            'customer_phone' => '9123456780',
            'table_id' => $table->id,
            'restaurant_id' => $restaurant->id,
            'order_items' => [
                ['id' => $dish2->id, 'name' => $dish2->name, 'price' => 200, 'qty' => 2, 'item_discount' => 0],
            ]
        ]);
        $rohiOrderRes->assertStatus(200);

        $rohiTempOrder = TempOrder::where('customer_phone', '9123456780')->latest('id')->first();
        $this->assertNotNull($rohiTempOrder);
        $this->assertEquals('Rohi', $rohiTempOrder->customer_name);

        // Admin approves Rohi's order
        $this->actingAs($adminUser);
        $this->get(route('admin.temporder.approve', $rohiTempOrder->id));
        auth()->logout();
        $this->app['auth']->forgetGuards();
        $rohiTempOrder->refresh();
        $rohiActiveOrderId = $rohiTempOrder->order_id;
        $this->assertNotNull($rohiActiveOrderId);
        $this->assertNotEquals($sayanActiveOrderId, $rohiActiveOrderId, "Sayan and Rohi must have distinct orders");

        $rohiSession = [
            'customer_qr_order_id' => $rohiActiveOrderId,
            'customer_qr_order_type' => 'main',
            "customer_qr_order_{$restaurant->id}_{$table->id}" => [
                'id' => $rohiActiveOrderId,
                'type' => 'main'
            ],
            'customer_qr_allowed_orders' => [$rohiTempOrder->id, $rohiActiveOrderId],
            'customer_name' => 'Rohi',
            'customer_phone' => '9123456780',
        ];

        // 7. Verify Rohi sees ONLY Rohi's order
        $rohiVisit2 = $this->withSession($rohiSession)->get(route('temp.order.create', [$table->id, $restaurant->id]));
        $rohiVisit2->assertStatus(200);
        $rohiOrderInView = $rohiVisit2->viewData('activeOrder');
        $this->assertNotNull($rohiOrderInView);
        $this->assertEquals($rohiActiveOrderId, $rohiOrderInView->id);
        $this->assertEquals('Rohi', $rohiOrderInView->customer_name);

        // 8. Verify Sayan still sees ONLY Sayan's order
        $sayanVisit3 = $this->withSession($sayanSession)->get(route('temp.order.create', [$table->id, $restaurant->id]));
        $sayanVisit3->assertStatus(200);
        $sayanOrderInView = $sayanVisit3->viewData('activeOrder');
        $this->assertNotNull($sayanOrderInView);
        $this->assertEquals($sayanActiveOrderId, $sayanOrderInView->id);
        $this->assertEquals('Sayan Ghosh', $sayanOrderInView->customer_name);

        // 8a. Sayan can view his OWN full order details page
        $sayanDetailsRes = $this->withSession($sayanSession)->get(route('order.details', $sayanActiveOrderId));
        $sayanDetailsRes->assertStatus(200);
        $sayanDetailsRes->assertSee('Sayan Ghosh');
        $sayanDetailsRes->assertSee('Running Bill Summary');
        $sayanDetailsRes->assertSee('Order More Items');

        // 8b. URL PROTECTION: Sayan attempts to access Rohi's order details URL -> 403 Forbidden!
        $sayanAccessRohiDetails = $this->withSession($sayanSession)->get(route('order.details', $rohiActiveOrderId));
        $sayanAccessRohiDetails->assertStatus(403);

        // 8c. URL PROTECTION: Rohi attempts to access Sayan's order details URL -> 403 Forbidden!
        $rohiAccessSayanDetails = $this->withSession($rohiSession)->get(route('order.details', $sayanActiveOrderId));
        $rohiAccessSayanDetails->assertStatus(403);

        // 8d. URL PROTECTION: Sayan attempts to access Rohi's order success URL -> 403 Forbidden!
        $sayanAccessRohiSuccess = $this->withSession($sayanSession)->get(route('order.success', $rohiTempOrder->id));
        $sayanAccessRohiSuccess->assertStatus(403);

        // 8e. URL PROTECTION: Sayan attempts to query Rohi's order status via API -> 403 Forbidden!
        $sayanAccessRohiStatus = $this->withSession($sayanSession)->getJson(route('order.status.check', $rohiActiveOrderId));
        $sayanAccessRohiStatus->assertStatus(403);

        // 8f. URL PROTECTION: Sayan attempts to tamper & add items to Rohi's order -> 403 Forbidden!
        $sayanAddItemsToRohi = $this->withSession($sayanSession)->postJson(route('temp.order.add_items'), [
            'order_id' => $rohiActiveOrderId,
            'restaurant_id' => $restaurant->id,
            'order_items' => [
                ['id' => $dish1->id, 'name' => $dish1->name, 'price' => 300, 'qty' => 1]
            ]
        ]);
        $sayanAddItemsToRohi->assertStatus(403);

        // 8g. Approved temp order visiting order.success redirects to order.details
        $sayanSuccessRedirect = $this->withSession($sayanSession)->get(route('order.success', $sayanTempOrder->id));
        $sayanSuccessRedirect->assertRedirect(route('order.details', $sayanActiveOrderId));

        // 8h. Sayan adds more items to his own order -> redirects to order.details
        $sayanAddMoreItems = $this->withSession($sayanSession)->postJson(route('temp.order.add_items'), [
            'order_id' => $sayanActiveOrderId,
            'restaurant_id' => $restaurant->id,
            'order_items' => [
                ['id' => $dish1->id, 'name' => $dish1->name, 'price' => 300, 'qty' => 1]
            ]
        ]);
        $sayanAddMoreItems->assertStatus(200);
        $sayanAddMoreItems->assertJson(['redirect' => route('order.details', $sayanActiveOrderId)]);

        // 9. Restaurant completes Sayan's order
        $sayanMainOrder = OrderManage::find($sayanActiveOrderId);
        $sayanMainOrder->order_complete = 'DONE';
        $sayanMainOrder->payment_status = 'PAID';
        $sayanMainOrder->save();

        // 10. Sayan opens menu again with his session:
        // Because the restaurant completed his order, it must be removed and Sayan can freshly order!
        $sayanAfterCompletion = $this->withSession($sayanSession)->get(route('temp.order.create', [$table->id, $restaurant->id]));
        $sayanAfterCompletion->assertStatus(200);
        $this->assertNull($sayanAfterCompletion->viewData('activeOrder'), 'Completed order must be removed so customer can freshly order');
        $this->assertNull($sayanAfterCompletion->viewData('pendingTempOrder'));

        // 11. Rohi's order is still active, so Rohi still sees his order
        $rohiAfterSayanCompletion = $this->withSession($rohiSession)->get(route('temp.order.create', [$table->id, $restaurant->id]));
        $rohiAfterSayanCompletion->assertStatus(200);
        $this->assertNotNull($rohiAfterSayanCompletion->viewData('activeOrder'));
        $this->assertEquals($rohiActiveOrderId, $rohiAfterSayanCompletion->viewData('activeOrder')->id);

        // 12. Sayan explicitly starts fresh order via endpoint
        $freshOrderRes = $this->withSession($sayanSession)->get(route('temp.order.fresh', [$table->id, $restaurant->id]));
        $freshOrderRes->assertRedirect(route('temp.order.create', [$table->id, $restaurant->id]));
    }
}
