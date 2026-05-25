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

    // Get restaurant GST info
    $restaurant = RestaurantMaster::find($request->restaurant_id);
    $restaurantGstin = $restaurant->gstin ?? null;
    $restaurantGstPercentage = $restaurant->gst_percentage ?? 0;
    $isGstRegistered = !empty($restaurantGstin);

    $originalSubtotal = 0;
    $totalTaxable = 0;
    $totalGst = 0;
    $totalCgst = 0;
    $totalSgst = 0;
    $totalIgst = 0;
    $totalItemDiscount = 0;

    $calculatedItems = [];

    foreach ($request->order_items as $item) {
        $itemDiscount = isset($item['item_discount']) ? floatval($item['item_discount']) : 0;
        $originalPrice = floatval($item['price']);
        $quantity = intval($item['qty']);
        
        // Calculate discounted price
        $discountedPrice = $originalPrice - ($originalPrice * $itemDiscount / 100);
        $taxableAmount = $discountedPrice * $quantity;
        
        // Calculate GST on discounted price
        $gstRate = $isGstRegistered ? $restaurantGstPercentage : 0;
        $gstAmount = ($taxableAmount * $gstRate) / 100;
        
        // Split GST
        $halfGstRate = $gstRate / 2;
        $cgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $sgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $totalAmount = $taxableAmount + $gstAmount;
        
        $originalSubtotal += $originalPrice * $quantity;
        $totalTaxable += $taxableAmount;
        $totalGst += $gstAmount;
        $totalCgst += $cgstAmount;
        $totalSgst += $sgstAmount;
        $totalItemDiscount += ($originalPrice * $quantity) - $taxableAmount;
        
        $calculatedItems[] = [
            'subcategory_id' => $item['id'],
            'quantity' => $quantity,
            'price' => $originalPrice,
            'discounted_price' => $discountedPrice,
            'item_discount_percentage' => $itemDiscount,
            'taxable_amount' => $taxableAmount,
            'gst_rate' => $gstRate,
            'gst_amount' => $gstAmount,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'igst_amount' => 0,
            'total_amount' => $totalAmount,
        ];
    }

    // Generate order number
    $restaurantId = $request->restaurant_id;
    $today = Carbon::now()->format('Ymd');
    $todayCount = OrderManage::where('restaurant_id', $restaurantId)
        ->whereDate('created_at', Carbon::today())
        ->count() + 1;
    $sequence = str_pad($todayCount, 4, '0', STR_PAD_LEFT);
    $orderNo = "ORD-{$restaurantId}-{$today}-{$sequence}";

    $tempOrder = TempOrder::create([
        'table_id' => $request->table_id,
        'order_id' => $orderNo,
        'restaurant_id' => $request->restaurant_id,
        'customer_name' => $request->customer_name,
        'customer_phone' => $request->customer_phone,
        'order_type' => 'DINE_IN',
        'total_amount' => $originalSubtotal,
        'taxable_amount' => $totalTaxable,
        'gst_amount' => $totalGst,
        'cgst_amount' => $totalCgst,
        'sgst_amount' => $totalSgst,
        'igst_amount' => $totalIgst,
        'discount' => $totalItemDiscount,
        'discount_percentage' => 0,
        'grand_total' => $totalTaxable + $totalGst,
        'round_off' => 0,
        'is_gst_bill' => $isGstRegistered ? 'YES' : 'NO',
        'restaurant_gst_percentage' => $restaurantGstPercentage,
        'restaurant_gstin' => $restaurantGstin,
        'remarks' => $request->remarks ?? null,
        'order_status' => 'PENDING',
        'payment_status' => 'PENDING',
        'user_id' => null,
    ]);

    foreach ($calculatedItems as $item) {
        TempOrderItem::create([
            'temp_order_id' => $tempOrder->id,
            'subcategory_id' => $item['subcategory_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'discounted_price' => $item['discounted_price'],
            'item_discount_percentage' => $item['item_discount_percentage'],
            'taxable_amount' => $item['taxable_amount'],
            'gst_rate' => $item['gst_rate'],
            'gst_amount' => $item['gst_amount'],
            'cgst_amount' => $item['cgst_amount'],
            'sgst_amount' => $item['sgst_amount'],
            'igst_amount' => $item['igst_amount'],
            'total_amount' => $item['total_amount'],
            'order_status' => 'PENDING',
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
