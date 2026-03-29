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
                ->where('status', 'active')
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
        // Get all products with their inventory
        $query = Inventory::with(['product.unit'])
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('total_qty', 'asc'); // Sort by stock quantity ascending
            
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('product_name', 'LIKE', "%{$request->search}%")
                  ->where('status', 'A');
            });
        } else {
            // Only show products with status 'A' (Active)
            $query->whereHas('product', function($q) {
                $q->where('status', 'A');
            });
        }
        
        // Filter by low stock
        if ($request->has('low_stock') && $request->low_stock == '1') {
            $query->where('total_qty', '<=', 10);
        }
        
        // Filter by out of stock
        if ($request->has('out_of_stock') && $request->out_of_stock == '1') {
            $query->where('total_qty', '<=', 0);
        }
        
        $inventories = $query->get();
        
        // Calculate summary
        $totalProducts = $inventories->count();
        $lowStockItems = $inventories->where('total_qty', '<=', 10)->where('total_qty', '>', 0)->count();
        $outOfStockItems = $inventories->where('total_qty', '<=', 0)->count();
        $totalStockValue = 0; // You can calculate this if you have price in products
        
        return view('inventory', compact('inventories', 'totalProducts', 'lowStockItems', 'outOfStockItems'));
    }
}