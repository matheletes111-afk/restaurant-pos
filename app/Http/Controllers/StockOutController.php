<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOut;
use App\Models\StockOutItem;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockOutController extends Controller
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

    
    public function index()
    {
        $stockOuts = StockOut::with(['items.product', 'user'])
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('stockout_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        return view('stock-outs.index', compact('stockOuts'));
    }

    public function create()
    {
        $products = Product::with('unit')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();
        
        // Generate automatic stockout number
        $today = Carbon::now()->format('ymd');
        $lastStockOut = StockOut::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('stockout_no', 'like', 'SO-' . $today . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastStockOut) {
            $lastNumber = intval(substr($lastStockOut->stockout_no, -3));
            $stockoutNo = 'SO-' . $today . '-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $stockoutNo = 'SO-' . $today . '-001';
        }
        
        return view('stock-outs.create', compact('products', 'stockoutNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stockout_no' => 'required|string|max:100',
            'stockout_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        // Check for duplicate stockout number
        $existingStockOut = StockOut::where('stockout_no', $request->stockout_no)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first();
        
        if ($existingStockOut) {
            return redirect()->back()->withInput()->with('error', 'Stock Out number already exists!');
        }

        DB::beginTransaction();
        try {
            // Check stock availability before proceeding
            $insufficientStock = false;
            $stockErrors = [];
            
            foreach ($request->items as $index => $item) {
                $currentStock = Inventory::getStock($item['product_id']);
                if ($currentStock < $item['quantity']) {
                    $product = Product::find($item['product_id']);
                    $stockErrors[] = "Product: {$product->product_name} - Available: {$currentStock}, Required: {$item['quantity']}";
                    $insufficientStock = true;
                }
            }
            
            if ($insufficientStock) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Insufficient stock!')
                    ->with('stock_errors', $stockErrors);
            }

            // Create stock out
            $stockOut = new StockOut();
            $stockOut->stockout_no = $request->stockout_no;
            $stockOut->stockout_date = $request->stockout_date;
            $stockOut->remarks = $request->remarks;
            $stockOut->restaurant_id = auth()->user()->restaurant_id;
            $stockOut->user_id = auth()->user()->id;
            $stockOut->status = 'COMPLETED';
            $stockOut->save();

            $totalItems = 0;

            // Create stock out items and update inventory
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                $stockOutItem = new StockOutItem();
                $stockOutItem->stockout_id = $stockOut->id;
                $stockOutItem->product_id = $item['product_id'];
                $stockOutItem->unit_id = $product->unit_id;
                $stockOutItem->quantity = $item['quantity'];
                $stockOutItem->restaurant_id = auth()->user()->restaurant_id;
                $stockOutItem->save();

                // Update inventory - SUBTRACT quantity
                Inventory::updateStock($item['product_id'], $item['quantity'], 'subtract');

                $totalItems++;
            }

            // Update stock out total items
            $stockOut->total_items = $totalItems;
            $stockOut->save();

            DB::commit();
            return redirect()->route('stock-outs.index')->with('success', 'Stock Out recorded successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to record stock out: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $stockOut = StockOut::with(['items.product', 'user'])
            ->where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        $products = Product::with('unit')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();
        
        return view('stock-outs.edit', compact('stockOut', 'products'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:stock_outs,id',
            'stockout_no' => 'required|string|max:100',
            'stockout_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $stockOut = StockOut::where('id', $request->id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();

            // Check for duplicate stockout number (excluding current stock out)
            $existingStockOut = StockOut::where('stockout_no', $request->stockout_no)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->where('id', '!=', $request->id)
                ->first();
            
            if ($existingStockOut) {
                return redirect()->back()->withInput()->with('error', 'Stock Out number already exists!');
            }

            // First, reverse inventory for old items (ADD back quantity)
            foreach ($stockOut->items as $oldItem) {
                Inventory::updateStock($oldItem->product_id, $oldItem->quantity, 'add');
            }

            // Check stock availability for new items
            $insufficientStock = false;
            $stockErrors = [];
            
            foreach ($request->items as $index => $item) {
                // Get current stock including what we're adding back
                $currentStock = Inventory::getStock($item['product_id']);
                if ($currentStock < $item['quantity']) {
                    $product = Product::find($item['product_id']);
                    $stockErrors[] = "Product: {$product->product_name} - Available: {$currentStock}, Required: {$item['quantity']}";
                    $insufficientStock = true;
                }
            }
            
            if ($insufficientStock) {
                // Roll back the inventory reversal
                foreach ($stockOut->items as $oldItem) {
                    Inventory::updateStock($oldItem->product_id, $oldItem->quantity, 'subtract');
                }
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Insufficient stock!')
                    ->with('stock_errors', $stockErrors);
            }

            // Delete old stock out items
            $stockOut->items()->delete();

            // Update stock out details
            $stockOut->stockout_no = $request->stockout_no;
            $stockOut->stockout_date = $request->stockout_date;
            $stockOut->remarks = $request->remarks;
            $stockOut->save();

            $totalItems = 0;

            // Create new stock out items and update inventory (SUBTRACT quantity)
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                $stockOutItem = new StockOutItem();
                $stockOutItem->stockout_id = $stockOut->id;
                $stockOutItem->product_id = $item['product_id'];
                $stockOutItem->unit_id = $product->unit_id;
                $stockOutItem->quantity = $item['quantity'];
                $stockOutItem->restaurant_id = auth()->user()->restaurant_id;
                $stockOutItem->save();

                // Update inventory - SUBTRACT quantity
                Inventory::updateStock($item['product_id'], $item['quantity'], 'subtract');

                $totalItems++;
            }

            // Update stock out total items
            $stockOut->total_items = $totalItems;
            $stockOut->save();

            DB::commit();
            return redirect()->route('stock-outs.index')->with('success', 'Stock Out updated successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to update stock out: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $stockOut = StockOut::with(['items.product.unit', 'user'])
            ->where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        return view('stock-outs.show', compact('stockOut'));
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $stockOut = StockOut::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();

            // Reverse inventory for all items (ADD back quantity)
            foreach ($stockOut->items as $item) {
                Inventory::updateStock($item->product_id, $item->quantity, 'add');
            }

            // Delete stock out items
            $stockOut->items()->delete();
            
            // Delete stock out
            $stockOut->delete();

            DB::commit();
            return redirect()->route('stock-outs.index')->with('success', 'Stock Out deleted successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete stock out: ' . $e->getMessage());
        }
    }

    public function getProduct($id)
    {
        $product = Product::with('unit')
            ->where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first(['id', 'product_name', 'unit_id']);
        
        if ($product) {
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        }
        
        return response()->json(['success' => false]);
    }

    public function checkStock($productId)
    {
        $stock = Inventory::getStock($productId);
        return response()->json(['stock' => $stock]);
    }
}