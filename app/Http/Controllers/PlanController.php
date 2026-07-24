<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan;
use App\Models\RestaurantToCustomPlan;
use App\Models\PlanHistory;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;

class PlanController extends Controller
{
    public function index()
    {
        if (auth()->user()->role!="SA") {
            return redirect()->route('restaurant.plans');
        }
        $plans = Plan::where('is_delete', 'N')
                    ->orderByRaw("CASE WHEN is_default_plan = 'Y' AND plan_status = 'A' THEN 0 WHEN plan_status = 'A' THEN 1 ELSE 2 END")
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('id', 'desc')
                    ->get();
        
        return view('admin.plans.index', compact('plans'));
    }

public function selectPlan()
{
    // Get restaurant ID from authenticated user
    $restaurantId = auth()->user()->restaurant_id;
    
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
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'desc')
        ->get();
    
    // Get assigned custom plans that are active versions
    $assignedPlans = Plan::whereIn('id', $assignedPlanIds)
        ->where('is_delete', 'N')
        ->where('plan_status', 'A')
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'desc')
        ->get();
    
    // Merge and unique them by id
    $plans = $defaultPlans->merge($assignedPlans)->unique('id');

    // Filter plans: if there is an active subscription, only show plans with price > current active plan price
    $activeSubscription = Subscription::where('user_id', $restaurantId)
        ->where('status', 'active')
        ->with('plan')
        ->first();

    if ($activeSubscription && $activeSubscription->plan) {
        $currentPlanPrice = $activeSubscription->plan->price;
        $plans = $plans->filter(function($plan) use ($currentPlanPrice) {
            return $plan->price > $currentPlanPrice;
        });
    }

    // Sort plans: Active default plans first, then other plans, ordered by sort_order and fallback ID
    $plans = $plans->sort(function($a, $b) {
        $a_def = ($a->is_default_plan == 'Y' && $a->plan_status == 'A') ? 0 : 1;
        $b_def = ($b->is_default_plan == 'Y' && $b->plan_status == 'A') ? 0 : 1;
        if ($a_def !== $b_def) {
            return $a_def <=> $b_def;
        }
        if ($a->sort_order !== $b->sort_order) {
            return $a->sort_order <=> $b->sort_order;
        }
        return $b->id <=> $a->id;
    })->values();
    
    // Pick the first default plan for backward compatibility or placeholder usage
    $defaultPlan = $defaultPlans->first();
    
    return view('plans', compact('plans', 'assignedPlanIds', 'defaultPlan'));
}

    public function create()
    {
        return view('admin.plans.create');
    }



public function store(Request $request)
{
    // $validator = Validator::make($request->all(), [
    //     'name' => 'required|string|max:255',
    //     'price' => 'required|numeric|min:0',
    //     'gst_percentage' => 'nullable|numeric|min:0|max:100',
    //     'country_id' => 'nullable|integer',
    //     'currency' => 'nullable|string|max:10',
    //     'billing_cycle' => 'required|in:monthly,quarterly,half-yearly,yearly',
    //     'duration_days' => 'required|integer|min:1',
    //     'description' => 'nullable|string',
    //     'is_default_free' => 'required|in:Y,N',
    //     'is_default_paid' => 'required|in:Y,N',
    //     'category_number' => 'nullable|integer|min:0',
    //     'total_number_of_dishes' => 'nullable|integer|min:0',
    //     'total_number_of_table' => 'nullable|integer|min:0',
    //     'inventory_checkbox' => 'nullable|in:Y,N',
    //     'is_default_plan' => 'nullable|in:Y,N'
    // ]);

    // if ($validator->fails()) {
    //     return redirect()->back()
    //         ->withErrors($validator)
    //         ->withInput();
    // }

    // Set default GST percentage (18% if not provided)
    $gstPercentage = $request->gst_percentage ?? 18.00;
    
    // Calculate taxable amount and GST amount (for database storage only)
    $priceIncludingGst = $request->price;
    $taxableAmount = $priceIncludingGst / (1 + ($gstPercentage / 100));
    $gstAmount = $priceIncludingGst - $taxableAmount;

    // Round to 2 decimal places
    $taxableAmount = round($taxableAmount, 2);
    $gstAmount = round($gstAmount, 2);

    // Check if plan already exists
    $planChk = Plan::where('name', $request->name)
        ->where('price', $request->price)
        ->where('is_delete', 'N')
        ->where('country_id', $request->country_id)
        ->where('duration_days', $request->duration_days)
        ->first();

    if ($planChk) {
        return redirect()->back()
            ->with('error', 'This Plan Already Exists')
            ->withInput();
    }

    // Check default free plan per country
    // if ($request->is_default_free === "Y" && $request->price == 0) {
    //     $freeChk = Plan::where('is_default_free', 'Y')
    //         ->where('price', 0)
    //         ->where('is_delete', 'N')
    //         ->where('country_id', $request->country_id)
    //         ->first();

    //     if ($freeChk) {
    //         return redirect()->back()
    //             ->with('error', 'Free Default Plan For This Country Already Exists')
    //             ->withInput();
    //     }
    // }

    // Check default paid plan per country
    // if ($request->is_default_paid === "Y" && $request->price > 0) {
    //     $paidChk = Plan::where('is_default_paid', 'Y')
    //         ->where('price', '>', 0)
    //         ->where('is_delete', 'N')
    //         ->where('country_id', $request->country_id)
    //         ->first();

    //     if ($paidChk) {
    //         return redirect()->back()
    //             ->with('error', 'Paid Default Plan For This Country Already Exists')
    //             ->withInput();
    //     }
    // }

   

    try {

        /* =====================================================
           RAZORPAY PLAN (ONLY FOR PAID PLANS)
           Using FULL amount including GST
        ====================================================== */

        $razorpayPlanId = null;

        if ($request->price > 0) {

            $api = new Api(
                env('RAZORPAY_KEY_ID'),
                env('RAZORPAY_KEY_SECRET')
            );

            $period = 'monthly';
            $interval = 1;

            if ($request->billing_cycle == 'quarterly') {
                $interval = 3;
            } elseif ($request->billing_cycle == 'half-yearly') {
                $interval = 6;
            } elseif ($request->billing_cycle == 'yearly') {
                $period = 'yearly';
            }

            // Use FULL amount including GST (customer pays this)
            // Razorpay settles this FULL amount to your bank account
            $razorpayAmountInPaise = max($request->price * 100, 100);
            
            $razorpayPlan = $api->plan->create([
                'period' => $period,
                'interval' => $interval,
                'item' => [
                    'name' => $request->name,
                    'amount' => round($razorpayAmountInPaise),
                    'currency' => $request->currency ?? 'INR',
                    'description' => $request->description ?? ''
                ],
                'notes' => [
                    'duration_days' => $request->duration_days,
                    'plan_type' => 'PAID',
                    'gst_percentage' => $gstPercentage,
                    'taxable_amount' => $taxableAmount,
                    'gst_amount' => $gstAmount,
                    'total_amount_with_gst' => $request->price
                ]
            ]);

            $razorpayPlanId = $razorpayPlan->id;
        }

        /* =====================================================
           SAVE PLAN (with GST details for records)
        ====================================================== */

        $plan = new Plan();
        $plan->name = $request->name;
        $plan->price = $request->price; // Price including GST
        $plan->gst_percentage = $gstPercentage;
        $plan->taxable_amount = $taxableAmount;
        $plan->gst_amount = $gstAmount;
        $plan->country_id = $request->country_id;
        $plan->currency = $request->currency ?? 'INR';
        $plan->billing_cycle = $request->billing_cycle;
        $plan->duration_days = $request->duration_days;
        $plan->description = $request->description;
        $plan->is_default_free = $request->is_default_free;
        $plan->is_default_paid = $request->is_default_paid;
        $plan->razorpay_plan_id = $razorpayPlanId;
        $plan->category_number = $request->category_number ?? 0;
        $plan->total_number_of_dishes = $request->total_number_of_dishes ?? 0;
        $plan->total_number_of_table = $request->total_number_of_table ?? 0;
        $plan->inventory_checkbox = $request->inventory_checkbox ?? 'N';
        $plan->is_default_plan = $request->is_default_plan ?? 'N';
        $plan->is_delete = 'N';
        $plan->plan_status = 'A';

        $plan->save();
        

        /* =====================================================
           PLAN HISTORY (for audit trail)
        ====================================================== */

        $history = new PlanHistory();
        $history->plan_id = $plan->id;
        $history->name = $request->name;
        $history->razorpay_plan_id = $razorpayPlanId;
        $history->status = "C";
        $history->price = $request->price;
        $history->gst_percentage = $gstPercentage;
        $history->taxable_amount = $taxableAmount;
        $history->gst_amount = $gstAmount;
        $history->country_id = $request->country_id;
        $history->currency = $request->currency ?? 'INR';
        $history->billing_cycle = $request->billing_cycle;
        $history->duration_days = $request->duration_days;
        $history->description = $request->description;
        $history->is_default_free = $request->is_default_free;
        $history->is_default_paid = $request->is_default_paid;
        $history->category_number = $request->category_number ?? 0;
        $history->total_number_of_dishes = $request->total_number_of_dishes ?? 0;
        $history->total_number_of_table = $request->total_number_of_table ?? 0;
        $history->inventory_checkbox = $request->inventory_checkbox ?? 'N';
        $history->save();

        return redirect()->route('plans.index')
            ->with('success', 'Plan created successfully');

    } catch (\Exception $e) {

        return redirect()->back()
            ->with('error', 'Error creating plan: ' . $e->getMessage())
            ->withInput();
    }
}


    public function edit($id)
    {
        $plan = Plan::where('id', $id)
                    ->where('is_delete', 'N')
                    ->firstOrFail();
        
        return view('admin.plans.edit', compact('plan'));
    }

public function update(Request $request, $id)
{
    // $validator = Validator::make($request->all(), [
    //     'name' => 'required|string|max:255',
    //     'price' => 'required|numeric',
    //     'gst_percentage' => 'nullable|numeric|min:0|max:100',
    //     'country_id' => 'nullable|integer',
    //     'currency' => 'nullable|string|max:10',
    //     'billing_cycle' => 'required|in:monthly,quarterly,half-yearly,yearly',
    //     'duration_days' => 'required|integer|min:1',
    //     'description' => 'nullable|string',
    //     'is_default_free' => 'required|in:Y,N',
    //     'is_default_paid' => 'required|in:Y,N'
    // ]);

    // if ($validator->fails()) {
    //     return redirect()->back()
    //         ->withErrors($validator)
    //         ->withInput();
    // }

    $plan = Plan::where('id', $id)
                ->where('is_delete', 'N')
                ->firstOrFail();

    // Set default GST percentage (18% if not provided)
    $gstPercentage = $request->gst_percentage ?? 18.00;
    
    // Calculate taxable amount and GST amount (for database storage only)
    $priceIncludingGst = $request->price;
    $taxableAmount = $priceIncludingGst / (1 + ($gstPercentage / 100));
    $gstAmount = $priceIncludingGst - $taxableAmount;
    
    // Round to 2 decimal places
    $taxableAmount = round($taxableAmount, 2);
    $gstAmount = round($gstAmount, 2);

    // Check if plan already exists with different ID (Commented out to allow editing plans multiple times)
    /*
    $planChk = Plan::where('name', $request->name)
        ->where('price', $request->price)
        ->where('id', '!=', $id)
        ->where('is_delete', 'N')
        ->where('country_id', $request->country_id)
        ->where('duration_days', $request->duration_days)
        ->first();

    if ($planChk) {
        return redirect()->back()
            ->with('error', 'This Plan Already Exists')
            ->withInput();
    }
    */

    // Check default free plan
    // if ($request->is_default_free == "Y" && $request->price == 0) {
    //     $planChk = Plan::where('is_default_free', 'Y')
    //         ->where('price', 0)
    //         ->where('is_delete', 'N')
    //         ->where('id', '!=', $id)
    //         ->where('country_id', $request->country_id)
    //         ->first();

    //     if ($planChk) {
    //         return redirect()->back()
    //             ->with('error', 'Free default Plan For That Country Already Exists')
    //             ->withInput();
    //     }
    // }

    // Check default paid plan
    // if ($request->is_default_paid == "Y" && $request->price > 0) {
    //     $planChk = Plan::where('is_default_paid', 'Y')
    //         ->where('id', '!=', $id)
    //         ->where('price', '>', 0)
    //         ->where('is_delete', 'N')
    //         ->where('country_id', $request->country_id)
    //         ->first();

    //     if ($planChk) {
    //         return redirect()->back()
    //             ->with('error', 'Paid default Plan For That Country Already Exists')
    //             ->withInput();
    //     }
    // }



    try {
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

        $billingCycle = $request->billing_cycle;
        $period = 'monthly';
        $interval = 1;

        if ($billingCycle == 'quarterly') {
            $interval = 3;
        } elseif ($billingCycle == 'half-yearly') {
            $interval = 6;
        } elseif ($billingCycle == 'yearly') {
            $period = 'yearly';
        }

        // Use FULL amount including GST for Razorpay
        $razorpayAmountInPaise = max($request->price * 100, 100);

        // Create new Razorpay plan
        $razorpayPlan = $api->plan->create([
            'period' => $period,
            'interval' => $interval,
            'item' => [
                'name' => $request->name,
                'amount' => round($razorpayAmountInPaise),
                'currency' => $request->currency ?? 'INR',
                'description' => $request->description ?? '',
            ],
            'notes' => [
                'duration_days' => $request->duration_days,
                'plan_type' => 'PAID',
                'gst_percentage' => $gstPercentage,
                'taxable_amount' => $taxableAmount,
                'gst_amount' => $gstAmount,
                'total_amount_with_gst' => $request->price
            ]
        ]);

        // Create new plan entry (like Razorpay)
        $newUpdatedPlan = new Plan();
        $newUpdatedPlan->name = $request->name;
        $newUpdatedPlan->plan_parent_id = $plan->id;
        $newUpdatedPlan->price = $request->price;
        $newUpdatedPlan->gst_percentage = $gstPercentage;
        $newUpdatedPlan->taxable_amount = $taxableAmount;
        $newUpdatedPlan->gst_amount = $gstAmount;
        $newUpdatedPlan->country_id = $request->country_id;
        $newUpdatedPlan->currency = $request->currency ?? 'INR';
        $newUpdatedPlan->billing_cycle = $request->billing_cycle;
        $newUpdatedPlan->duration_days = $request->duration_days;
        $newUpdatedPlan->description = $request->description;
        $newUpdatedPlan->is_default_free = $request->is_default_free;
        $newUpdatedPlan->is_default_paid = $request->is_default_paid;
        $newUpdatedPlan->razorpay_plan_id = $razorpayPlan->id;
        $newUpdatedPlan->category_number = $request->category_number;
        $newUpdatedPlan->total_number_of_dishes = $request->total_number_of_dishes;
        $newUpdatedPlan->total_number_of_table = $request->total_number_of_table;
        $newUpdatedPlan->inventory_checkbox = $request->inventory_checkbox;
        $newUpdatedPlan->is_default_plan = $request->is_default_plan;
        $newUpdatedPlan->plan_status = 'A';
        $newUpdatedPlan->save();

        // Update old plan end date
        $plan->end_date = now();
        $plan->plan_status = 'U';
        $plan->save();

        // Update assignments for any restaurants assigned to the old plan to point to the new replica plan ID
        RestaurantToCustomPlan::where('plan_id', $plan->id)
            ->update(['plan_id' => $newUpdatedPlan->id]);

        // Create plan history
        $insHis = new PlanHistory();
        $insHis->plan_id = $newUpdatedPlan->id;
        $insHis->name = $request->name;
        $insHis->razorpay_plan_id = $razorpayPlan->id;
        $insHis->status = "U";
        $insHis->price = $request->price;
        $insHis->gst_percentage = $gstPercentage;
        $insHis->taxable_amount = $taxableAmount;
        $insHis->gst_amount = $gstAmount;
        $insHis->country_id = $request->country_id;
        $insHis->currency = $request->currency ?? 'INR';
        $insHis->billing_cycle = $request->billing_cycle;
        $insHis->duration_days = $request->duration_days;
        $insHis->description = $request->description;
        $insHis->is_default_free = $request->is_default_free;
        $insHis->is_default_paid = $request->is_default_paid;
        $insHis->category_number = $request->category_number;
        $insHis->total_number_of_dishes = $request->total_number_of_dishes;
        $insHis->total_number_of_table = $request->total_number_of_table;
        $insHis->inventory_checkbox = $request->inventory_checkbox;
        $insHis->save();

        return redirect()->route('plans.index')
            ->with('success', 'Plan updated successfully');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error updating plan: ' . $e->getMessage())
            ->withInput();
    }
}

    public function destroy($id)
    {
        $plan = Plan::where('id', $id)
                    ->where('is_delete', 'N')
                    ->firstOrFail();

        $plan->is_delete = 'Y';
        $plan->save();

        // Create delete history
        $insHis = new PlanHistory();
        $insHis->plan_id = $plan->id;
        $insHis->name = $plan->name;
        $insHis->razorpay_plan_id = $plan->razorpay_plan_id;
        $insHis->status = "D";
        $insHis->price = $plan->price;
        $insHis->country_id = $plan->country_id;
        $insHis->currency = $plan->currency;
        $insHis->billing_cycle = $plan->billing_cycle;
        $insHis->duration_days = $plan->duration_days;
        $insHis->description = $plan->description;
        $insHis->is_default_free = $plan->is_default_free;
        $insHis->is_default_paid = $plan->is_default_paid;
        $insHis->save();

        return redirect()->back()
            ->with('success', 'Plan deleted successfully');
    }

    public function toggleDefaultPlan($id)
    {
        if (auth()->user()->role != "SA") {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $plan = Plan::findOrFail($id);
        $plan->is_default_plan = $plan->is_default_plan == 'Y' ? 'N' : 'Y';
        $plan->save();

        return response()->json([
            'success' => true,
            'message' => 'Plan default status updated successfully',
            'is_default_plan' => $plan->is_default_plan
        ]);
    }

    public function updateOrder(Request $request)
    {
        if (auth()->user()->role != "SA") {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer'
        ]);

        $newOrderIds = $request->order;

        DB::beginTransaction();
        try {
            foreach ($newOrderIds as $index => $currentId) {
                DB::table('plans')->where('id', $currentId)->update(['sort_order' => $index]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()], 500);
        }
    }
}