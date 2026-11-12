<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\FranchiseRestaurantMapping;
use App\Models\OrderManage;
use App\Models\OrderItems;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FranchiseDashboardController extends Controller
{
    /**
     * Display Franchise Sponsorship Owner Executive Dashboard
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        // Security / Authorization: FSO or SA allowed
        if (!$authUser || (!in_array($authUser->role, ['FSO', 'SA']))) {
            abort(403, 'Unauthorized access. Only Franchise Sponsorship Owners or Admins can access this dashboard.');
        }

        // Allow Super Admin to preview any FSO dashboard
        $fsoId = $authUser->id;
        $allFsoOwners = collect();
        if ($authUser->role === 'SA') {
            $allFsoOwners = User::where('role', 'FSO')->orderBy('name', 'asc')->get();
            if ($request->filled('fso_id')) {
                $fsoId = (int) $request->fso_id;
            } elseif ($allFsoOwners->isNotEmpty()) {
                $fsoId = $allFsoOwners->first()->id;
            }
        }

        $fsoUser = User::find($fsoId) ?? $authUser;

        // 1. Fetch Mapped Parent Restaurants (where parent_id IS NULL or 0)
        $mappedParents = FranchiseRestaurantMapping::where('user_id', $fsoUser->id)
            ->whereHas('restaurant', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('parent_id')->orWhere('parent_id', 0);
                })->where('status', '!=', 'D');
            })
            ->with(['restaurant' => function ($q) {
                $q->with(['outlets' => function ($oq) {
                    $oq->where('status', '!=', 'D');
                }]);
            }])
            ->get()
            ->pluck('restaurant')
            ->filter();

        $parentIds = $mappedParents->pluck('id')->toArray();

        // 2. Fetch Sub-Restaurants / Outlets of these mapped parents
        $subRestaurants = collect();
        if (!empty($parentIds)) {
            $subRestaurants = RestaurantMaster::whereIn('parent_id', $parentIds)
                ->where('status', '!=', 'D')
                ->with('parent')
                ->get();
        }

        $subRestaurantIds = $subRestaurants->pluck('id')->toArray();

        // 3. Combined network of restaurants (Mapped Parents + their Sub-restaurants)
        $allRestaurantIds = array_unique(array_merge($parentIds, $subRestaurantIds));

        // Counts
        $mappedSponsorsCount = count($parentIds);
        $subRestaurantsCount = count($subRestaurantIds);
        $totalLocationsCount = count($allRestaurantIds);

        $today = Carbon::today();

        // 4. Today's Order Qty & Sales (Parent + Sub-Restaurants)
        $todayOrdersCount = 0;
        $todayItemsQty = 0;
        $todayRevenue = 0.0;
        $todayParentOrdersCount = 0;
        $todaySubOrdersCount = 0;

        // 5. Lifetime / Total Invoice Value
        $totalInvoiceValue = 0.0;
        $totalOrdersCount = 0;
        $parentTotalInvoiceValue = 0.0;
        $subTotalInvoiceValue = 0.0;
        $avgOrderValue = 0.0;

        if (!empty($allRestaurantIds)) {
            // Today's Orders across parents & sub-restaurants
            $todayOrdersCount = (int) OrderManage::whereIn('restaurant_id', $allRestaurantIds)
                ->whereDate('created_at', $today)
                ->count();

            $todayItemsQty = (int) OrderItems::whereIn('restaurant_id', $allRestaurantIds)
                ->whereDate('created_at', $today)
                ->sum('quantity');

            $todayRevenue = (float) OrderManage::whereIn('restaurant_id', $allRestaurantIds)
                ->whereDate('created_at', $today)
                ->sum('grand_total');

            if (!empty($parentIds)) {
                $todayParentOrdersCount = (int) OrderManage::whereIn('restaurant_id', $parentIds)
                    ->whereDate('created_at', $today)
                    ->count();
                $parentTotalInvoiceValue = (float) OrderManage::whereIn('restaurant_id', $parentIds)
                    ->sum('grand_total');
            }

            if (!empty($subRestaurantIds)) {
                $todaySubOrdersCount = (int) OrderManage::whereIn('restaurant_id', $subRestaurantIds)
                    ->whereDate('created_at', $today)
                    ->count();
                $subTotalInvoiceValue = (float) OrderManage::whereIn('restaurant_id', $subRestaurantIds)
                    ->sum('grand_total');
            }

            // Total Invoice Value
            $totalInvoiceValue = (float) OrderManage::whereIn('restaurant_id', $allRestaurantIds)
                ->sum('grand_total');

            $totalOrdersCount = (int) OrderManage::whereIn('restaurant_id', $allRestaurantIds)
                ->count();

            $avgOrderValue = $totalOrdersCount > 0 ? ($totalInvoiceValue / $totalOrdersCount) : 0.0;
        }

        // 6. Top 10 Best Dishes across mapped network
        $topDishes = collect();
        if (!empty($allRestaurantIds)) {
            $topDishes = OrderItems::whereIn('order_items.restaurant_id', $allRestaurantIds)
                ->join('sub_category', 'order_items.subcategory_id', '=', 'sub_category.id')
                ->leftJoin('category', 'sub_category.category_id', '=', 'category.id')
                ->select(
                    'order_items.subcategory_id',
                    'sub_category.name as dish_name',
                    'sub_category.food_type',
                    'sub_category.price as base_price',
                    'sub_category.image as dish_image',
                    'category.name as category_name',
                    DB::raw('SUM(order_items.quantity) as total_qty'),
                    DB::raw('SUM(order_items.total_amount) as total_revenue'),
                    DB::raw('COUNT(DISTINCT order_items.order_id) as total_orders_count')
                )
                ->groupBy(
                    'order_items.subcategory_id',
                    'sub_category.name',
                    'sub_category.food_type',
                    'sub_category.price',
                    'sub_category.image',
                    'category.name'
                )
                ->orderByDesc('total_qty')
                ->limit(10)
                ->get();
        }

        // 7. Top Performing Restaurant (Parent) & then their actual performing Sub-restaurants
        $parentPerformance = [];

        foreach ($mappedParents as $parent) {
            $pSales = (float) OrderManage::where('restaurant_id', $parent->id)->sum('grand_total');
            $pOrders = (int) OrderManage::where('restaurant_id', $parent->id)->count();
            $pTodaySales = (float) OrderManage::where('restaurant_id', $parent->id)->whereDate('created_at', $today)->sum('grand_total');
            $pTodayOrders = (int) OrderManage::where('restaurant_id', $parent->id)->whereDate('created_at', $today)->count();

            $subList = [];
            $subTotalSales = 0.0;
            $subTotalOrders = 0;

            foreach ($parent->outlets as $sub) {
                $sSales = (float) OrderManage::where('restaurant_id', $sub->id)->sum('grand_total');
                $sOrders = (int) OrderManage::where('restaurant_id', $sub->id)->count();
                $sTodaySales = (float) OrderManage::where('restaurant_id', $sub->id)->whereDate('created_at', $today)->sum('grand_total');
                $sTodayOrders = (int) OrderManage::where('restaurant_id', $sub->id)->whereDate('created_at', $today)->count();

                $subTotalSales += $sSales;
                $subTotalOrders += $sOrders;

                $subList[] = [
                    'restaurant' => $sub,
                    'sales' => $sSales,
                    'orders' => $sOrders,
                    'today_sales' => $sTodaySales,
                    'today_orders' => $sTodayOrders,
                ];
            }

            // Rank sub-restaurants by actual performance (sales desc)
            usort($subList, fn($a, $b) => $b['sales'] <=> $a['sales']);

            $groupTotalSales = $pSales + $subTotalSales;
            $groupTotalOrders = $pOrders + $subTotalOrders;

            $parentPerformance[] = [
                'parent' => $parent,
                'direct_sales' => $pSales,
                'direct_orders' => $pOrders,
                'today_sales' => $pTodaySales,
                'today_orders' => $pTodayOrders,
                'sub_restaurants' => $subList,
                'sub_count' => count($subList),
                'group_sales' => $groupTotalSales,
                'group_orders' => $groupTotalOrders,
            ];
        }

        // Rank parent restaurants by Group Sales descending
        usort($parentPerformance, fn($a, $b) => $b['group_sales'] <=> $a['group_sales']);

        // 8. Graphs Data (Last 14 Days Sales & Orders Trends)
        $chartDays = [];
        $chartSales = [];
        $chartOrders = [];

        for ($i = 13; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $dateStr = $d->format('Y-m-d');
            $label = $d->format('d M');

            $dSales = 0.0;
            $dOrders = 0;

            if (!empty($allRestaurantIds)) {
                $dSales = (float) OrderManage::whereIn('restaurant_id', $allRestaurantIds)
                    ->whereDate('created_at', $dateStr)
                    ->sum('grand_total');

                $dOrders = (int) OrderManage::whereIn('restaurant_id', $allRestaurantIds)
                    ->whereDate('created_at', $dateStr)
                    ->count();
            }

            $chartDays[] = $label;
            $chartSales[] = round($dSales, 2);
            $chartOrders[] = $dOrders;
        }

        // Top Parent Brands Revenue Chart
        $brandLabels = [];
        $brandSales = [];
        foreach (array_slice($parentPerformance, 0, 5) as $item) {
            $brandLabels[] = $item['parent']->name;
            $brandSales[] = round($item['group_sales'], 2);
        }

        // Order Types distribution (DINE-IN, TAKEAWAY, DELIVERY)
        $orderTypesMap = [
            'Dine-In' => 0,
            'Takeaway' => 0,
            'Delivery' => 0,
            'Others' => 0,
        ];

        if (!empty($allRestaurantIds)) {
            $rawTypes = OrderManage::whereIn('restaurant_id', $allRestaurantIds)
                ->select('order_type', DB::raw('count(*) as count'))
                ->groupBy('order_type')
                ->get();

            foreach ($rawTypes as $rt) {
                $typeKey = strtoupper($rt->order_type ?? '');
                if (str_contains($typeKey, 'DINE') || str_contains($typeKey, 'TABLE')) {
                    $orderTypesMap['Dine-In'] += $rt->count;
                } elseif (str_contains($typeKey, 'TAKE') || str_contains($typeKey, 'PARCEL')) {
                    $orderTypesMap['Takeaway'] += $rt->count;
                } elseif (str_contains($typeKey, 'DELIV')) {
                    $orderTypesMap['Delivery'] += $rt->count;
                } else {
                    $orderTypesMap['Others'] += $rt->count;
                }
            }
        }

        return view('franchise.dashboard', compact(
            'fsoUser',
            'allFsoOwners',
            'mappedSponsorsCount',
            'subRestaurantsCount',
            'totalLocationsCount',
            'todayOrdersCount',
            'todayItemsQty',
            'todayRevenue',
            'todayParentOrdersCount',
            'todaySubOrdersCount',
            'totalInvoiceValue',
            'totalOrdersCount',
            'parentTotalInvoiceValue',
            'subTotalInvoiceValue',
            'avgOrderValue',
            'topDishes',
            'parentPerformance',
            'chartDays',
            'chartSales',
            'chartOrders',
            'brandLabels',
            'brandSales',
            'orderTypesMap'
        ));
    }
}

