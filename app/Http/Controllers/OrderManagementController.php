<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableManage;
use App\Models\Category;
use App\Models\User;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\RestaurantMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\FirebasePushService;

class OrderManagementController extends Controller
{
    /**
     * Display order management dashboard
     */
    public function index()
    {
        $data['data'] = TableManage::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->get();
        return view('order.index', $data);
    }

    // In OrderManagementController.php - create() method
    public function create($table_id = null)
    {
        $data = [];
        $data['takeaway'] = false;
        $data['table'] = null;

        if ($table_id === 'TAKEAWAY') {
            $data['takeaway'] = true;
        } elseif ($table_id) {
            $data['table'] = TableManage::find($table_id);

            if ($data['table'] && $data['table']->restaurant_id != auth()->user()->restaurant_id) {
                return redirect()->back()->with('error', 'Unauthorized Access');
            }

            if ($data['table'] && $data['table']->table_status === 'OCCUPIED') {
                $order = OrderManage::where('table_id', $table_id)
                    ->where('order_status', 'PENDING')
                    ->latest()
                    ->first();

                if ($order) {
                    return redirect()->route('order.edit', $order->id);
                }
            }
        }

        // Get categories with subcategories including discount_percentage
        $data['categories'] = Category::where('restaurant_id', auth()->user()->restaurant_id)
            ->with(['subcategories' => function($query) {
                $query->select('id', 'category_id', 'name', 'price', 'gst_rate', 'food_type', 'discount_percentage', 'status');
            }])
            ->get();
        
        $data['payment_methods'] = ['Cash', 'UPI', 'Card'];

        return view('order.create', $data);
    }

/**
 * Show edit order form
 */
public function edit($order_id)
{
    $order = OrderManage::with('orderItems.subcategory')->findOrFail($order_id);
    
    if (@$order->restaurant_id != auth()->user()->restaurant_id) {
        return redirect()->back()->with('error', 'Unauthorized Access');
    }

    // Calculate totals with discount first then GST
    $originalSubtotal = 0;
    $totalTaxable = 0;
    $totalGst = 0;
    $totalCgst = 0;
    $totalSgst = 0;
    $totalIgst = 0;
    
    foreach ($order->orderItems as $item) {
        // Get item discount percentage (from database if exists, otherwise 0)
        $itemDiscount = $item->item_discount_percentage ?? 0;
        $discountedPrice = $item->price - ($item->price * $itemDiscount / 100);
        $taxableAmount = $discountedPrice * $item->quantity;
        $gstAmount = ($taxableAmount * ($item->gst_rate ?? 0)) / 100;
        
        $originalSubtotal += $item->price * $item->quantity;
        $totalTaxable += $taxableAmount;
        $totalGst += $gstAmount;
        $totalCgst += $item->cgst_amount ?? 0;
        $totalSgst += $item->sgst_amount ?? 0;
        $totalIgst += $item->igst_amount ?? 0;
    }

    // Apply order level discount
    $discountPercent = $order->discount_percentage ?? 0;
    $totalBeforeDiscount = $totalTaxable + $totalGst;
    $discountAmount = ($totalBeforeDiscount * $discountPercent) / 100;
    $grandTotal = $totalBeforeDiscount - $discountAmount;
    $finalTotal = round($grandTotal);
    $roundOff = $finalTotal - $grandTotal;

    $data['order'] = $order;
    $data['table'] = $order->table_id ? TableManage::find($order->table_id) : null;
    
    // Get categories with subcategories including discount_percentage
    $data['categories'] = Category::where('restaurant_id', auth()->user()->restaurant_id)
        ->with(['subcategories' => function($query) {
            $query->select('id', 'category_id', 'name', 'price', 'gst_rate', 'food_type', 'discount_percentage', 'status');
        }])
        ->get();
    
    $data['payment_methods'] = ['CASH', 'CARD', 'UPI'];
    $data['original_subtotal'] = $originalSubtotal;
    $data['total_taxable'] = $totalTaxable;
    $data['total_gst'] = $totalGst;
    $data['total_cgst'] = $totalCgst;
    $data['total_sgst'] = $totalSgst;
    $data['total_igst'] = $totalIgst;
    $data['discount_percent'] = $discountPercent;
    $data['discount_amount'] = $discountAmount;
    $data['grand_total'] = $grandTotal;
    $data['final_total'] = $finalTotal;
    $data['round_off'] = $roundOff;

    return view('order.edit', $data);
}

    /**
     * Calculate GST for a single item
     * Formula: Apply discount first, then calculate GST on discounted price
     */
    private function calculateItemGST($originalPrice, $quantity, $gstRate, $itemDiscountPercent = 0)
    {
        // Step 1: Apply item discount to get discounted price per item
        $discountedPrice = $originalPrice - ($originalPrice * $itemDiscountPercent / 100);
        
        // Step 2: Calculate taxable amount (discounted price × quantity)
        $taxableAmount = $discountedPrice * $quantity;
        
        // Step 3: Calculate GST on taxable amount
        $gstAmount = ($taxableAmount * $gstRate) / 100;
        
        // Step 4: Split GST into CGST/SGST (50-50 for intra-state)
        $halfGstRate = $gstRate / 2;
        $cgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $sgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $igstAmount = 0;
        
        // Step 5: Calculate total amount (taxable + GST)
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
     * FIXED: Using 'quantity' key instead of 'qty'
     */
    private function calculateOrderTotals($items, $orderDiscountPercent = 0)
    {
        $originalSubtotal = 0;
        $totalTaxable = 0;
        $totalGst = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        
        foreach ($items as $item) {
            // Use 'quantity' key which is set in calculateItemGST
            $quantity = isset($item['quantity']) ? $item['quantity'] : (isset($item['qty']) ? $item['qty'] : 0);
            $originalSubtotal += $item['original_price'] * $quantity;
            $totalTaxable += $item['taxable_amount'];
            $totalGst += $item['gst_amount'];
            $totalCgst += $item['cgst_amount'];
            $totalSgst += $item['sgst_amount'];
            $totalIgst += $item['igst_amount'];
        }
        
        // Apply order discount on total (taxable + GST)
        $totalBeforeDiscount = $totalTaxable + $totalGst;
        $orderDiscountAmount = ($totalBeforeDiscount * $orderDiscountPercent) / 100;
        $grandTotal = $totalBeforeDiscount - $orderDiscountAmount;
        
        // Round off
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
 * Store new order
 * FIXED: Passing quantity correctly to calculateItemGST
 */
public function store(Request $request)
{
    $request->validate([
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'nullable|string|max:20',
        'order_items' => 'required|array|min:1',
        'discount' => 'nullable|numeric|min:0|max:100',
    ]);

    $isTakeaway = empty($request->table_id);
    
    // Calculate GST for each item
    $calculatedItems = [];
    foreach ($request->order_items as $item) {
        $itemDiscount = isset($item['item_discount']) ? floatval($item['item_discount']) : 0;
        $calculatedItems[] = $this->calculateItemGST(
            floatval($item['price']),
            intval($item['qty']),  // 'qty' from frontend
            floatval($item['gst']),
            $itemDiscount
        );
    }
    
    // Calculate order totals
    $orderDiscountPercent = floatval($request->discount ?? 0);
    $totals = $this->calculateOrderTotals($calculatedItems, $orderDiscountPercent);
    
    // Generate order number
    $restaurantId = auth()->user()->restaurant_id;
    $today = Carbon::now()->format('Ymd');
    $todayCount = OrderManage::where('restaurant_id', $restaurantId)
        ->whereDate('created_at', Carbon::today())
        ->count() + 1;
    $sequence = str_pad($todayCount, 4, '0', STR_PAD_LEFT);
    $orderNo = "ORD-{$restaurantId}-{$today}-{$sequence}";

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
        $order->discount = $totals['order_discount_amount'];
        $order->discount_percentage = $orderDiscountPercent;
        $order->grand_total = $totals['final_total'];
        $order->round_off = $totals['round_off'];
        
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
        $order->restaurant_id = auth()->user()->restaurant_id;
        $order->user_id = auth()->user()->id;
        $order->save();
       
        // Save order items with all GST details
        foreach ($request->order_items as $index => $item) {
            $calc = $calculatedItems[$index];
            
            $orderItem = new OrderItems();
            $orderItem->order_id = $order->id;
            $orderItem->subcategory_id = $item['id'];
            $orderItem->quantity = $calc['quantity']; // Use 'quantity' from calc array
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
            $orderItem->restaurant_id = auth()->user()->restaurant_id;
            $orderItem->user_id = auth()->user()->id;
            $orderItem->save();
        }

        // Update table status if dine-in
        if ($request->table_id) {
            TableManage::where('id', $request->table_id)->update([
                'table_status' => 'OCCUPIED',
                'order_id' => $order->id,
            ]);
        }
      
        // Send notification to kitchen staff (commented as per your request)
        // $kitchenStaffs = User::where('role_type', 'Kitchen Staff')
        //     ->whereNotNull('fcm_token')
        //     ->where('restaurant_id', auth()->user()->restaurant_id)
        //     ->where('status', 'A')
        //     ->get();

        // foreach ($kitchenStaffs as $staff) {
        //     FirebasePushService::send(
        //         $staff->fcm_token,
        //         'New Order Received',
        //         'Order #' . $order->id . ' - ' . $request->customer_name,
        //         [
        //             'order_id' => (string) $order->id,
        //             'type' => 'new_order'
        //         ]
        //     );
        // }

        DB::commit();

        // Return different redirect URLs based on order type
        // For TAKEAWAY: redirect to invoice page
        // For DINE_IN: redirect to order management dashboard
        $redirectUrl = $isTakeaway 
            ? route('order.invoice', $order->id) 
            : route('order.management.dashboard');

        return response()->json([
            'success' => true,
            'final_total' => $totals['final_total'],
            'order_id' => $order->id,
            'order_type' => $isTakeaway ? 'TAKEAWAY' : 'DINE_IN',
            'redirect_url' => $redirectUrl,
            'invoice_url' => $isTakeaway ? route('order.invoice', $order->id) : null
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error saving order: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Update existing order
 */
public function update(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $order = OrderManage::findOrFail($id);

        // Update order details
        $order->customer_phone = $request->customer_phone ?? $order->customer_phone;
        $order->remarks = $request->remarks ?? $order->remarks;
        $order->payment_method = $request->payment_method ?? $order->payment_method;
        $order->payment_status = $request->payment_status ?? $order->payment_status;
        
        // Update amount paid if provided
        if ($request->has('amount_paid') && $request->amount_paid !== null) {
            $order->amount_paid = floatval($request->amount_paid);
        }
        
        // Update discount percentage
        $orderDiscountPercent = floatval($request->discount ?? $order->discount_percentage);
        $order->discount_percentage = $orderDiscountPercent;
        
        $order->save();

        // Handle new item additions with discount
        if ($request->has('order_items') && is_array($request->order_items)) {
            foreach ($request->order_items as $item) {
                $itemDiscount = isset($item['item_discount']) ? floatval($item['item_discount']) : 0;
                $calc = $this->calculateItemGST(
                    floatval($item['price']),
                    intval($item['qty']),
                    floatval($item['gst']),
                    $itemDiscount
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
                    'restaurant_id' => auth()->user()->restaurant_id,
                    'user_id' => auth()->user()->id,
                    'order_status' => 'PENDING',
                    'is_new' => 1
                ]);
            }
        }

        // Handle item deletion
        if ($request->has('delete_item_id')) {
            OrderItems::where('id', $request->delete_item_id)->delete();
        }

        // Recalculate all totals from existing items (including their discounts)
        $items = OrderItems::where('order_id', $id)->get();
        $originalSubtotal = 0;
        $totalTaxable = 0;
        $totalGst = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        
        foreach ($items as $item) {
            // Use stored discounted values if available, otherwise calculate
            $itemDiscount = $item->item_discount_percentage ?? 0;
            $discountedPrice = $item->discounted_price ?? ($item->price - ($item->price * $itemDiscount / 100));
            $taxableAmount = $item->taxable_amount ?? ($discountedPrice * $item->quantity);
            $gstAmount = $item->gst_amount ?? (($taxableAmount * ($item->gst_rate ?? 0)) / 100);
            
            $originalSubtotal += $item->price * $item->quantity;
            $totalTaxable += $taxableAmount;
            $totalGst += $gstAmount;
            $totalCgst += $item->cgst_amount ?? 0;
            $totalSgst += $item->sgst_amount ?? 0;
            $totalIgst += $item->igst_amount ?? 0;
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
        $order->discount = $discountAmount;
        $order->grand_total = $finalTotal;
        $order->round_off = $roundOff;
        
        // Auto-fill amount_paid if status is PAID and amount_paid is empty
        if ($order->payment_status === 'PAID' && empty($order->amount_paid)) {
            $order->amount_paid = $finalTotal;
        }
        
        // Clear amount_paid if status is not PAID
        if ($order->payment_status !== 'PAID' && $order->payment_status !== 'MISCORDER') {
            $order->amount_paid = null;
        }
        
        $order->save();

        // Release table for PAID or MISCORDER status
        if (in_array($order->payment_status, ['PAID', 'MISCORDER']) && $order->table_id) {
            TableManage::where('id', $order->table_id)->update([
                'table_status' => 'AVAILABLE',
                'order_id' => null
            ]);
        }

        DB::commit();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'final_total' => number_format($finalTotal, 2),
                'amount_paid' => $order->amount_paid ? number_format($order->amount_paid, 2) : '0.00',
                'round_off' => number_format($roundOff, 2),
                'subtotal' => $originalSubtotal,
                'total_taxable' => $totalTaxable,
                'total_gst' => $totalGst,
                'total_cgst' => $totalCgst,
                'total_sgst' => $totalSgst,
                'total_igst' => $totalIgst,
                'discount_amount' => $discountAmount,
                'redirect_url' => in_array($order->payment_status, ['PAID', 'MISCORDER']) 
                    ? route('order.invoice', $order->id) : null
            ]);
        }

        return redirect()->back()->with('success', 'Order updated successfully.');

    } catch (\Exception $e) {
        DB::rollBack();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', $e->getMessage());
    }
}
    /**
     * Show invoice page
     */
    public function invoicePage($order_id)
    {
        $order = OrderManage::with('orderItems.subcategory')->findOrFail($order_id);
        
        // Calculate totals for display
        $originalSubtotal = 0;
        $totalTaxable = 0;
        $totalGst = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        
        foreach ($order->orderItems as $item) {
            $originalSubtotal += $item->price * $item->quantity;
            $totalTaxable += $item->taxable_amount;
            $totalGst += $item->gst_amount;
            $totalCgst += $item->cgst_amount;
            $totalSgst += $item->sgst_amount;
            $totalIgst += $item->igst_amount;
        }
        
        $data = [
            'order' => $order,
            'original_subtotal' => $originalSubtotal,
            'total_taxable' => $totalTaxable,
            'total_gst' => $totalGst,
            'total_cgst' => $totalCgst,
            'total_sgst' => $totalSgst,
            'total_igst' => $totalIgst,
        ];
        
        return view('order.invoice', $data);
    }

/**
 * Generate PDF receipt
 */
public function pdfReceipt($order_id)
{
    $order = OrderManage::with(['orderItems.subcategory', 'table'])->findOrFail($order_id);
    $restaurant_details = RestaurantMaster::where('id', $order->restaurant_id)->first();

    // Calculate all totals with discount first then GST
    $originalSubtotal = 0;
    $totalTaxable = 0;
    $totalGst = 0;
    $totalCgst = 0;
    $totalSgst = 0;
    $totalIgst = 0;
    $totalItemDiscount = 0;
    
    foreach ($order->orderItems as $item) {
        // Get item discount percentage
        $itemDiscount = $item->item_discount_percentage ?? 0;
        $originalPrice = $item->price;
        $quantity = $item->quantity;
        
        // Calculate discounted price
        $discountedPrice = $originalPrice - ($originalPrice * $itemDiscount / 100);
        $taxableAmount = $discountedPrice * $quantity;
        
        // Calculate GST on discounted price
        $gstRate = $item->gst_rate ?? 0;
        $gstAmount = ($taxableAmount * $gstRate) / 100;
        
        // Split GST (assuming 50-50 for CGST/SGST)
        $halfGstRate = $gstRate / 2;
        $cgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $sgstAmount = ($taxableAmount * $halfGstRate) / 100;
        
        $originalSubtotal += $originalPrice * $quantity;
        $totalTaxable += $taxableAmount;
        $totalGst += $gstAmount;
        $totalCgst += $cgstAmount;
        $totalSgst += $sgstAmount;
        $totalItemDiscount += ($originalPrice * $quantity) - $taxableAmount;
    }
    
    // Calculate order discount
    $orderDiscountPercent = $order->discount_percentage ?? 0;
    $totalBeforeOrderDiscount = $totalTaxable + $totalGst;
    $orderDiscountAmount = ($totalBeforeOrderDiscount * $orderDiscountPercent) / 100;
    $grandTotal = $totalBeforeOrderDiscount - $orderDiscountAmount;
    $finalTotal = round($grandTotal);
    $roundOff = $finalTotal - $grandTotal;

    $data = [
        'order' => $order,
        'restaurant_details' => $restaurant_details,
        'original_subtotal' => $originalSubtotal,
        'total_taxable' => $totalTaxable,
        'total_gst' => $totalGst,
        'total_cgst' => $totalCgst,
        'total_sgst' => $totalSgst,
        'total_igst' => $totalIgst,
        'total_item_discount' => $totalItemDiscount,
        'order_discount_percent' => $orderDiscountPercent,
        'order_discount_amount' => $orderDiscountAmount,
        'grand_total' => $grandTotal,
        'final_total' => $finalTotal,
        'round_off' => $roundOff,
    ];

    $pdf = Pdf::loadView('receipt', $data)
        ->setPaper([0, 0, 226, 600]); // 57mm thermal paper (width: 226 points = ~80mm)

    return $pdf->stream('receipt_' . $order->order_id . '.pdf');
}

    /**
     * Show payment page
     */
    public function paymentPage($order_id)
    {
        $order = OrderManage::with('orderItems', 'table')->findOrFail($order_id);
        return view('order.payment', compact('order'));
    }

    /**
     * Submit payment
     */
    public function submitPayment(Request $request, $order_id)
    {
        $request->validate([
            'payment_method' => 'required',
            'payment_status' => 'required|in:PENDING,PAID',
        ]);

        DB::transaction(function() use ($request, $order_id) {
            $order = OrderManage::findOrFail($order_id);
            $order->payment_method = $request->payment_method;
            $order->remarks = $request->remarks;
            $order->payment_status = $request->payment_status;
            
            if ($request->payment_status == 'PAID') {
                $order->amount_paid = $order->grand_total;
            }
            
            $order->save();

            if ($request->payment_status == 'PAID' && $order->table_id) {
                TableManage::where('id', $order->table_id)->update([
                    'table_status' => 'AVAILABLE',
                    'order_id' => null
                ]);
            }
        });

        return redirect()->route('order.management.dashboard')
            ->with('success', 'Payment recorded successfully!');
    }

    /**
     * Delete order item
     */
    public function deleteOrderItem($id)
    {
        $item = OrderItems::find($id);
        if ($item) {
            $item->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Item not found']);
    }

    /**
     * Kitchen view
     */
    public function kitchen(Request $request)
    {
        $data['tables'] = TableManage::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->get();

        $query = OrderItems::with(['order', 'subcategory', 'order.table'])
            ->where('restaurant_id', auth()->user()->restaurant_id);

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

        $data['OrderItems'] = $query->orderBy('created_at', 'desc')->get();
        $data['from_date'] = $request->from_date ?? Carbon::now()->subDays(3)->format('Y-m-d');
        $data['to_date'] = $request->to_date ?? Carbon::now()->format('Y-m-d');
        $data['selected_status'] = $request->status ?? 'all';
        $data['selected_table'] = $request->table_id ?? '';

        return view('kitchen.index', $data);
    }

    /**
     * Update kitchen item status
     */
    public function updateKitchenStatus(Request $request)
    {
        $item = OrderItems::find($request->id);
        if ($item) {
            $item->order_status = $request->order_status;
            $item->is_new = 0;
            $item->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Item not found.']);
    }

    /**
     * Refresh orders for kitchen
     */
    public function refreshOrders()
    {
        $newOrdersCount = OrderItems::where('created_at', '>=', Carbon::now()->subSeconds(30))
            ->where('order_status', 'PENDING')
            ->where('is_new', 1)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->count();
            
        return response()->json([
            'new_orders' => $newOrdersCount > 0,
            'count' => $newOrdersCount
        ]);
    }
}