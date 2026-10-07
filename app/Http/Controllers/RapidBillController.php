<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DishAddon;
use App\Models\TableManage;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\OrderToPayment;
use App\Models\RestaurantMaster;
use App\Services\CashDrawerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RapidBillController extends Controller
{
    /**
     * Display the ultra-fast one-page Rapid Bill POS interface.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $activeRestId = $user->restaurant_id;

        $restaurant = RestaurantMaster::find($activeRestId);
        $restaurantGstin = $restaurant->gstin ?? null;
        $restaurantGstPercentage = floatval($restaurant->gst_percentage ?? 0);
        $gstPercentage = $restaurantGstPercentage;
        $isGstEnabled = !empty($restaurantGstin) || (!empty($restaurant->is_gst_enable) && $restaurant->is_gst_enable != 0);
        $isGstRegistered = $isGstEnabled;

        // Fetch all active categories with their active subcategory dishes and mapped addons
        $categories = Category::where('restaurant_id', $activeRestId)
            ->where('status', '!=', 'D')
            ->with(['subcategories' => function($q) use ($activeRestId) {
                $q->where('sub_category.restaurant_id', $activeRestId)
                  ->where('sub_category.status', '!=', 'D')
                  ->with(['addons' => function($aq) use ($activeRestId) {
                      $aq->where('dish_addons.restaurant_id', $activeRestId)
                         ->where('dish_addons.status', '!=', 'D');
                  }]);
            }])
            ->orderBy('name', 'asc')
            ->get();

        // Fetch all active restaurant addons
        $restaurant_addons = DishAddon::where('restaurant_id', $activeRestId)
            ->where('status', '!=', 'D')
            ->orderBy('name', 'asc')
            ->get();

        // Fetch all active dining tables
        $tables = TableManage::where('restaurant_id', $activeRestId)
            ->where('status', '!=', 'D')
            ->orderBy('name', 'asc')
            ->get();

        return view('order.rapid_bill', compact(
            'restaurant',
            'restaurantGstin',
            'restaurantGstPercentage',
            'gstPercentage',
            'isGstRegistered',
            'isGstEnabled',
            'categories',
            'restaurant_addons',
            'tables'
        ));
    }

    /**
     * Store and process a rapid bill order with split Cash & UPI payment and KOT tracking.
     */
    public function store(Request $request)
    {
        // Support both 'items' and 'order_items'
        $rawItems = $request->items ?? $request->order_items ?? [];
        if (!is_array($rawItems) || empty($rawItems)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please add at least one item to generate bill.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Please add at least one item.');
        }

        $user = auth()->user();
        $restaurantId = $user->restaurant_id;
        $restaurant = RestaurantMaster::find($restaurantId);
        $restaurantGstin = $restaurant->gstin ?? null;
        $restaurantGstPercentage = floatval($restaurant->gst_percentage ?? 0);
        $isGstRegistered = !empty($restaurantGstin) || (!empty($restaurant->is_gst_enable) && $restaurant->is_gst_enable != 0);

        $orderDiscountPercent = floatval($request->discount_percentage ?? $request->discount ?? 0);
        $isTakeaway = empty($request->table_id) || strtolower($request->order_type ?? '') === 'takeaway';
        $orderType = $isTakeaway ? 'takeaway' : 'dine_in';

        // Customer details default fallback for rapid counter billing
        $customerName = trim($request->customer_name ?: '') ?: 'Walk-in Customer';
        $customerPhone = trim($request->customer_phone ?: '');

        // 1. Calculate items with GST & Addons
        $calculatedItems = [];
        $originalSubtotal = 0;
        $totalTaxable = 0;
        $totalGst = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;

        foreach ($rawItems as $item) {
            $dishId = $item['dish_id'] ?? $item['id'] ?? null;
            $origPrice = floatval($item['price'] ?? 0);
            $qty = floatval($item['quantity'] ?? $item['qty'] ?? 1);
            $itemDiscountPercent = floatval($item['discount_percentage'] ?? $item['item_discount'] ?? 0);
            $selectedAddons = $item['addons'] ?? [];

            // Calculate addons cost and summary with quantity support
            $addonsCost = 0;
            $addonNames = [];
            $cleanAddons = [];
            if (is_array($selectedAddons) && !empty($selectedAddons)) {
                foreach ($selectedAddons as $addon) {
                    $aPrice = floatval($addon['price'] ?? 0);
                    $aQty = max(1, intval($addon['qty'] ?? $addon['quantity'] ?? 1));
                    $aName = trim($addon['name'] ?? '');
                    $aId = $addon['id'] ?? null;
                    $addonLineTotal = $aPrice * $aQty;
                    $addonsCost += $addonLineTotal;
                    if ($aName) {
                        $addonNames[] = $aQty > 1 ? "{$aName} x{$aQty}" : $aName;
                        $cleanAddons[] = [
                            'id' => $aId,
                            'name' => $aName,
                            'price' => $aPrice,
                            'qty' => $aQty,
                            'quantity' => $aQty,
                            'total' => $addonLineTotal,
                        ];
                    }
                }
            }

            // Fetch dish name if missing
            $dishName = $item['name'] ?? null;
            if (!$dishName && $dishId) {
                $dishObj = SubCategory::find($dishId);
                $dishName = $dishObj->name ?? 'Dish Item';
                if ($origPrice <= 0 && $dishObj) {
                    $origPrice = floatval($dishObj->price ?? 0);
                }
            }
            if (!$dishName) {
                $dishName = 'Dish Item';
            }

            $isAddonItem = !empty($item['is_addon']) || str_starts_with(strval($dishId ?? ''), 'addon_') || !is_numeric($dishId);

            if ($isAddonItem && empty($cleanAddons)) {
                $cleanAddons[] = [
                    'id' => is_numeric($dishId) ? $dishId : null,
                    'name' => $dishName ?: 'Add-on',
                    'price' => $origPrice,
                    'qty' => 1,
                    'quantity' => 1,
                    'total' => $origPrice,
                    'food_type' => $item['food_type'] ?? 'VEG',
                ];
            }

            // Calculation: Dish base price scales with dish qty; addons cost is added separately
            $discountedUnitPrice = $origPrice - ($origPrice * $itemDiscountPercent / 100);
            if ($isAddonItem) {
                $taxableAmount = $discountedUnitPrice * $qty;
                $lineOriginal = $origPrice * $qty;
            } else {
                $taxableAmount = ($discountedUnitPrice * $qty) + $addonsCost;
                $lineOriginal = ($origPrice * $qty) + $addonsCost;
            }

            // Step B: GST Calculation
            $gstRate = $isGstRegistered ? $restaurantGstPercentage : 0;
            $gstAmount = ($taxableAmount * $gstRate) / 100;
            $halfGstRate = $gstRate / 2;
            $cgst = ($taxableAmount * $halfGstRate) / 100;
            $sgst = ($taxableAmount * $halfGstRate) / 100;
            $igst = 0;
            $itemTotal = $taxableAmount + $gstAmount;

            $displayName = $dishName;
            if (!empty($addonNames)) {
                $displayName .= ' (' . implode(', ', $addonNames) . ')';
            }

            $calculatedItems[] = [
                'id' => $dishId,
                'name' => $displayName,
                'dish_name' => $dishName,
                'addons' => $cleanAddons,
                'addons_cost' => $addonsCost,
                'base_price' => $origPrice,
                'quantity' => $qty,
                'original_price' => $origPrice,
                'discounted_price' => $discountedUnitPrice,
                'item_discount_percentage' => $itemDiscountPercent,
                'taxable_amount' => $taxableAmount,
                'gst_rate' => $gstRate,
                'gst_amount' => $gstAmount,
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => $igst,
                'total_amount' => $itemTotal,
                'is_addon_item' => $isAddonItem,
            ];

            $originalSubtotal += $lineOriginal;
            $totalTaxable += $taxableAmount;
            if ($isGstRegistered) {
                $totalGst += $gstAmount;
                $totalCgst += $cgst;
                $totalSgst += $sgst;
            }
        }

        // 2. Compute Item Discounts & Round off
        $totalItemDiscount = max(0, $originalSubtotal - $totalTaxable);
        $orderDiscountPercent = $originalSubtotal > 0 ? round(($totalItemDiscount / $originalSubtotal) * 100, 2) : 0;
        $orderDiscountAmount = $totalItemDiscount;
        $grandTotalRaw = $totalTaxable + $totalGst;
        $finalTotal = round($grandTotalRaw);
        $roundOff = $finalTotal - $grandTotalRaw;

        // 3. Payment Split Breakdown
        $cashAmount = max(0, floatval($request->cash_amount ?? 0));
        $upiAmount = max(0, floatval($request->upi_amount ?? 0));
        $totalPaidInput = $cashAmount + $upiAmount;

        // Auto-fill full payment if user did not specify split but completed transaction
        if ($totalPaidInput <= 0) {
            $cashAmount = $finalTotal;
            $totalPaidInput = $finalTotal;
        } elseif ($totalPaidInput > ($finalTotal + 0.01)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total payment amount (₹' . number_format($totalPaidInput, 2) . ') cannot exceed the Grand Total of ₹' . number_format($finalTotal, 2) . '.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Payment amount cannot exceed the Grand Total.');
        }

        $paymentStatus = 'PENDING';
        if ($totalPaidInput >= $finalTotal) {
            $paymentStatus = 'PAID';
        } elseif ($totalPaidInput > 0) {
            $paymentStatus = 'PARTIAL';
        }

        $paymentMethodLabel = 'CASH';
        if ($cashAmount > 0 && $upiAmount > 0) {
            $paymentMethodLabel = 'SPLIT (CASH + UPI)';
        } elseif ($upiAmount > 0) {
            $paymentMethodLabel = 'UPI';
        }

        // 4. Generate Order & KOT Numbers
        $todayCount = OrderManage::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', Carbon::today())
            ->count() + 1;
        $prefix = $this->getRestaurantPrefix($restaurantId);
        $dateStr = Carbon::now()->format('ymd');
        $orderNo = "{$prefix}-{$dateStr}-" . str_pad($todayCount, 3, '0', STR_PAD_LEFT);
        $kotNo = $this->generateKOTNumber($restaurantId);

        DB::beginTransaction();

        try {
            // Save Order
            $order = new OrderManage();
            $order->restaurant_id = $restaurantId;
            $order->user_id = $user->id;
            $order->customer_name = $customerName;
            $order->customer_phone = $customerPhone;
            $order->order_id = $orderNo;
            $order->table_id = $request->table_id ?: null;
            $order->order_type = $orderType;
            $order->total_amount = $originalSubtotal;
            $order->taxable_amount = $totalTaxable;
            $order->gst_amount = $totalGst;
            $order->cgst_amount = $totalCgst;
            $order->sgst_amount = $totalSgst;
            $order->igst_amount = $totalIgst;
            $order->discount = $orderDiscountAmount;
            $order->discount_percentage = $orderDiscountPercent;
            $order->grand_total = $finalTotal;
            $order->round_off = $roundOff;
            $order->is_gst_bill = $isGstRegistered ? 'YES' : 'NO';
            $order->restaurant_gst_percentage = $restaurantGstPercentage;
            $order->restaurant_gstin = $restaurantGstin;
            $order->amount_paid = min($totalPaidInput, $finalTotal);
            $order->payment_status = $paymentStatus;
            $order->payment_method = $paymentMethodLabel;
            $order->order_complete = ($paymentStatus === 'PAID') ? 'DONE' : 'PENDING';
            $order->order_status = 'PENDING';
            $order->remarks = $request->remarks ?: 'Rapid Bill Checkout';
            $order->save();

            // Save Order Items
            foreach ($calculatedItems as $cItem) {
                $isNumericSubcat = is_numeric($cItem['id']) && intval($cItem['id']) > 0 && empty($cItem['is_addon_item']);

                $orderItem = new OrderItems();
                $orderItem->order_id = $order->id;
                $orderItem->subcategory_id = $isNumericSubcat ? intval($cItem['id']) : null;
                $orderItem->quantity = $cItem['quantity'];
                $orderItem->price = $cItem['original_price'];
                $orderItem->addons = !empty($cItem['addons']) ? $cItem['addons'] : null;
                $orderItem->discounted_price = $cItem['discounted_price'];
                $orderItem->item_discount_percentage = $cItem['item_discount_percentage'];
                $orderItem->taxable_amount = $cItem['taxable_amount'];
                $orderItem->gst_rate = $cItem['gst_rate'];
                $orderItem->gst_amount = $cItem['gst_amount'];
                $orderItem->cgst_amount = $cItem['cgst_amount'];
                $orderItem->sgst_amount = $cItem['sgst_amount'];
                $orderItem->igst_amount = $cItem['igst_amount'];
                $orderItem->total_amount = $cItem['total_amount'];
                $orderItem->order_status = 'PENDING';
                $orderItem->is_new = 1;
                $orderItem->restaurant_id = $restaurantId;
                $orderItem->user_id = $user->id;
                $orderItem->kot_no = $kotNo;
                $orderItem->save();
            }

            // Record Payments (Split handling)
            // A. Cash Payment
            if ($cashAmount > 0) {
                $cashPayment = OrderToPayment::create([
                    'order_id' => $order->id,
                    'restaurant_id' => $restaurantId,
                    'amount' => $cashAmount,
                    'payment_method' => 'CASH',
                    'transaction_no' => null,
                    'remarks' => 'Rapid Bill Cash Payment',
                    'payment_date' => Carbon::now(),
                    'created_by' => $user->id,
                ]);

                // Sync with Cash Drawer
                app(CashDrawerService::class)->recordOrderPayment($cashPayment, $order);
            }

            // B. UPI Payment
            if ($upiAmount > 0) {
                OrderToPayment::create([
                    'order_id' => $order->id,
                    'restaurant_id' => $restaurantId,
                    'amount' => $upiAmount,
                    'payment_method' => 'UPI',
                    'transaction_no' => $request->upi_ref ?: null,
                    'remarks' => 'Rapid Bill UPI Payment' . ($request->upi_ref ? " (Ref: {$request->upi_ref})" : ''),
                    'payment_date' => Carbon::now(),
                    'created_by' => $user->id,
                ]);
            }

            // Release table if dine-in and paid
            if ($request->table_id && $paymentStatus === 'PAID') {
                TableManage::where('id', $request->table_id)->update([
                    'table_status' => 'AVAILABLE',
                    'order_id' => null
                ]);
            }

            DB::commit();

            $shouldPrint = filter_var($request->print_bill, FILTER_VALIDATE_BOOLEAN) || $request->print_bill === '1' || $request->print_bill === 'on' || $request->print_bill === true || !isset($request->print_bill);
            $invoiceUrl = route('order.invoice', $order->id) . ($shouldPrint ? '?autoprint=1&return=rapid_bill' : '?return=rapid_bill');

            $receiptData = [
                'restaurant_name' => $restaurant->name ?? 'Restaurant POS',
                'address' => $restaurant->address ?? '',
                'gstin' => $restaurantGstin,
                'order_id' => $order->id,
                'order_no' => $order->order_id,
                'kot_no' => $kotNo,
                'date' => Carbon::now()->format('d M Y, h:i A'),
                'customer' => $customerName . ($customerPhone ? " ({$customerPhone})" : ''),
                'items' => array_map(function($i) {
                    return [
                        'name' => $i['name'],
                        'qty' => $i['quantity'],
                        'price' => $i['original_price'],
                        'total' => $i['total_amount']
                    ];
                }, $calculatedItems),
                'subtotal' => $originalSubtotal,
                'taxable' => $totalTaxable,
                'gst' => $totalGst,
                'discount' => $orderDiscountAmount,
                'grand_total' => $finalTotal,
                'cash_amount' => $cashAmount,
                'upi_amount' => $upiAmount,
                'payments' => array_values(array_filter([
                    $cashAmount > 0 ? ['method' => 'Cash', 'amount' => $cashAmount] : null,
                    $upiAmount > 0 ? ['method' => 'UPI', 'amount' => $upiAmount] : null,
                ])),
                'payment_status' => $paymentStatus,
            ];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rapid Bill generated successfully!',
                    'order_id' => $order->id,
                    'order_no' => $order->order_id,
                    'kot_no' => $kotNo,
                    'grand_total' => $finalTotal,
                    'total_paid' => $totalPaidInput,
                    'cash_amount' => $cashAmount,
                    'upi_amount' => $upiAmount,
                    'print_bill' => $shouldPrint,
                    'invoice_url' => $invoiceUrl,
                    'receipt' => $receiptData,
                    'receipt_data' => $receiptData
                ]);
            }

            if ($shouldPrint) {
                return redirect($invoiceUrl);
            }

            return redirect()->route('rapid.bill')->with('success', "Order #{$order->order_id} placed successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rapid Bill Store Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error placing rapid order: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error creating bill: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Generate the next sequential KOT number
     */
    private function generateKOTNumber(int $restaurantId): string
    {
        $todayDateStr = Carbon::now()->format('ymd');
        $nextSequence = 1;

        $latestItem = OrderItems::where('restaurant_id', $restaurantId)
            ->whereNotNull('kot_no')
            ->where('kot_no', 'like', "KOT-{$todayDateStr}-%")
            ->orderBy('id', 'desc')
            ->first();

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
     * Generate restaurant short uppercase prefix
     */
    private function getRestaurantPrefix(int $restaurantId): string
    {
        $restaurant = RestaurantMaster::find($restaurantId);
        $prefix = $restaurant ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $restaurant->name), 0, 3)) : 'RB';
        return empty($prefix) ? 'RB' : $prefix;
    }
}
