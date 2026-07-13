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
    
    // Get all active default plans (free plan or plan marked as default)
    $defaultPlans = Plan::where(function($q) {
            $q->where('is_default_plan', 'Y')
              ->orWhere('is_default_free', 'Y')
              ->orWhere('is_default_paid', 'Y')
              ->orWhere('price', 0);
        })
        ->where('is_delete', 'N')
        ->where('plan_status', 'A')
        ->get();
    
    // Get assigned custom plans that are active versions
    $assignedPlans = Plan::whereIn('id', $assignedPlanIds)
        ->where('is_delete', 'N')
        ->where('plan_status', 'A')
        ->get();
    
    // Merge and unique them by id
    $plans = $defaultPlans->merge($assignedPlans)->unique('id');
    
    // Pick the first default plan for backward compatibility or view placeholder usage
    $defaultPlan = $defaultPlans->first();
    
    // Check if user has already used free trial
    $hasFreeTrial = Subscription::where('user_id', $user->restaurant_id)
        ->whereHas('plan', function($query) {
            $query->where('price', 0);
        })
        ->exists();
    
    // Get active subscription plan IDs (only for active parent plans)
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
