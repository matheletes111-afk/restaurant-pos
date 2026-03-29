<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Auth;
use DB;
class UnitMasterController extends Controller
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
        $data = Unit::orderBy('id', 'DESC')->where('restaurant_id',auth()->user()->restaurant_id)->get();
        return view('units', compact('data'));
    }

    public function insert(Request $req)
    {
        $validated = $req->validate([
            'name' => 'required',
        ]);

        Unit::create([
            'name' => $req->name,
            'restaurant_id' => Auth::user()->restaurant_id,
            'status' => 'A',
            'created_by' => Auth::user()->id,
        ]);

        return back()->with('success', 'Unit added successfully.');
    }

    public function update(Request $req)
    {
        $validated = $req->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $req->id,
        ]);

        Unit::where('id', $req->id)->update([
            'name' => $req->name,
            'restaurant_id' => $req->restaurant_id,
            'updated_by' => Auth::user()->name ?? 'Admin',
        ]);

        return back()->with('success', 'Unit updated successfully.');
    }

    public function delete($id)
    {
        $check = Unit::where('id', $id)->where('restaurant_id',auth()->user()->restaurant_id)->first();
        if (@$check=="") {
            return back()->with('error', 'Unauthorized Access');
        }
        Unit::where('id', $id)->update([
            'status' => 'D',
            'updated_by' => Auth::user()->name ?? 'Admin',
        ]);

        return back()->with('success', 'Unit deleted successfully.');
    }
}