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
        $purchases = Purchase::with(['supplier', 'items.product'])
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('supplier_name')
            ->get();
        
        $products = Product::with('unit')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();
        
        return view('purchases.create', compact('suppliers', 'products'));
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
            $purchase->restaurant_id = auth()->user()->restaurant_id;
            $purchase->user_id = auth()->user()->id;
            $purchase->status = 'COMPLETED';
            $purchase->save();

            $totalItems = 0;

            // Create purchase items and update inventory
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                $purchaseItem = new PurchaseItem();
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->unit_id = $product->unit_id;
                $purchaseItem->quantity = $item['quantity'];
                $purchaseItem->price = $item['price'];
                $purchaseItem->restaurant_id = auth()->user()->restaurant_id;
                $purchaseItem->save();

                // Update inventory - ADD quantity
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
        $purchase = Purchase::with(['items.product', 'supplier'])
            ->where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        $suppliers = Supplier::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('supplier_name')
            ->get();
        
        $products = Product::with('unit')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();
        
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
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
            $purchase = Purchase::where('id', $request->id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();

            // Check for duplicate invoice number (excluding current purchase)
            $existingInvoice = Purchase::where('invoice_no', $request->invoice_no)
                ->where('restaurant_id', auth()->user()->restaurant_id)
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

            // First, reverse inventory for old items (DECREASE quantity)
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
                
                $purchaseItem = new PurchaseItem();
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->unit_id = $product->unit_id;
                $purchaseItem->quantity = $item['quantity'];
                $purchaseItem->price = $item['price'];
                $purchaseItem->restaurant_id = auth()->user()->restaurant_id;
                $purchaseItem->save();

                // Update inventory - ADD quantity
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
        $purchase = Purchase::with(['items.product.unit', 'supplier', 'user'])
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