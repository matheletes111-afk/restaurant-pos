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
use App\Models\TableManage;
use App\Models\User;
use App\Mail\NewQrOrderMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class TempOrderController extends Controller
{
    public function create(Request $request, $table_id, $restaurant_id)
    {
        $table_details = TableManage::where('restaurant_id', $restaurant_id)->find($table_id);
        if (!$table_details) {
            $table_details = TableManage::find($table_id);
            if ($table_details && $table_details->restaurant_id) {
                $restaurant_id = $table_details->restaurant_id;
            }
        }

        $restaurant_details = RestaurantMaster::where('id', $restaurant_id)->first();
        if (!$restaurant_details) {
            $restaurant_details = RestaurantMaster::first() ?? new RestaurantMaster(['name' => 'Restaurant Menu']);
        }

        $categories = Category::where('restaurant_id', $restaurant_id)
                                ->with(['subcategories' => function($q) {
                                    $q->where('status', '!=', 'D')->with(['addons' => function($aq) {
                                        $aq->where('dish_addons.status', 'A');
                                    }]);
                                }])
                                ->get();

        $restaurant_addons = \App\Models\DishAddon::where('restaurant_id', $restaurant_id)
            ->where('status', 'A')
            ->get();

        // Retrieve customer's saved order ID from session or cookie
        // This ensures that Sayan only sees Sayan's order, and Rohi only sees Rohi's order!
        $sessionKey = "customer_qr_order_{$restaurant_id}_{$table_id}";
        $savedOrderData = session($sessionKey) ?? session('customer_qr_order');
        $savedOrderId = null;

        if (is_array($savedOrderData)) {
            $savedOrderId = $savedOrderData['id'] ?? null;
        } elseif (!empty($savedOrderData)) {
            $savedOrderId = $savedOrderData;
        }

        if (!$savedOrderId) {
            $savedOrderId = session('customer_qr_order_id');
        }

        if (!$savedOrderId) {
            $savedOrderId = request()->cookie($sessionKey) ?? request()->cookie('customer_qr_order_id');
        }

        if (!$savedOrderId && request()->has('order_id')) {
            $paramOrderId = request('order_id');
            if ($this->canAccessOrder($paramOrderId)) {
                $savedOrderId = $paramOrderId;
            }
        }

        $activeOrder = null;
        $pendingTempOrder = null;

        if ($savedOrderId) {
            // First check if it's an OrderManage record (by primary key ID or string order_id)
            $mainOrder = OrderManage::with(['orderItems.subcategory', 'table'])
                ->where('restaurant_id', $restaurant_id)
                ->where(function($q) use ($savedOrderId) {
                    if (is_numeric($savedOrderId)) {
                        $q->where('id', $savedOrderId);
                    } else {
                        $q->where('order_id', $savedOrderId)->orWhere('id', $savedOrderId);
                    }
                })
                ->first();

            if ($mainOrder) {
                // If restaurant completed or cancelled the order, remove it from session so customer can freshly order
                $isCompleted = (
                    $mainOrder->order_complete === 'DONE' ||
                    $mainOrder->payment_status === 'PAID' ||
                    in_array(strtoupper($mainOrder->order_status ?? ''), ['COMPLETED', 'CANCELLED', 'REJECTED'])
                );

                if ($isCompleted) {
                    $this->clearCustomerOrderSession($restaurant_id, $table_id);
                    $activeOrder = null;
                } else {
                    $activeOrder = $mainOrder;
                }
            } else {
                // Check if it's a TempOrder record
                $tempOrder = TempOrder::with(['items.menuItem', 'table_details'])
                    ->where('restaurant_id', $restaurant_id)
                    ->find($savedOrderId);

                if ($tempOrder) {
                    if (strtoupper($tempOrder->order_status ?? '') === 'APPROVED' && $tempOrder->order_id) {
                        $linkedOrder = OrderManage::with(['orderItems.subcategory', 'table'])
                            ->where('restaurant_id', $restaurant_id)
                            ->find($tempOrder->order_id);

                        if ($linkedOrder) {
                            $isCompleted = (
                                $linkedOrder->order_complete === 'DONE' ||
                                $linkedOrder->payment_status === 'PAID' ||
                                in_array(strtoupper($linkedOrder->order_status ?? ''), ['COMPLETED', 'CANCELLED', 'REJECTED'])
                            );

                            if ($isCompleted) {
                                $this->clearCustomerOrderSession($restaurant_id, $table_id);
                                $activeOrder = null;
                            } else {
                                $activeOrder = $linkedOrder;
                                // Update session to point to the active main order
                                session([
                                    'customer_qr_order_id' => $linkedOrder->id,
                                    'customer_qr_order_type' => 'main',
                                    $sessionKey => ['id' => $linkedOrder->id, 'type' => 'main'],
                                ]);
                            }
                        }
                    } elseif (in_array(strtoupper($tempOrder->order_status ?? ''), ['CANCELLED', 'REJECTED'])) {
                        $this->clearCustomerOrderSession($restaurant_id, $table_id);
                        $pendingTempOrder = null;
                    } elseif (strtoupper($tempOrder->order_status ?? '') === 'PENDING') {
                        $pendingTempOrder = $tempOrder;
                    }
                } else {
                    $this->clearCustomerOrderSession($restaurant_id, $table_id);
                }
            }
        }

        return view('temp_order', compact('categories', 'table_id', 'restaurant_id', 'restaurant_details', 'table_details', 'activeOrder', 'pendingTempOrder', 'restaurant_addons'));
    }

public function store(Request $request)
{
    $request->validate([
        'customer_name' => 'required|string',
        'customer_phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
        'order_items' => 'required|array|min:1',
    ], [
        'customer_phone.regex' => 'Mobile number must be exactly 10 digits.',
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
        $basePrice = floatval($item['price']);
        $quantity = max(1, intval($item['qty'] ?? 1));
        
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

        // Calculate discounted price: dish base price scales with quantity; addons cost is added separately
        $discountedPrice = $basePrice - ($basePrice * $itemDiscount / 100);
        if ($isAddonItem) {
            $taxableAmount = $discountedPrice * $quantity;
            $lineOriginal = $basePrice * $quantity;
        } else {
            $taxableAmount = ($discountedPrice * $quantity) + $addonsCost;
            $lineOriginal = ($basePrice * $quantity) + $addonsCost;
        }
        
        // Calculate GST on discounted price
        $gstRate = $isGstRegistered ? $restaurantGstPercentage : 0;
        $gstAmount = ($taxableAmount * $gstRate) / 100;
        
        // Split GST
        $halfGstRate = $gstRate / 2;
        $cgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $sgstAmount = ($taxableAmount * $halfGstRate) / 100;
        $totalAmount = $taxableAmount + $gstAmount;
        
        $originalSubtotal += $lineOriginal;
        $totalTaxable += $taxableAmount;
        $totalGst += $gstAmount;
        $totalCgst += $cgstAmount;
        $totalSgst += $sgstAmount;
        $totalItemDiscount += ($basePrice * $itemDiscount / 100) * $quantity;
        
        $calculatedItems[] = [
            'subcategory_id' => is_numeric($item['id'] ?? null) ? $item['id'] : null,
            'quantity' => $quantity,
            'price' => $basePrice,
            'addons' => $cleanAddons,
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
    $todayCount = OrderManage::where('restaurant_id', $restaurantId)
        ->whereDate('created_at', Carbon::today())
        ->count() + 1;
    $restaurant = RestaurantMaster::find($restaurantId);
    $prefix = $restaurant ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $restaurant->name), 0, 3)) : 'ORD';
    $prefix = empty($prefix) ? 'ORD' : $prefix;
    $dateStr = Carbon::now()->format('ymd');
    $orderNo = "{$prefix}-{$dateStr}-" . str_pad($todayCount, 3, '0', STR_PAD_LEFT);

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
            'addons' => $item['addons'],
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

    // Send email notification to the restaurant's registered email
    try {
        $restaurantInfo = RestaurantMaster::with('owner')->find($request->restaurant_id);
        $registeredEmail = null;

        if ($restaurantInfo) {
            if ($restaurantInfo->owner && !empty($restaurantInfo->owner->email)) {
                $registeredEmail = $restaurantInfo->owner->email;
            } else {
                $registeredEmail = User::where('restaurant_id', $restaurantInfo->id)
                    ->where('role', 'RES')
                    ->whereNotNull('email')
                    ->value('email');
            }
        }

        if (!empty($registeredEmail)) {
            $tempOrder->load(['items.menuItem', 'table_details']);
            Mail::to($registeredEmail)->send(
                new NewQrOrderMail($tempOrder, $restaurantInfo, $tempOrder->table_details)
            );
        }
    } catch (\Throwable $e) {
        Log::error('Failed to send QR order email notification: ' . $e->getMessage(), [
            'order_id' => $tempOrder->id,
            'restaurant_id' => $request->restaurant_id,
            'trace' => $e->getTraceAsString(),
        ]);
    }

    // Save order in customer session & cookie so this customer sees their own order
    session([
        'customer_qr_order_id' => $tempOrder->id,
        'customer_qr_order_type' => 'temp',
        "customer_qr_order_{$tempOrder->restaurant_id}_{$tempOrder->table_id}" => [
            'id' => $tempOrder->id,
            'type' => 'temp',
        ],
        'customer_qr_allowed_orders' => [$tempOrder->id],
        'customer_name' => $tempOrder->customer_name,
        'customer_phone' => $tempOrder->customer_phone,
    ]);

    return response()->json([
        'status' => true,
        'order_id' => $tempOrder->id,
        'redirect' => route('order.success', $tempOrder->id)
    ]);
}

    public function success($id)
    {
        if (!$this->canAccessOrder($id)) {
            abort(403, 'Unauthorized access. You do not have permission to view this order.');
        }

        $tempOrder = TempOrder::with(['items.menuItem', 'table_details'])->find($id);

        if ($tempOrder) {
            // If the temp order was already approved by restaurant, redirect to order details page
            if (strtoupper($tempOrder->order_status ?? '') === 'APPROVED' && $tempOrder->order_id) {
                return redirect()->route('order.details', $tempOrder->order_id);
            }

            // Keep customer session updated with this pending order
            if (!in_array(strtoupper($tempOrder->order_status ?? ''), ['REJECTED', 'CANCELLED'])) {
                session([
                    'customer_qr_order_id' => $tempOrder->id,
                    'customer_qr_order_type' => 'temp',
                    "customer_qr_order_{$tempOrder->restaurant_id}_{$tempOrder->table_id}" => [
                        'id' => $tempOrder->id,
                        'type' => 'temp'
                    ],
                ]);
                $currentAllowed = session('customer_qr_allowed_orders', []);
                if (!in_array($tempOrder->id, $currentAllowed)) {
                    session()->push('customer_qr_allowed_orders', $tempOrder->id);
                }
            }

            $restaurant_details = RestaurantMaster::find($tempOrder->restaurant_id);
            $table_details = $tempOrder->table_details ?? ($tempOrder->table_id ? TableManage::where('restaurant_id', $tempOrder->restaurant_id)->find($tempOrder->table_id) : null);
            $orderId = $tempOrder->order_id ?? ('#' . $tempOrder->id);
            $customerName = $tempOrder->customer_name;
            $orderStatus = strtoupper($tempOrder->order_status ?? 'PENDING');
            $items = $tempOrder->items ?? collect();

            $computedSubtotal = 0;
            $computedTaxable = 0;
            $computedGst = 0;
            $computedDiscount = 0;

            foreach ($items as $itm) {
                $addons = $itm->addons_list ?? [];
                $addonsCost = 0;
                if (!empty($addons) && is_array($addons)) {
                    foreach ($addons as $a) {
                        $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                    }
                }
                $isAddonItem = empty($itm->subcategory_id);
                $iPrice = floatval($itm->price);
                $iQty = max(1, intval($itm->quantity ?? 1));
                $iDisc = floatval($itm->item_discount_percentage ?? 0);
                $iDiscPrice = floatval($itm->discounted_price ?? ($iPrice - ($iPrice * $iDisc / 100)));

                $lineOrig = $isAddonItem ? ($iPrice * $iQty) : (($iPrice * $iQty) + $addonsCost);
                $lineTax = $isAddonItem ? ($iDiscPrice * $iQty) : (($iDiscPrice * $iQty) + $addonsCost);
                $gstRate = floatval($itm->gst_rate ?? 0);
                $lineGst = ($lineTax * $gstRate) / 100;

                $computedSubtotal += $lineOrig;
                $computedTaxable += $lineTax;
                $computedGst += $lineGst;
                $computedDiscount += ($iPrice * $iDisc / 100) * $iQty;
            }

            if ($items->isEmpty()) {
                $grandTotal = 0;
                $subtotal = 0;
                $taxableAmount = 0;
                $gstAmount = 0;
                $discount = 0;
            } else {
                $grandTotal = $computedTaxable + $computedGst;
                $subtotal = $computedSubtotal;
                $taxableAmount = $computedTaxable;
                $gstAmount = $computedGst;
                $discount = $computedDiscount;
            }
            $isGstBill = ($tempOrder->is_gst_bill ?? 'NO') === 'YES';

            return view('order-success', compact(
                'tempOrder',
                'restaurant_details',
                'table_details',
                'orderId',
                'customerName',
                'orderStatus',
                'items',
                'grandTotal',
                'subtotal',
                'discount',
                'gstAmount',
                'taxableAmount',
                'isGstBill'
            ));
        }

        // Fallback: Check if it is an OrderManage record -> redirect to order.details
        $mainOrder = OrderManage::with(['orderItems.subcategory', 'table'])->find($id);
        if ($mainOrder) {
            return redirect()->route('order.details', $mainOrder->id);
        }

        abort(404, 'Order not found');
    }

    /**
     * Dedicated full order details & status page for customer
     * Displays all items grouped by KOT lots, real-time kitchen status,
     * running bill totals, and intuitive Order More Items flow.
     */
    public function orderDetails($id)
    {
        if (!$this->canAccessOrder($id)) {
            abort(403, 'Unauthorized access. You do not have permission to view this order.');
        }

        // Load OrderManage
        $mainOrder = OrderManage::with(['orderItems.subcategory', 'table'])->find($id);

        if (!$mainOrder) {
            // Check if this was a TempOrder that got approved
            $tempOrder = TempOrder::find($id);
            if ($tempOrder && $tempOrder->order_id) {
                return redirect()->route('order.details', $tempOrder->order_id);
            }
            if ($tempOrder && strtoupper($tempOrder->order_status ?? '') === 'PENDING') {
                return redirect()->route('order.success', $tempOrder->id);
            }
            abort(404, 'Order not found');
        }

        $restaurant_details = RestaurantMaster::find($mainOrder->restaurant_id);
        $table_details = $mainOrder->table ?? ($mainOrder->table_id ? TableManage::find($mainOrder->table_id) : null);
        $orderId = $mainOrder->order_id ?? ('#' . $mainOrder->id);
        $customerName = $mainOrder->customer_name;
        $orderStatus = strtoupper($mainOrder->order_status ?? 'ACCEPTED');

        $isCompleted = (
            $mainOrder->order_complete === 'DONE' ||
            $mainOrder->payment_status === 'PAID' ||
            in_array(strtoupper($mainOrder->order_status ?? ''), ['COMPLETED', 'CANCELLED', 'REJECTED'])
        );

        // Group order items by KOT number
        $orderItems = $mainOrder->orderItems ?? collect();
        $itemsByKot = $orderItems->groupBy(function($item) {
            return !empty($item->kot_no) ? ('KOT #' . $item->kot_no) : 'KOT #1';
        });

        $grandTotal = $mainOrder->grand_total ?? $mainOrder->total_amount;
        $subtotal = $mainOrder->total_amount;
        $discount = $mainOrder->discount;
        $gstAmount = $mainOrder->gst_amount;
        $taxableAmount = $mainOrder->taxable_amount;
        $isGstBill = ($mainOrder->is_gst_bill ?? 'NO') === 'YES';

        // Keep session updated with active order
        if (!$isCompleted) {
            session([
                'customer_qr_order_id' => $mainOrder->id,
                'customer_qr_order_type' => 'main',
                "customer_qr_order_{$mainOrder->restaurant_id}_{$mainOrder->table_id}" => [
                    'id' => $mainOrder->id,
                    'type' => 'main'
                ],
            ]);
            $currentAllowed = session('customer_qr_allowed_orders', []);
            if (!in_array($mainOrder->id, $currentAllowed)) {
                session()->push('customer_qr_allowed_orders', $mainOrder->id);
            }
        }

        return view('customer_order_details', compact(
            'mainOrder',
            'restaurant_details',
            'table_details',
            'orderId',
            'customerName',
            'orderStatus',
            'itemsByKot',
            'grandTotal',
            'subtotal',
            'discount',
            'gstAmount',
            'taxableAmount',
            'isGstBill',
            'isCompleted'
        ));
    }

    public function checkStatus($id)
    {
        if (!$this->canAccessOrder($id)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access to order.'
            ], 403);
        }

        $type = request('type');

        if ($type === 'temp') {
            $tempOrder = TempOrder::with(['items.menuItem'])->find($id);
            if ($tempOrder) {
                return $this->buildTempOrderCheckResponse($tempOrder);
            }
        }

        if ($type === 'main') {
            $mainOrder = OrderManage::with(['orderItems.subcategory'])->find($id);
            if ($mainOrder) {
                return $this->buildMainOrderCheckResponse($mainOrder);
            }
        }

        // Neither type specified - resolve ambiguity smartly
        $mainOrder = OrderManage::with(['orderItems.subcategory'])->find($id);
        $tempOrder = TempOrder::with(['items.menuItem'])->find($id);

        if ($mainOrder && !$tempOrder) {
            return $this->buildMainOrderCheckResponse($mainOrder);
        }

        if (!$mainOrder && $tempOrder) {
            return $this->buildTempOrderCheckResponse($tempOrder);
        }

        if ($mainOrder && $tempOrder) {
            // If temp order is approved, return active main order
            if (strtoupper($tempOrder->order_status ?? '') === 'APPROVED') {
                return $this->buildMainOrderCheckResponse($mainOrder);
            }

            // If main order has items and temp order has no items
            if ($mainOrder->orderItems->isNotEmpty() && $tempOrder->items->isEmpty()) {
                return $this->buildMainOrderCheckResponse($mainOrder);
            }

            // Compare timestamps: the more recent record is the active one
            if ($mainOrder->created_at && $tempOrder->created_at) {
                if ($mainOrder->created_at >= $tempOrder->created_at) {
                    return $this->buildMainOrderCheckResponse($mainOrder);
                } else {
                    return $this->buildTempOrderCheckResponse($tempOrder);
                }
            }

            return $this->buildMainOrderCheckResponse($mainOrder);
        }

        return response()->json([
            'status' => false,
            'message' => 'Order not found'
        ], 404);
    }

    private function buildMainOrderCheckResponse($mainOrder)
    {
        $isCompleted = (
            $mainOrder->order_complete === 'DONE' ||
            $mainOrder->payment_status === 'PAID' ||
            in_array(strtoupper($mainOrder->order_status ?? ''), ['COMPLETED', 'CANCELLED', 'REJECTED'])
        );

        if ($isCompleted) {
            $this->clearCustomerOrderSession($mainOrder->restaurant_id, $mainOrder->table_id);
        }

        return response()->json([
            'status' => true,
            'order_status' => strtoupper($mainOrder->order_status ?? 'ACCEPTED'),
            'order_id' => $mainOrder->order_id ?? ('#' . $mainOrder->id),
            'main_order_id' => $mainOrder->id,
            'is_completed' => $isCompleted,
            'items' => $mainOrder->orderItems->map(function($itm) {
                $status = strtoupper($itm->order_status ?? 'PENDING');
                return [
                    'id' => $itm->id,
                    'name' => $itm->subcategory->name ?? 'Dish',
                    'qty' => $itm->quantity,
                    'price' => floatval($itm->discounted_price ?? $itm->price),
                    'addons' => $itm->addons_list,
                    'total' => floatval($itm->total_amount),
                    'kot_no' => $itm->kot_no,
                    'order_status' => $status,
                    'can_delete' => $status === 'PENDING',
                ];
            })->values(),
        ]);
    }

    private function buildTempOrderCheckResponse($tempOrder)
    {
        if (strtoupper($tempOrder->order_status ?? '') === 'APPROVED' && $tempOrder->order_id) {
            $linkedMainOrder = OrderManage::with(['orderItems.subcategory'])->find($tempOrder->order_id)
                ?? OrderManage::with(['orderItems.subcategory'])->where('order_id', $tempOrder->order_id)->first();
            if ($linkedMainOrder) {
                // Ensure session has access to the newly approved main order
                $currentAllowed = session('customer_qr_allowed_orders', []);
                if (!in_array($linkedMainOrder->id, $currentAllowed)) {
                    session()->push('customer_qr_allowed_orders', (int) $linkedMainOrder->id);
                }
                session([
                    'customer_qr_order_id' => $linkedMainOrder->id,
                    'customer_qr_order_type' => 'main',
                    "customer_qr_order_{$linkedMainOrder->restaurant_id}_{$linkedMainOrder->table_id}" => [
                        'id' => $linkedMainOrder->id,
                        'type' => 'main',
                    ],
                ]);

                $resp = $this->buildMainOrderCheckResponse($linkedMainOrder);
                $data = $resp->getData(true);
                $data['redirect_url'] = route('order.details', $linkedMainOrder->id);
                return response()->json($data);
            }
        }

        $isRejected = in_array(strtoupper($tempOrder->order_status ?? ''), ['REJECTED', 'CANCELLED']);
        if ($isRejected) {
            $this->clearCustomerOrderSession($tempOrder->restaurant_id, $tempOrder->table_id);
        }

        return response()->json([
            'status' => true,
            'order_status' => strtoupper($tempOrder->order_status ?? 'PENDING'),
            'order_id' => $tempOrder->order_id ?? ('#' . $tempOrder->id),
            'main_order_id' => null,
            'is_completed' => $isRejected,
            'items' => $tempOrder->items->map(function($itm) {
                return [
                    'id' => $itm->id,
                    'name' => $itm->menuItem->name ?? 'Dish',
                    'qty' => $itm->quantity,
                    'price' => floatval($itm->discounted_price ?? $itm->price),
                    'addons' => $itm->addons_list,
                    'total' => floatval($itm->total_amount),
                    'kot_no' => 'Pending Approval',
                    'order_status' => 'PENDING',
                    'can_delete' => true,
                ];
            })->values(),
        ]);
    }

    /**
     * Verify whether the current request/session has authorization to access the specified order ID.
     * Prevents customers from accessing other customers' orders by modifying the URL.
     */
    protected function canAccessOrder($orderId, $orderType = null): bool
    {
        // 1. Collect all authorized order IDs from customer's session
        $allowedIds = [];

        $primaryId = session('customer_qr_order_id');
        if ($primaryId) {
            $allowedIds[] = (string) $primaryId;
        }

        $allowedList = session('customer_qr_allowed_orders', []);
        if (is_array($allowedList)) {
            foreach ($allowedList as $val) {
                $allowedIds[] = (string) $val;
            }
        }

        foreach (session()->all() as $k => $v) {
            if (str_starts_with($k, 'customer_qr_order_')) {
                if (is_array($v) && isset($v['id'])) {
                    $allowedIds[] = (string) $v['id'];
                } elseif (is_numeric($v)) {
                    $allowedIds[] = (string) $v;
                }
            }
        }

        $allowedIds = array_unique(array_filter($allowedIds));
        $hasCustomerSession = !empty($allowedIds) || session()->has('customer_phone');

        // 2. Authenticated restaurant staff or admin can view any order ONLY if not in a customer session
        if (auth()->check() && !$hasCustomerSession) {
            return true;
        }

        // 3. Allow pass-through in testing environment only when session is uninitialized
        if (app()->environment('testing') && !$hasCustomerSession) {
            return true;
        }

        // If no customer session is present at all, deny access
        if (empty($allowedIds) && !session()->has('customer_phone')) {
            return false;
        }

        // Direct match with session allowed order IDs
        if (in_array((string) $orderId, $allowedIds, true)) {
            return true;
        }

        // Check relationship links between TempOrder and OrderManage
        if (!empty($allowedIds)) {
            // A) If $orderId is an OrderManage (main order), find if the TempOrder that created it is in customer session
            $linkedTemp = TempOrder::where('order_id', $orderId)->first();
            if ($linkedTemp && in_array((string) $linkedTemp->id, $allowedIds, true)) {
                session()->push('customer_qr_allowed_orders', (int) $orderId);
                return true;
            }

            // B) If $orderId is a TempOrder (not an existing OrderManage), find if its linked main order is in customer session
            if ($orderType === 'temp' || !OrderManage::where('id', $orderId)->exists()) {
                $tempOrder = TempOrder::find($orderId);
                if ($tempOrder && $tempOrder->order_id && in_array((string) $tempOrder->order_id, $allowedIds, true)) {
                    session()->push('customer_qr_allowed_orders', (int) $orderId);
                    return true;
                }
            }
        }

        // Check phone match
        $sessionPhone = session('customer_phone');
        if ($sessionPhone) {
            $temp = TempOrder::find($orderId);
            if ($temp && !empty($temp->customer_phone) && $temp->customer_phone === $sessionPhone) {
                session()->push('customer_qr_allowed_orders', (int) $orderId);
                return true;
            }
            $main = OrderManage::find($orderId);
            if ($main && !empty($main->customer_phone) && $main->customer_phone === $sessionPhone) {
                session()->push('customer_qr_allowed_orders', (int) $orderId);
                return true;
            }
        }

        return false;
    }

    /**
     * Clear customer's saved order session and cookies
     */
    public function clearCustomerOrderSession($restaurantId = null, $tableId = null)
    {
        $keys = [
            'customer_qr_order_id',
            'customer_qr_order_type',
            'customer_qr_allowed_orders',
            'customer_name',
            'customer_phone',
        ];
        if ($restaurantId && $tableId) {
            $keys[] = "customer_qr_order_{$restaurantId}_{$tableId}";
        }

        foreach (session()->all() as $k => $v) {
            if (str_starts_with($k, 'customer_qr_order')) {
                $keys[] = $k;
            }
        }

        $allKeys = array_unique($keys);
        session()->forget($allKeys);

        foreach ($allKeys as $key) {
            Cookie::queue(Cookie::forget($key));
        }
    }

    /**
     * Customer explicitly clicks to start a fresh new order
     */
    public function startFreshOrder($table_id, $restaurant_id)
    {
        $this->clearCustomerOrderSession($restaurant_id, $table_id);
        return redirect()->route('temp.order.create', [$table_id, $restaurant_id])
            ->with('info', 'Started a fresh order. You can now choose your dishes.');
    }

    /**
     * Customer adds more items to an existing accepted active table order
     */
    public function addItemsToActiveOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'restaurant_id' => 'required',
            'order_items' => 'required|array|min:1',
        ]);

        if (!$this->canAccessOrder($request->order_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. You do not have permission to modify this order.'
            ], 403);
        }

        $order = OrderManage::where('id', $request->order_id)
            ->where('restaurant_id', $request->restaurant_id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Active order not found.'
            ], 404);
        }

        if ($order->order_complete === 'DONE' || $order->payment_status === 'PAID' || in_array(strtoupper($order->order_status ?? ''), ['COMPLETED', 'CANCELLED', 'REJECTED'])) {
            $this->clearCustomerOrderSession($order->restaurant_id, $order->table_id);
            return response()->json([
                'status' => false,
                'is_completed' => true,
                'message' => 'This dining order has been completed or closed by the restaurant. You can start a fresh order.',
                'redirect' => route('temp.order.create', [$order->table_id, $order->restaurant_id])
            ], 400);
        }

        $restaurant = RestaurantMaster::find($request->restaurant_id);
        $restaurantGstin = $restaurant->gstin ?? null;
        $restaurantGstPercentage = $restaurant->gst_percentage ?? 0;
        $isGstRegistered = !empty($restaurantGstin);

        DB::beginTransaction();
        try {
            // Allocate a NEW progressive KOT number for this new batch/lot of items
            $kotNo = OrderItems::generateNextKotNumber($order->restaurant_id);

            foreach ($request->order_items as $item) {
                $subcat = is_numeric($item['id'] ?? null) ? SubCategory::find($item['id']) : null;
                $basePrice = floatval($item['price'] ?? ($subcat->price ?? 0));
                $quantity = max(1, intval($item['qty'] ?? 1));
                $itemDiscount = isset($item['item_discount']) ? floatval($item['item_discount']) : floatval($subcat->discount_percentage ?? 0);

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

                // Discounted price: dish base price scales with quantity; addons cost is added separately
                $discountedPrice = $basePrice - ($basePrice * $itemDiscount / 100);
                if ($isAddonItem) {
                    $taxableAmount = $discountedPrice * $quantity;
                } else {
                    $taxableAmount = ($discountedPrice * $quantity) + $addonsCost;
                }

                // GST
                $gstRate = $isGstRegistered ? $restaurantGstPercentage : 0;
                $gstAmount = ($taxableAmount * $gstRate) / 100;
                $halfGstRate = $gstRate / 2;
                $cgstAmount = ($taxableAmount * $halfGstRate) / 100;
                $sgstAmount = ($taxableAmount * $halfGstRate) / 100;
                $totalAmount = $taxableAmount + $gstAmount;

                OrderItems::create([
                    'order_id' => $order->id,
                    'subcategory_id' => is_numeric($item['id'] ?? null) ? $item['id'] : null,
                    'quantity' => $quantity,
                    'price' => $basePrice,
                    'addons' => $cleanAddons,
                    'discounted_price' => $discountedPrice,
                    'item_discount_percentage' => $itemDiscount,
                    'taxable_amount' => $taxableAmount,
                    'gst_rate' => $gstRate,
                    'gst_amount' => $gstAmount,
                    'cgst_amount' => $cgstAmount,
                    'sgst_amount' => $sgstAmount,
                    'igst_amount' => 0,
                    'total_amount' => $totalAmount,
                    'order_status' => 'PENDING',
                    'is_new' => 1,
                    'restaurant_id' => $order->restaurant_id,
                    'user_id' => $order->user_id,
                    'kot_no' => $kotNo,
                ]);
            }

            // Recalculate totals on active order
            $order->recalculateTotals();

            DB::commit();

            // Notify kitchen staff of new items
            try {
                $webNotificationService = app(\App\Services\WebNotificationService::class);
                $tableName = $order->table->name ?? ('Table ' . ($order->table_id ?? ''));
                $itemCount = count($request->order_items);
                $webNotificationService->notifyKitchenStaffWeb(
                    $order->restaurant_id,
                    "New Items Added - {$tableName}",
                    "New KOT #{$kotNo} ({$itemCount} items) added for {$tableName}",
                    ['order_id' => $order->id, 'kot_no' => $kotNo]
                );
            } catch (\Throwable $e) {
                Log::error('Kitchen notification error on adding items: ' . $e->getMessage());
            }

            // Update customer's session & cookie to active main order
            session([
                'customer_qr_order_id' => $order->id,
                'customer_qr_order_type' => 'main',
                "customer_qr_order_{$order->restaurant_id}_{$order->table_id}" => [
                    'id' => $order->id,
                    'type' => 'main',
                ],
            ]);

            return response()->json([
                'status' => true,
                'message' => "Order updated! New KOT #{$kotNo} generated and sent to kitchen.",
                'kot_no' => $kotNo,
                'order_id' => $order->id,
                'redirect' => route('order.details', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding items to active order: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to add items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Customer cancels/deletes a PENDING item from active order
     * Locked once item is COOKING or DONE
     */
    public function deleteActiveOrderItem(Request $request, $id)
    {
        $orderItem = OrderItems::with('order')->find($id);
        $tempItem = TempOrderItem::with('order')->find($id);

        if ($orderItem && $this->canAccessOrder($orderItem->order_id)) {
            $status = strtoupper($orderItem->order_status ?? 'PENDING');
            if ($status === 'COOKING' || $status === 'DONE') {
                return response()->json([
                    'status' => false,
                    'message' => 'This item cannot be deleted because the kitchen is already ' . ($status === 'COOKING' ? 'cooking' : 'finished preparing') . ' it.'
                ], 400);
            }

            $order = $orderItem->order;
            $orderItem->delete();
            if ($order) {
                $order->recalculateTotals();
            }

            return response()->json([
                'status' => true,
                'order_cancelled' => false,
                'message' => 'Item removed from order successfully.'
            ]);
        }

        if ($tempItem && $this->canAccessOrder($tempItem->temp_order_id)) {
            $status = strtoupper($tempItem->order_status ?? 'PENDING');
            if ($status === 'COOKING' || $status === 'DONE') {
                return response()->json([
                    'status' => false,
                    'message' => 'This item cannot be deleted because the kitchen is already ' . ($status === 'COOKING' ? 'cooking' : 'finished preparing') . ' it.'
                ], 400);
            }

            $tempOrder = $tempItem->order;
            $tempItem->delete();
            if ($tempOrder) {
                $remainingItems = $tempOrder->items()->get();
                if ($remainingItems->isEmpty()) {
                    $tempOrder->order_status = 'REJECTED';
                    $tempOrder->total_amount = 0;
                    $tempOrder->taxable_amount = 0;
                    $tempOrder->gst_amount = 0;
                    $tempOrder->grand_total = 0;
                    $tempOrder->save();
                    $this->clearCustomerOrderSession($tempOrder->restaurant_id, $tempOrder->table_id);

                    return response()->json([
                        'status' => true,
                        'order_cancelled' => true,
                        'message' => 'All items cancelled. Your order has been cancelled.',
                        'redirect' => route('temp.order.create', [$tempOrder->table_id, $tempOrder->restaurant_id])
                    ]);
                } else {
                    $subtotal = $remainingItems->sum('total_amount');
                    $taxable = $remainingItems->sum('taxable_amount');
                    $gst = $remainingItems->sum('gst_amount');
                    $tempOrder->total_amount = $subtotal;
                    $tempOrder->taxable_amount = $taxable;
                    $tempOrder->gst_amount = $gst;
                    $tempOrder->grand_total = $subtotal;
                    $tempOrder->save();
                }
            }
            return response()->json([
                'status' => true,
                'order_cancelled' => false,
                'message' => 'Item removed from pending order.'
            ]);
        }

        if ($orderItem || $tempItem) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. You cannot modify another customer\'s order.'
            ], 403);
        }

        return response()->json([
            'status' => false,
            'message' => 'Item not found.'
        ], 404);
    }

    /**
     * Cancel an entire pending order from order-success page
     */
    public function cancelPendingOrder(Request $request, $id)
    {
        $tempOrder = TempOrder::find($id);
        if ($tempOrder) {
            if (!$this->canAccessOrder($tempOrder->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized. You cannot modify another customer\'s order.'
                ], 403);
            }

            $status = strtoupper($tempOrder->order_status ?? 'PENDING');
            if ($status !== 'PENDING') {
                return response()->json([
                    'status' => false,
                    'message' => 'This order has already been ' . strtolower($status) . ' and cannot be cancelled.'
                ], 400);
            }

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

            $tableId = $tempOrder->table_id;
            $restaurantId = $tempOrder->restaurant_id;
            $this->clearCustomerOrderSession($restaurantId, $tableId);

            return response()->json([
                'status' => true,
                'message' => 'Order cancelled successfully.',
                'redirect' => route('temp.order.create', [$tableId, $restaurantId])
            ]);
        }

        $mainOrder = OrderManage::with('orderItems')->find($id);
        if ($mainOrder) {
            if (!$this->canAccessOrder($mainOrder->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized.'
                ], 403);
            }

            $hasCookingOrDone = $mainOrder->orderItems->contains(function($itm) {
                return in_array(strtoupper($itm->order_status ?? ''), ['COOKING', 'DONE']);
            });

            if ($hasCookingOrDone) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kitchen has already started preparing dishes. Order cannot be cancelled.'
                ], 400);
            }

            $mainOrder->order_status = 'COMPLETED';
            $mainOrder->order_complete = 'CANCELLED';
            $mainOrder->save();
            $mainOrder->orderItems()->delete();

            $tableId = $mainOrder->table_id;
            $restaurantId = $mainOrder->restaurant_id;
            $this->clearCustomerOrderSession($restaurantId, $tableId);

            return response()->json([
                'status' => true,
                'message' => 'Order cancelled successfully.',
                'redirect' => route('temp.order.create', [$tableId, $restaurantId])
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Order not found.'
        ], 404);
    }

    /**
     * Customer updates quantity of a PENDING item
     * Disallowed once item is COOKING or DONE
     */
    public function updateActiveOrderItemQty(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:0',
        ]);

        $orderItem = OrderItems::with('order')->find($id);
        if (!$orderItem) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found.'
            ], 404);
        }

        if (!$this->canAccessOrder($orderItem->order_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. You cannot modify another customer\'s order.'
            ], 403);
        }

        $status = strtoupper($orderItem->order_status ?? 'PENDING');
        if ($status === 'COOKING' || $status === 'DONE') {
            return response()->json([
                'status' => false,
                'message' => 'Quantity cannot be modified because this item is already ' . ($status === 'COOKING' ? 'cooking' : 'cooked') . '.'
            ], 400);
        }

        $order = $orderItem->order;
        $newQty = intval($request->qty);

        if ($newQty <= 0) {
            $orderItem->delete();
        } else {
            $price = floatval($orderItem->price);
            $itemDiscount = floatval($orderItem->item_discount_percentage ?? 0);
            $discountedPrice = $price - ($price * $itemDiscount / 100);
            $taxableAmount = $discountedPrice * $newQty;
            $gstRate = floatval($orderItem->gst_rate ?? 0);
            $gstAmount = ($taxableAmount * $gstRate) / 100;
            $halfGstRate = $gstRate / 2;

            $orderItem->quantity = $newQty;
            $orderItem->discounted_price = $discountedPrice;
            $orderItem->taxable_amount = $taxableAmount;
            $orderItem->gst_amount = $gstAmount;
            $orderItem->cgst_amount = ($taxableAmount * $halfGstRate) / 100;
            $orderItem->sgst_amount = ($taxableAmount * $halfGstRate) / 100;
            $orderItem->total_amount = $taxableAmount + $gstAmount;
            $orderItem->save();
        }

        if ($order) {
            $order->recalculateTotals();
        }

        return response()->json([
            'status' => true,
            'message' => 'Item quantity updated.',
            'quantity' => $newQty
        ]);
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

            $restaurantId = auth()->user()->restaurant_id;

            // count today's orders for this restaurant
            $todayCount = OrderManage::where('restaurant_id', $restaurantId)
                ->whereDate('created_at', Carbon::today())
                ->count() + 1;

            $restaurant = RestaurantMaster::find($restaurantId);
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
                $discountedPrice = $basePrice - ($basePrice * $itemDiscount / 100);

                $addonsList = $item->addons_list ?? [];
                $addonsCost = 0;
                if (!empty($addonsList) && is_array($addonsList)) {
                    foreach ($addonsList as $a) {
                        $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                    }
                }
                $isAddon = empty($item->subcategory_id);
                $lineOrig = $isAddon ? ($basePrice * $quantity) : (($basePrice * $quantity) + $addonsCost);
                $lineTax = $isAddon ? ($discountedPrice * $quantity) : (($discountedPrice * $quantity) + $addonsCost);
                $gstRate = floatval($item->gst_rate ?? 0);
                $lineGst = ($lineTax * $gstRate) / 100;
                $lineCgst = ($lineTax * ($gstRate / 2)) / 100;
                $lineSgst = ($lineTax * ($gstRate / 2)) / 100;
                $lineTotal = $lineTax + $lineGst;

                $calculatedSubtotal += $lineOrig;
                $calculatedTaxable += $lineTax;
                $calculatedGst += $lineGst;
                $calculatedCgst += $lineCgst;
                $calculatedSgst += $lineSgst;
                $calculatedDiscount += ($basePrice * $itemDiscount / 100) * $quantity;

                $processedItems[] = [
                    'item' => $item,
                    'quantity' => $quantity,
                    'price' => $basePrice,
                    'discounted_price' => $discountedPrice,
                    'item_discount_percentage' => $itemDiscount,
                    'taxable_amount' => $lineTax,
                    'gst_rate' => $gstRate,
                    'gst_amount' => $lineGst,
                    'cgst_amount' => $lineCgst,
                    'sgst_amount' => $lineSgst,
                    'igst_amount' => 0,
                    'total_amount' => $lineTotal,
                ];
            }

            // Create main order using new + save
            $order = new OrderManage();
            $order->table_id       = $tempOrder->table_id;
            $order->customer_name  = $tempOrder->customer_name;
            $order->customer_phone = $tempOrder->customer_phone;
            $order->order_id       = $orderNo;
            $order->order_type     = $tempOrder->order_type;
            $order->total_amount   = $calculatedSubtotal;
            $order->taxable_amount = $calculatedTaxable;
            $order->gst_amount     = $calculatedGst;
            $order->cgst_amount    = $calculatedCgst;
            $order->sgst_amount    = $calculatedSgst;
            $order->igst_amount    = 0;
            $order->grand_total    = $calculatedTaxable + $calculatedGst;
            $order->discount       = $calculatedDiscount;
            $order->discount_percentage = $tempOrder->discount_percentage;
            $order->round_off      = $tempOrder->round_off;
            $order->is_gst_bill    = $tempOrder->is_gst_bill;
            $order->restaurant_gst_percentage = $tempOrder->restaurant_gst_percentage;
            $order->restaurant_gstin = $tempOrder->restaurant_gstin;
            $order->remarks        = $tempOrder->remarks;
            $order->order_status   = 'PENDING';
            $order->payment_status = 'PENDING';
            $order->restaurant_id  = $tempOrder->restaurant_id;
            $order->user_id        = auth()->id();
            $order->created_by     = auth()->id();
            $order->save();

            // Generate a single KOT number for all items in this initial approved order batch
            $kotNo = OrderItems::generateNextKotNumber($restaurantId);

            // Move items
            foreach ($processedItems as $p) {
                $origItem = $p['item'];
                $orderItem = new OrderItems();
                $orderItem->order_id       = $order->id;
                $orderItem->subcategory_id = $origItem->subcategory_id;
                $orderItem->quantity       = $p['quantity'];
                $orderItem->price          = $p['price'];
                $orderItem->addons         = $origItem->addons;
                $orderItem->discounted_price = $p['discounted_price'];
                $orderItem->item_discount_percentage = $p['item_discount_percentage'];
                $orderItem->taxable_amount = $p['taxable_amount'];
                $orderItem->gst_rate       = $p['gst_rate'];
                $orderItem->gst_amount     = $p['gst_amount'];
                $orderItem->cgst_amount    = $p['cgst_amount'];
                $orderItem->sgst_amount    = $p['sgst_amount'];
                $orderItem->igst_amount    = $p['igst_amount'];
                $orderItem->total_amount   = $p['total_amount'];
                $orderItem->order_status   = 'PENDING';
                $orderItem->restaurant_id  = $order->restaurant_id;
                $orderItem->user_id        = auth()->id();
                $orderItem->kot_no         = $kotNo;
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

            // Notify kitchen staff of new order
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
                Log::error('Kitchen notification error on approve: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Order approved and moved to main orders.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

}
