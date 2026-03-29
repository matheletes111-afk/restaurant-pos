<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\TempOrder;
use App\Models\TempOrderItem;
use App\Models\RestaurantMaster;
use App\Models\OrderManage;
use App\Models\OrderItems;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class TempOrderController extends Controller
{
    public function create($table_id, $restaurant_id)
    {
        $categories = Category::where('restaurant_id', $restaurant_id)
                                ->with('subcategories')
                                ->get();
        $restaurant_details = RestaurantMaster::where('id',$restaurant_id)->first();                      
        return view('temp_order', compact('categories', 'table_id', 'restaurant_id','restaurant_details'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'order_items' => 'required|array|min:1',
        ]);

        $subtotal = 0;
        $gstTotal = 0;

        foreach ($request->order_items as $item) {
            $subtotal += $item['price'] * $item['qty'];
            $gstTotal += ($item['price'] * $item['qty'] * $item['gst']) / 100;
        }

        $discountAmount = (($subtotal + $gstTotal) * ($request->discount ?? 0)) / 100;
        $grandTotal = $subtotal + $gstTotal - $discountAmount;

        $restaurantId = $request->restaurant_id;
        $today = Carbon::now()->format('Ymd');

        // count today's orders for this restaurant
        $todayCount = OrderManage::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', Carbon::today())
            ->count() + 1;

        // pad sequence number (0001, 0002…)
        $sequence = str_pad($todayCount, 4, '0', STR_PAD_LEFT);

        // final order number
        $orderNo = "ORD-{$restaurantId}-{$today}-{$sequence}";

        $tempOrder = TempOrder::create([
            'table_id' => $request->table_id,
            'order_id' => $orderNo,
            'restaurant_id' => $request->restaurant_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'order_type' => 'DINE_IN',
            'total_amount' => $subtotal,
            'gst_amount' => $gstTotal,
            'grand_total' => $grandTotal,
            'discount' => $request->discount ?? 0,
            'remarks' => $request->remarks ?? null,
        ]);

        foreach ($request->order_items as $item) {
            TempOrderItem::create([
                'temp_order_id' => $tempOrder->id,
                'subcategory_id' => $item['id'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
                'gst_rate' => $item['gst'],
                'total_amount' => ($item['price'] * $item['qty']) + (($item['price'] * $item['qty'] * $item['gst']) / 100),
                'restaurant_id' => $request->restaurant_id,
            ]);
        }

        return response()->json([
            'status' => true,
            'redirect' => route('order.success', $tempOrder->id)
        ]);
    }

    public function success($id)
    {
       return view('order-success');
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
                $orderItem->user_id        = $item->user_id;
                $orderItem->save();
            }

            // Delete temp order
            $tempOrder->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Order approved and moved to main orders.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

}
