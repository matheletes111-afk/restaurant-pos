<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Expense;
use App\Models\RestaurantMaster;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MasterReportController extends Controller
{
    /**
     * Resolve the active target restaurant context with strict multi-branch authorization.
     */
    protected function resolveOutletContext(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        // 1. Super Admin access
        if ($user->role === 'SA') {
            $availableOutlets = RestaurantMaster::where('status', '!=', 'D')
                ->orderBy('name', 'asc')
                ->get();

            $requestedId = $request->query('outlet_id');
            $targetId = $requestedId ? (int) $requestedId : ($user->restaurant_id ?: ($availableOutlets->first() ? $availableOutlets->first()->id : null));
            $targetRestaurant = RestaurantMaster::find($targetId) ?: $availableOutlets->first();

            return [
                'targetRestaurant' => $targetRestaurant,
                'targetRestaurantId' => $targetRestaurant ? $targetRestaurant->id : null,
                'availableOutlets' => $availableOutlets,
                'isMultiBranch' => $availableOutlets->count() > 1,
                'selectedOutletId' => $targetRestaurant ? $targetRestaurant->id : null,
            ];
        }

        // 2. Restaurant Owner or Staff
        $availableOutlets = $user->getAvailableOutlets();
        if ($availableOutlets->isEmpty() && $user->restaurant_id) {
            $myRest = RestaurantMaster::find($user->restaurant_id);
            if ($myRest) {
                $availableOutlets = collect([$myRest]);
            }
        }

        $outletIds = $availableOutlets->pluck('id')->toArray();
        $requestedId = $request->query('outlet_id');

        if ($requestedId && in_array((int) $requestedId, $outletIds)) {
            $targetId = (int) $requestedId;
        } else {
            $targetId = $user->restaurant_id ?: ($availableOutlets->first() ? $availableOutlets->first()->id : null);
        }

        $targetRestaurant = RestaurantMaster::find($targetId) ?: ($availableOutlets->first() ?: null);

        return [
            'targetRestaurant' => $targetRestaurant,
            'targetRestaurantId' => $targetRestaurant ? $targetRestaurant->id : $user->restaurant_id,
            'availableOutlets' => $availableOutlets,
            'isMultiBranch' => $availableOutlets->count() > 1,
            'selectedOutletId' => $targetRestaurant ? $targetRestaurant->id : $user->restaurant_id,
        ];
    }

    /**
     * TAB 1: Master Executive Overview
     */
    public function overview(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $restaurantId = $context['targetRestaurantId'];
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

        // Due amount for today's orders
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

        return view('report.master.overview', compact(
            'context',
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
     * TAB 2: Orders List Report
     */
    public function orders(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $restaurantId = $context['targetRestaurantId'];

        // Default to current date if not specified
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = OrderManage::with(['table', 'items.subcategory'])
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate]);

        // Payment status filter
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

        // Order type filter
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        // Search filter (Customer Name, Phone, Order ID)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Summary calculations
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

        return view('report.master.orders', compact(
            'context',
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
    public function orderItems(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $restaurantId = $context['targetRestaurantId'];

        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = OrderItems::with(['order', 'subcategory.category'])
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate]);

        // Category filter
        if ($request->filled('category_id')) {
            $catId = (int) $request->category_id;
            $query->whereHas('subcategory', function ($q) use ($catId) {
                $q->where('category_id', $catId);
            });
        }

        // Search filter (Dish Name, Order ID, Customer Name, Phone)
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

        // Categories list for filter dropdown
        $categories = Category::where('restaurant_id', $restaurantId)->where('status', '!=', 'D')->get();

        // Summary calculations
        $summaryQuery = clone $query;
        $totalItemsCount = (clone $summaryQuery)->sum('quantity') ?: 0;
        $totalItemsRevenue = (clone $summaryQuery)->sum('total_amount') ?: 0;
        $distinctDishesCount = (clone $summaryQuery)->distinct('subcategory_id')->count('subcategory_id');

        $orderItems = $query->orderBy('id', 'desc')->get();

        return view('report.master.order-items', compact(
            'context',
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
    public function purchases(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $restaurantId = $context['targetRestaurantId'];

        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Purchase::with(['supplier', 'items.product', 'items.unit'])
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('purchase_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);

        // Supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Search filter
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

        return view('report.master.purchases', compact(
            'context',
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
    public function stock(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $restaurantId = $context['targetRestaurantId'];

        $query = Product::with(['unit', 'inventory'])
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D');

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('product_name', 'like', "%{$search}%");
        }

        $products = $query->orderBy('product_name', 'asc')->get();

        // Calculate current stock levels and map
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

        // Filter by stock status if selected
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

        return view('report.master.stock', compact(
            'context',
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
    public function expenses(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $restaurantId = $context['targetRestaurantId'];

        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Expense::with('user')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('expense_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);

        // Payment mode filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search filter
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

        // Breakdown by payment mode
        $paymentBreakdown = (clone $summaryQuery)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        $expenses = $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->get();

        return view('report.master.expenses', compact(
            'context',
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
    public function analytics(Request $request)
    {
        $context = $this->resolveOutletContext($request);
        $restaurantId = $context['targetRestaurantId'];

        // Default date range: From start of current month to today
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

        // 5. Non-Selling Items (Zero Sales in Period)
        $soldDishIds = OrderItems::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
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

        return view('report.master.analytics', compact(
            'context',
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
}
