<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TempOrder;
use App\Models\TempOrderItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableManage;
use App\Models\OrderManage;
use App\Models\OrderItems;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class TempOrderAdminController extends Controller
{
    public function index()
    {
        $orders = TempOrder::with('table_details')->where('restaurant_id',auth()->user()->restaurant_id)
            ->orderBy('id', 'DESC')   // OR created_at
            ->get();

        return view('temp_orders.index', compact('orders'));
    }


    public function view($id)
    {
        $order = TempOrder::with(['items.menuItem', 'table_details'])->findOrFail($id);
        $table = TableManage::where('id',$order->table_id)->first();
        return view('temp_orders.view', compact('order','table'));
    }

public function deleteItem($id)
{
    // Find the item
    $item = TempOrderItem::find($id);
    if (!$item) {
        return redirect()->back()->with('error', 'Item not found');
    }

    // Check if the order belongs to the authenticated restaurant
    $order = TempOrder::where('id', $item->temp_order_id)
        ->where('restaurant_id', auth()->user()->restaurant_id)
        ->first();

    if (!$order) {
        return redirect()->back()->with('error', 'Unauthorized Access');
    }

    // Deduct the item's totals from the order
    $order->decrement('total_amount', $item->total_amount - (($item->total_amount * $item->gst_rate) / (100 + $item->gst_rate)));
    $order->decrement('gst_amount', $item->total_amount - ($item->total_amount - (($item->total_amount * $item->gst_rate) / (100 + $item->gst_rate))));
    $order->decrement('grand_total', $item->total_amount);

    // Delete the item
    $item->delete();

    return redirect()->back()->with('success', 'Item deleted successfully and totals updated.');
}





public function approveOrder($id)
{
    DB::beginTransaction();

    try {
        // Get temp order with items
        $tempOrder = TempOrder::with('items')
            ->where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();

        // Check if table exists and is available
        if ($tempOrder->table_id) {
            $table = TableManage::find($tempOrder->table_id);
            if ($table && $table->table_status != 'AVAILABLE') {
                return redirect()->back()->with('error', 'Table not available');
            }
        }

        // Generate NEW order number (ignore the one from temp order)
        $restaurantId = auth()->user()->restaurant_id;
        $today = Carbon::now()->format('Ymd');
        $todayCount = OrderManage::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', Carbon::today())
            ->count() + 1;
        $sequence = str_pad($todayCount, 4, '0', STR_PAD_LEFT);
        $orderNo = "ORD-{$restaurantId}-{$today}-{$sequence}";

        // Create main order with all fields (using NEW order number)
        $order = new OrderManage();
        $order->table_id = $tempOrder->table_id;
        $order->customer_name = $tempOrder->customer_name;
        $order->customer_phone = $tempOrder->customer_phone;
        $order->order_id = $orderNo;  // NEW order number, not the temp one
        $order->order_type = $tempOrder->order_type;
        $order->total_amount = $tempOrder->total_amount;
        $order->taxable_amount = $tempOrder->taxable_amount;
        $order->gst_amount = $tempOrder->gst_amount;
        $order->cgst_amount = $tempOrder->cgst_amount;
        $order->sgst_amount = $tempOrder->sgst_amount;
        $order->igst_amount = $tempOrder->igst_amount;
        $order->discount = $tempOrder->discount;
        $order->discount_percentage = $tempOrder->discount_percentage;
        $order->grand_total = $tempOrder->grand_total;
        $order->round_off = $tempOrder->round_off;
        $order->is_gst_bill = $tempOrder->is_gst_bill;
        $order->restaurant_gst_percentage = $tempOrder->restaurant_gst_percentage;
        $order->restaurant_gstin = $tempOrder->restaurant_gstin;
        $order->remarks = $tempOrder->remarks;
        $order->order_status = 'PENDING';
        $order->payment_status = 'PENDING';
        $order->restaurant_id = $tempOrder->restaurant_id;
        $order->user_id = auth()->id();
        $order->created_by = auth()->id();
        $order->save();

        // Move items with all fields
        foreach ($tempOrder->items as $item) {
            $orderItem = new OrderItems();
            $orderItem->order_id = $order->id;
            $orderItem->subcategory_id = $item->subcategory_id;
            $orderItem->quantity = $item->quantity;
            $orderItem->price = $item->price;
            $orderItem->discounted_price = $item->discounted_price;
            $orderItem->item_discount_percentage = $item->item_discount_percentage;
            $orderItem->taxable_amount = $item->taxable_amount;
            $orderItem->gst_rate = $item->gst_rate;
            $orderItem->gst_amount = $item->gst_amount;
            $orderItem->cgst_amount = $item->cgst_amount;
            $orderItem->sgst_amount = $item->sgst_amount;
            $orderItem->igst_amount = $item->igst_amount;
            $orderItem->total_amount = $item->total_amount;
            $orderItem->order_status = 'PENDING';
            $orderItem->restaurant_id = $order->restaurant_id;
            $orderItem->user_id = auth()->id();
            $orderItem->save();
        }

        // Update table status if dine-in
        if ($tempOrder->table_id) {
            TableManage::where('id', $tempOrder->table_id)->update([
                'table_status' => 'OCCUPIED',
                'order_id' => $order->id
            ]);
        }

        // Delete temp order
        $tempOrder->delete();

        DB::commit();
        return redirect()->route('temp.orders')->with('success', 'Order approved and moved to main orders. Order Number: ' . $orderNo);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

}

