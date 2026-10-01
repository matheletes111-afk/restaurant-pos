<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DebitNoteController extends Controller
{
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

        $query = DebitNote::with(['supplier', 'items.product.unit', 'user'])
            ->where('restaurant_id', $restaurantId);

        if ($hasExplicitFilter) {
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->where(function($dq) use ($fromDate, $toDate) {
                    $dq->whereBetween('debit_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                       ->orWhereBetween('created_at', [$fromDate, $toDate]);
                });
            } elseif ($request->filled('from_date')) {
                $query->where(function($dq) use ($fromDate) {
                    $dq->where('debit_date', '>=', $fromDate->format('Y-m-d'))
                       ->orWhere('created_at', '>=', $fromDate);
                });
            } elseif ($request->filled('to_date')) {
                $query->where(function($dq) use ($toDate) {
                    $dq->where('debit_date', '<=', $toDate->format('Y-m-d'))
                       ->orWhere('created_at', '<=', $toDate);
                });
            }
        } else {
            // Check if debit notes exist in current month
            $currentMonthCount = (clone $query)->where(function($dq) use ($fromDate, $toDate) {
                $dq->whereBetween('debit_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                   ->orWhereBetween('created_at', [$fromDate, $toDate]);
            })->count();

            if ($currentMonthCount > 0) {
                $query->where(function($dq) use ($fromDate, $toDate) {
                    $dq->whereBetween('debit_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                       ->orWhereBetween('created_at', [$fromDate, $toDate]);
                });
            } else {
                // If no debit notes exist in current month, show all existing debit notes
                $minDate = DebitNote::where('restaurant_id', $restaurantId)->min('debit_date');
                if ($minDate) {
                    $fromDate = Carbon::parse($minDate)->startOfDay();
                } else {
                    $minCreated = DebitNote::where('restaurant_id', $restaurantId)->min('created_at');
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
                $q->where('debit_note_no', 'like', "%{$searchTerm}%")
                  ->orWhere('remarks', 'like', "%{$searchTerm}%")
                  ->orWhereHas('supplier', function($sq) use ($searchTerm) {
                      $sq->where('supplier_name', 'like', "%{$searchTerm}%")
                         ->orWhere('phone', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('items.product', function($pq) use ($searchTerm) {
                      $pq->where('product_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $debitNotes = $query->orderBy('debit_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $suppliers = Supplier::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->orderBy('supplier_name')
            ->get();
        
        return view('debit_notes.index', compact(
            'debitNotes', 
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
        // Generate debit note number
        $debitNoteNo = DebitNote::generateDebitNoteNo(auth()->user()->restaurant_id);
        
        $suppliers = Supplier::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('supplier_name')
            ->get();
        
        $products = Product::with('unit')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();
        
        return view('debit_notes.create', compact('debitNoteNo', 'suppliers', 'products'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'debit_note_no' => 'required|string|max:100',
            'supplier_id' => 'required|exists:suppliers,id',
            'debit_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);
        
        // Check for duplicate debit note number
        $existingNote = DebitNote::where('debit_note_no', $request->debit_note_no)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first();
        
        if ($existingNote) {
            return redirect()->back()->withInput()->with('error', 'Debit Note number already exists!');
        }
        
        DB::beginTransaction();
        try {
            // Create debit note
            $debitNote = new DebitNote();
            $debitNote->debit_note_no = $request->debit_note_no;
            $debitNote->supplier_id = $request->supplier_id;
            $debitNote->debit_date = $request->debit_date;
            $debitNote->remarks = $request->remarks;
            $debitNote->restaurant_id = auth()->user()->restaurant_id;
            $debitNote->user_id = auth()->user()->id;
            $debitNote->save();
            
            // Create debit note items and reduce inventory
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                $debitNoteItem = new DebitNoteItem();
                $debitNoteItem->debit_note_id = $debitNote->id;
                $debitNoteItem->product_id = $item['product_id'];
                $debitNoteItem->unit_id = $product->unit_id;
                $debitNoteItem->quantity = $item['quantity'];
                $debitNoteItem->restaurant_id = auth()->user()->restaurant_id;
                $debitNoteItem->save();
                
                // Reduce inventory stock
                Inventory::updateStock($item['product_id'], $item['quantity'], 'subtract');
            }
            
            DB::commit();
            return redirect()->route('debit-notes.index')->with('success', 'Debit Note created successfully! Stock reduced from inventory.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to create debit note: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $debitNote = DebitNote::with(['items.product.unit', 'supplier', 'user'])
            ->where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        return view('debit_notes.show', compact('debitNote'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $debitNote = DebitNote::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();
            
            // Restore inventory stock (add back what was reduced)
            foreach ($debitNote->items as $item) {
                Inventory::updateStock($item->product_id, $item->quantity, 'add');
            }
            
            // Delete items
            $debitNote->items()->delete();
            
            // Delete debit note
            $debitNote->delete();
            
            DB::commit();
            return redirect()->route('debit-notes.index')->with('success', 'Debit Note deleted successfully! Stock restored to inventory.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete debit note: ' . $e->getMessage());
        }
    }
    
    public function checkStock($productId)
    {
        $stock = Inventory::getStock($productId);
        return response()->json(['stock' => $stock]);
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
}