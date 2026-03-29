<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
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
        $suppliers = Supplier::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('id', 'desc')
            ->get();
        
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate(Supplier::$rules);

        // Check if phone already exists in this restaurant
        $existingSupplier = Supplier::where('phone', $request->phone)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->first();
        
        if ($existingSupplier) {
            return redirect()->back()->with('error', 'Supplier with this phone number already exists!');
        }

        DB::beginTransaction();
        try {
            $supplier = new Supplier();
            $supplier->supplier_name = $request->supplier_name;
            $supplier->shop_name = $request->shop_name;
            $supplier->phone = $request->phone;
            $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->opening_outstanding = $request->opening_outstanding ?? 0;
            $supplier->current_outstanding = $request->opening_outstanding ?? 0;
            $supplier->restaurant_id = auth()->user()->restaurant_id;
            $supplier->user_id = auth()->user()->id;
            $supplier->status = 'A';
            $supplier->save();

            DB::commit();
            return redirect()->back()->with('success', 'Supplier added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to add supplier: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $rules = Supplier::$rules;
        $rules['phone'] = 'required|string|max:20|unique:suppliers,phone,' . $request->id . ',id,restaurant_id,' . auth()->user()->restaurant_id . ',status,A';
        
        $request->validate($rules);

        DB::beginTransaction();
        try {
            $supplier = Supplier::where('id', $request->id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();
            
            $oldOutstanding = $supplier->current_outstanding;
            
            $supplier->supplier_name = $request->supplier_name;
            $supplier->shop_name = $request->shop_name;
            $supplier->phone = $request->phone;
            $supplier->email = $request->email;
            $supplier->address = $request->address;
            
            // Only update opening outstanding if it's being changed
            if ($request->opening_outstanding != $supplier->opening_outstanding) {
                $supplier->opening_outstanding = $request->opening_outstanding ?? 0;
                $supplier->current_outstanding = $request->opening_outstanding ?? 0;
            }
            
            $supplier->save();

            DB::commit();
            return redirect()->back()->with('success', 'Supplier updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update supplier: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $supplier = Supplier::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->firstOrFail();
            
            $supplier->status = 'D';
            $supplier->save();

            DB::commit();
            return redirect()->back()->with('success', 'Supplier deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete supplier: ' . $e->getMessage());
        }
    }
}