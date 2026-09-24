<?php

namespace Tests\Feature;

use App\Mail\NewQrOrderMail;
use App\Models\Category;
use App\Models\RestaurantMaster;
use App\Models\SubCategory;
use App\Models\TableManage;
use App\Models\TempOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QrOrderNotificationTest extends TestCase
{
    protected $restaurant;
    protected $ownerUser;
    protected $table;
    protected $subcategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Find or create test restaurant with owner
        $this->restaurant = RestaurantMaster::first();
        if (!$this->restaurant) {
            $this->restaurant = RestaurantMaster::create([
                'name' => 'Test Notification Restaurant',
                'address' => '123 Food Street',
                'pincode' => '700001',
                'gstin' => '19AAAAA0000A1Z5',
                'gst_percentage' => 5.00,
                'status' => 'A',
            ]);
        }

        // Ensure owner user with email
        if ($this->restaurant->owner_id) {
            $this->ownerUser = User::find($this->restaurant->owner_id);
        }
        if (!$this->ownerUser) {
            $this->ownerUser = User::firstOrCreate(
                ['email' => 'testowner_qr@example.com'],
                [
                    'name' => 'Owner Test User',
                    'password' => bcrypt('password123'),
                    'role' => 'RES',
                    'role_type' => 'ADMIN',
                    'restaurant_id' => $this->restaurant->id,
                    'status' => 'A'
                ]
            );
            $this->restaurant->owner_id = $this->ownerUser->id;
            $this->restaurant->save();
        }

        // Table
        $this->table = TableManage::where('restaurant_id', $this->restaurant->id)->first();
        if (!$this->table) {
            $this->table = TableManage::create([
                'name' => 'Table #10',
                'restaurant_id' => $this->restaurant->id,
                'status' => 'A',
            ]);
        }

        // Category & SubCategory (Menu Item)
        $category = Category::where('restaurant_id', $this->restaurant->id)->first();
        if (!$category) {
            $category = Category::create([
                'name' => 'Main Course',
                'restaurant_id' => $this->restaurant->id,
                'status' => 'A',
            ]);
        }

        $this->subcategory = SubCategory::where('category_id', $category->id)->first();
        if (!$this->subcategory) {
            $this->subcategory = new SubCategory();
            $this->subcategory->category_id = $category->id;
            $this->subcategory->restaurant_id = $this->restaurant->id;
            $this->subcategory->name = 'Butter Chicken';
            $this->subcategory->price = 320.00;
            $this->subcategory->status = 'A';
            $this->subcategory->save();
        }
    }

    public function test_qr_order_store_creates_unread_temp_order_and_sends_email_to_registered_restaurant_email()
    {
        Mail::fake();

        $payload = [
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'John Customer',
            'customer_phone' => '9876543210',
            'remarks' => 'Extra spicy please',
            'order_items' => [
                [
                    'id' => $this->subcategory->id,
                    'price' => 320.00,
                    'qty' => 2,
                    'item_discount' => 10,
                ]
            ]
        ];

        $response = $this->post(route('temp.order.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        // Assert order in DB has is_read = false
        $this->assertDatabaseHas('temp_orders', [
            'restaurant_id' => $this->restaurant->id,
            'customer_name' => 'John Customer',
            'is_read' => 0,
        ]);

        // Assert email was sent to restaurant owner email
        $expectedEmail = $this->ownerUser->email;
        Mail::assertSent(NewQrOrderMail::class, function ($mail) use ($expectedEmail) {
            return $mail->hasTo($expectedEmail) &&
                   $mail->restaurant->id === $this->restaurant->id &&
                   $mail->order->customer_name === 'John Customer';
        });
    }

    public function test_qr_notifications_api_returns_list_for_past_one_week_and_unread_count()
    {
        // Create an unread order within 7 days
        $recentOrder = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Recent QR Guest',
            'customer_phone' => '9998887776',
            'order_type' => 'DINE_IN',
            'total_amount' => 500.00,
            'grand_total' => 525.00,
            'order_status' => 'PENDING',
            'is_read' => 0,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // Create an older order outside 7 days
        $oldOrder = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Old QR Guest',
            'customer_phone' => '1112223334',
            'order_type' => 'DINE_IN',
            'total_amount' => 200.00,
            'grand_total' => 200.00,
            'order_status' => 'PENDING',
            'is_read' => 0,
        ]);
        \Illuminate\Support\Facades\DB::table('temp_orders')
            ->where('id', $oldOrder->id)
            ->update(['created_at' => Carbon::now()->subDays(10)]);

        $this->actingAs($this->ownerUser);

        $response = $this->get(route('restaurant.qr.notifications'));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, $data['unread_count']);

        $returnedIds = collect($data['notifications'])->pluck('id')->toArray();
        $this->assertContains($recentOrder->id, $returnedIds);
        $this->assertNotContains($oldOrder->id, $returnedIds);
    }

    public function test_mark_single_qr_notification_as_read()
    {
        $order = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Mark Read Test',
            'order_type' => 'DINE_IN',
            'total_amount' => 150.00,
            'grand_total' => 150.00,
            'order_status' => 'PENDING',
            'is_read' => 0,
            'created_at' => Carbon::now()->subHours(3),
        ]);

        $this->actingAs($this->ownerUser);

        $response = $this->post(route('restaurant.qr.notifications.mark-read', $order->id));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('temp_orders', [
            'id' => $order->id,
            'is_read' => 1,
        ]);
        $order->refresh();
        $this->assertNotNull($order->read_at);
    }

    public function test_mark_all_qr_notifications_as_read()
    {
        $order1 = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Bulk Read 1',
            'order_type' => 'DINE_IN',
            'total_amount' => 100.00,
            'grand_total' => 100.00,
            'order_status' => 'PENDING',
            'is_read' => 0,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        $order2 = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'Bulk Read 2',
            'order_type' => 'DINE_IN',
            'total_amount' => 200.00,
            'grand_total' => 200.00,
            'order_status' => 'PENDING',
            'is_read' => 0,
            'created_at' => Carbon::now()->subHours(2),
        ]);

        $this->actingAs($this->ownerUser);

        $response = $this->post(route('restaurant.qr.notifications.mark-all-read'));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('temp_orders', [
            'id' => $order1->id,
            'is_read' => 1,
        ]);
        $this->assertDatabaseHas('temp_orders', [
            'id' => $order2->id,
            'is_read' => 1,
        ]);
    }

    public function test_viewing_temp_order_in_admin_marks_it_as_read()
    {
        $order = TempOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'customer_name' => 'View Read Test',
            'order_type' => 'DINE_IN',
            'total_amount' => 250.00,
            'grand_total' => 250.00,
            'order_status' => 'PENDING',
            'is_read' => 0,
            'created_at' => Carbon::now(),
        ]);

        $this->actingAs($this->ownerUser);

        $response = $this->get(route('temp.orders.view', $order->id));
        $response->assertStatus(200);

        $this->assertDatabaseHas('temp_orders', [
            'id' => $order->id,
            'is_read' => 1,
        ]);
    }
}
