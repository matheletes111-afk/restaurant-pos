<?php

namespace App\Http\Controllers;

use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\TableManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestaurantAnalyticsController extends Controller
{
    public function dashboard($id)
    {
        $restaurantId = $id;
        
        // Today's date range
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        // Overall Statistics
        $totalRevenue = OrderManage::where('restaurant_id', $restaurantId)
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->sum('amount_paid') ?? 0;
            
        $totalOrders = OrderManage::where('restaurant_id', $restaurantId)->count();
        
        // Today's Statistics
        $todayRevenue = OrderManage::where('restaurant_id', $restaurantId)
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->whereDate('created_at', $today)
            ->sum('amount_paid') ?? 0;
            
        $todayOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $today)
            ->count();
            
        $todayPaidOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->whereDate('created_at', $today)
            ->count();
            
        $todayMiscOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'MISCORDER')
            ->whereDate('created_at', $today)
            ->count();
        
        // This Month Statistics
        $monthRevenue = OrderManage::where('restaurant_id', $restaurantId)
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount_paid') ?? 0;
            
        $monthOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        
        // Category and Dish Statistics
        $totalCategories = Category::where('restaurant_id', $restaurantId)->count();
        $totalDishes = Subcategory::where('restaurant_id', $restaurantId)->count();
        
        // Table Statistics
        $totalTables = TableManage::where('restaurant_id', $restaurantId)->count();
        $occupiedTables = TableManage::where('restaurant_id', $restaurantId)
            ->where('table_status', 'OCCUPIED')
            ->count();
        
        // Trending Dishes (Last 7 days)
        $trendingDishes = OrderItems::select(
                'subcategory_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->whereHas('order', function($q) use ($restaurantId) {
                $q->where('restaurant_id', $restaurantId)
                  ->where('created_at', '>=', Carbon::now()->subDays(7));
            })
            ->with('subcategory')
            ->groupBy('subcategory_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
        
        // Recent Orders (Last 10 orders)
        $recentOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->with(['table', 'user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        // Payment Method Distribution
        $paymentMethods = OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount_paid) as total'))
            ->groupBy('payment_method')
            ->get();
        
        // Daily Revenue for Chart (Last 30 days)
        $dailyRevenue = OrderManage::where('restaurant_id', $restaurantId)
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount_paid) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Order Status Distribution
        $orderStatus = OrderManage::where('restaurant_id', $restaurantId)
            ->select('order_status', DB::raw('COUNT(*) as count'))
            ->groupBy('order_status')
            ->get();
        
        return view('restaurant.analytics', compact(
            'restaurantId',
            'totalRevenue',
            'totalOrders',
            'todayRevenue',
            'todayOrders',
            'todayPaidOrders',
            'todayMiscOrders',
            'monthRevenue',
            'monthOrders',
            'totalCategories',
            'totalDishes',
            'totalTables',
            'occupiedTables',
            'trendingDishes',
            'recentOrders',
            'paymentMethods',
            'dailyRevenue',
            'orderStatus'
        ));
    }
    
    public function filter(Request $request, $id)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);
        
        $restaurantId = $id;
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->endOfDay();
        
        // Filtered Statistics
        $filteredRevenue = OrderManage::where('restaurant_id', $restaurantId)
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('amount_paid') ?? 0;
            
        $filteredOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
            
        $filteredPaidOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
            
        $filteredMiscOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'MISCORDER')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
        
        // Filtered Trending Dishes
        $filteredTrendingDishes = OrderItems::select(
                'subcategory_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->whereHas('order', function($q) use ($restaurantId, $fromDate, $toDate) {
                $q->where('restaurant_id', $restaurantId)
                  ->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->with('subcategory')
            ->groupBy('subcategory_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
        
        // Filtered Orders
        $filteredOrdersList = OrderManage::where('restaurant_id', $restaurantId)
            ->with(['table', 'user'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderByDesc('created_at')
            ->get();
        
        // Filtered Payment Methods
        $filteredPaymentMethods = OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount_paid) as total'))
            ->groupBy('payment_method')
            ->get();
        
        // Daily Revenue for filtered period
        $filteredDailyRevenue = OrderManage::where('restaurant_id', $restaurantId)
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount_paid) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return view('restaurant.analytics', compact(
            'restaurantId',
            'fromDate',
            'toDate',
            'filteredRevenue',
            'filteredOrders',
            'filteredPaidOrders',
            'filteredMiscOrders',
            'filteredTrendingDishes',
            'filteredOrdersList',
            'filteredPaymentMethods',
            'filteredDailyRevenue'
        ))->with('isFiltered', true);
    }
    
    public function dailyRevenue($id)
    {
        $revenueData = OrderManage::where('restaurant_id', $id)
            ->whereIn('payment_status', ['PAID', 'MISCORDER'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount_paid) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return response()->json($revenueData);
    }
    
    public function topItems($id)
    {
        $topItems = OrderItems::select(
                'subcategory_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->whereHas('order', function($q) use ($id) {
                $q->where('restaurant_id', $id)
                  ->where('created_at', '>=', Carbon::now()->subDays(7));
            })
            ->with('subcategory')
            ->groupBy('subcategory_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
        
        return response()->json($topItems);
    }
}