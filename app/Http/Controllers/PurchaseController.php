<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PurchaseController extends Controller
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
            $subRestId = method_exists(auth()->user(), 'getSubscriptionRestaurantId') ? auth()->user()->getSubscriptionRestaurantId() : auth()->user()->restaurant_id;
            $active = DB::table('subscriptions')
                ->where('user_id', $subRestId)
                ->whereIn('status', ['active', 'completed'])
                ->latest('id')
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

    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $hasExplicitFilter = $request->has('from_date') || $request->has('to_date') || $request->has('supplier_id') || $request->has('keyword');

        $fromDate = $request->filled('from_date') 
            ? Carbon::parse($request->from_date)->startOfDay() 
            : Carbon::now()->startOfMonth()->startOfDay();

        $toDate = $request->filled('to_date') 
            ? Carbon::parse($request->to_date)->endOfDay() 
            : Carbon::now()->endOfMonth()->endOfDay();

        $supplierId = $request->supplier_id;
        $keyword = $request->keyword;

        $query = Purchase::with(['supplier', 'items.product'])
            ->where('restaurant_id', $restaurantId);

        // If custom filter applied (via form submit or query params)
        if ($hasExplicitFilter) {
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->where(function($dq) use ($fromDate, $toDate) {
                    $dq->whereBetween('purchase_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                       ->orWhereBetween('created_at', [$fromDate, $toDate]);
                });
            } elseif ($request->filled('from_date')) {
                $query->where(function($dq) use ($fromDate) {
                    $dq->where('purchase_date', '>=', $fromDate->format('Y-m-d'))
                       ->orWhere('created_at', '>=', $fromDate);
                });
            } elseif ($request->filled('to_date')) {
                $query->where(function($dq) use ($toDate) {
                    $dq->where('purchase_date', '<=', $toDate->format('Y-m-d'))
                       ->orWhere('created_at', '<=', $toDate);
                });
            }
        } else {
            // Default initial load:
            // Check if purchases exist in current month
            $currentMonthPurchases = (clone $query)->where(function($dq) use ($fromDate, $toDate) {
                $dq->whereBetween('purchase_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                   ->orWhereBetween('created_at', [$fromDate, $toDate]);
            })->count();

            if ($currentMonthPurchases > 0) {
                $query->where(function($dq) use ($fromDate, $toDate) {
                    $dq->whereBetween('purchase_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                       ->orWhereBetween('created_at', [$fromDate, $toDate]);
                });
            } else {
                // If no purchases exist in current month, show all purchases so user never sees empty screen
                $minDate = Purchase::where('restaurant_id', $restaurantId)->min('purchase_date');
                if ($minDate) {
                    $fromDate = Carbon::parse($minDate)->startOfDay();
                } else {
                    $minCreated = Purchase::where('restaurant_id', $restaurantId)->min('created_at');
                    if ($minCreated) {
                        $fromDate = Carbon::parse($minCreated)->startOfDay();
                    }
                }
            }
        }

        if ($request->filled('supplier_id') && $request->supplier_id !== 'all') {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('keyword')) {
            $searchTerm = trim($request->keyword);
            $query->where(function($q) use ($searchTerm) {
                $q->where('invoice_no', 'like', "%{$searchTerm}%")
                  ->orWhere('remarks', 'like', "%{$searchTerm}%")
                  ->orWhereHas('supplier', function($sq) use ($searchTerm) {
                      $sq->where('supplier_name', 'like', "%{$searchTerm}%")
                         ->orWhere('company_name', 'like', "%{$searchTerm}%")
                         ->orWhere('phone', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('items.product', function($pq) use ($searchTerm) {
                      $pq->where('product_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $purchases = $query->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $suppliers = Supplier::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->orderBy('supplier_name')
            ->get();
        
        return view('purchases.index', compact(
            'purchases', 
            'suppliers', 
            'fromDate', 
            'toDate', 
            'supplierId', 
            'keyword',
            'hasExplicitFilter'
        ));
    }

    public function create()
    {
        $restaurantId = auth()->user()->restaurant_id;

        // Sync Bar Items into Products with ML unit so they appear in the Purchase dropdown
        $mlUnit = Product::syncBarItems($restaurantId);

        $suppliers = Supplier::where('restaurant_id', $restaurantId)
            ->where('status', 'A')
            ->orderBy('supplier_name')
            ->get();
        
        $products = Product::with(['unit', 'barItem.brand', 'barItem.category'])
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();
        
        return view('purchases.create', compact('suppliers', 'products', 'mlUnit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|max:100',
            'purchase_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'bill_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
            'bill_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Check for duplicate invoice number
        $existingInvoice = Purchase::where('invoice_no', $request->invoice_no)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first();
        
        if ($existingInvoice) {
            return redirect()->back()->withInput()->with('error', 'Invoice number already exists!');
        }

        DB::beginTransaction();
        try {
            $restaurantId = auth()->user()->restaurant_id;
            $mlUnit = Product::ensureMlUnit($restaurantId);

            // Handle file upload
            $billAttachmentPath = null;
            if ($request->hasFile('bill_attachment')) {
                $file = $request->file('bill_attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $billAttachmentPath = $file->storeAs('purchase_bills', $filename, 'public');
            }

            // Create purchase
            $purchase = new Purchase();
            $purchase->invoice_no = $request->invoice_no;
            $purchase->purchase_date = $request->purchase_date;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->bill_amount = $request->bill_amount;
            $purchase->total_amount = $request->bill_amount; // Same as bill amount
            $purchase->remarks = $request->remarks;
            $purchase->bill_attachment = $billAttachmentPath;
            $purchase->restaurant_id = $restaurantId;
            $purchase->user_id = auth()->user()->id;
            $purchase->status = 'COMPLETED';
            $purchase->save();

            $totalItems = 0;

            // Create purchase items and update inventory (including Bar Inventory in ML when bar item is selected)
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $isBarItem = $product && !empty($product->sub_category_id);

                if ($isBarItem && $product->unit_id != $mlUnit->id) {
                    $product->unit_id = $mlUnit->id;
                    $product->save();
                }

                $purchaseItem = new PurchaseItem();
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->sub_category_id = $isBarItem ? $product->sub_category_id : null;
                $purchaseItem->unit_id = $isBarItem ? $mlUnit->id : $product->unit_id;
                $purchaseItem->quantity = $item['quantity'];
                $purchaseItem->price = $item['price'];
                $purchaseItem->restaurant_id = $restaurantId;
                $purchaseItem->save();

                // Update inventory - ADD quantity (adds ML directly to sub_category.current_stock_ml if Bar Item)
                Inventory::updateStock($item['product_id'], $item['quantity'], 'add');

                $totalItems++;
            }

            // Update purchase total items
            $purchase->total_items = $totalItems;
            $purchase->save();

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase added successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to add purchase: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        // Sync Bar Items into Products with ML unit so they appear in the Purchase dropdown
        $mlUnit = Product::syncBarItems($restaurantId);

        $purchase = Purchase::with(['items.product.barItem', 'items.unit', 'supplier'])
            ->where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->firstOrFail();
        
        $suppliers = Supplier::where('restaurant_id', $restaurantId)
            ->where('status', 'A')
            ->orderBy('supplier_name')
            ->get();
        
        $products = Product::with(['unit', 'barItem.brand', 'barItem.category'])
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();
        
        return view('purchases.edit', compact('purchase', 'suppliers', 'products', 'mlUnit'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:purchases,id',
            'invoice_no' => 'required|string|max:100',
            'purchase_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'bill_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
            'bill_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $restaurantId = auth()->user()->restaurant_id;
            $mlUnit = Product::ensureMlUnit($restaurantId);

            $purchase = Purchase::where('id', $request->id)
                ->where('restaurant_id', $restaurantId)
                ->firstOrFail();

            // Check for duplicate invoice number (excluding current purchase)
            $existingInvoice = Purchase::where('invoice_no', $request->invoice_no)
                ->where('restaurant_id', $restaurantId)
                ->where('id', '!=', $request->id)
                ->first();
            
            if ($existingInvoice) {
                return redirect()->back()->withInput()->with('error', 'Invoice number already exists!');
            }

            // Handle file upload
            if ($request->hasFile('bill_attachment')) {
                // Delete old file if exists
                if ($purchase->bill_attachment && Storage::disk('public')->exists($purchase->bill_attachment)) {
                    Storage::disk('public')->delete($purchase->bill_attachment);
                }
                
                $file = $request->file('bill_attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $billAttachmentPath = $file->storeAs('purchase_bills', $filename, 'public');
                $purchase->bill_attachment = $billAttachmentPath;
            }

            // First, reverse inventory for old items (DECREASE quantity from Bar Inventory / general Inventory)
            foreach ($purchase->items as $oldItem) {
                Inventory::updateStock($oldItem->product_id, $oldItem->quantity, 'subtract');
            }

            // Delete old purchase items
            $purchase->items()->delete();

            // Update purchase details
            $purchase->invoice_no = $request->invoice_no;
            $purchase->purchase_date = $request->purchase_date;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->bill_amount = $request->bill_amount;
            $purchase->total_amount = $request->bill_amount;
            $purchase->remarks = $request->remarks;
            $purchase->save();

            $totalItems = 0;

            // Create new purchase items and update inventory (ADD quantity)
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $isBarItem = $product && !empty($product->sub_category_id);

                if ($isBarItem && $product->unit_id != $mlUnit->id) {
                    $product->unit_id = $mlUnit->id;
                    $product->save();
                }

                $purchaseItem = new PurchaseItem();
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->sub_category_id = $isBarItem ? $product->sub_category_id : null;
                $purchaseItem->unit_id = $isBarItem ? $mlUnit->id : $product->unit_id;
                $purchaseItem->quantity = $item['quantity'];
                $purchaseItem->price = $item['price'];
                $purchaseItem->restaurant_id = $restaurantId;
                $purchaseItem->save();

                // Update inventory - ADD quantity (adds ML directly to sub_category.current_stock_ml if Bar Item)
                Inventory::updateStock($item['product_id'], $item['quantity'], 'add');

                $totalItems++;
            }

            // Update purchase total items
            $purchase->total_items = $totalItems;
            $purchase->save();

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to update purchase: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['items.product.unit', 'items.product.barItem', 'items.unit', 'supplier', 'user'])
            ->where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        return view('purchases.show', compact('purchase'));
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();

            // Reverse inventory for all items (DECREASE quantity)
            foreach ($purchase->items as $item) {
                Inventory::updateStock($item->product_id, $item->quantity, 'subtract');
            }

            // Delete purchase items
            $purchase->items()->delete();
            
            // Delete purchase
            $purchase->delete();
            
            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }

    public function getProduct($id)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $product = Product::with(['unit', 'barItem'])
            ->where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->first(['id', 'product_name', 'unit_id', 'sub_category_id']);
        
        if ($product) {
            $isBarItem = !empty($product->sub_category_id);
            if ($isBarItem) {
                $mlUnit = Product::ensureMlUnit($restaurantId);
                if ($product->unit_id != $mlUnit->id) {
                    $product->unit_id = $mlUnit->id;
                    $product->save();
                    $product->load('unit');
                }
            }

            return response()->json([
                'success' => true,
                'product' => $product,
                'is_bar_item' => $isBarItem,
                'unit_name' => $isBarItem ? 'ML' : ($product->unit->name ?? 'N/A'),
                'bottle_size_ml' => $isBarItem ? (float) ($product->barItem->bottle_size_ml ?? 750) : null,
                'current_stock_ml' => $isBarItem ? (float) ($product->barItem->current_stock_ml ?? 0) : null,
            ]);
        }
        
        return response()->json(['success' => false]);
    }

    public function checkStock($productId)
    {
        $product = Product::with(['unit', 'barItem'])
            ->where('id', $productId)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first();

        $stock = Inventory::getStock($productId);
        $isBarItem = $product && !empty($product->sub_category_id);

        return response()->json([
            'stock' => $stock,
            'is_bar_item' => $isBarItem,
            'unit_name' => $isBarItem ? 'ML' : ($product->unit->name ?? ''),
            'bottle_size_ml' => $isBarItem ? (float) ($product->barItem->bottle_size_ml ?? 750) : null,
        ]);
    }

    public function stockReport()
    {
        $inventories = Inventory::with(['product.unit'])
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->get();
        
        $products = Product::with('unit')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->get();
        
        return view('inventory.stock-report', compact('inventories', 'products'));
    }
}
