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
    $paymentMethod = $request->payment_method;
    $paymentStatus = $request->payment_status;

    $restaurantId = auth()->user()->restaurant_id;

    // Base query
    $query = DB::table('orders')
        ->leftJoin('table_management', 'orders.table_id', '=', 'table_management.id')
        ->where('orders.restaurant_id', $restaurantId)
        ->whereBetween('orders.created_at', [$fromDate, $toDate]);

    // Apply filters
    if ($orderType && $orderType != 'all') {
        $query->where('orders.order_type', $orderType);
    }

    if ($paymentMethod && $paymentMethod != 'all') {
        $query->where('orders.payment_method', $paymentMethod);
    }

    if ($paymentStatus && $paymentStatus != 'all') {
        $query->where('orders.payment_status', $paymentStatus);
    }

    // Get orders with pagination
    $orders = $query->select(
            'orders.id',
            'orders.order_id',
            'orders.customer_name',
            'orders.customer_phone',
            'orders.order_type',
            'orders.total_amount',
            'orders.gst_amount',
            'orders.grand_total',
            'orders.amount_paid',
            'orders.payment_method',
            'orders.payment_status',
            'orders.created_at',
            'table_management.name as table_name'
        )
        ->orderBy('orders.created_at', 'desc')
        ->paginate(50);

    // Summary statistics
    $summary = [
        'total_orders' => $orders->total(),
        'total_revenue' => $orders->sum('grand_total'),
        'total_collected' => $orders->sum('amount_paid'),
        'pending_amount' => $orders->sum('grand_total') - $orders->sum('amount_paid'),
        
        // Count by order type
        'dine_in_count' => $query->clone()->where('order_type', 'DINE_IN')->count(),
        'takeaway_count' => $query->clone()->where('order_type', 'TAKEAWAY')->count(),
        
        // Count by payment status
        'paid_count' => $query->clone()->where('payment_status', 'PAID')->count(),
        'pending_count' => $query->clone()->where('payment_status', 'PENDING')->count(),
        'miscorder_count' => $query->clone()->where('payment_status', 'MISCORDER')->count(),
    ];

    // Get distinct values for dropdowns
    $orderTypes = DB::table('orders')
        ->where('restaurant_id', $restaurantId)
        ->distinct()
        ->pluck('order_type')
        ->filter()
        ->toArray();

    $paymentMethods = DB::table('orders')
        ->where('restaurant_id', $restaurantId)
        ->whereNotNull('payment_method')
        ->distinct()
        ->pluck('payment_method')
        ->filter()
        ->toArray();

    $paymentStatuses = DB::table('orders')
        ->where('restaurant_id', $restaurantId)
        ->distinct()
        ->pluck('payment_status')
        ->filter()
        ->toArray();

    return view('report.order-management', compact(
        'orders',
        'summary',
        'orderTypes',
        'paymentMethods',
        'paymentStatuses',
        'fromDate',
        'toDate',
        'orderType',
        'paymentMethod',
        'paymentStatus'
    ));
}
}
