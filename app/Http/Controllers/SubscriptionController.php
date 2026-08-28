<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\RazorpayCustomer;
use App\Models\RestaurantMaster;
use App\Models\User;
use Razorpay\Api\Api;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
    }

    // Show subscription page for a plan
    public function create($planId)
    {
        $plan = Plan::where('is_delete', 'N')->findOrFail($planId);
        $user = auth()->user();

        
        // Check if user already has this plan active
        $existingSubscription = Subscription::where('user_id', $user->restaurant_id)
            ->where('plan_id', $planId)
            ->where('status', 'active')
            ->first();
        
        if ($existingSubscription) {
            return redirect()->route('plans.index')
                ->with('warning', 'You already have an active subscription for this plan.');
        }
        
        return view('admin.subscriptions.create', compact('plan', 'user'));
    }

    // Process subscription creation - UPDATED TO HANDLE FREE PLANS
    public function store(Request $request, $planId)
    {
        $plan = Plan::where('is_delete', 'N')->findOrFail($planId);
        $user = auth()->user();
        
        // Check if user already has this plan active
        $existingSubscription = Subscription::where('user_id', $user->restaurant_id)
            ->where('plan_id', $planId)
            ->where('status', 'active')
            ->first();
        
        if ($existingSubscription) {
            return redirect()->route('plans.index')
                ->with('warning', 'You already have an active subscription for this plan.');
        }
        
        // Handle FREE PLAN (price = 0)
        if ($plan->price == 0) {
            return $this->activateFreePlan($user, $plan);
        }
        
        // Handle PAID PLAN (price > 0)
        return $this->processPaidPlan($user, $plan, $request);
    }

    // Activate free plan with trial check
    private function activateFreePlan($user, $plan)
    {
        // Check if user has already used a free trial
        $hasUsedFreeTrial = Subscription::where('user_id', $user->restaurant_id)
            ->whereHas('plan', function($query) {
                $query->where('price', 0);
            })
            ->exists();
        
        if ($hasUsedFreeTrial) {
            return redirect()->route('plans.index')
                ->with('error', 'You have already used your free trial. Please choose a paid plan.');
        }
        
        DB::beginTransaction();
        
        try {
            // 1. Create subscription record
            $subscription = new Subscription();
            $subscription->user_id = $user->restaurant_id;
            $subscription->plan_id = $plan->id;
            $subscription->razorpay_plan_id = $plan->razorpay_plan_id;
            $subscription->razorpay_subscription_id = null; // No Razorpay ID for free plans
            $subscription->status = 'active';
            $subscription->start_date = now()->startOfDay();
            $subscription->end_date = now()->addDays(30);
            $subscription->renewal_date = now()->addDays($plan->duration_days);
            $subscription->auto_renew = 0; // Free plans don't auto-renew
            $subscription->save();

            // 2. Create payment record for free plan
            $payment = new Payment();
            $payment->user_id = $user->restaurant_id;
            $payment->plan_id = $plan->id;
            $payment->subscription_id = $subscription->id;
            $payment->razorpay_payment_id = null;
            $payment->razorpay_order_id = null;
            $payment->razorpay_signature = null;
            $payment->amount = 0;
            $payment->currency = 'INR';
            $payment->status = 'success';
            $payment->description = "Free Plan Subscription: {$plan->name}";
            $payment->razorpay_response = null;
            $payment->save();

            DB::commit();

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Free plan activated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Free Plan Activation Error: ' . $e->getMessage());
            
            return redirect()->route('plans.index')
                ->with('error', 'Failed to activate free plan: ' . $e->getMessage());
        }
    }

    // Process paid plan subscription
    private function processPaidPlan($user, $plan, $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // 1. Customer Management
            $restaurant = RestaurantMaster::find($user->restaurant_id);
            $owner = $restaurant ? User::find($restaurant->owner_id) : $user;
            if (!$owner) {
                $owner = $user;
            }

            $razorpayCustomer = RazorpayCustomer::where('user_id', $owner->id)->first();
            
            if (!$razorpayCustomer) {
                $cust_id = null;
                if (!empty($owner->email)) {
                    try {
                        // Check if customer exists in Razorpay
                        $customers = $this->razorpay->customer->all([
                            'email' => $owner->email,
                            'count' => 1
                        ]);

                        if (count($customers['items']) > 0) {
                            $cust_id = $customers['items'][0]['id'];
                            // Update details in Razorpay
                            $this->razorpay->customer->fetch($cust_id)->edit([
                                'name' => $owner->name,
                                'email' => $owner->email,
                                'contact' => $owner->phone ?? auth()->user()->phone ?? '9999999999'
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Razorpay Customer Search/Update Error: ' . $e->getMessage());
                    }
                }

                if (!$cust_id) {
                    // Create new customer in Razorpay
                    $customer = $this->razorpay->customer->create([
                        'name' => $owner->name,
                        'email' => $owner->email ?? auth()->user()->email,
                        'contact' => $owner->phone ?? auth()->user()->phone ?? '9999999999'
                    ]);
                    $cust_id = $customer->id;
                }

                // Store in local DB (must link to users.id to satisfy foreign key constraint)
                $razorpayCustomer = new RazorpayCustomer();
                $razorpayCustomer->user_id = $owner->id;
                $razorpayCustomer->rzpay_customer_id = $cust_id;
                $razorpayCustomer->save();
            } else {
                $cust_id = $razorpayCustomer->rzpay_customer_id;
                // Always update customer details on Razorpay to keep them fresh
                try {
                    $this->razorpay->customer->fetch($cust_id)->edit([
                        'name' => $owner->name,
                        'email' => $owner->email ?? auth()->user()->email,
                        'contact' => $owner->phone ?? auth()->user()->phone ?? '9999999999'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Razorpay Customer Always-Update Error: ' . $e->getMessage());
                }
            }

            // 2. Check for existing subscription
            $existingSubscription = Subscription::where('user_id', $user->restaurant_id)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->where('status', 'active')
                ->latest()
                ->first();

            // 3. Determine total_count based on billing cycle
            $totalCount = $this->getTotalCount($plan->billing_cycle);

            $payableAmount = 0;
            $refundAmount = 0;

            if ($existingSubscription) {
                // Upgrade logic
                $oldPlan = Plan::find($existingSubscription->plan_id);
                
                if ($plan->price <= $oldPlan->price) {
                    return redirect()->back()
                        ->with('error', 'Only plan upgrades allowed during active subscription');
                }

                // Calculate proration for refund
                $prorationData = $this->calculateProrationForRefund($existingSubscription, $oldPlan);
                $refundAmount = $prorationData['refund_amount'];
                
                // Full price for new plan
                $payableAmount = $plan->price;

                // Create subscription with notes about previous plan
                $subscription = $this->razorpay->subscription->create([
                    'plan_id' => $plan->razorpay_plan_id,
                    'customer_notify' => 1,
                    'total_count' => $totalCount,
                    'customer_id' => $cust_id,
                    'notes' => [
                        'user_id' => (string)$user->restaurant_id,
                        'previous_plan' => (string)$oldPlan->id,
                        'proration_total_days' => (string)$prorationData['total_days'],
                        'proration_used_days' => (string)$prorationData['used_days'],
                        'proration_refund_amount' => (string)$refundAmount,
                        'is_upgrade_with_refund' => true
                    ]
                ]);
            } else {
                // New subscription
                $payableAmount = $plan->price;
                $subscription = $this->razorpay->subscription->create([
                    'plan_id' => $plan->razorpay_plan_id,
                    'customer_notify' => 1,
                    'total_count' => $totalCount,
                    'customer_id' => $cust_id,
                    'notes' => [
                        'user_id' => (string)$user->restaurant_id,
                        'is_initial' => true
                    ]
                ]);
            }

            // 4. Create payment record for new plan
            $payment = new Payment();
            $payment->user_id = $user->restaurant_id;
            $payment->plan_id = $plan->id;
            $payment->razorpay_order_id = $subscription->id;
            $payment->amount = $plan->price;
            $payment->gst_percentage ='18';
            $payment->currency = 'INR';
            $payment->status = 'pending';
            $payment->description = isset($existingSubscription) 
                ? 'Upgrade with refund' 
                : 'New subscription';
            $payment->save();

            // 5. Store data in session for payment page
            session([
                'razorpay_subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'user_id' => $user->restaurant_id,
                'payable_amount' => $payableAmount,
                'existing_subscription_id' => $existingSubscription->id ?? null,
                'refund_amount' => $refundAmount,
                'credit_amount' => $refundAmount
            ]);

            // 6. Return to payment page
            return view('admin.subscriptions.payment', [
                'subscription_id' => $subscription->id,
                'customer_id' => $cust_id,
                'plan' => $plan,
                'user' => $owner,
                'payable_amount' => $payableAmount,
                'is_upgrade' => isset($existingSubscription),
                'existing_subscription' => $existingSubscription ?? null,
                'refund_amount' => $refundAmount
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    // New method for refund calculation
    private function calculateProrationForRefund($subscription, $oldPlan)
    {
        // Total days in the subscription period
        $totalDays = Carbon::parse($subscription->start_date)
            ->diffInDays(Carbon::parse($subscription->end_date)) + 1;
            
        // Calculate actual used days
        $usedDays = min(
            Carbon::parse($subscription->start_date)->diffInDays(now()) + 1,
            $totalDays
        );
        
        // Ensure at least 1 day is used for calculation
        $usedDays = max(1, $usedDays);
        
        // Calculate per day cost
        $perDayCost = $oldPlan->price / $totalDays;
        
        // Refund amount for unused days
        $refundAmount = $oldPlan->price - ($perDayCost * $usedDays);
        
        // Ensure refund is not negative
        $refundAmount = max(0, $refundAmount);

        return [
            'total_days' => $totalDays,
            'used_days' => $usedDays,
            'refund_amount' => round($refundAmount, 2)
        ];
    }

    // Show payment page
    public function payment()
    {
        if (!session()->has('razorpay_subscription_id')) {
            return redirect()->route('plans.index')
                ->with('error', 'No subscription found. Please create a subscription first.');
        }

        $plan = Plan::find(session('plan_id'));
        $user = auth()->user();
        $restaurant = RestaurantMaster::find($user->restaurant_id);
        $owner = $restaurant ? User::find($restaurant->owner_id) : $user;
        if (!$owner) {
            $owner = $user;
        }

        return view('admin.subscriptions.payment', [
            'subscription_id' => session('razorpay_subscription_id'),
            'plan' => $plan,
            'user' => $owner,
            'payable_amount' => session('payable_amount'),
            'razorpay_key' => config('services.razorpay.key_id'),
            'existing_subscription_id' => session('existing_subscription_id'),
            'credit_amount' => session('credit_amount')
        ]);
    }




public function paymentSuccess(Request $request)
{
    DB::beginTransaction();
    
    try {
        Log::info('Payment Success Request:', $request->all());
        
        // Get subscription ID from multiple possible sources
        $subscriptionId = $request->razorpay_subscription_id ?? 
                         session('razorpay_subscription_id') ?? 
                         $request->subscription_id;
        
        if (!$subscriptionId) {
            throw new \Exception('Subscription ID not found in request or session');
        }
        
        $plan = Plan::find(session('plan_id') ?? $request->plan_id);
        $user = auth()->user() ?? User::find($request->user_id);
        
        if (!$plan || !$user) {
            throw new \Exception('Plan or User not found');
        }

        // Get restaurant details
        $restaurant = RestaurantMaster::where('owner_id', $user->id)->first();
        
        // Verify payment signature for subscription payment
        if ($request->razorpay_payment_id && $request->razorpay_signature) {
            try {
                $attributes = [
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_subscription_id' => $subscriptionId,
                    'razorpay_signature' => $request->razorpay_signature
                ];
                $this->razorpay->utility->verifyPaymentSignature($attributes);
                Log::info('Payment signature verified successfully');
            } catch (\Exception $e) {
                Log::warning('Payment signature verification failed: ' . $e->getMessage());
                // Don't throw exception for signature failure in development
                if (env('APP_ENV') === 'production') {
                    throw new \Exception('Payment verification failed');
                }
            }
        }

        // 1. Fetch subscription from Razorpay to check its status
        try {
            $razorpaySubscription = $this->razorpay->subscription->fetch($subscriptionId);
            Log::info('Razorpay Subscription Status: ' . $razorpaySubscription->status);
            
            // Check if subscription is in created state
            if ($razorpaySubscription->status === 'created') {
                // Wait a moment for Razorpay to process
                sleep(2);
                $razorpaySubscription = $this->razorpay->subscription->fetch($subscriptionId);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch subscription from Razorpay: ' . $e->getMessage());
            throw new \Exception('Unable to verify subscription status with payment gateway');
        }

        // 2. Fetch payment details from Razorpay to get method
        $paymentMethod = 'N/A';
        if ($request->razorpay_payment_id) {
            try {
                $razorpayPayment = $this->razorpay->payment->fetch($request->razorpay_payment_id);
                $paymentMethod = $razorpayPayment->method ?? 'N/A';
            } catch (\Exception $e) {
                Log::warning('Failed to fetch payment method from Razorpay: ' . $e->getMessage());
            }
        }

        // Create or update payment record
        $payment = Payment::updateOrCreate(
            [
                'razorpay_order_id' => $subscriptionId,
                'user_id' => $user->restaurant_id
            ],
            [
                'plan_id' => $plan->id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'razorpay_response' => json_encode($request->all()),
                'amount' => session('payable_amount') ?? $plan->price,
                'currency' => 'INR',
                'status' => 'success',
                'description' => 'Subscription payment for ' . $plan->name,
                'payment_method' => $paymentMethod,
                'created_at' => now()
            ]
        );

        // 3. Create or update subscription record
        $start_at_ts = $razorpaySubscription->current_start ?? $razorpaySubscription->start_at;
        $end_at_ts = $razorpaySubscription->current_end ?? $razorpaySubscription->end_at;
        $charge_at_ts = $razorpaySubscription->charge_at;

        $startDate = ($start_at_ts && $start_at_ts > 0) ? date('Y-m-d H:i:s', $start_at_ts) : now()->toDateTimeString();
        
        // Ensure endDate is calculated if end_at_ts is missing or is the same/earlier than start_at_ts
        $endDate = ($end_at_ts && $end_at_ts > $start_at_ts) 
            ? date('Y-m-d H:i:s', $end_at_ts) 
            : \Carbon\Carbon::parse($startDate)->addDays($plan->duration_days - 1)->endOfDay()->toDateTimeString();
            
        // Ensure renewalDate is calculated if charge_at_ts is missing or is the same/earlier than start_at_ts
        $renewalDate = ($charge_at_ts && $charge_at_ts > $start_at_ts) 
            ? date('Y-m-d H:i:s', $charge_at_ts) 
            : \Carbon\Carbon::parse($startDate)->addDays($plan->duration_days)->toDateTimeString();

        $status = $razorpaySubscription->status;
        if (in_array($status, ['authenticated', 'created', 'active'])) {
            $status = 'active';
        }

        $subscription = Subscription::updateOrCreate(
            [
                'razorpay_subscription_id' => $subscriptionId,
                'user_id' => $user->restaurant_id
            ],
            [
                'plan_id' => $plan->id,
                'razorpay_plan_id' => $plan->razorpay_plan_id,
                'status' => $status,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'renewal_date' => $renewalDate,
                'auto_renew' => 1,
                'created_at' => now()
            ]
        );

        // 4. Update payment with subscription ID
        $payment->subscription_id = $subscription->id;
        $payment->save();

        DB::commit();

        // 5. Send email notification (continue even if email fails)
        try {
            \Mail::to($user->email)->send(new \App\Mail\SubscriptionSuccessMail($user, $plan, $subscription, $payment, $restaurant));
            Log::info('Subscription success email sent to customer: ' . $user->email);

            // Send copy to admin also
            $adminEmail = config('mail.admin_email') ?? env('ADMIN_EMAIL');
            if ($adminEmail) {
                \Mail::to($adminEmail)->send(new \App\Mail\SubscriptionSuccessMail($user, $plan, $subscription, $payment, $restaurant));
                Log::info('Subscription success email sent to admin: ' . $adminEmail);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send subscription success email: ' . $e->getMessage());
            // Continue execution - don't throw exception
        }

        // 6. Handle upgrade scenario - Process refund asynchronously
        $existingSubscriptionId = session('existing_subscription_id') ?? $request->existing_subscription_id;
        $creditAmount = session('credit_amount') ?? $request->credit_amount ?? 0;
        
        if ($existingSubscriptionId && $creditAmount > 0) {
            // Process refund immediately but handle timing issues
            $this->processImmediateRefund($existingSubscriptionId, $creditAmount);
            
            $successMessage = 'Subscription activated successfully! Refund of ₹' . $creditAmount . ' will be processed shortly.';
        } else {
            $successMessage = 'Subscription activated successfully!';
        }

        // 7. Clear session data
        session()->forget([
            'razorpay_subscription_id',
            'plan_id',
            'user_id',
            'payable_amount',
            'existing_subscription_id',
            'credit_amount',
            'refund_amount'
        ]);

        // Return JSON response for AJAX or redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'subscription' => $subscription,
                'razorpay_status' => $razorpaySubscription->status,
                'redirect' => route('admin.subscriptions.index')
            ]);
        }

        return redirect()->route('admin.subscriptions.index')
            ->with('success', $successMessage)
            ->with('subscription', $subscription)
            ->with('razorpay_status', $razorpaySubscription->status);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Payment Success Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->route('admin.subscriptions.payment.failed')
            ->with('error', 'Payment processing failed: ' . $e->getMessage());
    }
}




    // Process refund immediately with proper timing
    private function processImmediateRefund($existingSubscriptionId, $creditAmount)
    {
        try {
            $existingSubscription = Subscription::find($existingSubscriptionId);
            
            if (!$existingSubscription) {
                Log::warning('Old subscription not found for ID: ' . $existingSubscriptionId);
                return;
            }

            // Update old subscription status first
            $existingSubscription->update([
                'status' => 'expired',
                'end_date' => now(),
                'refund_amount' => $creditAmount // Store refund amount immediately
            ]);

            // Find the payment for this subscription
            $paymentDetails = Payment::where('subscription_id', $existingSubscriptionId)
                ->where('status', 'success')
                ->first();

            if (!$paymentDetails || !$paymentDetails->razorpay_payment_id) {
                Log::warning('No valid payment found for refund for subscription: ' . $existingSubscriptionId);
                return;
            }

            // Try to process refund with retry logic for payment capture
            $this->processRefundWithRetry($paymentDetails, $existingSubscription, $creditAmount);
            
        } catch (\Exception $e) {
            Log::error('Immediate Refund Error: ' . $e->getMessage());
            
            // Update payment with error
            if (isset($paymentDetails)) {
                $paymentDetails->update([
                    'refund_amount' => $creditAmount,
                    'description' => $paymentDetails->description . ' (Refund error: ' . $e->getMessage() . ')'
                ]);
            }
        }
    }

    // Process refund with retry logic for payment capture
    private function processRefundWithRetry($paymentDetails, $existingSubscription, $creditAmount)
    {
        $maxAttempts = 3;
        $attempt = 1;
        
        while ($attempt <= $maxAttempts) {
            try {
                Log::info("Refund attempt {$attempt} for subscription: " . $existingSubscription->id);
                
                // Fetch payment to check status
                $payment = $this->razorpay->payment->fetch($paymentDetails->razorpay_payment_id);
                
                Log::info("Payment status: " . $payment->status);
                
                if ($payment->status === 'captured') {
                    // Payment is captured, proceed with refund
                    $refund = $this->razorpay->payment
                        ->fetch($payment->id)
                        ->refund([
                            'amount' => (int)($creditAmount * 100), // in paise
                            'speed' => 'optimum',
                            'notes' => [
                                'reason' => 'proration_adjustment',
                                'subscription_id' => $existingSubscription->razorpay_subscription_id,
                                'user_id' => (string)$existingSubscription->user_id
                            ]
                        ]);

                    Log::info('Refund created: ' . $refund->id);
                    
                    // Update refund details in database
                    $existingSubscription->refresh(); // Refresh to get latest data
                    $existingSubscription->update([
                        'refund_amount' => $creditAmount
                    ]);

                    $paymentDetails->update(['refund_amount' => $creditAmount]);
                    
                    Log::info('Refund processed successfully: ' . $refund->id . ' for subscription: ' . $existingSubscription->id);
                    
                    // Cancel old subscription
                    $this->cancelOldSubscription($existingSubscription);
                    
                    return true;
                } else {
                    Log::info("Payment not captured yet. Status: {$payment->status}. Waiting 3 seconds...");
                    
                    // Wait before next attempt
                    sleep(3); // Wait 3 seconds
                    $attempt++;
                }
                
            } catch (\Exception $e) {
                Log::error("Refund attempt {$attempt} failed: " . $e->getMessage());
                
                if (strpos($e->getMessage(), 'captured for action') !== false || 
                    strpos($e->getMessage(), 'payment status should be captured') !== false) {
                    // Payment not captured error - wait and retry
                    Log::info("Payment not captured error. Waiting 3 seconds before retry...");
                    sleep(3);
                    $attempt++;
                } else {
                    // Other error, break and log
                    Log::error("Refund failed with error: " . $e->getMessage());
                    
                    // Update subscription with error info
                    $existingSubscription->update([
                        'refund_amount' => $creditAmount
                    ]);
                    
                    // Update payment with error info
                    $paymentDetails->update([
                        'refund_amount' => $creditAmount,
                        'description' => $paymentDetails->description . ' (Refund failed: ' . $e->getMessage() . ')'
                    ]);
                    
                    break;
                }
            }
        }
        
        // Max attempts reached
        if ($attempt > $maxAttempts) {
            Log::warning("Max refund attempts reached for subscription: " . $existingSubscription->id);
            
            // Still store the refund amount
            $existingSubscription->update([
                'refund_amount' => $creditAmount
            ]);
            
            $paymentDetails->update(['refund_amount' => $creditAmount]);
            
            // Note: Subscription cancellation happens even if refund fails
            $this->cancelOldSubscription($existingSubscription);
        }
        
        return false;
    }

    // Add a new method to check subscription status
    public function checkSubscriptionStatus(Request $request)
    {
        try {
            $subscriptionId = $request->subscription_id ?? session('razorpay_subscription_id');
            
            if (!$subscriptionId) {
                return response()->json(['error' => 'Subscription ID not found'], 404);
            }

            // Check in Razorpay
            $razorpaySubscription = $this->razorpay->subscription->fetch($subscriptionId);
            
            // Check in local database
            $localSubscription = Subscription::where('razorpay_subscription_id', $subscriptionId)->first();
            $payment = Payment::where('razorpay_order_id', $subscriptionId)->first();

            return response()->json([
                'razorpay_status' => $razorpaySubscription->status,
                'local_status' => $localSubscription ? $localSubscription->status : 'not_found',
                'payment_status' => $payment ? $payment->status : 'not_found',
                'subscription_id' => $subscriptionId,
                'payment_id' => $payment ? $payment->razorpay_payment_id : null
            ]);

        } catch (\Exception $e) {
            Log::error('Check Subscription Status Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Handle payment failure
    public function paymentFailed(Request $request)
    {
        try {
            $subscriptionId = $request->razorpay_subscription_id ?? session('razorpay_subscription_id');
            
            if ($subscriptionId) {
                Payment::where('razorpay_order_id', $subscriptionId)
                    ->where('user_id', auth()->id())
                    ->update([
                        'status' => 'failed',
                        'razorpay_payment_id' => $request->razorpay_payment_id ?? null,
                        'razorpay_signature' => $request->razorpay_signature ?? null,
                        'razorpay_response' => $request->all_response ?? null
                    ]);
            }

            session()->forget([
                'razorpay_subscription_id',
                'plan_id',
                'user_id',
                'payable_amount',
                'existing_subscription_id',
                'credit_amount',
                'refund_amount'
            ]);

            return redirect()->route('plans.index')
                ->with('error', 'Payment failed. Please try again.');

        } catch (\Exception $e) {
            Log::error('Payment Failed Error: ' . $e->getMessage());
            return redirect()->route('plans.index')
                ->with('error', 'Error processing payment failure.');
        }
    }

    // List user subscriptions
    public function index()
    {
        $user = auth()->user();
        $subscriptions = Subscription::where('user_id', $user->restaurant_id)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Check if user has used free trial
        $hasUsedFreeTrial = Subscription::where('user_id', $user->restaurant_id)
            ->whereHas('plan', function($query) {
                $query->where('price', 0);
            })
            ->exists();

        return view('admin.subscriptions.index', compact('subscriptions', 'hasUsedFreeTrial'));
    }

    // Toggle auto-renew status of subscription
    public function toggleAutoRenew($id)
    {
        try {
            $subscription = Subscription::where('id', $id)
                ->where('user_id', auth()->user()->restaurant_id)
                ->where('status', 'active')
                ->firstOrFail();

            $newStatus = $subscription->auto_renew ? 0 : 1;

            // If Razorpay subscription exists, try to update it
            if ($subscription->razorpay_subscription_id) {
                try {
                    $rzSubscription = $this->razorpay->subscription->fetch($subscription->razorpay_subscription_id);
                    if ($newStatus == 0) {
                        // Cancel at end of current cycle (turn off auto-renew)
                        $rzSubscription->cancel(['cancel_at_cycle_end' => 1]);
                    } else {
                        // Razorpay subscriptions cancelled at cycle end cannot be reactivated directly.
                        // But we allow updating the database field to keep state consistent.
                    }
                } catch (\Exception $e) {
                    Log::warning('Razorpay auto-renew toggle sync failed: ' . $e->getMessage());
                }
            }

            $subscription->update([
                'auto_renew' => $newStatus
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Auto-renew updated successfully!',
                'auto_renew' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Toggle Auto Renew Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update auto-renew: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cancel subscription
    public function cancel($id)
    {
        try {
            $subscription = Subscription::where('id', $id)
                ->where('user_id', auth()->user()->restaurant_id)
                ->where('status', 'active')
                ->firstOrFail();

            // Only cancel Razorpay subscriptions (not free plans)
            if ($subscription->razorpay_subscription_id) {
                $this->razorpay->subscription->fetch($subscription->razorpay_subscription_id)
                    ->cancel(['cancel_at_cycle_end' => 0]);
            }

            // Update local record
            $subscription->update([
                'status' => 'cancelled',
                'renewal_date' => null,
                'auto_renew' => 0
            ]);

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Subscription cancelled successfully.');

        } catch (\Exception $e) {
            Log::error('Cancel Subscription Error: ' . $e->getMessage());
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    // Helper Methods
    private function getTotalCount($billingCycle)
    {
        switch ($billingCycle) {
            case 'monthly': return 24;       // 2 years
            case 'quarterly': return 8;       // 2 years
            case 'half-yearly': return 4;     // 2 years
            default: return 1;                // 1 year
        }
    }

    private function cancelOldSubscription($subscription)
    {
        try {
            if ($subscription->razorpay_subscription_id) {
                $this->razorpay->subscription->fetch($subscription->razorpay_subscription_id)
                    ->cancel(['cancel_at_cycle_end' => 0]);
                Log::info('Old subscription cancelled: ' . $subscription->razorpay_subscription_id);
            }
        } catch (\Exception $e) {
            Log::error('Cancel Old Subscription Error: ' . $e->getMessage());
        }
    }
}