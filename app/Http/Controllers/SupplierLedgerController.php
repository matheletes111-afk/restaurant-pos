<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\SupplierDeposit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierLedgerController extends Controller
{
    public function ledger(Request $request, $id)
    {
        // Get supplier with restaurant check
        $supplier = Supplier::where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        // Default date range (current month)
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Get purchases for date range
        $purchases = Purchase::with(['items.product'])
            ->where('supplier_id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        // Get deposits for date range
        $deposits = SupplierDeposit::where('supplier_id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->whereBetween('deposit_date', [$startDate, $endDate])
            ->orderBy('deposit_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        // Calculate totals
        $totalPurchases = $purchases->sum('total_amount');
        $totalDeposits = $deposits->sum('amount');
        
        // Get opening outstanding (before start date)
        $openingOutstanding = $supplier->opening_outstanding;
        
        // Get purchases before start date
        $previousPurchases = Purchase::where('supplier_id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('purchase_date', '<', $startDate)
            ->sum('total_amount');
        
        // Get deposits before start date
        $previousDeposits = SupplierDeposit::where('supplier_id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('deposit_date', '<', $startDate)
            ->sum('amount');
        
        // Calculate opening balance for period
        $openingBalance = $openingOutstanding + $previousPurchases - $previousDeposits;
        
        // Calculate closing balance
        $closingBalance = $openingBalance + $totalPurchases - $totalDeposits;
        
        // Payment modes for form
        $paymentModes = SupplierDeposit::PAYMENT_MODES;
        
        return view('suppliers.ledger', compact(
            'supplier',
            'purchases',
            'deposits',
            'startDate',
            'endDate',
            'totalPurchases',
            'totalDeposits',
            'openingBalance',
            'closingBalance',
            'paymentModes'
        ));
    }
    
    public function storeDeposit(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'deposit_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:CASH,UPI,BANK_TRANSFER,CHEQUE,OTHER',
            'transaction_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);
        
        // Verify supplier belongs to user's restaurant
        $supplier = Supplier::where('id', $request->supplier_id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        DB::beginTransaction();
        try {
            $deposit = new SupplierDeposit();
            $deposit->supplier_id = $request->supplier_id;
            $deposit->deposit_date = $request->deposit_date;
            $deposit->amount = $request->amount;
            $deposit->payment_mode = $request->payment_mode;
            $deposit->transaction_no = $request->transaction_no;
            $deposit->remarks = $request->remarks;
            $deposit->restaurant_id = auth()->user()->restaurant_id;
            $deposit->user_id = auth()->user()->id;
            $deposit->save();
            
            // Update supplier's outstanding balance
            $supplier->updateOutstanding($request->amount, 'deposit');
            
            DB::commit();
            
            return redirect()->route('suppliers.ledger', $supplier->id)
                ->with('success', 'Deposit added successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add deposit: ' . $e->getMessage());
        }
    }
    
    public function deleteDeposit($id)
    {
        DB::beginTransaction();
        try {
            $deposit = SupplierDeposit::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();
            
            $supplierId = $deposit->supplier_id;
            $amount = $deposit->amount;
            
            // Reverse the deposit effect on supplier
            $supplier = Supplier::find($supplierId);
            $supplier->current_outstanding += $amount;
            $supplier->total_deposits -= $amount;
            if ($supplier->total_deposits < 0) {
                $supplier->total_deposits = 0;
            }
            $supplier->save();
            
            // Delete the deposit
            $deposit->delete();
            
            DB::commit();
            
            return redirect()->route('suppliers.ledger', $supplierId)
                ->with('success', 'Deposit deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete deposit: ' . $e->getMessage());
        }
    }
    
    public function exportLedger(Request $request, $id)
    {
        $supplier = Supplier::where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        // Similar logic to ledger method for data
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // For now, redirect back with message
        return redirect()->route('suppliers.ledger', $id)
            ->with('info', 'Export feature coming soon!');
    }
}