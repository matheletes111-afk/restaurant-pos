<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantMaster;
use App\Models\FranchiseRestaurantMapping;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FranchisePerformanceController extends Controller
{
    /**
     * Helper to retrieve authorized mapped parent restaurant IDs for the current user.
     */
    protected function getAuthorizedParentIds()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        if ($user->role === 'SA') {
            return RestaurantMaster::where(function ($q) {
                    $q->whereNull('parent_id')->orWhere('parent_id', 0);
                })
                ->where('status', '!=', 'D')
                ->pluck('id')
                ->toArray();
        }

        return FranchiseRestaurantMapping::where('user_id', $user->id)
            ->whereHas('restaurant', function ($q) {
                $q->where(function ($p) {
                    $p->whereNull('parent_id')->orWhere('parent_id', 0);
                })->where('status', '!=', 'D');
            })
            ->pluck('restaurant_id')
            ->toArray();
    }

    /**
     * Resolve and authorize context for a specific restaurant report.
     */
    protected function resolveRestaurantContext($restaurantId, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $restaurant = RestaurantMaster::with(['owner', 'outlets'])->findOrFail($restaurantId);

        // Security check for FSO: must be a mapped parent OR a child of a mapped parent
        if ($user->role !== 'SA') {
            $authorizedParentIds = $this->getAuthorizedParentIds();
            $effectiveParentId = $restaurant->parent_id ?: $restaurant->id;

            if (!in_array($effectiveParentId, $authorizedParentIds)) {
                abort(403, 'Unauthorized access to this restaurant report.');
            }
        }

        $mainRestaurant = $restaurant->getMainRestaurant();
        $availableOutlets = collect([$mainRestaurant])->merge($mainRestaurant->outlets);

        return [
            'targetRestaurant' => $restaurant,
            'targetRestaurantId' => $restaurant->id,
            'mainRestaurant' => $mainRestaurant,
            'availableOutlets' => $availableOutlets,
            'isMultiBranch' => $availableOutlets->count() > 1,
            'selectedOutletId' => $restaurant->id,
        ];
    }

    /**
     * PAGE 1: Performance Mapped Parent Restaurants
     * Lists mapped parent restaurants with name, address, and associated restaurant count (or 0).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $parentIds = $this->getAuthorizedParentIds();

        $query = RestaurantMaster::with(['outlets', 'owner'])
            ->whereIn('id', $parentIds)
            ->where('status', '!=', 'D');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('restaurant_id_unique', 'like', "%{$search}%");
            });
        }

        $parents = $query->orderBy('name', 'asc')->get();

        // Calculate quick KPIs for each parent group
        $today = Carbon::today();
        foreach ($parents as $parent) {
            $associatedCount = $parent->outlets->count();
            $parent->associated_count = $associatedCount;

            // Direct link logic: if 0 associated, direct to report, else to associated restaurants page
            if ($associatedCount === 0) {
                $parent->target_url = route('franchise.performance.report', ['restaurantId' => $parent->id]);
                $parent->is_direct_report = true;
            } else {
                $parent->target_url = route('franchise.performance.parent', ['parentId' => $parent->id]);
                $parent->is_direct_report = false;
            }

            // Quick metrics for card display
            $groupIds = array_merge([$parent->id], $parent->outlets->pluck('id')->toArray());
            $parent->group_today_orders = OrderManage::whereIn('restaurant_id', $groupIds)->whereDate('created_at', $today)->count();
            $parent->group_total_revenue = OrderManage::whereIn('restaurant_id', $groupIds)->sum('grand_total') ?: 0;
            $parent->group_total_orders = OrderManage::whereIn('restaurant_id', $groupIds)->count();
        }

        $totalSponsorsCount = $parents->count();
        $totalNetworkOutlets = $parents->sum(function($p) {
            return 1 + $p->associated_count;
        });
        $totalTodayOrders = $parents->sum('group_today_orders');
        $totalNetworkRevenue = $parents->sum('group_total_revenue');

        return view('franchise.performance.index', compact(
            'parents',
            'totalSponsorsCount',
            'totalNetworkOutlets',
            'totalTodayOrders',
            'totalNetworkRevenue'
        ));
    }

    /**
     * PAGE 2: Associated Restaurants Page (Including Parent)
     * Triggered when clicking a parent restaurant that has associated restaurants.
     */
    public function branches($parentId, Request $request)
    {
        $user = auth()->user();
        $authorizedParentIds = $this->getAuthorizedParentIds();

        if ($user->role !== 'SA' && !in_array((int)$parentId, $authorizedParentIds)) {
            abort(403, 'Unauthorized access to this parent restaurant.');
        }

        $parent = RestaurantMaster::with(['outlets', 'owner'])->findOrFail($parentId);
        $today = Carbon::today();

        // Collect Parent (HQ) + all associated sub-restaurants
        $allRestaurants = collect([$parent])->merge($parent->outlets);

        foreach ($allRestaurants as $rest) {
            $rest->is_hq = empty($rest->parent_id);
            $rest->today_orders = OrderManage::where('restaurant_id', $rest->id)->whereDate('created_at', $today)->count();
            $rest->today_revenue = OrderManage::where('restaurant_id', $rest->id)->whereDate('created_at', $today)->sum('grand_total') ?: 0;
            $rest->total_orders = OrderManage::where('restaurant_id', $rest->id)->count();
            $rest->total_revenue = OrderManage::where('restaurant_id', $rest->id)->sum('grand_total') ?: 0;
            $rest->total_dishes = SubCategory::where('restaurant_id', $rest->id)->where('status', '!=', 'D')->count();
            // Link to that restaurant's master report
            $rest->report_url = route('franchise.performance.report', ['restaurantId' => $rest->id]);
        }

        $parentTotalNetworkOrders = $allRestaurants->sum('total_orders');
        $parentTotalNetworkRevenue = $allRestaurants->sum('total_revenue');
        $parentTodayNetworkOrders = $allRestaurants->sum('today_orders');
        $parentTodayNetworkRevenue = $allRestaurants->sum('today_revenue');

        return view('franchise.performance.branches', compact(
            'parent',
            'allRestaurants',
            'parentTotalNetworkOrders',
            'parentTotalNetworkRevenue',
            'parentTodayNetworkOrders',
            'parentTodayNetworkRevenue'
        ));
    }

    /**
     * PAGE 3 - TAB 1: Live Interactive Performance Dashboard (Before Executive Overview)
     */
    public function reportDashboard($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];
        $today = Carbon::today();

        // 1. All-time Sales Sum Till Now & Total Orders
        $totalSalesSum = (float) OrderManage::where('restaurant_id', $restaurantId)->sum('grand_total') ?: 0.0;
        $totalOrdersCount = (int) OrderManage::where('restaurant_id', $restaurantId)->count();
        $avgOrderValue = $totalOrdersCount > 0 ? round($totalSalesSum / $totalOrdersCount, 2) : 0.0;
        $totalCollected = (float) OrderManage::where('restaurant_id', $restaurantId)->sum('amount_paid') ?: 0.0;
        $totalDue = (float) OrderManage::where('restaurant_id', $restaurantId)
            ->where(function ($q) {
                $q->whereNull('payment_status')->orWhere('payment_status', '!=', 'PAID');
            })
            ->selectRaw('COALESCE(SUM(GREATEST(0, grand_total - COALESCE(amount_paid, 0))), 0) as due')
            ->value('due') ?: 0.0;

        // 2. Today's Key Metrics
        $todayOrdersCount = (int) OrderManage::where('restaurant_id', $restaurantId)->whereDate('created_at', $today)->count();
        $todayOrderValue = (float) OrderManage::where('restaurant_id', $restaurantId)->whereDate('created_at', $today)->sum('grand_total') ?: 0.0;
        $todayItemsQty = (int) OrderItems::where('restaurant_id', $restaurantId)->whereDate('created_at', $today)->sum('quantity');

        // 3. This Month Key Metrics
        $monthOrdersCount = (int) OrderManage::where('restaurant_id', $restaurantId)->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->count();
        $monthOrderValue = (float) OrderManage::where('restaurant_id', $restaurantId)->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->sum('grand_total') ?: 0.0;

        // 4. Top 10 Best Dishes
        $topDishes = OrderItems::where('order_items.restaurant_id', $restaurantId)
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

        // 5. Trending Graphs (Last 14 Days Daily Sales Revenue & Order Volume)
        $chartDays = [];
        $chartSales = [];
        $chartOrders = [];

        for ($i = 13; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $dateStr = $d->format('Y-m-d');
            $label = $d->format('d M');

            $dSales = (float) OrderManage::where('restaurant_id', $restaurantId)
                ->whereDate('created_at', $dateStr)
                ->sum('grand_total');

            $dOrders = (int) OrderManage::where('restaurant_id', $restaurantId)
                ->whereDate('created_at', $dateStr)
                ->count();

            $chartDays[] = $label;
            $chartSales[] = round($dSales, 2);
            $chartOrders[] = $dOrders;
        }

        // 6. Peak Hourly Rush (24 Hours distribution)
        $hourlyRush = array_fill(0, 24, 0);
        $hourlyData = OrderManage::where('restaurant_id', $restaurantId)
            ->selectRaw('HOUR(created_at) as hour_num, count(*) as count')
            ->groupBy('hour_num')
            ->get();
        foreach ($hourlyData as $hd) {
            $hourlyRush[(int) $hd->hour_num] = (int) $hd->count;
        }
        $hourlyLabels = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyLabels[] = sprintf('%02d:00', $h);
        }
        $hourlyValues = array_values($hourlyRush);

        // 7. Payment Methods Breakdown (Pie/Donut Chart)
        $paymentMethods = OrderManage::where('restaurant_id', $restaurantId)
            ->whereNotNull('payment_method')
            ->where('payment_method', '!=', '')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('SUM(grand_total) as amount'))
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get();

        // 8. Order Types Breakdown (Donut Chart)
        $orderTypes = OrderManage::where('restaurant_id', $restaurantId)
            ->whereNotNull('order_type')
            ->where('order_type', '!=', '')
            ->select('order_type', DB::raw('count(*) as count'), DB::raw('SUM(grand_total) as amount'))
            ->groupBy('order_type')
            ->orderByDesc('count')
            ->get();

        // 9. Top 5 Category Revenue Share (Pie Chart)
        $categoryShare = OrderItems::where('order_items.restaurant_id', $restaurantId)
            ->join('sub_category', 'order_items.subcategory_id', '=', 'sub_category.id')
            ->join('category', 'sub_category.category_id', '=', 'category.id')
            ->select('category.name as category_name', DB::raw('SUM(order_items.total_amount) as total_amount'))
            ->groupBy('category.name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        // 10. General Counts
        $totalDishes = SubCategory::where('restaurant_id', $restaurantId)->where('status', '!=', 'D')->count();
        $totalCategories = Category::where('restaurant_id', $restaurantId)->where('status', '!=', 'D')->count();

        // 11. Recent 5 Live Orders
        $recentOrders = OrderManage::where('restaurant_id', $restaurantId)
            ->with(['table', 'items.subcategory'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('franchise.performance.report.dashboard', compact(
            'context',
            'restaurant',
            'totalSalesSum',
            'totalOrdersCount',
            'avgOrderValue',
            'totalCollected',
            'totalDue',
            'todayOrdersCount',
            'todayOrderValue',
            'todayItemsQty',
            'monthOrdersCount',
            'monthOrderValue',
            'topDishes',
            'chartDays',
            'chartSales',
            'chartOrders',
            'hourlyLabels',
            'hourlyValues',
            'paymentMethods',
            'orderTypes',
            'categoryShare',
            'totalDishes',
            'totalCategories',
            'recentOrders'
        ));
    }

    /**
     * PAGE 3 - TAB 2: Master Executive Overview for Performance Tracking
     */
    public function reportOverview($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];
        $today = Carbon::today();

        // 1. Dishes & Categories
        $totalDishes = SubCategory::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->count();

        $totalCategories = Category::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->count();

        // 2. Today's Key Metrics
        $todayOrdersQuery = OrderManage::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $today);

        $todayOrdersCount = (clone $todayOrdersQuery)->count();
        $todayOrderValue = (clone $todayOrdersQuery)->sum('grand_total') ?: 0;
        $todayCollectedAmount = (clone $todayOrdersQuery)->sum('amount_paid') ?: 0;

        $todayDueAmount = (clone $todayOrdersQuery)
            ->where(function ($q) {
                $q->whereNull('payment_status')
                  ->orWhere('payment_status', '!=', 'PAID');
            })
            ->selectRaw('COALESCE(SUM(GREATEST(0, grand_total - COALESCE(amount_paid, 0))), 0) as due')
            ->value('due') ?: 0;

        $todayTaxAmount = (clone $todayOrdersQuery)->sum('gst_amount') ?: 0;
        $todayDiscountAmount = (clone $todayOrdersQuery)->sum('discount') ?: 0;

        // 3. Today's Order Types Breakdown
        $orderTypes = (clone $todayOrdersQuery)
            ->select('order_type', DB::raw('COUNT(*) as count'), DB::raw('COALESCE(SUM(grand_total), 0) as total'))
            ->groupBy('order_type')
            ->get()
            ->keyBy('order_type');

        // 4. Today's Payment Methods Breakdown
        $paymentMethods = (clone $todayOrdersQuery)
            ->whereNotNull('payment_method')
            ->where('payment_method', '!=', '')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('COALESCE(SUM(amount_paid), 0) as total'))
            ->groupBy('payment_method')
            ->get();

        // 5. Month-to-Date Performance
        $startOfMonth = Carbon::now()->startOfMonth();
        $monthOrdersQuery = OrderManage::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$startOfMonth->startOfDay(), Carbon::now()->endOfDay()]);

        $monthOrdersCount = (clone $monthOrdersQuery)->count();
        $monthOrderValue = (clone $monthOrdersQuery)->sum('grand_total') ?: 0;
        $monthCollected = (clone $monthOrdersQuery)->sum('amount_paid') ?: 0;
        $monthDue = (clone $monthOrdersQuery)
            ->where(function ($q) {
                $q->whereNull('payment_status')
                  ->orWhere('payment_status', '!=', 'PAID');
            })
            ->selectRaw('COALESCE(SUM(GREATEST(0, grand_total - COALESCE(amount_paid, 0))), 0) as due')
            ->value('due') ?: 0;

        // 6. Recent 5 Orders Today
        $recentOrders = (clone $todayOrdersQuery)
            ->with(['items.subcategory', 'table'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // 7. Quick Inventory & Purchases summary
        $totalProducts = Product::where('restaurant_id', $restaurantId)->where('status', '!=', 'D')->count();
        $todayExpenses = Expense::where('restaurant_id', $restaurantId)->whereDate('expense_date', $today)->sum('amount') ?: 0;
        $todayPurchases = Purchase::where('restaurant_id', $restaurantId)->whereDate('purchase_date', $today)->sum('total_amount') ?: 0;

        return view('franchise.performance.report.overview', compact(
            'context',
            'restaurant',
            'totalDishes',
            'totalCategories',
            'todayOrdersCount',
            'todayOrderValue',
            'todayCollectedAmount',
            'todayDueAmount',
            'todayTaxAmount',
            'todayDiscountAmount',
            'orderTypes',
            'paymentMethods',
            'monthOrdersCount',
            'monthOrderValue',
            'monthCollected',
            'monthDue',
            'recentOrders',
            'totalProducts',
            'todayExpenses',
            'todayPurchases'
        ));
    }

    /**
     * TAB 2: Orders List Report for Performance Tracking
     */
    public function reportOrders($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];

        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = OrderManage::with(['table', 'items.subcategory'])
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate]);

        if ($request->filled('payment_status')) {
            $pStatus = strtoupper(trim($request->payment_status));
            if ($pStatus === 'PAID') {
                $query->where('payment_status', 'PAID');
            } elseif ($pStatus === 'UNPAID') {
                $query->where(function ($q) {
                    $q->whereNull('payment_status')
                      ->orWhere('payment_status', 'UNPAID')
                      ->orWhere('payment_status', 'PENDING');
                });
            } elseif ($pStatus === 'PARTIAL') {
                $query->where('payment_status', 'PARTIAL');
            }
        }

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $summaryQuery = clone $query;
        $totalOrdersCount = (clone $summaryQuery)->count();
        $totalOrderValue = (clone $summaryQuery)->sum('grand_total') ?: 0;
        $totalCollected = (clone $summaryQuery)->sum('amount_paid') ?: 0;
        $totalDue = (clone $summaryQuery)
            ->where(function ($q) {
                $q->whereNull('payment_status')
                  ->orWhere('payment_status', '!=', 'PAID');
            })
            ->selectRaw('COALESCE(SUM(GREATEST(0, grand_total - COALESCE(amount_paid, 0))), 0) as due')
            ->value('due') ?: 0;

        $orders = $query->orderBy('id', 'desc')->get();

        return view('franchise.performance.report.orders', compact(
            'context',
            'restaurant',
            'orders',
            'fromDate',
            'toDate',
            'totalOrdersCount',
            'totalOrderValue',
            'totalCollected',
            'totalDue'
        ));
    }

    /**
     * TAB 3: Order Items Report
     */
    public function reportOrderItems($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];

        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = OrderItems::with(['order', 'subcategory.category'])
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate]);

        if ($request->filled('category_id')) {
            $catId = (int) $request->category_id;
            $query->whereHas('subcategory', function ($q) use ($catId) {
                $q->where('category_id', $catId);
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('subcategory', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhere('order_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('customer_phone', 'like', "%{$search}%");
                  });
            });
        }

        $categories = Category::where('restaurant_id', $restaurantId)->where('status', '!=', 'D')->get();

        $summaryQuery = clone $query;
        $totalItemsCount = (clone $summaryQuery)->sum('quantity') ?: 0;
        $totalItemsRevenue = (clone $summaryQuery)->sum('total_amount') ?: 0;
        $distinctDishesCount = (clone $summaryQuery)->distinct('subcategory_id')->count('subcategory_id');

        $orderItems = $query->orderBy('id', 'desc')->get();

        return view('franchise.performance.report.order-items', compact(
            'context',
            'restaurant',
            'orderItems',
            'categories',
            'fromDate',
            'toDate',
            'totalItemsCount',
            'totalItemsRevenue',
            'distinctDishesCount'
        ));
    }

    /**
     * TAB 4: Purchases Report
     */
    public function reportPurchases($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];

        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Purchase::with(['supplier', 'items.product', 'items.unit'])
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('purchase_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('supplier_name', 'like', "%{$search}%")
                         ->orWhere('shop_name', 'like', "%{$search}%");
                  });
            });
        }

        $suppliers = Supplier::where('restaurant_id', $restaurantId)->where('status', '!=', 'D')->get();

        $summaryQuery = clone $query;
        $totalPurchasesCount = (clone $summaryQuery)->count();
        $totalPurchasesAmount = (clone $summaryQuery)->sum('total_amount') ?: 0;
        $totalItemsPurchased = (clone $summaryQuery)->sum('total_items') ?: 0;

        $purchases = $query->orderBy('purchase_date', 'desc')->orderBy('id', 'desc')->get();

        return view('franchise.performance.report.purchases', compact(
            'context',
            'restaurant',
            'purchases',
            'suppliers',
            'fromDate',
            'toDate',
            'totalPurchasesCount',
            'totalPurchasesAmount',
            'totalItemsPurchased'
        ));
    }

    /**
     * TAB 5: Live Stock Report
     */
    public function reportStock($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];

        $query = Product::with(['unit', 'inventory'])
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('product_name', 'like', "%{$search}%");
        }

        $products = $query->orderBy('product_name', 'asc')->get();

        $products->transform(function ($item) {
            $currentStock = $item->inventory ? (float)$item->inventory->total_qty : (float)$item->opening_qty;
            $item->current_stock = $currentStock;
            
            if ($currentStock <= 0) {
                $item->stock_status = 'OUT_OF_STOCK';
                $item->status_badge = 'danger';
                $item->status_label = 'Out of Stock';
            } elseif ($currentStock <= 10) {
                $item->stock_status = 'LOW_STOCK';
                $item->status_badge = 'warning';
                $item->status_label = 'Low Stock';
            } else {
                $item->stock_status = 'IN_STOCK';
                $item->status_badge = 'success';
                $item->status_label = 'In Stock';
            }
            return $item;
        });

        if ($request->filled('status')) {
            $statusFilter = strtoupper(trim($request->status));
            if ($statusFilter !== 'ALL') {
                $products = $products->filter(function ($item) use ($statusFilter) {
                    return $item->stock_status === $statusFilter;
                });
            }
        }

        $totalStockUnits = $products->sum('current_stock');
        $lowStockCount = $products->where('stock_status', 'LOW_STOCK')->count();
        $outOfStockCount = $products->where('stock_status', 'OUT_OF_STOCK')->count();
        $inStockCount = $products->where('stock_status', 'IN_STOCK')->count();

        return view('franchise.performance.report.stock', compact(
            'context',
            'restaurant',
            'products',
            'totalStockUnits',
            'lowStockCount',
            'outOfStockCount',
            'inStockCount'
        ));
    }

    /**
     * TAB 6: Expenses Report
     */
    public function reportExpenses($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];

        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Expense::with('user')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('expense_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $summaryQuery = clone $query;
        $totalExpensesCount = (clone $summaryQuery)->count();
        $totalExpensesAmount = (clone $summaryQuery)->sum('amount') ?: 0;

        $paymentBreakdown = (clone $summaryQuery)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        $expenses = $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->get();

        return view('franchise.performance.report.expenses', compact(
            'context',
            'restaurant',
            'expenses',
            'fromDate',
            'toDate',
            'totalExpensesCount',
            'totalExpensesAmount',
            'paymentBreakdown'
        ));
    }

    /**
     * TAB 7: Business Intelligence / Analytics Report
     */
    public function reportAnalytics($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];

        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        // 1. Trending / Top Food Items
        $trendingDishes = OrderItems::select(
                'sub_category.id as dish_id',
                'sub_category.name as dish_name',
                'sub_category.food_type',
                'category.name as category_name',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_qty'),
                DB::raw('COALESCE(SUM(order_items.total_amount), 0) as total_revenue')
            )
            ->join('sub_category', 'order_items.subcategory_id', '=', 'sub_category.id')
            ->leftJoin('category', 'sub_category.category_id', '=', 'category.id')
            ->where('order_items.restaurant_id', $restaurantId)
            ->whereBetween('order_items.created_at', [$fromDate, $toDate])
            ->groupBy('sub_category.id', 'sub_category.name', 'sub_category.food_type', 'category.name')
            ->orderByDesc('total_qty')
            ->limit(15)
            ->get();

        $totalDishesRevenueInPeriod = $trendingDishes->sum('total_revenue') ?: 1;

        // 2. Best VIP Customers
        $bestCustomers = OrderManage::select(
                'customer_name',
                'customer_phone',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(grand_total), 0) as total_spent'),
                DB::raw('COALESCE(AVG(grand_total), 0) as avg_order_value'),
                DB::raw('MAX(created_at) as last_visit')
            )
            ->where('restaurant_id', $restaurantId)
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('customer_name', 'customer_phone')
            ->orderByDesc('total_spent')
            ->limit(15)
            ->get();

        // 3. Peak Sales Date
        $salesByDate = OrderManage::select(
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('COALESCE(SUM(grand_total), 0) as total_sales')
            )
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('total_sales')
            ->get();

        $peakSalesDay = $salesByDate->first();

        // 4. Peak Hours of the Day (0-23 hours)
        $hourlyOrders = OrderManage::select(
                DB::raw('HOUR(created_at) as order_hour'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('COALESCE(SUM(grand_total), 0) as total_sales')
            )
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('order_hour', 'asc')
            ->get()
            ->keyBy('order_hour');

        $peakHour = $hourlyOrders->sortByDesc('order_count')->first();

        // 5. Non-Selling Items
        $soldDishIds = OrderItems::where('order_items.restaurant_id', $restaurantId)
            ->whereBetween('order_items.created_at', [$fromDate, $toDate])
            ->pluck('subcategory_id')
            ->unique()
            ->toArray();

        $nonSellingDishes = SubCategory::with('category')
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->whereNotIn('id', $soldDishIds)
            ->orderBy('name', 'asc')
            ->get();

        // 6. Purchases vs Sales Dynamics
        $totalSalesRevenue = OrderManage::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('grand_total') ?: 0;

        $totalPurchasesAmount = Purchase::where('restaurant_id', $restaurantId)
            ->whereBetween('purchase_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
            ->sum('total_amount') ?: 0;

        $totalExpensesAmount = Expense::where('restaurant_id', $restaurantId)
            ->whereBetween('expense_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
            ->sum('amount') ?: 0;

        $grossMargin = $totalSalesRevenue - ($totalPurchasesAmount + $totalExpensesAmount);

        return view('franchise.performance.report.analytics', compact(
            'context',
            'restaurant',
            'fromDate',
            'toDate',
            'trendingDishes',
            'totalDishesRevenueInPeriod',
            'bestCustomers',
            'salesByDate',
            'peakSalesDay',
            'hourlyOrders',
            'peakHour',
            'nonSellingDishes',
            'totalSalesRevenue',
            'totalPurchasesAmount',
            'totalExpensesAmount',
            'grossMargin'
        ));
    }

    /**
     * Shared helper to get aggregated deduplicated customer list from orders.
     */
    protected function getAggregatedCustomers($restaurantId, $keyword = null)
    {
        $orders = OrderManage::where('restaurant_id', $restaurantId)
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->whereRaw("LOWER(TRIM(customer_name)) NOT LIKE '%walk%in%'")
            ->select('id', 'customer_name', 'customer_phone', 'grand_total', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $customersByPhone = [];
        $customersWithoutPhone = [];

        foreach ($orders as $order) {
            $name = trim($order->customer_name);
            $rawPhone = trim($order->customer_phone ?? '');
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            $email = '';
            $orderTotal = (float) ($order->grand_total ?? 0);
            $orderDate = $order->created_at ? Carbon::parse($order->created_at) : null;

            if (!empty($cleanPhone)) {
                if (!isset($customersByPhone[$cleanPhone])) {
                    $customersByPhone[$cleanPhone] = (object) [
                        'name' => $name,
                        'phone' => $rawPhone,
                        'clean_phone' => $cleanPhone,
                        'email' => $email ?: null,
                        'total_orders' => 0,
                        'total_spent' => 0.0,
                        'last_order_date' => $orderDate,
                    ];
                }

                $customersByPhone[$cleanPhone]->total_orders++;
                $customersByPhone[$cleanPhone]->total_spent += $orderTotal;

                if (empty($customersByPhone[$cleanPhone]->email) && !empty($email)) {
                    $customersByPhone[$cleanPhone]->email = $email;
                }

                if (!empty($name) && ($customersByPhone[$cleanPhone]->name === 'Not Found' || strlen($name) > strlen($customersByPhone[$cleanPhone]->name))) {
                    $customersByPhone[$cleanPhone]->name = $name;
                }

                if ($orderDate && (!$customersByPhone[$cleanPhone]->last_order_date || $orderDate->gt($customersByPhone[$cleanPhone]->last_order_date))) {
                    $customersByPhone[$cleanPhone]->last_order_date = $orderDate;
                }
            } else {
                $normName = strtolower($name);
                if (!isset($customersWithoutPhone[$normName])) {
                    $customersWithoutPhone[$normName] = (object) [
                        'name' => $name,
                        'phone' => null,
                        'clean_phone' => null,
                        'email' => $email ?: null,
                        'total_orders' => 0,
                        'total_spent' => 0.0,
                        'last_order_date' => $orderDate,
                    ];
                }

                $customersWithoutPhone[$normName]->total_orders++;
                $customersWithoutPhone[$normName]->total_spent += $orderTotal;

                if (empty($customersWithoutPhone[$normName]->email) && !empty($email)) {
                    $customersWithoutPhone[$normName]->email = $email;
                }

                if ($orderDate && (!$customersWithoutPhone[$normName]->last_order_date || $orderDate->gt($customersWithoutPhone[$normName]->last_order_date))) {
                    $customersWithoutPhone[$normName]->last_order_date = $orderDate;
                }
            }
        }

        $all = collect(array_merge(array_values($customersByPhone), array_values($customersWithoutPhone)));

        if (!empty($keyword)) {
            $kw = strtolower(trim($keyword));
            $all = $all->filter(function ($c) use ($kw) {
                return (strpos(strtolower($c->name ?? ''), $kw) !== false)
                    || (strpos(strtolower($c->phone ?? ''), $kw) !== false)
                    || (strpos(strtolower($c->email ?? ''), $kw) !== false);
            });
        }

        return $all->sortByDesc(function ($c) {
            return $c->last_order_date ? $c->last_order_date->timestamp : 0;
        })->values();
    }

    /**
     * TAB 8: Customers Directory for Performance Tracking
     */
    public function reportCustomers($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];

        $keyword = $request->input('keyword', $request->input('search'));
        $customers = $this->getAggregatedCustomers($restaurantId, $keyword);

        $totalCustomersCount = $customers->count();
        $customersWithPhone = $customers->whereNotNull('phone')->count();
        $customersWithEmail = $customers->whereNotNull('email')->count();
        $totalCustomerSpent = $customers->sum('total_spent');
        $repeatCustomersCount = $customers->where('total_orders', '>', 1)->count();

        return view('franchise.performance.report.customers', compact(
            'context',
            'restaurant',
            'customers',
            'keyword',
            'totalCustomersCount',
            'customersWithPhone',
            'customersWithEmail',
            'totalCustomerSpent',
            'repeatCustomersCount'
        ));
    }

    /**
     * Export Customers in Franchise Performance Report to Excel (.xlsx)
     */
    public function exportCustomers($restaurantId, Request $request)
    {
        $context = $this->resolveRestaurantContext($restaurantId, $request);
        $restaurant = $context['targetRestaurant'];

        $keyword = $request->input('keyword', $request->input('search'));
        $customers = $this->getAggregatedCustomers($restaurantId, $keyword);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer Report');

        $restaurantName = $restaurant ? $restaurant->name : 'Restaurant';
        $sheet->setCellValue('A1', strtoupper($restaurantName) . ' - CUSTOMER DIRECTORY REPORT');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $subtitle = 'Generated on ' . Carbon::now()->format('d M Y, h:i A') . ' | Unique Customers: ' . $customers->count();
        if (!empty($keyword)) {
            $subtitle .= ' | Keyword: "' . $keyword . '"';
        }
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $headers = ['S.No', 'Customer Name', 'Mobile Number', 'Email Address', 'Total Orders', 'Total Spent (₹)', 'Last Order Date'];
        $colIndex = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($colIndex . '4', $h);
            $colIndex++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]]
        ];
        $sheet->getStyle('A4:G4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(26);

        $row = 5;
        foreach ($customers as $idx => $c) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $c->name ?: 'Not Found');
            $sheet->setCellValue('C' . $row, $c->phone ?: 'Not Found');
            $sheet->setCellValue('D' . $row, $c->email ?: 'Not Found');
            $sheet->setCellValue('E' . $row, $c->total_orders);
            $sheet->setCellValue('F' . $row, round($c->total_spent, 2));
            $sheet->setCellValue('G' . $row, $c->last_order_date ? $c->last_order_date->format('d-m-Y h:i A') : '-');

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $row++;
        }

        if ($customers->count() > 0) {
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->setCellValue('E' . $row, $customers->sum('total_orders'));
            $sheet->setCellValue('F' . $row, round($customers->sum('total_spent'), 2));
            $sheet->setCellValue('G' . $row, $customers->count() . ' Unique Customers');

            $totalStyle = [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E7FF']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF4F46E5']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF4F46E5']]
                ]
            ];
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($totalStyle);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($row)->setRowHeight(24);
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $cleanSlug = preg_replace('/[^A-Za-z0-9_-]/', '_', $restaurantName);
        $filename = "Performance_Customers_" . $cleanSlug . "_" . Carbon::now()->format('Ymd_His') . ".xlsx";
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

