<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\RestaurantToCustomPlan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class RestaurantPlanController extends Controller
{
    /**
     * Show plans for restaurant with subscription status
     */
    public function showPlans()
    {
        $user = Auth::user();
        $restaurantId = $user->restaurant_id;
        
        // Get assigned plan IDs for this restaurant from custom assignments
        $assignedPlanIds = RestaurantToCustomPlan::where('restaurant_id', $restaurantId)
            ->pluck('plan_id')
            ->toArray();
        
        // Get default plan (free plan or plan marked as default)
        $defaultPlan = Plan::where(function($q) {
                $q->where('is_default_plan', 'Y')
                  ->orWhere('price', 0);
            })
            ->where('is_delete', 'N')
            ->first();
        
        // Merge default plan ID with assigned plan IDs (remove duplicates)
        $planIdsToShow = array_unique(array_merge($assignedPlanIds, $defaultPlan ? [$defaultPlan->id] : []));
        
        // Get ONLY the plans that are assigned to this restaurant OR the default plan
        $plans = Plan::whereIn('id', $planIdsToShow)
            ->where('is_delete', 'N')
            ->orderByRaw("FIELD(id, " . implode(',', $planIdsToShow) . ")")
            ->get();
        
        // Check if user has already used free trial
        $hasFreeTrial = Subscription::where('user_id', $user->restaurant_id)
            ->whereHas('plan', function($query) {
                $query->where('price', 0);
            })
            ->exists();
        
        // Get active subscription plan IDs
        $activeSubscriptionPlanIds = Subscription::where('user_id', $user->restaurant_id)
            ->where('status', 'active')
            ->pluck('plan_id')
            ->toArray();
        
        // Get active subscriptions for expiry dates
        $activeSubscriptions = Subscription::where('user_id', $user->restaurant_id)
            ->where('status', 'active')
            ->get()
            ->keyBy('plan_id');
        
        return view('restaurant.plans', compact('plans', 'assignedPlanIds', 'defaultPlan', 'activeSubscriptionPlanIds', 'activeSubscriptions', 'hasFreeTrial'));
    }
}
