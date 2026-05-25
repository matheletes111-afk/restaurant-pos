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
    $query = OrderManage::with('table')
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
        'total_discount_amount' => $allOrders->sum('discount'),
        // Add to your summary array in the controller
'total_item_discount' => $allOrders->sum(function($order) {
    $total = 0;
    foreach ($order->orderItems as $item) {
        $total += ($item->price * $item->quantity) - $item->taxable_amount;
    }
    return $total;
}),
'total_order_discount' => $allOrders->sum('discount'),
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

}
