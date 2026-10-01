<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
class InventoryController extends Controller
{
                /**
     * Constructor - Check inventory permission for all methods
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Check if user is authenticated
            if (!auth()->check()) {
                return redirect()->route('login')
                    ->with('error', 'Please login to continue.');
            }

            // Get active subscription using restaurant_id
            $active = DB::table('subscriptions')
                ->where('user_id', auth()->user()->restaurant_id)
                ->whereIn('status', ['active', 'completed'])
                ->first();

            // If no active subscription found
            if (!$active) {
                return redirect()->back()
                    ->with('error', 'No active subscription found. Please subscribe to a plan.');
            }

            // Get plan details
            $plan_details = DB::table('plans')
                ->where('id', $active->plan_id)
                ->first();

            // Check if inventory_checkbox is NOT "Y"
            if (@$plan_details->inventory_checkbox != "Y") {
                return redirect()->back()
                    ->with('error', 'Unauthorized access. Your plan does not include inventory management features.');
            }

            // Permission granted, continue to the requested method
            return $next($request);
        });
    }
    public function live(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        // Base query for stats across all active restaurant products
        $allInventories = Inventory::with(['product.unit'])
            ->where('restaurant_id', $restaurantId)
            ->whereHas('product', function ($q) {
                $q->where('status', 'A');
            })
            ->get();

        $totalProducts = $allInventories->count();
        $goodStockItems = $allInventories->where('total_qty', '>', 10)->count();
        $lowStockItems = $allInventories->where('total_qty', '<=', 10)->where('total_qty', '>', 0)->count();
        $outOfStockItems = $allInventories->where('total_qty', '<=', 0)->count();
        $totalStockQuantity = $allInventories->sum('total_qty');

        // Filtered query for display
        $query = Inventory::with(['product.unit'])
            ->where('restaurant_id', $restaurantId)
            ->whereHas('product', function ($q) {
                $q->where('status', 'A');
            });

        // Search functionality (Product Name, Unit Name, Created By)
        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('product', function ($pq) use ($keyword) {
                    $pq->where('product_name', 'LIKE', "%{$keyword}%")
                       ->orWhereHas('unit', function ($uq) use ($keyword) {
                           $uq->where('name', 'LIKE', "%{$keyword}%");
                       });
                })->orWhere('created_by', 'LIKE', "%{$keyword}%");
            });
        }

        // Unit filter
        if ($request->filled('unit_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            });
        }

        // Stock status filter (supporting stock_status, low_stock, out_of_stock)
        $stockStatus = $request->get('stock_status', '');
        if ($request->has('low_stock') && $request->low_stock == '1') {
            $stockStatus = 'low';
        } elseif ($request->has('out_of_stock') && $request->out_of_stock == '1') {
            $stockStatus = 'out';
        }

        if ($stockStatus === 'low') {
            $query->where('total_qty', '<=', 10)->where('total_qty', '>', 0);
        } elseif ($stockStatus === 'out') {
            $query->where('total_qty', '<=', 0);
        } elseif ($stockStatus === 'good') {
            $query->where('total_qty', '>', 10);
        }

        // Sort by stock quantity ascending (lowest stock on top)
        $inventories = $query->orderBy('total_qty', 'asc')->get();

        // Get units for filter dropdown
        $units = Unit::where('restaurant_id', $restaurantId)->where('status', 'A')->orderBy('name')->get();

        return view('inventory', compact(
            'inventories',
            'totalProducts',
            'goodStockItems',
            'lowStockItems',
            'outOfStockItems',
            'totalStockQuantity',
            'units',
            'stockStatus'
        ));
    }
}