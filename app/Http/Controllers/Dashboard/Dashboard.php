<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\User;
use DB;

class Dashboard extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $today = Carbon::today();

        // COUNTERS
        $totalDishes = SubCategory::where('status', '!=', 'D')
            ->where('restaurant_id', $restaurantId)->count();

        $totalVeg = SubCategory::where('food_type', 'VEG')
            ->where('restaurant_id', $restaurantId)->count();

        $totalNonVeg = SubCategory::where('food_type', 'NON-VEG')
            ->where('restaurant_id', $restaurantId)->count();

        $totalOrdersToday = OrderManage::whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->count();

        $totalRevenueToday = OrderManage::whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'PAID')
            ->sum('grand_total');

        $totalStaff = User::where('restaurant_id', $restaurantId)->count();

        // HOT DISHES
        $hotDaily = OrderItems::select('subcategory_id', DB::raw('SUM(quantity) as total'))
            ->whereDate('created_at', $today)
            ->where('restaurant_id', $restaurantId)
            ->groupBy('subcategory_id')
            ->orderByDesc('total')
            ->with('subcategory')
            ->take(4)
            ->get();

        $hotMonthly = OrderItems::select('subcategory_id', DB::raw('SUM(quantity) as total'))
            ->whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->where('restaurant_id', $restaurantId)
            ->groupBy('subcategory_id')
            ->orderByDesc('total')
            ->with('subcategory')
            ->take(4)
            ->get();

        $hotYearly = OrderItems::select('subcategory_id', DB::raw('SUM(quantity) as total'))
            ->whereYear('created_at', $today->year)
            ->where('restaurant_id', $restaurantId)
            ->groupBy('subcategory_id')
            ->orderByDesc('total')
            ->with('subcategory')
            ->take(4)
            ->get();

        // TOP PRODUCT SERIES
        $topDailySeries   = $this->topProductsSeries("daily");
        $topMonthlySeries = $this->topProductsSeries("monthly");
        $topYearlySeries  = $this->topProductsSeries("yearly");

        // DISH LIST FOR DROPDOWN
        $dishes = SubCategory::where('status', '!=', 'D')
            ->where('restaurant_id', $restaurantId)
            ->get();

        // LATEST ORDERS
        $orders = OrderManage::with('table')
            ->where('restaurant_id', $restaurantId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view("dashboard.index", compact(
            "totalDishes", "totalVeg", "totalNonVeg",
            "totalOrdersToday", "totalRevenueToday", "totalStaff",
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
