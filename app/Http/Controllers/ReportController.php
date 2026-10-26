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
use App\Mail\OrderDeleteOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends Controller
{
public function topAnalysisReport(Request $request)
{
    // Default to last 30 days if no dates provided
    $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
    $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();

    $restaurantId = auth()->user()->restaurant_id;

    // TOP 10 CUSTOMERS (PAID ORDERS ONLY)
    $topCustomers = OrderManage::select(
            'customer_name',
            'customer_phone',
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(grand_total) as total_spent'),
            DB::raw('AVG(grand_total) as avg_order_value'),
            DB::raw('MAX(created_at) as last_order_date')
        )
        ->where('restaurant_id', $restaurantId)
        ->where('payment_status', 'PAID')
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereNotNull('customer_name')
        ->groupBy('customer_name', 'customer_phone')
        ->orderByDesc('total_spent')
        ->limit(10)
        ->get()
        ->map(function ($customer, $index) {
            $customer->rank = $index + 1;
            $customer->avg_order_value = number_format($customer->avg_order_value, 2);
            $customer->total_spent = number_format($customer->total_spent, 2);
            $customer->last_order_date = $customer->last_order_date ? Carbon::parse($customer->last_order_date)->format('d M Y') : 'N/A';
            return $customer;
        });

    // TOP 10 DISHES (ALL ORDER STATUSES)
    $topDishes = OrderItems::select(
            'sub_category.name as dish_name',
            'sub_category.food_type',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('COUNT(DISTINCT order_items.order_id) as total_orders'),
            DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'),
            DB::raw('AVG(order_items.price) as avg_price')
        )
        ->join('sub_category', 'order_items.subcategory_id', '=', 'sub_category.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('order_items.restaurant_id', $restaurantId) // Specify order_items.restaurant_id
        ->whereBetween('order_items.created_at', [$fromDate, $toDate])
        ->groupBy('sub_category.name', 'sub_category.food_type')
        ->orderByDesc('total_quantity')
        ->limit(10)
        ->get()
        ->map(function ($dish, $index) {
            $dish->rank = $index + 1;
            $dish->total_revenue = number_format($dish->total_revenue, 2);
            $dish->avg_price = number_format($dish->avg_price, 2);
            $dish->food_type_badge = $dish->food_type == 'veg' ? 'Veg' : 'Non-Veg';
            return $dish;
        });

    // Summary Statistics - FIXED ambiguous column references
    $summary = [
        'total_customers' => OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->distinct('customer_phone')
            ->count(),
        
        'total_orders' => OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count(),
        
        'total_revenue' => number_format(OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('grand_total'), 2),
        
        'avg_order_value' => number_format(OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->avg('grand_total') ?: 0, 2),
        
        'total_dishes_sold' => OrderItems::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.restaurant_id', $restaurantId) // SPECIFY table
            ->whereBetween('order_items.created_at', [$fromDate, $toDate])
            ->sum('order_items.quantity'),
        
        'unique_dishes' => OrderItems::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.restaurant_id', $restaurantId) // SPECIFY table
            ->whereBetween('order_items.created_at', [$fromDate, $toDate])
            ->distinct('order_items.subcategory_id')
            ->count(),
        
        'date_range' => $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y')
    ];

        return view('report.top-analysis', compact(
            'topCustomers', 
            'topDishes', 
            'summary',
            'fromDate',
            'toDate'
        ));
    }

public function orderAnalysisReport(Request $request)
{
    // Default to last 7 days if no dates provided
    $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
    $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();

    $restaurantId = auth()->user()->restaurant_id;

    // 1. ORDER TYPE COUNTS - from orders table
    $orderTypeCounts = DB::table('orders')
        ->select(
            'order_type',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(amount_paid) as total_amount')
        )
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->groupBy('order_type')
        ->get()
        ->keyBy('order_type');

    // 2. PAYMENT METHOD COUNTS - Check what values actually exist
    $paymentMethods = DB::table('orders')
        ->select('payment_method')
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->whereNotNull('payment_method')
        ->distinct()
        ->get()
        ->pluck('payment_method')
        ->toArray();

    // Get payment method counts based on actual values
    $paymentMethodCounts = DB::table('orders')
        ->select(
            'payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(amount_paid) as total_amount')
        )
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->whereNotNull('payment_method')
        ->groupBy('payment_method')
        ->get()
        ->keyBy('payment_method');

    // 3. PAYMENT STATUS COUNTS
    $paymentStatusCounts = DB::table('orders')
        ->select(
            'payment_status',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(amount_paid) as total_amount')
        )
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->groupBy('payment_status')
        ->get()
        ->keyBy('payment_status');

    // 4. VEG/NON-VEG ORDER COUNTS - Fixed join
    $vegNonVegCounts = DB::table('order_items')
        ->select(
            'sub_category.food_type',
            DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
            DB::raw('SUM(order_items.quantity) as item_count')
        )
        ->join('sub_category', 'order_items.subcategory_id', '=', 'sub_category.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('order_items.restaurant_id', $restaurantId)
        ->whereBetween('order_items.created_at', [$fromDate, $toDate])
        ->whereIn('orders.payment_status', ['PAID', 'MISCORDER'])
        ->whereNotNull('sub_category.food_type')
        ->groupBy('sub_category.food_type')
        ->get()
        ->keyBy('food_type');

    // 5. TOTAL AMOUNT (amount_paid)
    $totalAmount = DB::table('orders')
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->sum('amount_paid');

    // 6. PEAK ORDER DAY (top 1)
    $peakDay = DB::table('orders')
        ->select(
            DB::raw('DATE(created_at) as order_date'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(amount_paid) as total_amount')
        )
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderByDesc('order_count')
        ->first();

    // 7. DAILY ORDER TREND
    $dailyTrend = DB::table('orders')
        ->select(
            DB::raw('DATE(created_at) as order_date'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(amount_paid) as total_amount')
        )
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('order_date')
        ->get();

    // 8. HOURLY ORDER DISTRIBUTION
    $hourlyDistribution = DB::table('orders')
        ->select(
            DB::raw('HOUR(created_at) as order_hour'),
            DB::raw('COUNT(*) as order_count')
        )
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->groupBy(DB::raw('HOUR(created_at)'))
        ->orderBy('order_hour')
        ->get();

    // 9. AVERAGE ORDER VALUE
    $avgOrderValue = DB::table('orders')
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->avg('amount_paid') ?: 0;

    // 10. TOTAL ORDERS COUNT
    $totalOrders = DB::table('orders')
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->whereIn('payment_status', ['PAID', 'MISCORDER'])
        ->count();

    return view('report.order-analysis', compact(
        'fromDate',
        'toDate',
        'orderTypeCounts',
        'paymentMethodCounts',
        'paymentStatusCounts',
        'vegNonVegCounts',
        'totalAmount',
        'peakDay',
        'dailyTrend',
        'hourlyDistribution',
        'avgOrderValue',
        'totalOrders',
        'paymentMethods' // For displaying actual payment methods
    ));
 }

public function orderManagementReport(Request $request)
{
    // Get filter parameters
    $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
    $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
    $orderType = $request->order_type;
    $paymentStatus = $request->payment_status;

    $restaurantId = auth()->user()->restaurant_id;

    // Base query
    $query = OrderManage::with(['table', 'orderItems'])
        ->where('restaurant_id', $restaurantId)
        ->whereBetween('created_at', [$fromDate, $toDate]);

    // Apply filters
    if ($orderType && $orderType != 'all') {
        $query->where('order_type', $orderType);
    }

    if ($paymentStatus && $paymentStatus != 'all') {
        $query->where('payment_status', $paymentStatus);
    }

    // Get orders with pagination
    $orders = $query->orderBy('created_at', 'desc')->paginate(50);

    // Calculate summary statistics
    $summaryQuery = clone $query;
    $allOrders = $summaryQuery->get();
    
    $summary = [
        'total_orders' => $orders->total(),
        'total_revenue' => $allOrders->sum('grand_total'),
        'total_collected' => $allOrders->sum('amount_paid'),
        'pending_amount' => $allOrders->sum('grand_total') - $allOrders->sum('amount_paid'),
        
        // Count by order type
        'dine_in_count' => $allOrders->where('order_type', 'DINE_IN')->count(),
        'takeaway_count' => $allOrders->where('order_type', 'TAKEAWAY')->count(),
        
        // Count by payment status
        'paid_count' => $allOrders->where('payment_status', 'PAID')->count(),
        'pending_count' => $allOrders->where('payment_status', 'PENDING')->count(),
        'miscorder_count' => $allOrders->where('payment_status', 'MISCORDER')->count(),
        
        // GST Summary
        'gst_bills_count' => $allOrders->where('is_gst_bill', 'YES')->count(),
        'non_gst_bills_count' => $allOrders->where('is_gst_bill', 'NO')->count(),
        
        // GST Amount Summary
        'total_gst_amount' => $allOrders->sum('gst_amount'),
        'total_taxable_amount' => $allOrders->sum('taxable_amount'),
        
        // Discount Summary
        'total_item_discount' => $allOrders->sum(function($order) {
            $total = 0;
            if ($order->orderItems) {
                foreach ($order->orderItems as $item) {
                    $itemDiscPercent = floatval($item->item_discount_percentage ?? 0);
                    $qty = intval($item->quantity ?? 1);
                    $basePrice = floatval($item->price ?? 0);
                    if ($itemDiscPercent > 0) {
                        $total += ($basePrice * $itemDiscPercent / 100) * $qty;
                    } elseif (!empty($item->discounted_price) && $item->discounted_price < $basePrice) {
                        $total += ($basePrice - floatval($item->discounted_price)) * $qty;
                    }
                }
            }
            return $total;
        }),
        'total_order_discount' => $allOrders->sum(function($order) {
            return floatval($order->discount ?? 0);
        }),
        'total_discount_amount' => $allOrders->sum(function($order) {
            $itemDisc = 0;
            if ($order->orderItems) {
                foreach ($order->orderItems as $item) {
                    $itemDiscPercent = floatval($item->item_discount_percentage ?? 0);
                    $qty = intval($item->quantity ?? 1);
                    $basePrice = floatval($item->price ?? 0);
                    if ($itemDiscPercent > 0) {
                        $itemDisc += ($basePrice * $itemDiscPercent / 100) * $qty;
                    } elseif (!empty($item->discounted_price) && $item->discounted_price < $basePrice) {
                        $itemDisc += ($basePrice - floatval($item->discounted_price)) * $qty;
                    }
                }
            }
            return $itemDisc + floatval($order->discount ?? 0);
        }),
    ];

    // Get distinct values for dropdowns
    $orderTypes = OrderManage::where('restaurant_id', $restaurantId)
        ->distinct()
        ->pluck('order_type')
        ->filter()
        ->values()
        ->toArray();

    $paymentStatuses = OrderManage::where('restaurant_id', $restaurantId)
        ->distinct()
        ->pluck('payment_status')
        ->filter()
        ->values()
        ->toArray();

    return view('report.order-management', compact(
        'orders',
        'summary',
        'orderTypes',
        'paymentStatuses',
        'fromDate',
        'toDate',
        'orderType',
        'paymentStatus'
    ));
}



public function menuAvailability()
{
    // Get all subcategories (products) with their category relationship
    $data = SubCategory::with('category')
        ->where('restaurant_id', auth()->user()->restaurant_id)
        ->where('status', '!=', 'D')
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('menu_availability', compact('data'));
}

public function toggleAvailability(Request $request)
{
    try {
        $request->validate([
            'id' => 'required'
        ]);
        
        $product = SubCategory::find($request->id);
        
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        
        // Toggle status: A (Active) <-> I (Inactive)
        $newStatus = $product->status === 'A' ? 'I' : 'A';
        $product->status = $newStatus;
        $product->save();
        
        $statusText = $newStatus === 'A' ? 'Active' : 'Inactive';
        $statusClass = $newStatus === 'A' ? 'active' : 'inactive';
        
        return response()->json([
            'success' => true,
            'message' => "Product has been marked as {$statusText}",
            'status' => $newStatus,
            'status_text' => $statusText,
            'status_class' => $statusClass
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage()
        ], 500);
    }
}


public function updateDiscount(Request $request)
{
    try {
        
        $product = SubCategory::find($request->id);
        
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        
        $discount = $request->discount_percentage ?? 0;
        $product->discount_percentage = $discount;
        $product->save();
        
        return response()->json([
            'success' => true,
            'message' => "Discount updated to {$discount}% successfully",
            'discount_percentage' => $discount
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage()
        ], 500);
    }
}


/**
 * Item GST Summary Report
 */
public function itemGstSummary(Request $request)
{
    $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
    $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
    
    $restaurantId = auth()->user()->restaurant_id;
    
    // Query to get order items with details
    $query = OrderItems::with(['order', 'subcategory'])
        ->where('restaurant_id', $restaurantId)
        ->whereHas('order', function($q) use ($fromDate, $toDate) {
            $q->whereBetween('created_at', [$fromDate, $toDate])
              ->where('payment_status', 'PAID'); // Only show paid orders
        });
    
    // Get all items
    $items = $query->get();
    
    // Calculate totals
    $totals = [
        'total_taxable' => 0,
        'total_discount' => 0,
        'total_gst' => 0,
        'total_amount' => 0,
    ];
    
    $reportData = [];
    foreach ($items as $item) {
        $itemDiscount = $item->item_discount_percentage ?? 0;
        $originalPrice = $item->price;
        $quantity = $item->quantity;
        
        // Calculate discounted price
        $discountedPrice = $originalPrice - ($originalPrice * $itemDiscount / 100);
        $taxableAmount = $discountedPrice * $quantity;
        $gstAmount = $item->gst_amount ?? (($taxableAmount * ($item->gst_rate ?? 0)) / 100);
        $totalAmount = $taxableAmount + $gstAmount;
        $discountAmount = ($originalPrice * $quantity) - $taxableAmount;
        
        $reportData[] = [
            'id' => $item->id,
            'invoice_no' => $item->order->order_id ?? 'N/A',
            'item_name' => $item->subcategory->name ?? 'N/A',
            'category' => $item->subcategory->category->name ?? 'N/A',
            'quantity' => $quantity,
            'original_price' => $originalPrice,
            'discount_percentage' => $itemDiscount,
            'taxable_amount' => $taxableAmount,
            'discount_amount' => $discountAmount,
            'gst_rate' => $item->gst_rate ?? 0,
            'gst_amount' => $gstAmount,
            'total_amount' => $totalAmount,
            'order_date' => $item->order->created_at ?? null,
        ];
        
        $totals['total_taxable'] += $taxableAmount;
        $totals['total_discount'] += $discountAmount;
        $totals['total_gst'] += $gstAmount;
        $totals['total_amount'] += $totalAmount;
    }
    
    return view('report.item-gst-summary', compact('reportData', 'totals', 'fromDate', 'toDate'));
}

    /**
     * Send OTP for deleting an order to the restaurant admin's email.
     */
    public function sendOrderDeleteOtp(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'remarks' => 'required|string|max:1000',
        ]);

        $restaurantId = auth()->user()->restaurant_id;
        $order = OrderManage::where('restaurant_id', $restaurantId)->where('id', $request->order_id)->first();
        
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        // Find the admin user of this restaurant
        $admin = User::where('restaurant_id', $restaurantId)->where('role_type', 'ADMIN')->first();
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Restaurant Administrator email not found.'], 404);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Save OTP, order_id, remarks, and timestamp in session (or cache)
        Session::put('order_delete_otp_' . $order->id, [
            'otp' => $otp,
            'remarks' => $request->remarks,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send email to admin
        try {
            $mailData = [
                'name' => $admin->name,
                'email' => $admin->email,
                'order_uid' => $order->order_id,
                'remarks' => $request->remarks,
                'otp' => $otp
            ];
            Mail::to($admin->email)->send(new OrderDeleteOtpMail($mailData));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP email: ' . $e->getMessage()], 500);
        }

        // Helper function to mask email
        $maskEmail = function ($email) {
            $parts = explode('@', $email);
            if (count($parts) < 2) return $email;
            $name = $parts[0];
            $domain = $parts[1];
            $len = strlen($name);
            if ($len <= 3) {
                $maskedName = substr($name, 0, 1) . str_repeat('*', $len - 1);
            } else {
                $maskedName = substr($name, 0, 2) . str_repeat('*', $len - 4) . substr($name, -2);
            }
            return $maskedName . '@' . $domain;
        };

        return response()->json(['success' => true, 'message' => 'OTP sent successfully to ' . $maskEmail($admin->email)]);
    }

    /**
     * Verify OTP and soft delete the order.
     */
    public function verifyAndDeleteOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'otp' => 'required|numeric',
        ]);

        $restaurantId = auth()->user()->restaurant_id;
        $order = OrderManage::where('restaurant_id', $restaurantId)->where('id', $request->order_id)->first();
        
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $otpData = Session::get('order_delete_otp_' . $order->id);

        if (!$otpData) {
            return response()->json(['success' => false, 'message' => 'OTP request expired or not found. Please request a new OTP.'], 400);
        }

        if (Carbon::now()->greaterThan($otpData['expires_at'])) {
            Session::forget('order_delete_otp_' . $order->id);
            return response()->json(['success' => false, 'message' => 'OTP has expired. Please request a new OTP.'], 400);
        }

        if (intval($request->otp) !== intval($otpData['otp'])) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP. Please try again.'], 400);
        }

        // Save remarks and soft delete the order
        $order->remarks = ($order->remarks ? $order->remarks . "\n" : "") . "[Soft Deleted Reason: " . $otpData['remarks'] . "]";
        $order->save();
        $order->delete(); // Soft deletes the order

        // Clear OTP session
        Session::forget('order_delete_otp_' . $order->id);

        return response()->json(['success' => true, 'message' => 'Order deleted successfully.']);
    }

    /**
     * Resolve active restaurant ID (handles multi-outlets and standard restaurant logins)
     */
    private function getActiveRestaurantId()
    {
        $user = auth()->user();
        if (session()->has('active_outlet_id')) {
            return session('active_outlet_id');
        }
        return method_exists($user, 'getSubscriptionRestaurantId') ? $user->getSubscriptionRestaurantId() : $user->restaurant_id;
    }

    /**
     * 1. Sales By Category & Food Item Report
     */
    public function salesByCategoryReport(Request $request)
    {
        // Always default to current month
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedCategoryId = $request->category_id;

        $restaurantId = $this->getActiveRestaurantId();

        $categories = Category::where('restaurant_id', $restaurantId)
            ->where('status', 'A')
            ->orderBy('name')
            ->get();

        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('sub_category', 'order_items.subcategory_id', '=', 'sub_category.id')
            ->leftJoin('category', 'sub_category.category_id', '=', 'category.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereIn('orders.payment_status', ['PAID', 'MISCORDER'])
            ->whereBetween('orders.created_at', [$fromDate, $toDate]);

        if ($selectedCategoryId && $selectedCategoryId !== 'all') {
            $query->where('category.id', $selectedCategoryId);
        }

        $salesData = $query->select(
            'category.id as category_id',
            DB::raw('COALESCE(category.name, "Uncategorized") as category_name'),
            'sub_category.id as item_id',
            'sub_category.name as item_name',
            'sub_category.food_type',
            DB::raw('COUNT(DISTINCT orders.id) as order_count'),
            DB::raw('SUM(order_items.quantity) as total_qty'),
            DB::raw('AVG(order_items.price) as avg_price'),
            DB::raw('SUM(COALESCE(order_items.taxable_amount, order_items.price * order_items.quantity)) as total_taxable'),
            DB::raw('SUM(COALESCE(order_items.gst_amount, 0)) as total_gst'),
            DB::raw('SUM(COALESCE(order_items.total_amount, order_items.price * order_items.quantity)) as total_amount')
        )
        ->groupBy('category.id', 'category.name', 'sub_category.id', 'sub_category.name', 'sub_category.food_type')
        ->orderBy('category_name', 'asc')
        ->orderByDesc('total_amount')
        ->get();

        $totalRevenue = $salesData->sum('total_amount');
        $totalQuantity = $salesData->sum('total_qty');
        $totalTaxable = $salesData->sum('total_taxable');
        $totalGst = $salesData->sum('total_gst');
        $totalOrdersCount = $salesData->sum('order_count');
        $uniqueCategoriesCount = $salesData->pluck('category_name')->unique()->count();
        $uniqueDishesCount = $salesData->pluck('item_id')->unique()->count();

        // Calculate % of total sales for each item
        $salesData->transform(function ($item) use ($totalRevenue) {
            $item->share_pct = $totalRevenue > 0 ? round(($item->total_amount / $totalRevenue) * 100, 1) : 0;
            return $item;
        });

        $groupedByCategory = $salesData->groupBy('category_name');

        $summary = [
            'total_revenue' => $totalRevenue,
            'total_quantity' => $totalQuantity,
            'total_taxable' => $totalTaxable,
            'total_gst' => $totalGst,
            'total_orders' => $totalOrdersCount,
            'unique_categories' => $uniqueCategoriesCount,
            'unique_dishes' => $uniqueDishesCount,
            'date_range' => $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y')
        ];

        return view('report.sales-by-category', compact(
            'groupedByCategory',
            'categories',
            'summary',
            'fromDate',
            'toDate',
            'selectedCategoryId'
        ));
    }

    /**
     * 1. Export Sales By Category & Food Item (Excel)
     */
    public function exportSalesByCategory(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedCategoryId = $request->category_id;
        $restaurantId = $this->getActiveRestaurantId();
        $restaurant = RestaurantMaster::find($restaurantId);

        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('sub_category', 'order_items.subcategory_id', '=', 'sub_category.id')
            ->leftJoin('category', 'sub_category.category_id', '=', 'category.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereIn('orders.payment_status', ['PAID', 'MISCORDER'])
            ->whereBetween('orders.created_at', [$fromDate, $toDate]);

        if ($selectedCategoryId && $selectedCategoryId !== 'all') {
            $query->where('category.id', $selectedCategoryId);
        }

        $salesData = $query->select(
            DB::raw('COALESCE(category.name, "Uncategorized") as category_name'),
            'sub_category.name as item_name',
            'sub_category.food_type',
            DB::raw('COUNT(DISTINCT orders.id) as order_count'),
            DB::raw('SUM(order_items.quantity) as total_qty'),
            DB::raw('AVG(order_items.price) as avg_price'),
            DB::raw('SUM(COALESCE(order_items.taxable_amount, order_items.price * order_items.quantity)) as total_taxable'),
            DB::raw('SUM(COALESCE(order_items.gst_amount, 0)) as total_gst'),
            DB::raw('SUM(COALESCE(order_items.total_amount, order_items.price * order_items.quantity)) as total_amount')
        )
        ->groupBy('category.id', 'category.name', 'sub_category.id', 'sub_category.name', 'sub_category.food_type')
        ->orderBy('category_name', 'asc')
        ->orderByDesc('total_amount')
        ->get();

        $totalRevenue = $salesData->sum('total_amount');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales By Category');

        // Title & metadata banner
        $sheet->setCellValue('A1', ($restaurant->name ?? 'Restaurant') . ' - Sales By Category Food Item Report');
        $sheet->setCellValue('A2', 'Date Range: ' . $fromDate->format('d M Y') . ' to ' . $toDate->format('d M Y') . ' | Generated: ' . now()->format('d M Y h:i A'));
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getFont()->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        // Table Header
        $headers = [
            'A4' => 'Category',
            'B4' => 'Food Item Name',
            'C4' => 'Type (Veg/Non-Veg)',
            'D4' => 'Orders Count',
            'E4' => 'Quantity Sold',
            'F4' => 'Avg Price (₹)',
            'G4' => 'Taxable Amount (₹)',
            'H4' => 'GST Amount (₹)',
            'I4' => 'Total Amount (₹)'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF5E14']
            ]
        ];
        $sheet->getStyle('A4:I4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(28);

        $row = 5;
        foreach ($salesData as $item) {
            $sheet->setCellValue('A' . $row, $item->category_name);
            $sheet->setCellValue('B' . $row, $item->item_name);
            $sheet->setCellValue('C' . $row, strtoupper($item->food_type ?: 'N/A'));
            $sheet->setCellValue('D' . $row, $item->order_count);
            $sheet->setCellValue('E' . $row, $item->total_qty);
            $sheet->setCellValue('F' . $row, round($item->avg_price, 2));
            $sheet->setCellValue('G' . $row, round($item->total_taxable, 2));
            $sheet->setCellValue('H' . $row, round($item->total_gst, 2));
            $sheet->setCellValue('I' . $row, round($item->total_amount, 2));

            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }
            $row++;
        }

        // Totals Row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('D' . $row, $salesData->sum('order_count'));
        $sheet->setCellValue('E' . $row, $salesData->sum('total_qty'));
        $sheet->setCellValue('G' . $row, round($salesData->sum('total_taxable'), 2));
        $sheet->setCellValue('H' . $row, round($salesData->sum('total_gst'), 2));
        $sheet->setCellValue('I' . $row, round($totalRevenue, 2));

        $totalStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFE4D6']
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFF5E14']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FFFF5E14']]
            ]
        ];
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($totalStyle);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "sales_by_category_" . $fromDate->format('Ymd') . "_" . $toDate->format('Ymd') . ".xlsx";

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * 2. Popular Item Report
     */
    public function popularItemsReport(Request $request)
    {
        // Always default to current month
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedCategoryId = $request->category_id;
        $foodType = $request->food_type;
        $sortBy = $request->sort_by ?? 'qty'; // qty, orders, revenue

        $restaurantId = $this->getActiveRestaurantId();

        $categories = Category::where('restaurant_id', $restaurantId)
            ->where('status', 'A')
            ->orderBy('name')
            ->get();

        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('sub_category', 'order_items.subcategory_id', '=', 'sub_category.id')
            ->leftJoin('category', 'sub_category.category_id', '=', 'category.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereIn('orders.payment_status', ['PAID', 'MISCORDER'])
            ->whereBetween('orders.created_at', [$fromDate, $toDate]);

        if ($selectedCategoryId && $selectedCategoryId !== 'all') {
            $query->where('category.id', $selectedCategoryId);
        }
        if ($foodType && in_array(strtolower($foodType), ['veg', 'non-veg', 'non_veg'])) {
            $query->where('sub_category.food_type', strtolower($foodType));
        }

        $query->select(
            'sub_category.id as item_id',
            'sub_category.name as item_name',
            'sub_category.food_type',
            DB::raw('COALESCE(category.name, "General") as category_name'),
            DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
            DB::raw('SUM(order_items.quantity) as total_qty'),
            DB::raw('AVG(order_items.price) as avg_price'),
            DB::raw('SUM(COALESCE(order_items.total_amount, order_items.price * order_items.quantity)) as total_revenue')
        )
        ->groupBy('sub_category.id', 'sub_category.name', 'sub_category.food_type', 'category.name');

        if ($sortBy === 'orders') {
            $query->orderByDesc('total_orders');
        } elseif ($sortBy === 'revenue') {
            $query->orderByDesc('total_revenue');
        } else {
            $query->orderByDesc('total_qty');
        }

        $popularItems = $query->get();

        $totalRevenueAll = $popularItems->sum('total_revenue');
        $totalQtyAll = $popularItems->sum('total_qty');
        $totalOrdersCount = $popularItems->sum('total_orders');

        // Calculate rank & percentage share
        $popularItems = $popularItems->map(function ($item, $index) use ($totalRevenueAll, $totalQtyAll) {
            $item->rank = $index + 1;
            $item->rev_share_pct = $totalRevenueAll > 0 ? round(($item->total_revenue / $totalRevenueAll) * 100, 1) : 0;
            $item->qty_share_pct = $totalQtyAll > 0 ? round(($item->total_qty / $totalQtyAll) * 100, 1) : 0;
            return $item;
        });

        $topItem = $popularItems->first();

        $summary = [
            'top_item_name' => $topItem ? $topItem->item_name : 'N/A',
            'top_item_qty' => $topItem ? $topItem->total_qty : 0,
            'top_item_rev' => $topItem ? $topItem->total_revenue : 0,
            'total_unique_items' => $popularItems->count(),
            'total_quantity_sold' => $totalQtyAll,
            'total_revenue' => $totalRevenueAll,
            'total_orders' => $totalOrdersCount,
            'date_range' => $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y')
        ];

        return view('report.popular-items', compact(
            'popularItems',
            'categories',
            'summary',
            'fromDate',
            'toDate',
            'selectedCategoryId',
            'foodType',
            'sortBy'
        ));
    }

    /**
     * 2. Export Popular Item Report (Excel)
     */
    public function exportPopularItems(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedCategoryId = $request->category_id;
        $foodType = $request->food_type;
        $sortBy = $request->sort_by ?? 'qty';
        $restaurantId = $this->getActiveRestaurantId();
        $restaurant = RestaurantMaster::find($restaurantId);

        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('sub_category', 'order_items.subcategory_id', '=', 'sub_category.id')
            ->leftJoin('category', 'sub_category.category_id', '=', 'category.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereIn('orders.payment_status', ['PAID', 'MISCORDER'])
            ->whereBetween('orders.created_at', [$fromDate, $toDate]);

        if ($selectedCategoryId && $selectedCategoryId !== 'all') {
            $query->where('category.id', $selectedCategoryId);
        }
        if ($foodType && in_array(strtolower($foodType), ['veg', 'non-veg', 'non_veg'])) {
            $query->where('sub_category.food_type', strtolower($foodType));
        }

        $query->select(
            'sub_category.name as item_name',
            'sub_category.food_type',
            DB::raw('COALESCE(category.name, "General") as category_name'),
            DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
            DB::raw('SUM(order_items.quantity) as total_qty'),
            DB::raw('AVG(order_items.price) as avg_price'),
            DB::raw('SUM(COALESCE(order_items.total_amount, order_items.price * order_items.quantity)) as total_revenue')
        )
        ->groupBy('sub_category.id', 'sub_category.name', 'sub_category.food_type', 'category.name');

        if ($sortBy === 'orders') {
            $query->orderByDesc('total_orders');
        } elseif ($sortBy === 'revenue') {
            $query->orderByDesc('total_revenue');
        } else {
            $query->orderByDesc('total_qty');
        }

        $items = $query->get();
        $totalRevenueAll = $items->sum('total_revenue');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Popular Items');

        $sheet->setCellValue('A1', ($restaurant->name ?? 'Restaurant') . ' - Popular Item Report');
        $sheet->setCellValue('A2', 'Date Range: ' . $fromDate->format('d M Y') . ' to ' . $toDate->format('d M Y') . ' | Generated: ' . now()->format('d M Y h:i A'));
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getFont()->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        $headers = [
            'A4' => 'Rank',
            'B4' => 'Item Name',
            'C4' => 'Category',
            'D4' => 'Type',
            'E4' => 'Total Orders',
            'F4' => 'Quantity Sold',
            'G4' => 'Avg Price (₹)',
            'H4' => 'Total Revenue (₹)'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF5E14']
            ]
        ];
        $sheet->getStyle('A4:H4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(28);

        $row = 5;
        $rank = 1;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, '#' . $rank++);
            $sheet->setCellValue('B' . $row, $item->item_name);
            $sheet->setCellValue('C' . $row, $item->category_name);
            $sheet->setCellValue('D' . $row, strtoupper($item->food_type ?: 'N/A'));
            $sheet->setCellValue('E' . $row, $item->total_orders);
            $sheet->setCellValue('F' . $row, $item->total_qty);
            $sheet->setCellValue('G' . $row, round($item->avg_price, 2));
            $sheet->setCellValue('H' . $row, round($item->total_revenue, 2));

            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':H' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }
            $row++;
        }

        // Totals Row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('E' . $row, $items->sum('total_orders'));
        $sheet->setCellValue('F' . $row, $items->sum('total_qty'));
        $sheet->setCellValue('H' . $row, round($totalRevenueAll, 2));

        $totalStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFE4D6']],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFF5E14']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FFFF5E14']]
            ]
        ];
        $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($totalStyle);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "popular_items_" . $fromDate->format('Ymd') . "_" . $toDate->format('Ymd') . ".xlsx";

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * 3. Payment Method Report With Graph
     */
    public function paymentMethodsReport(Request $request)
    {
        // Always default to current month
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedMethod = $request->payment_method;

        $restaurantId = $this->getActiveRestaurantId();

        // 1. Summary by Payment Method
        $rawSummaries = DB::table('orders')
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(amount_paid) as total_collected'),
                DB::raw('AVG(amount_paid) as avg_order_val')
            )
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->groupBy('payment_method')
            ->get();

        $grouped = [];
        foreach ($rawSummaries as $row) {
            $method = !empty(trim($row->payment_method ?? '')) ? strtoupper(trim($row->payment_method)) : 'UNSPECIFIED';
            if (!isset($grouped[$method])) {
                $grouped[$method] = (object)[
                    'method' => $method,
                    'total_orders' => 0,
                    'total_collected' => 0,
                    'avg_order_val' => 0,
                ];
            }
            $grouped[$method]->total_orders += intval($row->total_orders);
            $grouped[$method]->total_collected += floatval($row->total_collected);
        }

        $methodSummaries = collect($grouped)->map(function ($item) {
            $item->avg_order_val = $item->total_orders > 0 ? ($item->total_collected / $item->total_orders) : 0;
            return $item;
        })->sortByDesc('total_collected')->values();

        $totalRevenue = $methodSummaries->sum('total_collected');
        $totalOrders = $methodSummaries->sum('total_orders');

        $methodSummaries->transform(function ($item) use ($totalRevenue, $totalOrders) {
            $item->rev_pct = $totalRevenue > 0 ? round(($item->total_collected / $totalRevenue) * 100, 1) : 0;
            $item->orders_pct = $totalOrders > 0 ? round(($item->total_orders / $totalOrders) * 100, 1) : 0;
            return $item;
        });

        // 2. Daily trends for Chart
        $rawDailyTrends = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as order_date'),
                'payment_method',
                DB::raw('SUM(amount_paid) as daily_amount'),
                DB::raw('COUNT(*) as daily_orders')
            )
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->groupBy(DB::raw('DATE(created_at)'), 'payment_method')
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        $dailyTrends = $rawDailyTrends->map(function ($t) {
            $t->method = !empty(trim($t->payment_method ?? '')) ? strtoupper(trim($t->payment_method)) : 'UNSPECIFIED';
            return $t;
        });

        // Build structured data for Chart.js
        $chartPieLabels = $methodSummaries->pluck('method')->toArray();
        $chartPieRevenue = $methodSummaries->pluck('total_collected')->map(fn($v) => round($v, 2))->toArray();
        $chartPieOrders = $methodSummaries->pluck('total_orders')->toArray();

        // Group daily trend by dates
        $uniqueDates = $dailyTrends->pluck('order_date')->unique()->values()->toArray();
        $trendDatasets = [];
        $palette = ['#ff5e14', '#10b981', '#0284c7', '#8b5cf6', '#f59e0b', '#ec4899', '#6366f1'];
        $idx = 0;
        foreach ($methodSummaries as $m) {
            $methodName = $m->method;
            $amountsByDate = [];
            foreach ($uniqueDates as $d) {
                $match = $dailyTrends->first(fn($t) => $t->order_date === $d && $t->method === $methodName);
                $amountsByDate[] = $match ? round($match->daily_amount, 2) : 0;
            }
            $trendDatasets[] = [
                'label' => $methodName,
                'data' => $amountsByDate,
                'borderColor' => $palette[$idx % count($palette)],
                'backgroundColor' => $palette[$idx % count($palette)],
                'tension' => 0.3,
                'fill' => false
            ];
            $idx++;
        }

        // 3. Detailed Orders Query
        $ordersQuery = OrderManage::with('table')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIn('payment_status', ['PAID', 'MISCORDER']);

        if ($selectedMethod && $selectedMethod !== 'all') {
            $ordersQuery->where('payment_method', $selectedMethod);
        }

        $recentOrders = $ordersQuery->orderByDesc('created_at')->paginate(25)->withQueryString();

        $summary = [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'avg_order_value' => $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0,
            'top_method' => $methodSummaries->first()->method ?? 'N/A',
            'top_method_rev' => $methodSummaries->first()->total_collected ?? 0,
            'date_range' => $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y')
        ];

        return view('report.payment-methods', compact(
            'methodSummaries',
            'recentOrders',
            'summary',
            'fromDate',
            'toDate',
            'selectedMethod',
            'chartPieLabels',
            'chartPieRevenue',
            'chartPieOrders',
            'uniqueDates',
            'trendDatasets'
        ));
    }

    /**
     * 3. Export Payment Method Report (Excel)
     */
    public function exportPaymentMethods(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedMethod = $request->payment_method;
        $restaurantId = $this->getActiveRestaurantId();
        $restaurant = RestaurantMaster::find($restaurantId);

        $query = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIn('payment_status', ['PAID', 'MISCORDER']);

        if ($selectedMethod && $selectedMethod !== 'all') {
            $query->where('payment_method', $selectedMethod);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Payment Methods');

        $sheet->setCellValue('A1', ($restaurant->name ?? 'Restaurant') . ' - Payment Method Report');
        $sheet->setCellValue('A2', 'Date Range: ' . $fromDate->format('d M Y') . ' to ' . $toDate->format('d M Y') . ' | Generated: ' . now()->format('d M Y h:i A'));
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getFont()->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        $headers = [
            'A4' => 'Invoice #',
            'B4' => 'Date & Time',
            'C4' => 'Customer Name',
            'D4' => 'Customer Phone',
            'E4' => 'Order Type',
            'F4' => 'Payment Method',
            'G4' => 'Amount Paid (₹)'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF5E14']]
        ];
        $sheet->getStyle('A4:G4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(28);

        $row = 5;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $order->order_id ?: ('#' . $order->id));
            $sheet->setCellValue('B' . $row, Carbon::parse($order->created_at)->format('d M Y h:i A'));
            $sheet->setCellValue('C' . $row, $order->customer_name ?: 'Guest');
            $sheet->setCellValue('D' . $row, $order->customer_phone ?: 'N/A');
            $sheet->setCellValue('E' . $row, $order->order_type);
            $sheet->setCellValue('F' . $row, strtoupper($order->payment_method ?: 'UNSPECIFIED'));
            $sheet->setCellValue('G' . $row, round(floatval($order->amount_paid ?: $order->grand_total), 2));

            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }
            $row++;
        }

        // Totals Row
        $sheet->setCellValue('A' . $row, 'TOTAL (' . count($orders) . ' Orders)');
        $sheet->setCellValue('G' . $row, round($orders->sum(fn($o) => floatval($o->amount_paid ?: $o->grand_total)), 2));

        $totalStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFE4D6']],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFF5E14']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FFFF5E14']]
            ]
        ];
        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($totalStyle);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "payment_methods_" . $fromDate->format('Ymd') . "_" . $toDate->format('Ymd') . ".xlsx";

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * 4. New Vs Return Customer Report Analysis
     */
    public function customerAnalysisReport(Request $request)
    {
        // Always default to current month
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedType = $request->customer_type; // all, new, returning

        $restaurantId = $this->getActiveRestaurantId();

        // 1. Fetch all orders with a customer phone or name in this period
        $periodOrders = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->where(function($q) {
                $q->whereNotNull('customer_phone')->where('customer_phone', '!=', '')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('customer_name')->where('customer_name', '!=', '');
                  });
            })
            ->get();

        // Group orders in current period by customer identifier (phone or name)
        $customerGroups = [];
        $dailyCustomerStats = [];

        foreach ($periodOrders as $order) {
            $phone = trim($order->customer_phone ?? '');
            $name = trim($order->customer_name ?? '');
            $key = $phone ?: $name;
            if (!$key) continue;

            $dateKey = Carbon::parse($order->created_at)->format('Y-m-d');

            if (!isset($customerGroups[$key])) {
                $customerGroups[$key] = [
                    'key' => $key,
                    'phone' => $phone ?: 'N/A',
                    'name' => $name ?: 'Guest Customer',
                    'period_orders_count' => 0,
                    'period_spent' => 0,
                    'latest_period_order' => $order->created_at,
                    'order_dates' => [],
                ];
            }

            $customerGroups[$key]['period_orders_count']++;
            $customerGroups[$key]['period_spent'] += floatval($order->amount_paid ?: $order->grand_total);
            $customerGroups[$key]['order_dates'][] = $dateKey;
            if ($order->created_at > $customerGroups[$key]['latest_period_order']) {
                $customerGroups[$key]['latest_period_order'] = $order->created_at;
            }
        }

        // Get lifetime stats for all identified customers
        $phones = collect($customerGroups)->pluck('phone')->filter(fn($p) => $p !== 'N/A')->unique()->toArray();
        $names = collect($customerGroups)->filter(fn($c) => $c['phone'] === 'N/A')->pluck('name')->unique()->toArray();

        $lifetimeStatsByPhone = [];
        if (!empty($phones)) {
            $lifetimeStatsByPhone = DB::table('orders')
                ->select(
                    'customer_phone',
                    DB::raw('MIN(created_at) as first_order_date'),
                    DB::raw('COUNT(*) as lifetime_orders'),
                    DB::raw('SUM(amount_paid) as lifetime_spent')
                )
                ->where('restaurant_id', $restaurantId)
                ->whereIn('payment_status', ['PAID', 'MISCORDER'])
                ->whereIn('customer_phone', $phones)
                ->groupBy('customer_phone')
                ->get()
                ->keyBy('customer_phone')
                ->toArray();
        }

        $lifetimeStatsByName = [];
        if (!empty($names)) {
            $lifetimeStatsByName = DB::table('orders')
                ->select(
                    'customer_name',
                    DB::raw('MIN(created_at) as first_order_date'),
                    DB::raw('COUNT(*) as lifetime_orders'),
                    DB::raw('SUM(amount_paid) as lifetime_spent')
                )
                ->where('restaurant_id', $restaurantId)
                ->whereIn('payment_status', ['PAID', 'MISCORDER'])
                ->whereIn('customer_name', $names)
                ->groupBy('customer_name')
                ->get()
                ->keyBy('customer_name')
                ->toArray();
        }

        $processedCustomers = collect();
        $newCount = 0;
        $returningCount = 0;
        $newRevenue = 0;
        $returningRevenue = 0;
        $newOrders = 0;
        $returningOrders = 0;

        $dailyNewCount = [];
        $dailyReturningCount = [];

        foreach ($customerGroups as $key => $c) {
            $stats = null;
            if ($c['phone'] !== 'N/A' && isset($lifetimeStatsByPhone[$c['phone']])) {
                $stats = $lifetimeStatsByPhone[$c['phone']];
            } elseif (isset($lifetimeStatsByName[$c['name']])) {
                $stats = $lifetimeStatsByName[$c['name']];
            }

            $firstOrderDate = $stats ? Carbon::parse($stats->first_order_date) : Carbon::parse($c['latest_period_order']);
            $isNew = $firstOrderDate->greaterThanOrEqualTo($fromDate);

            if ($isNew) {
                $newCount++;
                $newRevenue += $c['period_spent'];
                $newOrders += $c['period_orders_count'];
            } else {
                $returningCount++;
                $returningRevenue += $c['period_spent'];
                $returningOrders += $c['period_orders_count'];
            }

            // Track daily visits
            foreach (array_unique($c['order_dates']) as $d) {
                if ($isNew) {
                    $dailyNewCount[$d] = ($dailyNewCount[$d] ?? 0) + 1;
                } else {
                    $dailyReturningCount[$d] = ($dailyReturningCount[$d] ?? 0) + 1;
                }
            }

            $customerObj = (object) [
                'name' => $c['name'],
                'phone' => $c['phone'],
                'type' => $isNew ? 'NEW' : 'RETURNING',
                'period_orders' => $c['period_orders_count'],
                'period_spent' => $c['period_spent'],
                'lifetime_orders' => $stats ? $stats->lifetime_orders : $c['period_orders_count'],
                'lifetime_spent' => $stats ? $stats->lifetime_spent : $c['period_spent'],
                'first_order_date' => $firstOrderDate->format('d M Y'),
                'latest_order_date' => Carbon::parse($c['latest_period_order'])->format('d M Y h:i A'),
            ];

            if ($selectedType === 'new' && !$isNew) continue;
            if ($selectedType === 'returning' && $isNew) continue;

            $processedCustomers->push($customerObj);
        }

        $totalCustomers = $newCount + $returningCount;
        $totalPeriodRevenue = $newRevenue + $returningRevenue;
        $repeatRate = $totalCustomers > 0 ? round(($returningCount / $totalCustomers) * 100, 1) : 0;
        $newRate = $totalCustomers > 0 ? round(($newCount / $totalCustomers) * 100, 1) : 0;

        // Build Daily Trend Chart Labels and Datasets
        $periodDates = [];
        $current = Carbon::parse($fromDate);
        $end = Carbon::parse($toDate);
        while ($current->lte($end)) {
            $periodDates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        $chartNewData = [];
        $chartReturningData = [];
        foreach ($periodDates as $d) {
            $chartNewData[] = $dailyNewCount[$d] ?? 0;
            $chartReturningData[] = $dailyReturningCount[$d] ?? 0;
        }

        $summary = [
            'total_customers' => $totalCustomers,
            'new_customers' => $newCount,
            'new_customers_pct' => $newRate,
            'returning_customers' => $returningCount,
            'returning_customers_pct' => $repeatRate,
            'new_revenue' => $newRevenue,
            'returning_revenue' => $returningRevenue,
            'total_revenue' => $totalPeriodRevenue,
            'new_orders' => $newOrders,
            'returning_orders' => $returningOrders,
            'new_aov' => $newOrders > 0 ? ($newRevenue / $newOrders) : 0,
            'returning_aov' => $returningOrders > 0 ? ($returningRevenue / $returningOrders) : 0,
            'date_range' => $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y')
        ];

        $customersList = $processedCustomers->sortByDesc('period_spent')->values();

        return view('report.customer-analysis', compact(
            'customersList',
            'summary',
            'fromDate',
            'toDate',
            'selectedType',
            'periodDates',
            'chartNewData',
            'chartReturningData'
        ));
    }

    /**
     * 4. Export New Vs Return Customer Report (Excel)
     */
    public function exportCustomerAnalysis(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedType = $request->customer_type;
        $restaurantId = $this->getActiveRestaurantId();
        $restaurant = RestaurantMaster::find($restaurantId);

        $periodOrders = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->where(function($q) {
                $q->whereNotNull('customer_phone')->where('customer_phone', '!=', '')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('customer_name')->where('customer_name', '!=', '');
                  });
            })
            ->get();

        $customerGroups = [];
        foreach ($periodOrders as $order) {
            $phone = trim($order->customer_phone ?? '');
            $name = trim($order->customer_name ?? '');
            $key = $phone ?: $name;
            if (!$key) continue;

            if (!isset($customerGroups[$key])) {
                $customerGroups[$key] = [
                    'key' => $key,
                    'phone' => $phone ?: 'N/A',
                    'name' => $name ?: 'Guest',
                    'period_orders_count' => 0,
                    'period_spent' => 0,
                    'latest_period_order' => $order->created_at,
                ];
            }
            $customerGroups[$key]['period_orders_count']++;
            $customerGroups[$key]['period_spent'] += floatval($order->amount_paid ?: $order->grand_total);
            if ($order->created_at > $customerGroups[$key]['latest_period_order']) {
                $customerGroups[$key]['latest_period_order'] = $order->created_at;
            }
        }

        $phones = collect($customerGroups)->pluck('phone')->filter(fn($p) => $p !== 'N/A')->unique()->toArray();
        $names = collect($customerGroups)->filter(fn($c) => $c['phone'] === 'N/A')->pluck('name')->unique()->toArray();

        $lifetimeStatsByPhone = [];
        if (!empty($phones)) {
            $lifetimeStatsByPhone = DB::table('orders')
                ->select(
                    'customer_phone',
                    DB::raw('MIN(created_at) as first_order_date'),
                    DB::raw('COUNT(*) as lifetime_orders'),
                    DB::raw('SUM(amount_paid) as lifetime_spent')
                )
                ->where('restaurant_id', $restaurantId)
                ->whereIn('payment_status', ['PAID', 'MISCORDER'])
                ->whereIn('customer_phone', $phones)
                ->groupBy('customer_phone')
                ->get()
                ->keyBy('customer_phone')
                ->toArray();
        }

        $lifetimeStatsByName = [];
        if (!empty($names)) {
            $lifetimeStatsByName = DB::table('orders')
                ->select(
                    'customer_name',
                    DB::raw('MIN(created_at) as first_order_date'),
                    DB::raw('COUNT(*) as lifetime_orders'),
                    DB::raw('SUM(amount_paid) as lifetime_spent')
                )
                ->where('restaurant_id', $restaurantId)
                ->whereIn('payment_status', ['PAID', 'MISCORDER'])
                ->whereIn('customer_name', $names)
                ->groupBy('customer_name')
                ->get()
                ->keyBy('customer_name')
                ->toArray();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer Analysis');

        $sheet->setCellValue('A1', ($restaurant->name ?? 'Restaurant') . ' - New vs Returning Customer Analysis');
        $sheet->setCellValue('A2', 'Date Range: ' . $fromDate->format('d M Y') . ' to ' . $toDate->format('d M Y') . ' | Generated: ' . now()->format('d M Y h:i A'));
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getFont()->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        $headers = [
            'A4' => 'Customer Name',
            'B4' => 'Phone Number',
            'C4' => 'Customer Type',
            'D4' => 'Orders in Period',
            'E4' => 'Spent in Period (₹)',
            'F4' => 'Lifetime Orders',
            'G4' => 'Lifetime Spent (₹)',
            'H4' => 'First Visit Date'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF5E14']]
        ];
        $sheet->getStyle('A4:H4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(28);

        $row = 5;
        $totalPeriodSpend = 0;
        foreach ($customerGroups as $key => $c) {
            $stats = null;
            if ($c['phone'] !== 'N/A' && isset($lifetimeStatsByPhone[$c['phone']])) {
                $stats = $lifetimeStatsByPhone[$c['phone']];
            } elseif (isset($lifetimeStatsByName[$c['name']])) {
                $stats = $lifetimeStatsByName[$c['name']];
            }

            $firstOrderDate = $stats ? Carbon::parse($stats->first_order_date) : Carbon::parse($c['latest_period_order']);
            $isNew = $firstOrderDate->greaterThanOrEqualTo($fromDate);

            if ($selectedType === 'new' && !$isNew) continue;
            if ($selectedType === 'returning' && $isNew) continue;

            $sheet->setCellValue('A' . $row, $c['name']);
            $sheet->setCellValue('B' . $row, $c['phone']);
            $sheet->setCellValue('C' . $row, $isNew ? 'NEW' : 'RETURNING');
            $sheet->setCellValue('D' . $row, $c['period_orders_count']);
            $sheet->setCellValue('E' . $row, round($c['period_spent'], 2));
            $sheet->setCellValue('F' . $row, $stats ? $stats->lifetime_orders : $c['period_orders_count']);
            $sheet->setCellValue('G' . $row, round($stats ? $stats->lifetime_spent : $c['period_spent'], 2));
            $sheet->setCellValue('H' . $row, $firstOrderDate->format('d M Y'));

            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':H' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }
            $totalPeriodSpend += $c['period_spent'];
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('E' . $row, round($totalPeriodSpend, 2));

        $totalStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFE4D6']],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFF5E14']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FFFF5E14']]
            ]
        ];
        $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($totalStyle);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "customer_analysis_" . $fromDate->format('Ymd') . "_" . $toDate->format('Ymd') . ".xlsx";

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

}

