<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderToPayment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PublicInvoiceDownloadTest extends TestCase
{
    use DatabaseTransactions;

    private $restaurant;
    private $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::create([
            'name' => 'Royal Spice Bistro',
            'email' => 'royalspice@test.com',
            'phone_number' => '9876543210',
            'address' => '456 Gourmet Street, Mumbai',
            'pincode' => '400001',
            'gstin' => '27AAAAA0000A1Z5',
            'gst_percentage' => 5,
            'status' => 'A',
            'upi_id' => 'royalspice@upi',
        ]);

        $category = Category::create([
            'name' => 'Main Course',
            'restaurant_id' => $this->restaurant->id,
            'status' => 'A'
        ]);

        $subCategory = SubCategory::create([
            'name' => 'Paneer Butter Masala',
            'category_id' => $category->id,
            'restaurant_id' => $this->restaurant->id,
            'price' => 250.00,
            'food_type' => 'veg',
            'status' => 'A'
        ]);

        $this->order = OrderManage::create([
            'restaurant_id' => $this->restaurant->id,
            'order_id' => 9917,
            'order_type' => 'TAKEAWAY',
            'order_status' => 'COMPLETED',
            'order_complete' => 'DONE',
            'customer_name' => 'Aditya Sharma',
            'customer_phone' => '9876500000',
            'total_amount' => 250.00,
            'taxable_amount' => 250.00,
            'gst_amount' => 12.50,
            'cgst_amount' => 6.25,
            'sgst_amount' => 6.25,
            'restaurant_gst_percentage' => 5,
            'is_gst_bill' => 'YES',
            'discount_amount' => 0.00,
            'grand_total' => 263.00,
            'amount_paid' => 263.00,
            'payment_status' => 'PAID',
            'payment_method' => 'SPLIT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        OrderItems::create([
            'order_id' => $this->order->id,
            'subcategory_id' => $subCategory->id,
            'restaurant_id' => $this->restaurant->id,
            'quantity' => 1,
            'price' => 250.00,
            'taxable_amount' => 250.00,
            'gst_amount' => 12.50,
            'total_amount' => 262.50,
        ]);

        OrderToPayment::create([
            'order_id' => $this->order->id,
            'amount' => 200.00,
            'payment_method' => 'CASH',
            'status' => 'PAID',
            'restaurant_id' => $this->restaurant->id
        ]);

        OrderToPayment::create([
            'order_id' => $this->order->id,
            'amount' => 63.00,
            'payment_method' => 'UPI',
            'status' => 'PAID',
            'restaurant_id' => $this->restaurant->id
        ]);
    }

    public function test_guest_can_access_public_invoice_view_without_auth()
    {
        $response = $this->get(route('order.public.invoice', $this->order->id));

        $response->assertStatus(200);
        $response->assertSee('Royal Spice Bistro');
        $response->assertSee('Aditya Sharma');
        $response->assertSee('Paneer Butter Masala');
        $response->assertSee('263.00');
        $response->assertSee('Download PDF');
    }

    public function test_guest_can_download_invoice_pdf_without_auth()
    {
        $response = $this->get(route('order.public.download', $this->order->id));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_public_invoice_download_attachment_mode()
    {
        $response = $this->get(route('order.public.download', ['id' => $this->order->id, 'download' => 1]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
    }
}
