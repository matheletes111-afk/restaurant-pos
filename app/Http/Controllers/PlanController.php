<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan;
use App\Models\PlanHistory;
use Razorpay\Api\Api;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_delete', 'N')
                    ->orderBy('id', 'desc')
                    ->get();
        
        return view('admin.plans.index', compact('plans'));
    }

    public function selectPlan()
    {
        $plans = Plan::where('is_delete', 'N')
                    ->orderBy('id', 'desc')
                    ->get();
        
        return view('plans', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }



public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'country_id' => 'nullable|integer',
        'currency' => 'nullable|string|max:10',
        'billing_cycle' => 'required|in:monthly,quarterly,half-yearly,yearly',
        'duration_days' => 'required|integer|min:1',
        'description' => 'nullable|string',
        'is_default_free' => 'required|in:Y,N',
        'is_default_paid' => 'required|in:Y,N'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

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
    if ($request->is_default_free === "Y" && $request->price == 0) {
        $freeChk = Plan::where('is_default_free', 'Y')
            ->where('price', 0)
            ->where('is_delete', 'N')
            ->where('country_id', $request->country_id)
            ->first();

        if ($freeChk) {
            return redirect()->back()
                ->with('error', 'Free Default Plan For This Country Already Exists')
                ->withInput();
        }
    }

    // Check default paid plan per country
    if ($request->is_default_paid === "Y" && $request->price > 0) {
        $paidChk = Plan::where('is_default_paid', 'Y')
            ->where('price', '>', 0)
            ->where('is_delete', 'N')
            ->where('country_id', $request->country_id)
            ->first();

        if ($paidChk) {
            return redirect()->back()
                ->with('error', 'Paid Default Plan For This Country Already Exists')
                ->withInput();
        }
    }

    try {

        /* =====================================================
           RAZORPAY PLAN (ONLY FOR PAID PLANS)
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

            $razorpayPlan = $api->plan->create([
                'period' => $period,
                'interval' => $interval,
                'item' => [
                    'name' => $request->name,
                    'amount' => $request->price * 100, // Razorpay requires min ₹1
                    'currency' => $request->currency ?? 'INR',
                    'description' => $request->description ?? ''
                ],
                'notes' => [
                    'duration_days' => $request->duration_days,
                    'plan_type' => 'PAID'
                ]
            ]);

            $razorpayPlanId = $razorpayPlan->id;
        }

        /* =====================================================
           SAVE PLAN
        ====================================================== */

        $plan = new Plan();
        $plan->name = $request->name;
        $plan->price = $request->price;
        $plan->country_id = $request->country_id;
        $plan->currency = $request->currency ?? 'INR';
        $plan->billing_cycle = $request->billing_cycle;
        $plan->duration_days = $request->duration_days;
        $plan->description = $request->description;
        $plan->is_default_free = $request->is_default_free;
        $plan->is_default_paid = $request->is_default_paid;
        $plan->razorpay_plan_id = $razorpayPlanId; // NULL for free plans
        $plan->category_number = $request->category_number;
        $plan->total_number_of_dishes = $request->total_number_of_dishes;
        $plan->total_number_of_table = $request->total_number_of_table;
        $plan->inventory_checkbox = $request->inventory_checkbox;

        $plan->save();

        /* =====================================================
           PLAN HISTORY
        ====================================================== */

        $history = new PlanHistory();
        $history->plan_id = $plan->id;
        $history->name = $request->name;
        $history->razorpay_plan_id = $razorpayPlanId;
        $history->status = "C";
        $history->price = $request->price;
        $history->country_id = $request->country_id;
        $history->currency = $request->currency ?? 'INR';
        $history->billing_cycle = $request->billing_cycle;
        $history->duration_days = $request->duration_days;
        $history->description = $request->description;
        $history->is_default_free = $request->is_default_free;
        $history->is_default_paid = $request->is_default_paid;
        $history->category_number = $request->category_number;
        $history->total_number_of_dishes = $request->total_number_of_dishes;
        $history->total_number_of_table = $request->total_number_of_table;
        $history->inventory_checkbox = $request->inventory_checkbox;
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'country_id' => 'nullable|integer',
            'currency' => 'nullable|string|max:10',
            'billing_cycle' => 'required|in:monthly,quarterly,half-yearly,yearly',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_default_free' => 'required|in:Y,N',
            'is_default_paid' => 'required|in:Y,N'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $plan = Plan::where('id', $id)
                    ->where('is_delete', 'N')
                    ->firstOrFail();

        // Check if plan already exists with different ID
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

        // Check default free plan
        if ($request->is_default_free == "Y" && $request->price < 1) {
            $planChk = Plan::where('is_default_free', 'Y')
                ->where('price', 0)
                ->where('is_delete', 'N')
                ->where('id', '!=', $id)
                ->where('country_id', $request->country_id)
                ->first();

            if ($planChk) {
                return redirect()->back()
                    ->with('error', 'Free default Plan For That Country Already Exists')
                    ->withInput();
            }
        }

        // Check default paid plan
        if ($request->is_default_paid == "Y" && $request->price > 0) {
            $planChk = Plan::where('is_default_paid', 'Y')
                ->where('id', '!=', $id)
                ->where('price', '!=', 0)
                ->where('is_delete', 'N')
                ->where('country_id', $request->country_id)
                ->first();

            if ($planChk) {
                return redirect()->back()
                    ->with('error', 'Paid default Plan For That Country Already Exists')
                    ->withInput();
            }
        }

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

            // Create new Razorpay plan
            $razorpayPlan = $api->plan->create([
                'period' => $period,
                'interval' => $interval,
                'item' => [
                    'name' => $request->name,
                    'amount' => $request->price * 100,
                    'currency' => $request->currency ?? 'INR',
                    'description' => $request->description ?? '',
                ],
                'notes' => [
                    'duration_days' => $request->duration_days
                ]
            ]);

            // Create new plan entry (like Razorpay)
            $newUpdatedPlan = new Plan();
            $newUpdatedPlan->name = $request->name;
            $newUpdatedPlan->plan_parent_id = $plan->id;
            $newUpdatedPlan->price = $request->price;
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
            $newUpdatedPlan->save();

            // Update old plan end date
            $plan->end_date = now();
            $plan->save();

            // Create plan history
            $insHis = new PlanHistory();
            $insHis->plan_id = $newUpdatedPlan->id;
            $insHis->name = $request->name;
            $insHis->razorpay_plan_id = $razorpayPlan->id;
            $insHis->status = "U";
            $insHis->price = $request->price;
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
}