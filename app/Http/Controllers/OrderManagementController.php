<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableManage;
use App\Models\Category;
use App\Models\User;
use App\Models\SubCategory;
use App\Models\OrderToPayment;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\RestaurantMaster;
use App\Models\DishAddon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\FirebasePushService;
use App\Services\CashDrawerService;

class OrderManagementController extends Controller
{
    /**
     * Display order management dashboard
     */
    public function index()
    {
        $data['data'] = TableManage::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->with(['activeOrders' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
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

        }

        // Get restaurant GST info
        $restaurant = RestaurantMaster::find(auth()->user()->restaurant_id);
        $data['restaurant_gstin'] = $restaurant->gstin ?? null;
        $data['restaurant_gst_percentage'] = $restaurant->gst_percentage ?? 0;
        $data['is_gst_registered'] = !empty($restaurant->gstin);
        
        // Get all active restaurant addons
        $data['restaurant_addons'] = DishAddon::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', '!=', 'D')
            ->orderBy('name', 'asc')
            ->get();

        // Get categories with subcategories including mapped addons
        $data['categories'] = Category::where('restaurant_id', auth()->user()->restaurant_id)
            ->with(['subcategories' => function($query) {
                $query->with('addons')
                    ->where('status', '!=', 'D');
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
        $order = OrderManage::with('orderItems.subcategory.addons')->findOrFail($order_id);
        
        if (@$order->restaurant_id != auth()->user()->restaurant_id) {
            return redirect()->back()->with('error', 'Unauthorized Access');
        }
        
        // Get restaurant GST info
        $restaurant = RestaurantMaster::find(auth()->user()->restaurant_id);
        $restaurant_gstin = $restaurant->gstin ?? null;
        $restaurant_gst_percentage = $restaurant->gst_percentage ?? 0;
        $is_gst_registered = !empty($restaurant_gstin);

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
            $discountedPrice = $item->discounted_price ?? ($item->price - ($item->price * $itemDiscount / 100));
            $addonsCost = 0;
            if (!empty($item->addons_list) && is_array($item->addons_list)) {
                foreach ($item->addons_list as $a) {
                    $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                }
            }
            $isAddonItem = empty($item->subcategory_id);
            $taxableAmount = $item->taxable_amount ?? ($isAddonItem ? ($discountedPrice * $item->quantity) : (($discountedPrice * $item->quantity) + $addonsCost));
            
            // Use restaurant GST percentage if GST registered, otherwise 0
            $gstRate = $is_gst_registered ? ($item->gst_rate ?? $restaurant_gst_percentage) : 0;
            $gstAmount = $item->gst_amount ?? (($taxableAmount * $gstRate) / 100);
            
            $lineOriginal = $isAddonItem ? ($item->price * $item->quantity) : (($item->price * $item->quantity) + $addonsCost);
            $originalSubtotal += $lineOriginal;
            $totalTaxable += $taxableAmount;
            if ($is_gst_registered) {
                $totalGst += $gstAmount;
                // Split GST (50-50 for CGST/SGST)
                $halfGstRate = $gstRate / 2;
                $totalCgst += $item->cgst_amount ?? (($taxableAmount * $halfGstRate) / 100);
                $totalSgst += $item->sgst_amount ?? (($taxableAmount * $halfGstRate) / 100);
            }
        }

        // Apply order level discount
        $discountPercent = $order->discount_percentage ?? 0;
        $totalBeforeDiscount = $totalTaxable + $totalGst;
        $discountAmount = ($totalBeforeDiscount * $discountPercent) / 100;
        $grandTotal = $totalBeforeDiscount - $discountAmount;
        $finalTotal = round($grandTotal);
        $roundOff = $finalTotal - $grandTotal;

        // Get all payments for this order
        $payments = OrderToPayment::where('order_id', $order_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $totalPaid = (float)$payments->sum('amount');
        $balanceDue = max(0, $finalTotal - $totalPaid);

        $data['order'] = $order;
        $data['table'] = $order->table_id ? TableManage::find($order->table_id) : null;
        $data['payments'] = $payments;
        $data['total_paid'] = $totalPaid;
        $data['balance_due'] = $balanceDue;
        
        // Get all active restaurant addons
        $data['restaurant_addons'] = DishAddon::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', '!=', 'D')
            ->orderBy('name', 'asc')
            ->get();

        // Get categories with subcategories including mapped addons
        $data['categories'] = Category::where('restaurant_id', auth()->user()->restaurant_id)
            ->with(['subcategories' => function($query) {
                $query->with('addons')
                    ->where('status', '!=', 'D');
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
        $data['restaurant_gstin'] = $restaurant_gstin;
        $data['restaurant_gst_percentage'] = $restaurant_gst_percentage;
        $data['is_gst_registered'] = $is_gst_registered;
        
        return view('order.edit', $data);
    }

    /**
     * Calculate GST for a single item
     * Formula: Apply discount first, then calculate GST on discounted price
     * Uses restaurant GST percentage if GST registered, otherwise 0
     */
    private function calculateItemGST($originalPrice, $quantity, $itemDiscountPercent = 0, $restaurantGstPercentage = 0, $isGstRegistered = false, $addonsCost = 0, $isAddonItem = false)
    {
        // Step 1: Apply item discount to get discounted price per item
        $discountedPrice = $originalPrice - ($originalPrice * $itemDiscountPercent / 100);
        
        // Step 2: Calculate taxable amount (dish discounted price × quantity + addons cost)
        $taxableAmount = $isAddonItem ? ($discountedPrice * $quantity) : (($discountedPrice * $quantity) + $addonsCost);
        
        // Step 3: Calculate GST on taxable amount (use restaurant GST if registered)
        $gstRate = $isGstRegistered ? $restaurantGstPercentage : 0;
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
            'addons_cost' => $addonsCost,
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
            $addonsCost = floatval($item['addons_cost'] ?? 0);
            $originalSubtotal += ($item['original_price'] * $quantity) + $addonsCost;
            $totalTaxable += $item['taxable_amount'];
            if ($isGstRegistered) {
                $totalGst += $item['gst_amount'];
                $totalCgst += $item['cgst_amount'];
                $totalSgst += $item['sgst_amount'];
                $totalIgst += $item['igst_amount'];
            }
        }
        
        // Make sure orderDiscountPercent is properly converted
        $orderDiscountPercent = floatval($orderDiscountPercent);
        
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
        
        // Get restaurant GST info
        $restaurant = RestaurantMaster::find(auth()->user()->restaurant_id);
        $restaurantGstin = $restaurant->gstin ?? null;
        $restaurantGstPercentage = $restaurant->gst_percentage ?? 0;
        $isGstRegistered = !empty($restaurantGstin);
        
        // Get discount percentage from request (this is the order level discount)
        $orderDiscountPercent = floatval($request->discount ?? 0);
        
        // Calculate GST for each item using restaurant GST percentage (including addons)
        $calculatedItems = [];
        foreach ($request->order_items as $item) {
            $itemDiscount = isset($item['item_discount']) ? floatval($item['item_discount']) : 0;
            $basePrice = floatval($item['price']);
            $selectedAddons = $item['addons'] ?? [];
            $addonsCost = 0;
            $cleanAddons = [];
            $isAddonItem = !empty($item['is_addon']) || str_starts_with(strval($item['id'] ?? ''), 'addon_') || !is_numeric($item['id'] ?? null);

            if (is_array($selectedAddons) && !empty($selectedAddons)) {
                foreach ($selectedAddons as $addon) {
                    $aPrice = floatval($addon['price'] ?? 0);
                    $aQty = max(1, intval($addon['qty'] ?? $addon['quantity'] ?? 1));
                    $aName = trim($addon['name'] ?? '');
                    $aId = $addon['id'] ?? null;
                    $addonLineTotal = $aPrice * $aQty;
                    $addonsCost += $addonLineTotal;
                    if ($aName) {
                        $cleanAddons[] = [
                            'id' => $aId,
                            'name' => $aName,
                            'price' => $aPrice,
                            'qty' => $aQty,
                            'quantity' => $aQty,
                            'total' => $addonLineTotal,
                            'food_type' => $addon['food_type'] ?? 'VEG',
                        ];
                    }
                }
            }

            if ($isAddonItem && empty($cleanAddons)) {
                $cleanAddons[] = [
                    'id' => is_numeric($item['id'] ?? null) ? $item['id'] : null,
                    'name' => trim($item['name'] ?? 'Add-on'),
                    'price' => $basePrice,
                    'qty' => 1,
                    'quantity' => 1,
                    'total' => $basePrice,
                    'food_type' => $item['food_type'] ?? 'VEG',
                ];
            }

            $itemCalc = $this->calculateItemGST(
                $basePrice,
                intval($item['qty']),
                $itemDiscount,
                $restaurantGstPercentage,
                $isGstRegistered,
                $addonsCost,
                $isAddonItem
            );
            $itemCalc['addons'] = $cleanAddons;
            $itemCalc['base_price'] = $basePrice;
            $itemCalc['is_addon_item'] = $isAddonItem;
            $calculatedItems[] = $itemCalc;
        }
        
        // Calculate order totals
        $totals = $this->calculateOrderTotals($calculatedItems, $orderDiscountPercent, $isGstRegistered);
        
        // Generate order number
        $restaurantId = auth()->user()->restaurant_id;
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
            
            // Store discount - IMPORTANT FIX
            $order->discount = $totals['order_discount_amount']; // Discount amount in rupees
            $order->discount_percentage = $orderDiscountPercent; // Discount percentage
            
            $order->grand_total = $totals['final_total'];
            $order->round_off = $totals['round_off'];
            
            // GST Bill tracking
            $order->is_gst_bill = $isGstRegistered ? 'YES' : 'NO';
            $order->restaurant_gst_percentage = $restaurantGstPercentage;
            $order->restaurant_gstin = $restaurantGstin;
            
            // Payment and Checkout info
            $cashAmount = floatval($request->cash_amount ?? 0);
            $upiAmount = floatval($request->upi_amount ?? 0);
            $orderComplete = $request->order_complete ?? ($isTakeaway ? 'DONE' : 'PENDING');
            $totalPaying = $cashAmount + $upiAmount;

            // Fallback if marked PAID but explicit amounts weren't supplied in input
            if ($orderComplete === 'DONE' && $totalPaying == 0 && ($request->payment_status === 'PAID')) {
                $cashAmount = $totals['final_total'];
                $totalPaying = $cashAmount;
            }

            $grandTotal = $totals['final_total'];

            if ($totalPaying > ($grandTotal + 0.01)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total payment amount (₹' . number_format($totalPaying, 2) . ') cannot exceed the Grand Total of ₹' . number_format($grandTotal, 2) . '.'
                ], 422);
            }

            if ($totalPaying >= $grandTotal && $grandTotal > 0) {
                $order->payment_status = 'PAID';
                $order->amount_paid = $grandTotal;
                $order->order_complete = 'DONE';
                $order->order_status = 'COMPLETED';
            } elseif ($totalPaying > 0) {
                $order->payment_status = 'PARTIAL';
                $order->amount_paid = $totalPaying;
                $order->order_complete = $orderComplete;
                $order->order_status = ($orderComplete === 'DONE') ? 'COMPLETED' : 'PENDING';
            } else {
                $order->payment_status = $request->payment_status ?? 'PENDING';
                $order->amount_paid = 0;
                $order->order_complete = $orderComplete;
                $order->order_status = ($orderComplete === 'DONE') ? 'COMPLETED' : 'PENDING';
            }

            if ($cashAmount > 0 && $upiAmount > 0) {
                $order->payment_method = 'SPLIT';
            } elseif ($cashAmount > 0) {
                $order->payment_method = 'CASH';
            } elseif ($upiAmount > 0) {
                $order->payment_method = 'UPI';
            } else {
                $order->payment_method = $request->payment_method ?? null;
            }
            
            $order->remarks = $request->remarks ?? null;
            $order->restaurant_id = auth()->user()->restaurant_id;
            $order->user_id = auth()->user()->id;
            $order->save();

            // Record split payments in OrderToPayment history
            if ($cashAmount > 0) {
                $cashPayment = OrderToPayment::create([
                    'order_id' => $order->id,
                    'restaurant_id' => $order->restaurant_id,
                    'amount' => $cashAmount,
                    'payment_method' => 'CASH',
                    'transaction_no' => $request->transaction_no ?? null,
                    'remarks' => 'POS Cash Payment at checkout',
                    'payment_date' => now(),
                    'created_by' => auth()->id()
                ]);
                app(CashDrawerService::class)->recordOrderPayment($cashPayment, $order);
            }

            if ($upiAmount > 0) {
                OrderToPayment::create([
                    'order_id' => $order->id,
                    'restaurant_id' => $order->restaurant_id,
                    'amount' => $upiAmount,
                    'payment_method' => 'UPI',
                    'transaction_no' => $request->upi_transaction_no ?? $request->transaction_no ?? null,
                    'remarks' => 'POS UPI Payment at checkout',
                    'payment_date' => now(),
                    'created_by' => auth()->id()
                ]);
            }
       
            // Generate a single KOT number for all items in this initial order batch
            $kotNo = $this->generateKOTNumber($restaurantId);

            // Save order items with all GST details
            foreach ($request->order_items as $index => $item) {
                $calc = $calculatedItems[$index];
                $isNumericSubcat = is_numeric($item['id'] ?? null) && intval($item['id']) > 0 && empty($calc['is_addon_item']);
                
                $orderItem = new OrderItems();
                $orderItem->order_id = $order->id;
                $orderItem->subcategory_id = $isNumericSubcat ? intval($item['id']) : null;
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
                $orderItem->addons = !empty($calc['addons']) ? $calc['addons'] : null;
                $orderItem->order_status = 'PENDING';
                $orderItem->is_new = 1;
                $orderItem->restaurant_id = auth()->user()->restaurant_id;
                $orderItem->user_id = auth()->user()->id;
                $orderItem->kot_no = $kotNo;
                $orderItem->save();
            }

            // Update table status if dine-in
            if ($request->table_id) {
                if ($order->order_complete === 'DONE') {
                    $this->updateTableStatus($request->table_id);
                } else {
                    TableManage::where('id', $request->table_id)->update([
                        'table_status' => 'OCCUPIED',
                        'order_id' => $order->id,
                    ]);
                }
            }

            DB::commit();

            // Return redirect URLs based on order state (Auto-print trigger on checkout)
            $isCompleteOrPaid = ($order->order_complete === 'DONE' || $order->payment_status === 'PAID');
            $redirectUrl = ($isCompleteOrPaid || $isTakeaway)
                ? route('order.invoice', $order->id) . '?autoprint=1' 
                : route('order.management.dashboard');

            return response()->json([
                'success' => true,
                'final_total' => $totals['final_total'],
                'discount_amount' => $totals['order_discount_amount'],
                'discount_percentage' => $orderDiscountPercent,
                'order_id' => $order->id,
                'order_type' => $isTakeaway ? 'TAKEAWAY' : 'DINE_IN',
                'redirect_url' => $redirectUrl,
                'invoice_url' => route('order.invoice', $order->id) . '?autoprint=1'
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
            
            // Update order complete status
            if ($request->has('order_complete')) {
                $order->order_complete = $request->order_complete;
            }
            
            // Update discount percentage
            $orderDiscountPercent = floatval($request->discount ?? $order->discount_percentage);
            $order->discount_percentage = $orderDiscountPercent;
            
            $order->save();

            // Get restaurant GST info for new items
            $restaurant = RestaurantMaster::find(auth()->user()->restaurant_id);
            $restaurantGstPercentage = $restaurant->gst_percentage ?? 0;
            $isGstRegistered = !empty($restaurant->gstin);

            // Handle new item additions with discount
            if ($request->has('order_items') && is_array($request->order_items) && count($request->order_items) > 0) {
                // Generate a single new KOT number for all items in this edit/addition batch
                $kotNo = $this->generateKOTNumber($restaurant->id);

                foreach ($request->order_items as $item) {
                    $itemDiscount = isset($item['item_discount']) ? floatval($item['item_discount']) : 0;
                    $basePrice = floatval($item['price']);
                    $selectedAddons = $item['addons'] ?? [];
                    $addonsCost = 0;
                    $cleanAddons = [];
                    $isAddonItem = !empty($item['is_addon']) || str_starts_with(strval($item['id'] ?? ''), 'addon_') || !is_numeric($item['id'] ?? null);

                    if (is_array($selectedAddons) && !empty($selectedAddons)) {
                        foreach ($selectedAddons as $addon) {
                            $aPrice = floatval($addon['price'] ?? 0);
                            $aQty = max(1, intval($addon['qty'] ?? $addon['quantity'] ?? 1));
                            $aName = trim($addon['name'] ?? '');
                            $aId = $addon['id'] ?? null;
                            $addonLineTotal = $aPrice * $aQty;
                            $addonsCost += $addonLineTotal;
                            if ($aName) {
                                $cleanAddons[] = [
                                    'id' => $aId,
                                    'name' => $aName,
                                    'price' => $aPrice,
                                    'qty' => $aQty,
                                    'quantity' => $aQty,
                                    'total' => $addonLineTotal,
                                    'food_type' => $addon['food_type'] ?? 'VEG',
                                ];
                            }
                        }
                    }

                    if ($isAddonItem && empty($cleanAddons)) {
                        $cleanAddons[] = [
                            'id' => is_numeric($item['id'] ?? null) ? $item['id'] : null,
                            'name' => trim($item['name'] ?? 'Add-on'),
                            'price' => $basePrice,
                            'qty' => 1,
                            'quantity' => 1,
                            'total' => $basePrice,
                            'food_type' => $item['food_type'] ?? 'VEG',
                        ];
                    }

                    $calc = $this->calculateItemGST(
                        $basePrice,
                        intval($item['qty']),
                        $itemDiscount,
                        $restaurantGstPercentage,
                        $isGstRegistered,
                        $addonsCost,
                        $isAddonItem
                    );

                    $isNumericSubcat = is_numeric($item['id'] ?? null) && intval($item['id']) > 0 && empty($isAddonItem);
                    
                    OrderItems::create([
                        'order_id' => $id,
                        'subcategory_id' => $isNumericSubcat ? intval($item['id']) : null,
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
                        'is_new' => 1,
                        'kot_no' => $kotNo,
                        'addons' => !empty($cleanAddons) ? $cleanAddons : null
                    ]);
                }
            }

            // Handle item deletion
            if ($request->has('delete_item_id')) {
                OrderItems::where('id', $request->delete_item_id)->delete();
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
                $addonsCost = 0;
                if (!empty($item->addons_list) && is_array($item->addons_list)) {
                    foreach ($item->addons_list as $a) {
                        $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                    }
                }
                $isAddon = empty($item->subcategory_id);
                if ($isAddon) {
                    $originalSubtotal += $item->price * $item->quantity;
                } else {
                    $originalSubtotal += ($item->price * $item->quantity) + $addonsCost;
                }
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
            $order->grand_total = $finalTotal;
            $order->round_off = $roundOff;
            
            // Store discount - IMPORTANT FIX
            $order->discount = $discountAmount; // Discount amount in rupees
            $order->discount_percentage = $orderDiscountPercent; // Discount percentage
            
            // Process new Cash & UPI payments if provided
            $cashAmount = floatval($request->cash_amount ?? 0);
            $upiAmount = floatval($request->upi_amount ?? 0);
            $newPayment = $cashAmount + $upiAmount;

            $existingPaid = (float)OrderToPayment::where('order_id', $order->id)->sum('amount');
            $remainingDue = max(0, $finalTotal - $existingPaid);

            if ($newPayment > ($remainingDue + 0.01)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount (₹' . number_format($newPayment, 2) . ') cannot exceed the remaining balance due of ₹' . number_format($remainingDue, 2) . '.'
                ], 422);
            }

            if ($cashAmount > 0) {
                $cashPayment = OrderToPayment::create([
                    'order_id' => $order->id,
                    'restaurant_id' => $order->restaurant_id,
                    'amount' => $cashAmount,
                    'payment_method' => 'CASH',
                    'transaction_no' => $request->transaction_no ?? null,
                    'remarks' => 'POS Cash Payment on order update',
                    'payment_date' => now(),
                    'created_by' => auth()->id()
                ]);
                app(CashDrawerService::class)->recordOrderPayment($cashPayment, $order);
            }

            if ($upiAmount > 0) {
                OrderToPayment::create([
                    'order_id' => $order->id,
                    'restaurant_id' => $order->restaurant_id,
                    'amount' => $upiAmount,
                    'payment_method' => 'UPI',
                    'transaction_no' => $request->upi_transaction_no ?? $request->transaction_no ?? null,
                    'remarks' => 'POS UPI Payment on order update',
                    'payment_date' => now(),
                    'created_by' => auth()->id()
                ]);
            }

            // Recalculate total payments from OrderToPayment history
            $totalPaid = (float)OrderToPayment::where('order_id', $order->id)->sum('amount');
            $orderComplete = $request->order_complete ?? $order->order_complete;
            $order->order_complete = $orderComplete;
            $order->amount_paid = $totalPaid;

            if ($totalPaid >= $finalTotal && $finalTotal > 0) {
                $order->payment_status = 'PAID';
                $order->amount_paid = $finalTotal;
                if ($orderComplete === 'DONE') {
                    $order->order_status = 'COMPLETED';
                }
            } elseif ($totalPaid > 0) {
                $order->payment_status = 'PARTIAL';
                if ($orderComplete === 'DONE') {
                    $order->order_status = 'COMPLETED';
                }
            } else {
                $order->payment_status = $request->payment_status ?? $order->payment_status ?? 'PENDING';
                if ($orderComplete === 'DONE') {
                    $order->order_status = 'COMPLETED';
                }
            }

            // Update primary payment method name
            $distinctMethods = OrderToPayment::where('order_id', $order->id)
                ->pluck('payment_method')
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            if (count($distinctMethods) > 1) {
                $order->payment_method = 'SPLIT';
            } elseif (count($distinctMethods) === 1) {
                $order->payment_method = $distinctMethods[0];
            }

            $order->save();

            // Release/Update table status based on remaining active orders
            if ($order->table_id) {
                $this->updateTableStatus($order->table_id);
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                $isDoneOrPaid = ($order->order_complete === 'DONE' || $order->payment_status === 'PAID');
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
                    'discount_percentage' => $orderDiscountPercent,
                    'redirect_url' => $isDoneOrPaid 
                        ? route('order.invoice', $order->id) . '?autoprint=1' 
                        : null
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
 * Show invoice page with payments
 */
public function invoicePage($order_id)
{
    $order = OrderManage::with(['orderItems.subcategory.addons', 'table', 'restaurant'])->findOrFail($order_id);
    
    // Get all payments for this order
    $payments = OrderToPayment::where('order_id', $order_id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    $totalPaid = $payments->sum('amount');
    $balanceDue = $order->grand_total - $totalPaid;
    
    return view('order.invoice', compact('order', 'payments', 'totalPaid', 'balanceDue'));
}

/**
 * Public Invoice View (Outside Auth - for Customer WhatsApp Sharing & Web View)
 */
public function publicInvoice($order_id)
{
    $order = OrderManage::with(['orderItems.subcategory.addons', 'table', 'restaurant'])->findOrFail($order_id);
    
    $payments = OrderToPayment::where('order_id', $order_id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    $totalPaid = $payments->sum('amount');
    $balanceDue = $order->grand_total - $totalPaid;
    $isPublic = true;
    
    return view('order.public_invoice', compact('order', 'payments', 'totalPaid', 'balanceDue', 'isPublic'));
}

/**
 * Public Invoice Download / PDF Stream (Outside Auth - One Click Invoice Download)
 */
public function publicDownloadInvoice($order_id)
{
    $order = OrderManage::with(['orderItems.subcategory.addons', 'table', 'restaurant'])->findOrFail($order_id);
    $restaurant_details = $order->restaurant ?? RestaurantMaster::where('id', $order->restaurant_id)->first();

    $originalSubtotal = 0;
    $totalTaxable = 0;
    $totalGst = 0;
    $totalCgst = 0;
    $totalSgst = 0;
    $totalIgst = 0;
    $totalItemDiscount = 0;
    
    foreach ($order->orderItems as $item) {
        $itemDiscount = (float)($item->item_discount_percentage ?? 0);
        $originalPrice = (float)$item->price;
        $quantity = (int)$item->quantity;
        
        $addons = $item->addons_list ?? [];
        $addonsCost = 0;
        if (!empty($addons) && is_array($addons)) {
            foreach ($addons as $a) {
                $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
            }
        }
        $isAddonItem = empty($item->subcategory_id);
        $discountedPrice = $originalPrice - ($originalPrice * $itemDiscount / 100);
        $taxableAmount = $item->taxable_amount ?? ($isAddonItem ? ($discountedPrice * $quantity) : (($discountedPrice * $quantity) + $addonsCost));
        $lineOriginal = $isAddonItem ? ($originalPrice * $quantity) : (($originalPrice * $quantity) + $addonsCost);
        
        $gstRate = (float)($item->gst_rate ?? 0);
        $gstAmount = $item->gst_amount ?? (($taxableAmount * $gstRate) / 100);
        
        $halfGstRate = $gstRate / 2;
        $cgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $sgstAmount = ($taxableAmount * $halfGstRate) / 100;
        
        $originalSubtotal += $lineOriginal;
        $totalTaxable += $taxableAmount;
        $totalGst += $gstAmount;
        $totalCgst += $cgstAmount;
        $totalSgst += $sgstAmount;
        $totalItemDiscount += ($originalPrice * $itemDiscount / 100) * $quantity;
    }
    
    $orderDiscountPercent = (float)($order->discount_percentage ?? 0);
    $totalBeforeOrderDiscount = $totalTaxable + $totalGst;
    $orderDiscountAmount = ($totalBeforeOrderDiscount * $orderDiscountPercent) / 100;
    $grandTotal = $totalBeforeOrderDiscount - $orderDiscountAmount;
    $finalTotal = round($grandTotal);
    $roundOff = $finalTotal - $grandTotal;

    $payments = OrderToPayment::where('order_id', $order->id)
        ->orderBy('created_at', 'desc')
        ->get();
    $totalPaid = $payments->sum('amount');
    $balanceDue = max(0, $finalTotal - $totalPaid);

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
        'payments' => $payments,
        'totalPaid' => $totalPaid,
        'balanceDue' => $balanceDue,
    ];

    $itemCount = count($order->orderItems ?? []);
    $paperHeight = max(550, 320 + ($itemCount * 45));

    $pdf = Pdf::loadView('receipt', $data)
        ->setPaper([0, 0, 226, $paperHeight]);

    $filename = 'invoice_' . ($order->order_id ?? $order->id) . '.pdf';
    
    if (request()->has('download') || request()->get('action') === 'download') {
        return $pdf->download($filename);
    }
    
    return $pdf->stream($filename);
}

/**
 * Get payments for order (AJAX)
 */
public function getPayments($order_id)
{
    try {
        $payments = OrderToPayment::where('order_id', $order_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalPaid = $payments->sum('amount');
        $order = OrderManage::findOrFail($order_id);
        $balanceDue = $order->grand_total - $totalPaid;
        
        return response()->json([
            'success' => true,
            'payments' => $payments,
            'total_paid' => $totalPaid,
            'balance_due' => $balanceDue
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

/**
 * Add payment to order
 */
public function addPayment(Request $request, $order_id)
{
    try {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:CASH,UPI,CARD,BANK_TRANSFER,OTHER',
            'transaction_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string'
        ]);
        
        $order = OrderManage::findOrFail($order_id);
        
        // Calculate current total paid
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
            'restaurant_id' => $order->restaurant_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_no' => $request->transaction_no,
            'remarks' => $request->remarks,
            'payment_date' => now(),
            'created_by' => auth()->id()
        ]);

        // Sync with Cash Drawer if CASH payment
        if (strtoupper($payment->payment_method) === 'CASH') {
            app(CashDrawerService::class)->recordOrderPayment($payment, $order);
        }
        
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
        
        // Get updated payments
        $payments = OrderToPayment::where('order_id', $order_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully',
            'payments' => $payments,
            'total_paid' => $totalPaid,
            'balance_due' => $order->grand_total - $totalPaid
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

/**
 * Delete payment from order
 */
public function deletePayment($payment_id)
{
    try {
        $payment = OrderToPayment::findOrFail($payment_id);
        $order = OrderManage::findOrFail($payment->order_id);
        
        // If cash payment, remove from Cash Drawer
        if (strtoupper($payment->payment_method) === 'CASH') {
            app(CashDrawerService::class)->removeOrderPayment($payment->id);
        }

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
        
        // Get updated payments
        $payments = OrderToPayment::where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully',
            'payments' => $payments,
            'total_paid' => $totalPaid,
            'balance_due' => $balanceDue
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
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
            $itemDiscount = (float)($item->item_discount_percentage ?? 0);
            $originalPrice = (float)$item->price;
            $quantity = (int)$item->quantity;
            
            $addons = $item->addons_list ?? [];
            $addonsCost = 0;
            if (!empty($addons) && is_array($addons)) {
                foreach ($addons as $a) {
                    $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                }
            }
            $isAddonItem = empty($item->subcategory_id);
            $discountedPrice = $originalPrice - ($originalPrice * $itemDiscount / 100);
            $taxableAmount = $isAddonItem ? ($discountedPrice * $quantity) : (($discountedPrice * $quantity) + $addonsCost);
            $lineOriginal = $isAddonItem ? ($originalPrice * $quantity) : (($originalPrice * $quantity) + $addonsCost);
            
            $gstRate = (float)($item->gst_rate ?? 0);
            $gstAmount = ($taxableAmount * $gstRate) / 100;
            
            $halfGstRate = $gstRate / 2;
            $cgstAmount = ($taxableAmount * $halfGstRate) / 100;
            $sgstAmount = ($taxableAmount * $halfGstRate) / 100;
            
            $originalSubtotal += $lineOriginal;
            $totalTaxable += $taxableAmount;
            $totalGst += $gstAmount;
            $totalCgst += $cgstAmount;
            $totalSgst += $sgstAmount;
            $totalItemDiscount += ($originalPrice * $itemDiscount / 100) * $quantity;
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
            ->setPaper([0, 0, 226, 600]);

        return $pdf->stream('receipt_' . $order->order_id . '.pdf');
    }

    /**
     * Generate KOT PDF receipt for kitchen
     */
    public function kotPdf($id)
    {
        $item = OrderItems::with(['order.table', 'subcategory.category', 'subcategory.addons'])->findOrFail($id);
        $restaurant_details = RestaurantMaster::where('id', $item->restaurant_id)->first();

        // If this item has a KOT number, get all items belonging to this same KOT
        $kotItems = null;
        if (!empty($item->kot_no)) {
            $kotItems = OrderItems::with(['order.table', 'subcategory.category', 'subcategory.addons'])
                ->where('order_id', $item->order_id)
                ->where('kot_no', $item->kot_no)
                ->get();
        }

        $data = [
            'item' => $item,
            'items' => $kotItems,
            'restaurant_details' => $restaurant_details,
        ];

        $itemCount = $kotItems ? $kotItems->count() : 1;
        $paperHeight = max(350, 200 + ($itemCount * 45));

        $pdf = Pdf::loadView('invoice_kot', $data)
            ->setPaper([0, 0, 226, $paperHeight]);

        return $pdf->stream('kot_' . ($item->kot_no ?? $item->id) . '.pdf');
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
                $order->order_complete = 'DONE';
            }
            
            $order->save();

            if ($order->table_id) {
                $this->updateTableStatus($order->table_id);
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
            $orderId = $item->order_id;
            $item->delete();
            if ($orderId) {
                $order = OrderManage::find($orderId);
                if ($order) {
                    $order->recalculateTotals();
                }
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Item not found']);
    }

    /**
     * Kitchen view
     */
    public function kitchen(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $data['tables'] = TableManage::where('restaurant_id', $restaurantId)
            ->where('status', 'A')
            ->get();

        $query = OrderItems::with(['order', 'subcategory.addons', 'subcategory.category', 'order.table'])
            ->where('restaurant_id', $restaurantId);

        // Always default to last 2 days (yesterday 00:00:00 to today 23:59:59) if from_date / to_date not provided
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($request->filled('from_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $query->where('created_at', '>=', $fromDate);
        } elseif ($request->filled('to_date')) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->where('created_at', '<=', $toDate);
        } else {
            // Default: last 2 days
            $fromDate = Carbon::now()->subDays(1)->startOfDay();
            $toDate = Carbon::now()->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
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

        // Group into KOT tickets where same KOT number and same Order ID share a single card
        $kotGroups = $orderItems->groupBy(function($item) {
            $kotKey = !empty($item->kot_no) ? $item->kot_no : ('ITEM_' . $item->id);
            return ($item->order_id ?? 0) . '_' . $kotKey;
        })->map(function($items) {
            $first = $items->first();
            
            // Overall status for the KOT card
            $statuses = $items->pluck('order_status')->unique()->toArray();
            if (count($statuses) === 1) {
                $overallStatus = $statuses[0];
            } elseif (in_array('PENDING', $statuses)) {
                $overallStatus = 'PENDING';
            } elseif (in_array('COOKING', $statuses)) {
                $overallStatus = 'COOKING';
            } else {
                $overallStatus = 'DONE';
            }

            return (object) [
                'card_id' => 'kot_' . ($first->kot_no ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $first->kot_no) : $first->id) . '_' . ($first->order_id ?? 0),
                'order_id' => $first->order_id,
                'order_no' => @$first->order->order_id ?? $first->order_id,
                'kot_no' => $first->kot_no,
                'table_name' => @$first->order->table->name ?? 'Take Away',
                'is_takeaway' => empty(@$first->order->table),
                'created_at' => $first->created_at,
                'status' => $overallStatus,
                'items' => $items,
                'item_ids' => $items->pluck('id')->toArray(),
                'primary_item_id' => $first->id,
                'total_quantity' => $items->sum('quantity'),
                'total_dishes' => $items->count(),
            ];
        });

        $data['OrderItems'] = $orderItems;
        $data['kotGroups'] = $kotGroups;
        $data['from_date'] = $request->from_date ?? Carbon::now()->subDays(1)->format('Y-m-d');
        $data['to_date'] = $request->to_date ?? Carbon::now()->format('Y-m-d');
        $data['selected_status'] = $request->status ?? 'all';
        $data['selected_table'] = $request->table_id ?? '';

        return view('kitchen.index', $data);
    }

    /**
     * Update kitchen item status (supports single item, KOT lot by kot_no + order_id, or array of item_ids)
     */
    public function updateKitchenStatus(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        // 1. By KOT Number + Order ID (updates all dishes in that KOT at once)
        if ($request->filled('kot_no') && $request->filled('order_id')) {
            OrderItems::where('restaurant_id', $restaurantId)
                ->where('order_id', $request->order_id)
                ->where('kot_no', $request->kot_no)
                ->update([
                    'order_status' => $request->order_status,
                    'is_new' => 0
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All dishes in KOT ' . $request->kot_no . ' updated to ' . $request->order_status . '.'
            ]);
        }

        // 2. By Item IDs Array
        if ($request->filled('item_ids') && is_array($request->item_ids)) {
            OrderItems::where('restaurant_id', $restaurantId)
                ->whereIn('id', $request->item_ids)
                ->update([
                    'order_status' => $request->order_status,
                    'is_new' => 0
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Kitchen order status updated to ' . $request->order_status . '.'
            ]);
        }

        // 3. By Single Item ID
        if ($request->filled('id')) {
            $item = OrderItems::where('restaurant_id', $restaurantId)->find($request->id);
            if ($item) {
                if (!empty($item->kot_no)) {
                    // Update all items sharing this KOT number & order_id
                    OrderItems::where('restaurant_id', $restaurantId)
                        ->where('order_id', $item->order_id)
                        ->where('kot_no', $item->kot_no)
                        ->update([
                            'order_status' => $request->order_status,
                            'is_new' => 0
                        ]);
                } else {
                    $item->order_status = $request->order_status;
                    $item->is_new = 0;
                    $item->save();
                }

                return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Item or KOT not found.']);
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

    /**
     * Generate the next KOT number for a restaurant
     */
    private function generateKOTNumber($restaurantId)
    {
        $todayDateStr = Carbon::now()->format('ymd');
        $nextSequence = 1;

        // Find the latest KOT number generated today for this restaurant
        $latestItem = OrderItems::where('restaurant_id', $restaurantId)
            ->whereNotNull('kot_no')
            ->where('kot_no', 'like', "KOT-{$todayDateStr}-%")
            ->orderBy('id', 'desc')
            ->first();

        // If none found for today with prefix, fallback to latest overall item
        if (!$latestItem) {
            $latestItem = OrderItems::where('restaurant_id', $restaurantId)
                ->whereNotNull('kot_no')
                ->orderBy('id', 'desc')
                ->first();
        }

        if ($latestItem && preg_match('/KOT-(\d{6})-(\d+)/', $latestItem->kot_no, $matches)) {
            $latestDateStr = $matches[1];
            $latestSequence = intval($matches[2]);
            if ($latestDateStr === $todayDateStr) {
                $nextSequence = $latestSequence + 1;
            }
        }
        
        return "KOT-{$todayDateStr}-" . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
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