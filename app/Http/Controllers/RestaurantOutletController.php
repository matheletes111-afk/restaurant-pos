<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantMaster;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RestaurantOutletController extends Controller
{
    /**
     * Display list of all outlets for the restaurant owner.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            abort(403, 'Unauthorized. Only restaurant owners can manage outlets.');
        }

        $mainRestaurant = $user->getMainRestaurant();
        if (!$mainRestaurant) {
            return redirect()->route('dashboard')->with('error', 'Main restaurant not found.');
        }

        // Get all child outlets under this main restaurant
        $outlets = RestaurantMaster::where('parent_id', $mainRestaurant->id)
            ->where('status', '!=', 'D')
            ->withCount(['orders', 'tables'])
            ->orderBy('id', 'asc')
            ->get();

        // Get main restaurant active subscription and plan
        $activeSubscription = Subscription::where('user_id', $mainRestaurant->id)
            ->active()
            ->with('plan')
            ->first();

        $plan = $activeSubscription ? $activeSubscription->plan : null;

        // Multi-outlet capability calculation
        $isMultiOutletEnabled = $plan ? ($plan->multi_outlet_checkbox === 'Y') : false;
        $maxOutlets = $plan ? (int) ($plan->total_number_of_outlets ?? 1) : 1; // 0 means unlimited
        
        // Total active branches (Main restaurant = 1 + child outlets)
        $totalOutletsCount = 1 + $outlets->count();

        // Check if can add more outlets
        $canAddMore = false;
        if ($isMultiOutletEnabled) {
            if ($maxOutlets === 0 || $totalOutletsCount < $maxOutlets) {
                $canAddMore = true;
            }
        }

        $currentActiveRestaurantId = $user->restaurant_id;

        return view('restaurant.outlets.index', compact(
            'mainRestaurant',
            'outlets',
            'plan',
            'isMultiOutletEnabled',
            'maxOutlets',
            'totalOutletsCount',
            'canAddMore',
            'currentActiveRestaurantId'
        ));
    }

    /**
     * Store a new outlet branch.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            abort(403, 'Unauthorized. Only restaurant owners can create outlets.');
        }

        $mainRestaurant = $user->getMainRestaurant();
        if (!$mainRestaurant) {
            return redirect()->back()->with('error', 'Main restaurant not found.');
        }

        // Subscription and Plan checks
        $activeSubscription = Subscription::where('user_id', $mainRestaurant->id)
            ->active()
            ->with('plan')
            ->first();

        $plan = $activeSubscription ? $activeSubscription->plan : null;

        if (!$plan || $plan->multi_outlet_checkbox !== 'Y') {
            return redirect()->back()->with('error', 'Your current subscription plan does not support multiple outlets. Please upgrade your plan to add more branches.');
        }

        $maxOutlets = (int) ($plan->total_number_of_outlets ?? 1);
        $currentOutletsCount = 1 + RestaurantMaster::where('parent_id', $mainRestaurant->id)->where('status', '!=', 'D')->count();

        if ($maxOutlets > 0 && $currentOutletsCount >= $maxOutlets) {
            return redirect()->back()->with('error', 'Maximum outlet limit (' . $maxOutlets . ') reached for your current plan. Please upgrade your plan to add more outlets.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'pincode' => 'required|string|max:10',
            'gstin' => 'nullable|string|max:50',
            'fssai_number' => 'nullable|string|max:50',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'upi_id' => 'nullable|string|max:100',
        ]);

        $outlet = new RestaurantMaster();
        $outlet->parent_id = $mainRestaurant->id;
        $outlet->name = $request->name;
        $outlet->address = $request->address;
        $outlet->pincode = $request->pincode;
        $outlet->gstin = $request->gstin;
        $outlet->fssai_number = $request->fssai_number;
        $outlet->gst_percentage = $request->gst_percentage ?? $mainRestaurant->gst_percentage ?? 0.00;
        $outlet->upi_id = $request->upi_id;
        $outlet->owner_id = $user->id;
        $outlet->created_by = $user->id;
        $outlet->status = 'A';
        $outlet->save();

        return redirect()->back()->with('success', 'Outlet branch "' . $outlet->name . '" created successfully!');
    }

    /**
     * Update an outlet's details.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            abort(403, 'Unauthorized. Only restaurant owners can edit outlets.');
        }

        $mainRestaurant = $user->getMainRestaurant();
        if (!$mainRestaurant) {
            return redirect()->back()->with('error', 'Main restaurant not found.');
        }

        $outlet = RestaurantMaster::where('id', $id)
            ->where(function ($q) use ($mainRestaurant) {
                $q->where('id', $mainRestaurant->id)
                  ->orWhere('parent_id', $mainRestaurant->id);
            })
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'pincode' => 'required|string|max:10',
            'gstin' => 'nullable|string|max:50',
            'fssai_number' => 'nullable|string|max:50',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'upi_id' => 'nullable|string|max:100',
        ]);

        $outlet->name = $request->name;
        $outlet->address = $request->address;
        $outlet->pincode = $request->pincode;
        $outlet->gstin = $request->gstin;
        $outlet->fssai_number = $request->fssai_number;
        if ($request->has('gst_percentage')) {
            $outlet->gst_percentage = $request->gst_percentage;
        }
        $outlet->upi_id = $request->upi_id;
        $outlet->updated_by = $user->id;
        $outlet->save();

        return redirect()->back()->with('success', 'Outlet "' . $outlet->name . '" updated successfully!');
    }

    /**
     * Toggle outlet status (Active / Inactive).
     */
    public function status($id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            abort(403, 'Unauthorized.');
        }

        $mainRestaurant = $user->getMainRestaurant();
        $outlet = RestaurantMaster::where('id', $id)
            ->where('parent_id', $mainRestaurant->id)
            ->firstOrFail();

        $outlet->status = ($outlet->status === 'A') ? 'I' : 'A';
        $outlet->save();

        $statusLabel = ($outlet->status === 'A') ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', 'Outlet "' . $outlet->name . '" has been ' . $statusLabel . '.');
    }

    /**
     * Soft-delete an outlet branch.
     */
    public function delete($id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            abort(403, 'Unauthorized.');
        }

        $mainRestaurant = $user->getMainRestaurant();
        
        // Cannot delete main restaurant through outlet manager
        if ($id == $mainRestaurant->id) {
            return redirect()->back()->with('error', 'The primary main restaurant cannot be deleted from outlet management.');
        }

        $outlet = RestaurantMaster::where('id', $id)
            ->where('parent_id', $mainRestaurant->id)
            ->firstOrFail();

        $outlet->status = 'D';
        $outlet->save();

        // If the user's current context was this outlet, switch back to main restaurant
        if ($user->restaurant_id == $id) {
            $user->restaurant_id = $mainRestaurant->id;
            $user->save();
            session(['active_restaurant_id' => $mainRestaurant->id]);
        }

        return redirect()->back()->with('success', 'Outlet "' . $outlet->name . '" deleted successfully.');
    }

    /**
     * Switch current working outlet context for the owner.
     */
    public function switchOutlet($id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            abort(403, 'Unauthorized.');
        }

        $mainRestaurant = $user->getMainRestaurant();
        if (!$mainRestaurant) {
            return redirect()->back()->with('error', 'Main restaurant not found.');
        }

        // Validate that target restaurant is either the main restaurant or one of its child outlets
        $targetRestaurant = RestaurantMaster::where('id', $id)
            ->where(function ($q) use ($mainRestaurant) {
                $q->where('id', $mainRestaurant->id)
                  ->orWhere('parent_id', $mainRestaurant->id);
            })
            ->where('status', '!=', 'D')
            ->first();

        if (!$targetRestaurant) {
            return redirect()->back()->with('error', 'Selected outlet does not exist or has been removed.');
        }

        if ($targetRestaurant->status === 'I') {
            return redirect()->back()->with('warning', 'The selected outlet "' . $targetRestaurant->name . '" is currently marked inactive.');
        }

        // Update user's active restaurant_id and session
        $user->restaurant_id = $targetRestaurant->id;
        $user->save();
        session(['active_restaurant_id' => $targetRestaurant->id]);

        return redirect()->back()->with('success', 'Switched to outlet: ' . $targetRestaurant->name);
    }
}
