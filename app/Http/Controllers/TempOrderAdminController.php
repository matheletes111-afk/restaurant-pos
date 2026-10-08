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
        $orders = TempOrder::with(['items.menuItem', 'table_details'])
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('order_status', 'PENDING')
            ->orderBy('id', 'DESC')
            ->get();

        return view('temp_orders.index', compact('orders'));
    }


    public function view($id)
    {
        $order = TempOrder::with(['items.menuItem', 'table_details'])
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->findOrFail($id);

        if (!$order->is_read) {
            $order->update([
                'is_read' => true,
                'read_at' => Carbon::now()
            ]);
        }

        $table = TableManage::where('id', $order->table_id)->first();
        return view('temp_orders.view', compact('order', 'table'));
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

    // Delete the item
    $item->delete();

    // Recalculate totals from remaining items
    $remainingItems = $order->items()->get();
    if ($remainingItems->isEmpty()) {
        $order->order_status = 'REJECTED';
        $order->total_amount = 0;
        $order->taxable_amount = 0;
        $order->gst_amount = 0;
        $order->cgst_amount = 0;
        $order->sgst_amount = 0;
        $order->igst_amount = 0;
        $order->grand_total = 0;
        $order->discount = 0;
        $order->discount_percentage = 0;
        $order->save();
    } else {
        $subtotal = 0;
        $taxable = 0;
        $gst = 0;
        $cgst = 0;
        $sgst = 0;
        $discount = 0;
        foreach ($remainingItems as $remItem) {
            $remAddonsCost = 0;
            if (!empty($remItem->addons_list) && is_array($remItem->addons_list)) {
                foreach ($remItem->addons_list as $a) {
                    $remAddonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                }
            }
            $basePrice = floatval($remItem->price);
            $qty = max(1, intval($remItem->quantity ?? 1));
            $itemDisc = floatval($remItem->item_discount_percentage ?? 0);
            $discPrice = $basePrice - ($basePrice * $itemDisc / 100);
            $isAddon = empty($remItem->subcategory_id);
            $lineOrig = $isAddon ? ($basePrice * $qty) : (($basePrice * $qty) + $remAddonsCost);
            $lineTax = $isAddon ? ($discPrice * $qty) : (($discPrice * $qty) + $remAddonsCost);
            $gstRate = floatval($remItem->gst_rate ?? 0);
            $lineGst = ($lineTax * $gstRate) / 100;
            $lineCgst = ($lineTax * ($gstRate / 2)) / 100;
            $lineSgst = ($lineTax * ($gstRate / 2)) / 100;

            $subtotal += $lineOrig;
            $taxable += $lineTax;
            $gst += $lineGst;
            $cgst += $lineCgst;
            $sgst += $lineSgst;
            $discount += ($basePrice * $itemDisc / 100) * $qty;
        }
        $order->total_amount = $subtotal;
        $order->taxable_amount = $taxable;
        $order->gst_amount = $gst;
        $order->cgst_amount = $cgst;
        $order->sgst_amount = $sgst;
        $order->igst_amount = 0;
        $order->discount = $discount;
        $order->grand_total = $taxable + $gst;
        $order->save();
    }

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

        // Check if table exists and is active (if dine-in)
        if ($tempOrder->table_id) {
            $table = TableManage::where('restaurant_id', auth()->user()->restaurant_id)->find($tempOrder->table_id);
            if ($table && ($table->table_status == 'INACTIVE' || $table->status == 'I' || $table->status == 'D')) {
                return redirect()->back()->with('error', 'Table is currently inactive or under maintenance.');
            }
        }

        // Generate NEW order number (ignore the one from temp order)
        $restaurantId = auth()->user()->restaurant_id;
        $todayCount = OrderManage::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', Carbon::today())
            ->count() + 1;
        $restaurant = \App\Models\RestaurantMaster::find($restaurantId);
        $prefix = $restaurant ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $restaurant->name), 0, 3)) : 'ORD';
        $prefix = empty($prefix) ? 'ORD' : $prefix;
        $dateStr = Carbon::now()->format('ymd');
        $orderNo = "{$prefix}-{$dateStr}-" . str_pad($todayCount, 3, '0', STR_PAD_LEFT);

        $calculatedSubtotal = 0;
        $calculatedTaxable = 0;
        $calculatedGst = 0;
        $calculatedCgst = 0;
        $calculatedSgst = 0;
        $calculatedDiscount = 0;

        // Pre-calculate accurate totals from items
        $processedItems = [];
        foreach ($tempOrder->items as $item) {
            $basePrice = floatval($item->price);
            $quantity = max(1, intval($item->quantity ?? 1));
            $itemDiscount = floatval($item->item_discount_percentage ?? 0);
            $discountedPrice = floatval($item->discounted_price ?: ($basePrice - ($basePrice * $itemDiscount / 100)));
            $gstRate = floatval($item->gst_rate ?? 0);

            // Read stored item amounts
            $taxableAmount = floatval($item->taxable_amount);
            $totalAmount = floatval($item->total_amount);
            $gstAmount = floatval($item->gst_amount);
            $cgstAmount = floatval($item->cgst_amount);
            $sgstAmount = floatval($item->sgst_amount);

            if ($totalAmount <= 0) {
                $addonsList = $item->addons_list ?? [];
                $addonsCost = 0;
                $isAddon = empty($item->subcategory_id);
                if (!$isAddon && !empty($addonsList) && is_array($addonsList)) {
                    foreach ($addonsList as $a) {
                        $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                    }
                }
                $lineOrig = $isAddon ? ($basePrice * $quantity) : (($basePrice * $quantity) + $addonsCost);
                $taxableAmount = $isAddon ? ($discountedPrice * $quantity) : (($discountedPrice * $quantity) + $addonsCost);
                $gstAmount = ($taxableAmount * $gstRate) / 100;
                $cgstAmount = ($taxableAmount * ($gstRate / 2)) / 100;
                $sgstAmount = ($taxableAmount * ($gstRate / 2)) / 100;
                $totalAmount = $taxableAmount + $gstAmount;
                $lineOrigSubtotal = $lineOrig;
            } else {
                $lineOrigSubtotal = $taxableAmount + (($basePrice * $itemDiscount / 100) * $quantity);
            }

            $calculatedSubtotal += $lineOrigSubtotal;
            $calculatedTaxable += $taxableAmount;
            $calculatedGst += $gstAmount;
            $calculatedCgst += $cgstAmount;
            $calculatedSgst += $sgstAmount;
            $calculatedDiscount += ($basePrice * $itemDiscount / 100) * $quantity;

            $processedItems[] = [
                'item' => $item,
                'quantity' => $quantity,
                'price' => $basePrice,
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

        $orderGrandTotal = floatval($tempOrder->grand_total ?: ($calculatedTaxable + $calculatedGst));
        $orderTotalAmount = floatval($tempOrder->total_amount ?: $calculatedSubtotal);
        $orderTaxableAmount = floatval($tempOrder->taxable_amount ?: $calculatedTaxable);
        $orderGstAmount = floatval($tempOrder->gst_amount ?: $calculatedGst);
        $orderDiscount = floatval($tempOrder->discount ?: $calculatedDiscount);

        // Create main order with all fields (using NEW order number)
        $order = new OrderManage();
        $order->table_id = $tempOrder->table_id;
        $order->customer_name = $tempOrder->customer_name;
        $order->customer_phone = $tempOrder->customer_phone;
        $order->order_id = $orderNo;  // NEW order number, not the temp one
        $order->order_type = $tempOrder->order_type;
        $order->total_amount = $orderTotalAmount;
        $order->taxable_amount = $orderTaxableAmount;
        $order->gst_amount = $orderGstAmount;
        $order->cgst_amount = $calculatedCgst;
        $order->sgst_amount = $calculatedSgst;
        $order->igst_amount = 0;
        $order->discount = $orderDiscount;
        $order->discount_percentage = $tempOrder->discount_percentage;
        $order->grand_total = $orderGrandTotal;
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

        // Generate a single KOT number for all items in this initial approved order batch
        $kotNo = OrderItems::generateNextKotNumber($restaurantId);

        // Move items with all fields
        foreach ($processedItems as $p) {
            $origItem = $p['item'];
            $orderItem = new OrderItems();
            $orderItem->order_id = $order->id;
            $orderItem->subcategory_id = $origItem->subcategory_id;
            $orderItem->quantity = $p['quantity'];
            $orderItem->price = $p['price'];
            $orderItem->addons = $origItem->addons;
            $orderItem->discounted_price = $p['discounted_price'];
            $orderItem->item_discount_percentage = $p['item_discount_percentage'];
            $orderItem->taxable_amount = $p['taxable_amount'];
            $orderItem->gst_rate = $p['gst_rate'];
            $orderItem->gst_amount = $p['gst_amount'];
            $orderItem->cgst_amount = $p['cgst_amount'];
            $orderItem->sgst_amount = $p['sgst_amount'];
            $orderItem->igst_amount = $p['igst_amount'];
            $orderItem->total_amount = $p['total_amount'];
            $orderItem->order_status = 'PENDING';
            $orderItem->restaurant_id = $order->restaurant_id;
            $orderItem->user_id = auth()->id();
            $orderItem->kot_no = $kotNo;
            $orderItem->save();
        }

        // Update table status if dine-in
        if ($tempOrder->table_id) {
            TableManage::where('id', $tempOrder->table_id)->update([
                'table_status' => 'OCCUPIED',
                'order_id' => $order->id
            ]);
        }

        // Update temp order status to APPROVED
        $tempOrder->order_status = 'APPROVED';
        $tempOrder->order_id = $order->id;
        $tempOrder->save();

        DB::commit();

        // Notify kitchen staff of the new approved order
        try {
            $webNotificationService = app(\App\Services\WebNotificationService::class);
            $tableName = isset($table) && $table ? $table->name : ('Table ' . ($order->table_id ?? ''));
            $webNotificationService->notifyKitchenStaffWeb(
                $restaurantId,
                "New Order Approved - {$tableName}",
                "Order #{$orderNo} (KOT #{$kotNo}) approved for {$tableName}",
                ['order_id' => $order->id, 'kot_no' => $kotNo]
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Kitchen notification error on approve: ' . $e->getMessage());
        }

        return redirect()->route('temp.orders')->with('success', 'Order approved and moved to main orders. Order Number: ' . $orderNo);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

public function rejectOrder($id)
{
    DB::beginTransaction();

    try {
        $tempOrder = TempOrder::where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();

        $tempOrder->order_status = 'REJECTED';
        $tempOrder->total_amount = 0;
        $tempOrder->taxable_amount = 0;
        $tempOrder->gst_amount = 0;
        $tempOrder->cgst_amount = 0;
        $tempOrder->sgst_amount = 0;
        $tempOrder->igst_amount = 0;
        $tempOrder->grand_total = 0;
        $tempOrder->discount = 0;
        $tempOrder->discount_percentage = 0;
        $tempOrder->save();
        $tempOrder->items()->delete();

        DB::commit();
        return redirect()->route('temp.orders')->with('success', 'Order #' . ($tempOrder->order_id ?? $tempOrder->id) . ' has been rejected.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

}

