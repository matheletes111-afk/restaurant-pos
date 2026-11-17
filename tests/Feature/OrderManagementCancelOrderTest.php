<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\TableManage;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

class OrderManagementCancelOrderTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndAdmin()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Cancel Order Test Restro ' . uniqid(),
            'restaurant_id_unique' => 'REST-DEL-' . rand(100, 999),
            'parent_id' => null,
            'address' => '400 Audit St',
            'status' => 'A'
        ]);

        $admin = User::create([
            'name' => 'Report Admin ' . uniqid(),
            'email' => 'report_admin_' . uniqid() . '@example.com',
            'phone' => '98765' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $plan = Plan::create([
            'name' => 'Report Plan ' . uniqid(),
            'price' => 999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(29),
            'amount' => 999.00,
            'currency' => 'INR',
        ]);

        return [$restaurant, $admin];
    }

    public function test_order_management_report_shows_tabs_and_active_orders()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $order = OrderManage::create([
            'order_id' => 'ORD-ACT-' . rand(1000, 9999),
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '9898989898',
            'order_type' => 'TAKEAWAY',
            'total_amount' => 250.00,
            'grand_total' => 250.00,
            'amount_paid' => 250.00,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED',
            'created_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('order.report.management', ['tab' => 'active']));

        $response->assertStatus(200);
        $response->assertSee('Active Orders');
        $response->assertSee('Cancel Order');
        $response->assertSee($order->order_id);
    }

    public function test_soft_delete_order_requires_reason()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $order = OrderManage::create([
            'order_id' => 'ORD-DEL-REQ-' . rand(1000, 9999),
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Test Customer',
            'total_amount' => 100.00,
            'grand_total' => 100.00,
            'payment_status' => 'PENDING',
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('order.report.management.delete'), [
                'order_id' => $order->id,
                'cancel_reason' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cancel_reason']);
    }

    public function test_soft_delete_order_with_reason_succeeds_and_releases_table()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $table = TableManage::create([
            'name' => 'Table 99',
            'capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'table_status' => 'OCCUPIED',
        ]);

        $order = OrderManage::create([
            'order_id' => 'ORD-OCC-' . rand(1000, 9999),
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Dine Guest',
            'table_id' => $table->id,
            'order_type' => 'DINE_IN',
            'total_amount' => 450.00,
            'grand_total' => 450.00,
            'amount_paid' => 0.00,
            'payment_status' => 'PENDING',
            'order_status' => 'PREPARING',
        ]);

        $table->order_id = $order->id;
        $table->save();

        $item = OrderItems::create([
            'order_id' => $order->id,
            'restaurant_id' => $restaurant->id,
            'price' => 450.00,
            'total_amount' => 450.00,
            'quantity' => 1,
            'order_status' => 'PREPARING',
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('order.report.management.delete'), [
                'order_id' => $order->id,
                'cancel_reason' => 'Guest changed mind and left',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Check order is soft deleted
        $this->assertSoftDeleted('orders', ['id' => $order->id]);

        $deletedOrder = OrderManage::onlyTrashed()->find($order->id);
        $this->assertNotNull($deletedOrder);
        $this->assertEquals('Guest changed mind and left', $deletedOrder->cancel_reason);
        $this->assertEquals($admin->id, $deletedOrder->cancelled_by);
        $this->assertEquals('CANCELLED', $deletedOrder->order_status);

        // Check table is released
        $table->refresh();
        $this->assertEquals('AVAILABLE', $table->table_status);
        $this->assertNull($table->order_id);

        // Check items marked as CANCELLED
        $item->refresh();
        $this->assertEquals('CANCELLED', $item->order_status);
    }

    public function test_cancel_order_tab_displays_deleted_orders_with_reason()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $order = OrderManage::create([
            'order_id' => 'ORD-TRASHED-' . rand(1000, 9999),
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Cancelled Client',
            'total_amount' => 320.00,
            'grand_total' => 320.00,
            'amount_paid' => 320.00,
            'payment_status' => 'PAID',
            'order_status' => 'CANCELLED',
            'cancel_reason' => 'Duplicate punch detected',
            'cancelled_by' => $admin->id,
            'created_at' => Carbon::now(),
        ]);
        $order->delete(); // Soft delete

        // Active tab should NOT show this order
        $activeResponse = $this->actingAs($admin)
            ->get(route('order.report.management', ['tab' => 'active']));
        $activeResponse->assertStatus(200);
        $activeResponse->assertDontSee($order->order_id);

        // Cancelled tab SHOULD show this order and reason
        $cancelledResponse = $this->actingAs($admin)
            ->get(route('order.report.management', ['tab' => 'cancelled']));
        $cancelledResponse->assertStatus(200);
        $cancelledResponse->assertSee($order->order_id);
        $cancelledResponse->assertSee('Duplicate punch detected');
        $cancelledResponse->assertSee('Cancelled Invoices Ledger');
    }

    public function test_order_deletion_reduces_counters_in_report_and_dashboard()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $today = Carbon::today();

        // Order 1: 500
        $order1 = OrderManage::create([
            'order_id' => 'ORD-REV1-' . rand(1000, 9999),
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Customer A',
            'order_type' => 'TAKEAWAY',
            'total_amount' => 500.00,
            'grand_total' => 500.00,
            'amount_paid' => 500.00,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED',
            'created_at' => $today->copy()->setTime(10, 0),
        ]);

        // Order 2: 700
        $order2 = OrderManage::create([
            'order_id' => 'ORD-REV2-' . rand(1000, 9999),
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'Customer B',
            'order_type' => 'TAKEAWAY',
            'total_amount' => 700.00,
            'grand_total' => 700.00,
            'amount_paid' => 700.00,
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED',
            'created_at' => $today->copy()->setTime(11, 0),
        ]);

        // Check initial dashboard counters
        $dashResponseBefore = $this->actingAs($admin)->get(route('dashboard'));
        $dashResponseBefore->assertStatus(200);
        $this->assertEquals(1200.00, $dashResponseBefore->viewData('totalRevenueToday'));
        $this->assertEquals(2, $dashResponseBefore->viewData('totalOrdersToday'));

        // Delete order 1 with reason
        $deleteResponse = $this->actingAs($admin)
            ->postJson(route('order.report.management.delete'), [
                'order_id' => $order1->id,
                'cancel_reason' => 'Test order cancelled',
            ]);
        $deleteResponse->assertStatus(200);

        // Check report counters after deletion (should be 700, 1 order)
        $reportResponseAfter = $this->actingAs($admin)->get(route('order.report.management', ['tab' => 'active']));
        $reportResponseAfter->assertStatus(200);
        $summary = $reportResponseAfter->viewData('summary');
        $this->assertEquals(1, $summary['total_orders']);
        $this->assertEquals(700.00, $summary['total_revenue']);
        $this->assertEquals(700.00, $summary['total_collected']);
        $this->assertEquals(1, $summary['cancelled_orders_count']);
        $this->assertEquals(500.00, $summary['cancelled_total_amount']);

        // Check dashboard counters after deletion (should be reduced to 700, 1 order)
        $dashResponseAfter = $this->actingAs($admin)->get(route('dashboard'));
        $dashResponseAfter->assertStatus(200);
        $this->assertEquals(700.00, $dashResponseAfter->viewData('totalRevenueToday'));
        $this->assertEquals(1, $dashResponseAfter->viewData('totalOrdersToday'));
    }

    public function test_viewing_cancelled_order_invoice_and_details_works()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();

        $order = OrderManage::create([
            'order_id' => 'ORD-VIEW-CANC-' . rand(1000, 9999),
            'restaurant_id' => $restaurant->id,
            'customer_name' => 'View Customer',
            'total_amount' => 350.00,
            'grand_total' => 350.00,
            'amount_paid' => 350.00,
            'payment_status' => 'PAID',
            'order_status' => 'CANCELLED',
            'cancel_reason' => 'Requested cancellation',
            'created_at' => Carbon::now(),
        ]);
        $order->delete(); // Soft delete

        // Test invoice view for soft-deleted order does not 404
        $invoiceResponse = $this->actingAs($admin)->get(route('order.invoice', $order->id));
        $invoiceResponse->assertStatus(200);

        // Test order details breakdown for soft-deleted order does not 404
        $detailsResponse = $this->actingAs($admin)->get(route('order.report.order.details', $order->id));
        $detailsResponse->assertStatus(200);
    }
}
