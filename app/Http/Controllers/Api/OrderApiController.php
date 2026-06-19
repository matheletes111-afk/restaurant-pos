<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableManage;
use App\Models\Category;
use App\Models\User;
use App\Models\SubCategory;
use App\Models\OrderToPayment;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\RestaurantMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends Controller
{
    /**
     * List all tables and active orders
     */
    public function index(Request $request)
    {
        try {
            $restaurantId = $request->user()->restaurant_id;
            
            $tables = TableManage::where('restaurant_id', $restaurantId)
                ->where('status', 'A')
                ->with(['activeOrders' => function($q) {
                    $q->orderBy('created_at', 'desc');
                }])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Tables and active orders retrieved successfully',
                'data' => $tables
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get categories, active tables, and restaurant settings for order creation
     */
    public function getCreateData(Request $request, $table_id = null)
    {
        try {
            $restaurantId = $request->user()->restaurant_id;
            $takeaway = false;
            $table = null;

            if ($table_id === 'TAKEAWAY') {
                $takeaway = true;
            } elseif ($table_id) {
                $table = TableManage::find($table_id);
                if ($table && $table->restaurant_id != $restaurantId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized Access',
                        'data' => null
                    ], 403);
                }
            }

            $restaurant = RestaurantMaster::find($restaurantId);
            $gstin = $restaurant->gstin ?? null;
            $gstPercentage = $restaurant->gst_percentage ?? 0;
            $isGstRegistered = !empty($gstin);

            $categories = Category::where('restaurant_id', $restaurantId)
                ->with(['subcategories' => function($query) {
                    $query->select('id', 'category_id', 'name', 'price', 'gst_rate', 'food_type', 'discount_percentage', 'status');
                }])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Create order metadata retrieved successfully',
                'data' => [
                    'takeaway' => $takeaway,
                    'table' => $table,
                    'restaurant_gstin' => $gstin,
                    'restaurant_gst_percentage' => $gstPercentage,
                    'is_gst_registered' => $isGstRegistered,
                    'categories' => $categories,
                    'payment_methods' => ['Cash', 'UPI', 'Card']
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Store new order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order_items' => 'required|array|min:1',
            'order_items.*.id' => 'required|integer|exists:sub_categories,id',
            'order_items.*.qty' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric|min:0',
            'order_items.*.item_discount' => 'nullable|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0|max:100',
            'table_id' => 'nullable|integer|exists:table_manages,id',
            'payment_status' => 'nullable|string|in:PENDING,PAID',
            'payment_method' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $restaurantId = $request->user()->restaurant_id;
        $userId = $request->user()->id;
        $isTakeaway = empty($request->table_id);
        
        // Get restaurant GST info
        $restaurant = RestaurantMaster::find($restaurantId);
        $restaurantGstin = $restaurant->gstin ?? null;
        $restaurantGstPercentage = $restaurant->gst_percentage ?? 0;
        $isGstRegistered = !empty($restaurantGstin);
        
        $orderDiscountPercent = floatval($request->discount ?? 0);
        
        // Calculate GST for each item using restaurant GST percentage
        $calculatedItems = [];
        foreach ($request->order_items as $item) {
            $itemDiscount = isset($item['item_discount']) ? floatval($item['item_discount']) : 0;
            $calculatedItems[] = $this->calculateItemGST(
                floatval($item['price']),
                intval($item['qty']),
                $itemDiscount,
                $restaurantGstPercentage,
                $isGstRegistered
            );
        }
        
        // Calculate order totals
        $totals = $this->calculateOrderTotals($calculatedItems, $orderDiscountPercent, $isGstRegistered);
        
        // Generate order number
        $todayCount = OrderManage::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', Carbon::today())
            ->count() + 1;
        $prefix = $this->getRestaurantPrefix($restaurantId);
        $dateStr = Carbon::now()->format('ymd');
        $orderNo = "{$prefix}-{$dateStr}-" . str_pad($todayCount, 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        
        try {
            // Create order
            $order = new OrderManage();
            $order->customer_name = $request->customer_name;
            $order->customer_phone = $request->customer_phone;
            $order->order_id = $orderNo;
            $order->table_id = $request->table_id;
            $order->order_type = $isTakeaway ? 'TAKEAWAY' : 'DINE_IN';
            
            // Store amounts
            $order->total_amount = $totals['original_subtotal'];
            $order->taxable_amount = $totals['total_taxable'];
            $order->gst_amount = $totals['total_gst'];
            $order->cgst_amount = $totals['total_cgst'];
            $order->sgst_amount = $totals['total_sgst'];
            $order->igst_amount = $totals['total_igst'];
            
            $order->discount = $totals['order_discount_amount']; // Discount amount in rupees
            $order->discount_percentage = $orderDiscountPercent; // Discount percentage
            
            $order->grand_total = $totals['final_total'];
            $order->round_off = $totals['round_off'];
            
            // GST Bill tracking
            $order->is_gst_bill = $isGstRegistered ? 'YES' : 'NO';
            $order->restaurant_gst_percentage = $restaurantGstPercentage;
            $order->restaurant_gstin = $restaurantGstin;
            
            // Payment info
            if ($isTakeaway) {
                $order->amount_paid = $request->payment_status === 'PAID' ? $totals['final_total'] : 0;
                $order->payment_status = $request->payment_status ?? 'PENDING';
                $order->payment_method = $request->payment_method ?? null;
            } else {
                $order->payment_status = 'PENDING';
                $order->payment_method = null;
                $order->amount_paid = 0;
            }
            
            $order->remarks = $request->remarks ?? null;
            $order->order_status = 'PENDING';
            $order->restaurant_id = $restaurantId;
            $order->user_id = $userId;
            $order->save();
       
            // Generate KOT number for this order placement
            $kotNo = $this->generateKOTNumber($restaurantId);

            // Save order items with all GST details
            foreach ($request->order_items as $index => $item) {
                $calc = $calculatedItems[$index];
                
                $orderItem = new OrderItems();
                $orderItem->order_id = $order->id;
                $orderItem->subcategory_id = $item['id'];
                $orderItem->quantity = $calc['quantity'];
                $orderItem->price = $calc['original_price'];
                $orderItem->discounted_price = $calc['discounted_price'];
                $orderItem->item_discount_percentage = $calc['item_discount_percentage'];
                $orderItem->taxable_amount = $calc['taxable_amount'];
                $orderItem->gst_rate = $calc['gst_rate'];
                $orderItem->gst_amount = $calc['gst_amount'];
                $orderItem->cgst_amount = $calc['cgst_amount'];
                $orderItem->sgst_amount = $calc['sgst_amount'];
                $orderItem->igst_amount = $calc['igst_amount'];
                $orderItem->total_amount = $calc['total_amount'];
                $orderItem->order_status = 'PENDING';
                $orderItem->is_new = 1;
                $orderItem->restaurant_id = $restaurantId;
                $orderItem->user_id = $userId;
                $orderItem->kot_no = $kotNo;
                $orderItem->save();
            }

            // Update table status if dine-in
            if ($request->table_id) {
                TableManage::where('id', $request->table_id)->update([
                    'table_status' => 'OCCUPIED',
                    'order_id' => $order->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_no' => $order->order_id,
                    'final_total' => $totals['final_total'],
                    'discount_amount' => $totals['order_discount_amount'],
                    'discount_percentage' => $orderDiscountPercent,
                    'order_type' => $order->order_type,
                    'payment_status' => $order->payment_status,
                    'invoice_url' => route('order.invoice', $order->id),
                    'receipt_pdf_url' => route('order.receipt.pdf', $order->id)
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving order: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Show single order details with payments and calculations
     */
    public function show(Request $request, $id)
    {
        try {
            $restaurantId = $request->user()->restaurant_id;
            
            $order = OrderManage::with(['orderItems.subcategory', 'table'])->find($id);

            if (!$order || $order->restaurant_id != $restaurantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or unauthorized',
                    'data' => null
                ], 404);
            }

            $payments = OrderToPayment::where('order_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalPaid = $payments->sum('amount');
            $balanceDue = $order->grand_total - $totalPaid;

            return response()->json([
                'success' => true,
                'message' => 'Order details retrieved successfully',
                'data' => [
                    'order' => $order,
                    'payments' => $payments,
                    'total_paid' => $totalPaid,
                    'balance_due' => $balanceDue,
                    'invoice_url' => route('order.invoice', $order->id),
                    'receipt_pdf_url' => route('order.receipt.pdf', $order->id)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Update order details, add/remove items
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_phone' => 'nullable|string|max:20',
            'remarks' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string|in:PENDING,PAID,PARTIAL',
            'amount_paid' => 'nullable|numeric|min:0',
            'order_complete' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0|max:100',
            'order_items' => 'nullable|array',
            'order_items.*.id' => 'required_with:order_items|integer|exists:sub_categories,id',
            'order_items.*.qty' => 'required_with:order_items|integer|min:1',
            'order_items.*.price' => 'required_with:order_items|numeric|min:0',
            'order_items.*.item_discount' => 'nullable|numeric|min:0|max:100',
            'delete_item_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $restaurantId = $request->user()->restaurant_id;
        $userId = $request->user()->id;

        DB::beginTransaction();
        try {
            $order = OrderManage::findOrFail($id);

            if ($order->restaurant_id != $restaurantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized Access',
                    'data' => null
                ], 403);
            }

            // Update order details
            $order->customer_phone = $request->customer_phone ?? $order->customer_phone;
            $order->remarks = $request->remarks ?? $order->remarks;
            $order->payment_method = $request->payment_method ?? $order->payment_method;
            $order->payment_status = $request->payment_status ?? $order->payment_status;
            
            if ($request->has('amount_paid') && $request->amount_paid !== null) {
                $order->amount_paid = floatval($request->amount_paid);
            }
            
            if ($request->has('order_complete')) {
                $order->order_complete = $request->order_complete;
            }
            
            $orderDiscountPercent = floatval($request->discount ?? $order->discount_percentage);
            $order->discount_percentage = $orderDiscountPercent;
            $order->save();

            // Get restaurant GST info for new items
            $restaurant = RestaurantMaster::find($restaurantId);
            $restaurantGstPercentage = $restaurant->gst_percentage ?? 0;
            $isGstRegistered = !empty($restaurant->gstin);

            // Handle new item additions
            if ($request->has('order_items') && is_array($request->order_items)) {
                $kotNo = $this->generateKOTNumber($restaurantId);
                foreach ($request->order_items as $item) {
                    $itemDiscount = isset($item['item_discount']) ? floatval($item['item_discount']) : 0;
                    $calc = $this->calculateItemGST(
                        floatval($item['price']),
                        intval($item['qty']),
                        $itemDiscount,
                        $restaurantGstPercentage,
                        $isGstRegistered
                    );
                    
                    OrderItems::create([
                        'order_id' => $id,
                        'subcategory_id' => $item['id'],
                        'quantity' => $calc['quantity'],
                        'price' => $calc['original_price'],
                        'discounted_price' => $calc['discounted_price'],
                        'item_discount_percentage' => $calc['item_discount_percentage'],
                        'taxable_amount' => $calc['taxable_amount'],
                        'gst_rate' => $calc['gst_rate'],
                        'gst_amount' => $calc['gst_amount'],
                        'cgst_amount' => $calc['cgst_amount'],
                        'sgst_amount' => $calc['sgst_amount'],
                        'igst_amount' => $calc['igst_amount'],
                        'total_amount' => $calc['total_amount'],
                        'restaurant_id' => $restaurantId,
                        'user_id' => $userId,
                        'order_status' => 'PENDING',
                        'is_new' => 1,
                        'kot_no' => $kotNo
                    ]);
                }
            }

            // Handle item deletion from request input
            if ($request->has('delete_item_id')) {
                OrderItems::where('id', $request->delete_item_id)
                    ->where('restaurant_id', $restaurantId)
                    ->delete();
            }

            // Recalculate all totals from existing items
            $items = OrderItems::where('order_id', $id)->get();
            $originalSubtotal = 0;
            $totalTaxable = 0;
            $totalGst = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;
            
            foreach ($items as $item) {
                $originalSubtotal += $item->price * $item->quantity;
                $totalTaxable += $item->taxable_amount;
                $totalGst += $item->gst_amount;
                $totalCgst += $item->cgst_amount;
                $totalSgst += $item->sgst_amount;
                $totalIgst += $item->igst_amount;
            }
            
            // Apply order discount
            $totalBeforeDiscount = $totalTaxable + $totalGst;
            $discountAmount = ($totalBeforeDiscount * $orderDiscountPercent) / 100;
            $grandTotal = $totalBeforeDiscount - $discountAmount;
            $finalTotal = round($grandTotal);
            $roundOff = $finalTotal - $grandTotal;

            // Update order with recalculated totals
            $order->total_amount = $originalSubtotal;
            $order->taxable_amount = $totalTaxable;
            $order->gst_amount = $totalGst;
            $order->cgst_amount = $totalCgst;
            $order->sgst_amount = $totalSgst;
            $order->igst_amount = $totalIgst;
            
            $order->discount = $discountAmount; // Discount amount in rupees
            $order->discount_percentage = $orderDiscountPercent; // Discount percentage
            
            $order->grand_total = $finalTotal;
            $order->round_off = $roundOff;
            $order->save();

            if ($order->table_id) {
                $this->updateTableStatus($order->table_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'data' => [
                    'final_total' => $finalTotal,
                    'amount_paid' => $order->amount_paid,
                    'round_off' => $roundOff,
                    'subtotal' => $originalSubtotal,
                    'total_taxable' => $totalTaxable,
                    'total_gst' => $totalGst,
                    'discount_amount' => $discountAmount,
                    'discount_percentage' => $orderDiscountPercent,
                    'invoice_url' => route('order.invoice', $order->id)
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Delete order item and recalculate
     */
    public function deleteOrderItem(Request $request, $order_id, $item_id)
    {
        $restaurantId = $request->user()->restaurant_id;
        
        $order = OrderManage::find($order_id);
        if (!$order || $order->restaurant_id != $restaurantId) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized'
            ], 404);
        }

        $item = OrderItems::where('id', $item_id)->where('order_id', $order_id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $item->delete();

            // Recalculate all totals from existing items
            $items = OrderItems::where('order_id', $order_id)->get();
            $originalSubtotal = 0;
            $totalTaxable = 0;
            $totalGst = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;
            
            foreach ($items as $itm) {
                $originalSubtotal += $itm->price * $itm->quantity;
                $totalTaxable += $itm->taxable_amount;
                $totalGst += $itm->gst_amount;
                $totalCgst += $itm->cgst_amount;
                $totalSgst += $itm->sgst_amount;
                $totalIgst += $itm->igst_amount;
            }
            
            $orderDiscountPercent = floatval($order->discount_percentage);
            // Apply order discount
            $totalBeforeDiscount = $totalTaxable + $totalGst;
            $discountAmount = ($totalBeforeDiscount * $orderDiscountPercent) / 100;
            $grandTotal = $totalBeforeDiscount - $discountAmount;
            $finalTotal = round($grandTotal);
            $roundOff = $finalTotal - $grandTotal;

            // Update order with recalculated totals
            $order->total_amount = $originalSubtotal;
            $order->taxable_amount = $totalTaxable;
            $order->gst_amount = $totalGst;
            $order->cgst_amount = $totalCgst;
            $order->sgst_amount = $totalSgst;
            $order->igst_amount = $totalIgst;
            
            $order->discount = $discountAmount;
            $order->grand_total = $finalTotal;
            $order->round_off = $roundOff;
            $order->save();

            if ($order->table_id) {
                $this->updateTableStatus($order->table_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted and order recalculated successfully',
                'data' => [
                    'final_total' => $finalTotal,
                    'subtotal' => $originalSubtotal,
                    'discount_amount' => $discountAmount
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payments for order
     */
    public function getPayments(Request $request, $order_id)
    {
        $restaurantId = $request->user()->restaurant_id;
        $order = OrderManage::find($order_id);
        
        if (!$order || $order->restaurant_id != $restaurantId) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized'
            ], 404);
        }

        try {
            $payments = OrderToPayment::where('order_id', $order_id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $totalPaid = $payments->sum('amount');
            $balanceDue = $order->grand_total - $totalPaid;
            
            return response()->json([
                'success' => true,
                'message' => 'Payments retrieved successfully',
                'data' => [
                    'payments' => $payments,
                    'total_paid' => $totalPaid,
                    'balance_due' => $balanceDue
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add payment to order
     */
    public function addPayment(Request $request, $order_id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:CASH,UPI,CARD,BANK_TRANSFER,OTHER',
            'transaction_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $restaurantId = $request->user()->restaurant_id;
        $userId = $request->user()->id;
        $order = OrderManage::find($order_id);

        if (!$order || $order->restaurant_id != $restaurantId) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $currentPaid = OrderToPayment::where('order_id', $order_id)->sum('amount');
            $newTotal = $currentPaid + $request->amount;
            
            if ($newTotal > $order->grand_total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds remaining balance'
                ], 400);
            }
            
            // Create payment
            $payment = OrderToPayment::create([
                'order_id' => $order_id,
                'restaurant_id' => $restaurantId,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_no' => $request->transaction_no,
                'remarks' => $request->remarks,
                'payment_date' => now(),
                'created_by' => $userId
            ]);
            
            // Update order payment status
            $totalPaid = OrderToPayment::where('order_id', $order_id)->sum('amount');
            
            if ($totalPaid >= $order->grand_total) {
                $order->payment_status = 'PAID';
                $order->amount_paid = $order->grand_total;
                $order->order_complete = 'DONE';
            } elseif ($totalPaid > 0) {
                $order->payment_status = 'PARTIAL';
                $order->amount_paid = $totalPaid;
            }
            
            $order->save();
            
            if ($order->table_id) {
                $this->updateTableStatus($order->table_id);
            }
            
            DB::commit();

            $payments = OrderToPayment::where('order_id', $order_id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully',
                'data' => [
                    'payments' => $payments,
                    'total_paid' => $totalPaid,
                    'balance_due' => $order->grand_total - $totalPaid
                ]
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete payment
     */
    public function deletePayment(Request $request, $payment_id)
    {
        $restaurantId = $request->user()->restaurant_id;
        $payment = OrderToPayment::find($payment_id);

        if (!$payment || $payment->restaurant_id != $restaurantId) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found or unauthorized'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $order = OrderManage::findOrFail($payment->order_id);
            $payment->delete();
            
            // Update order payment status
            $totalPaid = OrderToPayment::where('order_id', $order->id)->sum('amount');
            $balanceDue = $order->grand_total - $totalPaid;
            
            if ($totalPaid >= $order->grand_total) {
                $order->payment_status = 'PAID';
                $order->amount_paid = $order->grand_total;
            } elseif ($totalPaid > 0) {
                $order->payment_status = 'PARTIAL';
                $order->amount_paid = $totalPaid;
            } else {
                $order->payment_status = 'PENDING';
                $order->amount_paid = 0;
            }
            
            $order->save();
            
            if ($order->table_id) {
                $this->updateTableStatus($order->table_id);
            }

            DB::commit();
            
            $payments = OrderToPayment::where('order_id', $order->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully',
                'data' => [
                    'payments' => $payments,
                    'total_paid' => $totalPaid,
                    'balance_due' => $balanceDue
                ]
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record final payment method and status
     */
    public function submitPayment(Request $request, $order_id)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'payment_status' => 'required|in:PENDING,PAID',
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $restaurantId = $request->user()->restaurant_id;
        $order = OrderManage::find($order_id);

        if (!$order || $order->restaurant_id != $restaurantId) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $order->payment_method = $request->payment_method;
            $order->remarks = $request->remarks;
            $order->payment_status = $request->payment_status;
            
            if ($request->payment_status == 'PAID') {
                $order->amount_paid = $order->grand_total;
                $order->order_complete = 'DONE';
            }
            
            $order->save();

            if ($order->table_id) {
                $this->updateTableStatus($order->table_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully',
                'data' => $order
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kitchen view order items
     */
    public function kitchen(Request $request)
    {
        try {
            $restaurantId = $request->user()->restaurant_id;

            $query = OrderItems::with(['order', 'subcategory', 'order.table'])
                ->where('restaurant_id', $restaurantId);

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $toDate = Carbon::parse($request->to_date)->endOfDay();
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            } else {
                $query->where('created_at', '>=', Carbon::now()->subDays(3)->startOfDay());
            }

            if ($request->filled('status') && $request->status != 'all') {
                $query->where('order_status', $request->status);
            }

            if ($request->filled('table_id')) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->where('table_id', $request->table_id);
                });
            }

            $orderItems = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Kitchen items retrieved successfully',
                'data' => $orderItems
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Update kitchen item status
     */
    public function updateKitchenStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'order_status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $restaurantId = $request->user()->restaurant_id;
            $item = OrderItems::where('id', $request->id)
                ->where('restaurant_id', $restaurantId)
                ->first();

            if ($item) {
                $item->order_status = $request->order_status;
                $item->is_new = 0;
                $item->save();

                return response()->json([
                    'success' => true, 
                    'message' => 'Kitchen item status updated successfully.'
                ], 200);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Item not found or unauthorized.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh new pending orders count for kitchen
     */
    public function refreshOrders(Request $request)
    {
        try {
            $restaurantId = $request->user()->restaurant_id;
            $newOrdersCount = OrderItems::where('created_at', '>=', Carbon::now()->subSeconds(30))
                ->where('order_status', 'PENDING')
                ->where('is_new', 1)
                ->where('restaurant_id', $restaurantId)
                ->count();
                
            return response()->json([
                'success' => true,
                'new_orders' => $newOrdersCount > 0,
                'count' => $newOrdersCount
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // --- Private Helper Methods ---

    /**
     * Calculate GST for a single item
     */
    private function calculateItemGST($originalPrice, $quantity, $itemDiscountPercent = 0, $restaurantGstPercentage = 0, $isGstRegistered = false)
    {
        $discountedPrice = $originalPrice - ($originalPrice * $itemDiscountPercent / 100);
        $taxableAmount = $discountedPrice * $quantity;
        
        $gstRate = $isGstRegistered ? $restaurantGstPercentage : 0;
        $gstAmount = ($taxableAmount * $gstRate) / 100;
        
        $halfGstRate = $gstRate / 2;
        $cgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $sgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $igstAmount = 0;
        
        $totalAmount = $taxableAmount + $gstAmount;
        
        return [
            'original_price' => $originalPrice,
            'quantity' => $quantity,
            'discounted_price' => $discountedPrice,
            'item_discount_percentage' => $itemDiscountPercent,
            'taxable_amount' => $taxableAmount,
            'gst_rate' => $gstRate,
            'gst_amount' => $gstAmount,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'igst_amount' => $igstAmount,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Calculate order totals
     */
    private function calculateOrderTotals($items, $orderDiscountPercent = 0, $isGstRegistered = false)
    {
        $originalSubtotal = 0;
        $totalTaxable = 0;
        $totalGst = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        
        foreach ($items as $item) {
            $quantity = isset($item['quantity']) ? $item['quantity'] : (isset($item['qty']) ? $item['qty'] : 0);
            $originalSubtotal += $item['original_price'] * $quantity;
            $totalTaxable += $item['taxable_amount'];
            if ($isGstRegistered) {
                $totalGst += $item['gst_amount'];
                $totalCgst += $item['cgst_amount'];
                $totalSgst += $item['sgst_amount'];
                $totalIgst += $item['igst_amount'];
            }
        }
        
        $orderDiscountPercent = floatval($orderDiscountPercent);
        $totalBeforeDiscount = $totalTaxable + $totalGst;
        $orderDiscountAmount = ($totalBeforeDiscount * $orderDiscountPercent) / 100;
        $grandTotal = $totalBeforeDiscount - $orderDiscountAmount;
        $finalTotal = round($grandTotal);
        $roundOff = $finalTotal - $grandTotal;
        
        return [
            'original_subtotal' => $originalSubtotal,
            'total_taxable' => $totalTaxable,
            'total_gst' => $totalGst,
            'total_cgst' => $totalCgst,
            'total_sgst' => $totalSgst,
            'total_igst' => $totalIgst,
            'order_discount_percentage' => $orderDiscountPercent,
            'order_discount_amount' => $orderDiscountAmount,
            'grand_total' => $grandTotal,
            'final_total' => $finalTotal,
            'round_off' => $roundOff,
        ];
    }

    /**
     * Generate the next KOT number for a restaurant
     */
    private function generateKOTNumber($restaurantId)
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        
        $latestItem = OrderItems::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->whereNotNull('kot_no')
            ->orderBy('id', 'desc')
            ->first();
            
        if ($latestItem && preg_match('/KOT-\d{6}-(\d+)/', $latestItem->kot_no, $matches)) {
            $nextSequence = intval($matches[1]) + 1;
        } else {
            $nextSequence = 1;
        }
        
        $dateStr = Carbon::now()->format('ymd');
        return "KOT-{$dateStr}-" . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Release or update table status based on remaining active orders
     */
    private function updateTableStatus($tableId, $excludeOrderId = null)
    {
        if (!$tableId) return;
        
        $query = OrderManage::where('table_id', $tableId)
            ->where('order_complete', '!=', 'DONE');
            
        if ($excludeOrderId) {
            $query->where('id', '!=', $excludeOrderId);
        }
        
        $nextActiveOrder = $query->first();
        
        if ($nextActiveOrder) {
            TableManage::where('id', $tableId)->update([
                'table_status' => 'OCCUPIED',
                'order_id' => $nextActiveOrder->id
            ]);
        } else {
            TableManage::where('id', $tableId)->update([
                'table_status' => 'AVAILABLE',
                'order_id' => null
            ]);
        }
    }

    /**
     * Get short prefix from restaurant name
     */
    private function getRestaurantPrefix($restaurantId)
    {
        $restaurant = RestaurantMaster::find($restaurantId);
        $prefix = $restaurant ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $restaurant->name), 0, 3)) : 'ORD';
        return empty($prefix) ? 'ORD' : $prefix;
    }
}
