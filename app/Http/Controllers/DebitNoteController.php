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

class DebitNoteController extends Controller
{
    public function index()
    {
        $debitNotes = DebitNote::with(['supplier', 'items.product.unit', 'user'])
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('debit_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);
        
        return view('debit_notes.index', compact('debitNotes'));
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