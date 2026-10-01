<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\User;
use App\Models\TableManage;
use DB;

class Dashboard extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $today = Carbon::today();

        // COUNTERS - Menu & Staff
        $totalDishes = SubCategory::where('status', '!=', 'D')
            ->where('restaurant_id', $restaurantId)->count();

        $totalVeg = SubCategory::where('food_type', 'VEG')
            ->where('restaurant_id', $restaurantId)->count();

        $totalNonVeg = SubCategory::where('food_type', 'NON-VEG')
            ->where('restaurant_id', $restaurantId)->count();

        $totalStaff = User::where('restaurant_id', $restaurantId)->count();

        // COUNTERS - Tables
        $totalTables = TableManage::where('status', '!=', 'D')
            ->where('restaurant_id', $restaurantId)->count();

        $occupiedTables = TableManage::where('status', '!=', 'D')
            ->where('restaurant_id', $restaurantId)
            ->where(function($q) {
                $q->where('table_status', 'OCCUPIED')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('order_id')->where('order_id', '>', 0);
                  });
            })->count();

        // COUNTERS - Orders & Revenue Today
        $totalOrdersToday = OrderManage::whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->count();

        $totalRevenueToday = OrderManage::whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->sum('grand_total');

        $avgOrderValue = $totalOrdersToday > 0 ? ($totalRevenueToday / $totalOrdersToday) : 0;

        // COUNTERS - Orders & Revenue Month
        $totalOrdersMonth = OrderManage::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->count();

        $totalRevenueMonth = OrderManage::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->sum('grand_total');

        $pendingOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->where('payment_status', '!=', 'PAID')
            ->whereNotIn('order_status', ['COMPLETED', 'CANCELLED', 'REJECTED'])
            ->count();

        // HOT DISHES
        $hotDaily = OrderItems::select('subcategory_id', DB::raw('SUM(quantity) as total'))
            ->whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->groupBy('subcategory_id')
            ->orderByDesc('total')
            ->with('subcategory')
            ->take(5)
            ->get();

        $hotMonthly = OrderItems::select('subcategory_id', DB::raw('SUM(quantity) as total'))
            ->whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->where('restaurant_id', $restaurantId)
            ->groupBy('subcategory_id')
            ->orderByDesc('total')
            ->with('subcategory')
            ->take(5)
            ->get();

        $hotYearly = OrderItems::select('subcategory_id', DB::raw('SUM(quantity) as total'))
            ->whereYear('created_at', $today->year)
            ->where('restaurant_id', $restaurantId)
            ->groupBy('subcategory_id')
            ->orderByDesc('total')
            ->with('subcategory')
            ->take(5)
            ->get();

        // TOP PRODUCT SERIES
        $topDailySeries   = $this->topProductsSeries("daily");
        $topMonthlySeries = $this->topProductsSeries("monthly");
        $topYearlySeries  = $this->topProductsSeries("yearly");

        // DISH LIST FOR DROPDOWN
        $dishes = SubCategory::where('status', '!=', 'D')
            ->where('restaurant_id', $restaurantId)
            ->orderBy('name', 'asc')
            ->get();

        // LATEST ORDERS
        $orders = OrderManage::with(['table', 'orderItems.subcategory'])
            ->where('restaurant_id', $restaurantId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view("dashboard.index", compact(
            "totalDishes", "totalVeg", "totalNonVeg", "totalStaff",
            "totalTables", "occupiedTables",
            "totalOrdersToday", "totalRevenueToday", "avgOrderValue",
            "totalOrdersMonth", "totalRevenueMonth", "pendingOrders",
            "hotDaily", "hotMonthly", "hotYearly",
            "topDailySeries", "topMonthlySeries", "topYearlySeries",
            "dishes", "orders"
        ));
    }

    // TOP PRODUCT SERIES FUNCTION
    protected function topProductsSeries($period = 'daily', $limit = 4)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $today = Carbon::today();

        $query = OrderItems::select('subcategory_id', DB::raw('SUM(quantity) as total'))
            ->where('restaurant_id', $restaurantId)
            ->groupBy('subcategory_id')
            ->with('subcategory')
            ->orderByDesc('total');

        if ($period == "daily") {
            $query->whereDate("created_at", $today);
        } elseif ($period == "monthly") {
            $query->whereYear('created_at', $today->year)
                  ->whereMonth('created_at', $today->month);
        } elseif ($period == "yearly") {
            $query->whereYear('created_at', $today->year);
        }

        return $query->take($limit)->get();
    }

    // DISH MONTHLY TREND
    public function dishMonthly($id)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $labels = [];
        $data = [];
        $now = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $labels[] = $m->format("M Y");

            $count = OrderItems::where("subcategory_id", $id)
                ->where("restaurant_id", $restaurantId)
                ->whereYear("created_at", $m->year)
                ->whereMonth("created_at", $m->month)
                ->sum("quantity");

            $data[] = $count;
        }

        return response()->json([
            "labels" => $labels,
            "data" => $data
        ]);
    }
}
