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

        // Check table status if needed
        if ($tempOrder->table && $tempOrder->table->status != 'AVAILABLE') {
            return redirect()->back()->with('error', 'Table not available');
        }
        $restaurantId = auth()->user()->restaurant_id;
        $today = Carbon::now()->format('Ymd');
        // count today's orders for this restaurant
        $todayCount = OrderManage::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', Carbon::today())
            ->count() + 1;

        // pad sequence number (0001, 0002…)
        $sequence = str_pad($todayCount, 4, '0', STR_PAD_LEFT);

        // final order number
        $orderNo = "ORD-{$restaurantId}-{$today}-{$sequence}";

        // Create main order using new + save
        $order = new OrderManage();
        $order->table_id       = $tempOrder->table_id;
        $order->customer_name  = $tempOrder->customer_name;
        $order->customer_phone = $tempOrder->customer_phone;
        $order->order_id       = $orderNo;
        $order->order_type     = $tempOrder->order_type;
        $order->total_amount   = $tempOrder->total_amount;
        $order->gst_amount     = $tempOrder->gst_amount;
        $order->grand_total    = $tempOrder->grand_total;
        $order->discount       = $tempOrder->discount;
        $order->remarks        = $tempOrder->remarks;
        $order->order_status   = 'PENDING';
        $order->payment_status = 'PENDING';
        $order->restaurant_id  = $tempOrder->restaurant_id;
        $order->user_id        = $tempOrder->user_id;
        $order->created_by     = auth()->id();
        $order->save();

        // Move items
        foreach ($tempOrder->items as $item) {
            $orderItem = new OrderItems();
            $orderItem->order_id       = $order->id;
            $orderItem->subcategory_id = $item->subcategory_id;
            $orderItem->quantity       = $item->quantity;
            $orderItem->price          = $item->price;
            $orderItem->gst_rate       = $item->gst_rate;
            $orderItem->total_amount   = $item->total_amount;
            $orderItem->order_status   = 'PENDING';
            $orderItem->restaurant_id  = $item->restaurant_id;
            $orderItem->user_id        = auth()->user()->id;
            $orderItem->save();
        }


        TableManage::where('id',$tempOrder->table_id)->update(['table_status'=>'OCCUPIED','order_id'=>$order->id]);
        // Delete temp order
        $tempOrder->delete();

        DB::commit();
        return redirect()->route('temp.orders')->with('success', 'Order approved and moved to main orders.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
    }
}

}

