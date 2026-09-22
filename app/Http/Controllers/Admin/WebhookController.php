<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\RestaurantMaster;
use Razorpay\Api\Api;

class WebhookController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
    }

    public function handle(Request $request)
    {
        try {
            $this->razorpay->utility->verifyWebhookSignature(
                $request->getContent(),
                $request->header('X-Razorpay-Signature'),
                config('services.razorpay.webhook_secret')
            );

            $payload = $request->all();
            $event = $payload['event'];
            
            Log::info('Webhook Received: ' . $event, $payload);

            switch ($event) {
                case 'subscription.charged':
                    $this->handleSubscriptionCharged($payload);
                    break;
                    
                case 'subscription.cancelled':
                    $this->handleSubscriptionCancelled($payload);
                    break;
                    
                case 'subscription.completed':
                    $this->handleSubscriptionCompleted($payload);
                    break;
                    
                case 'subscription.activated':
                    $this->handleSubscriptionActivated($payload);
                    break;

                case 'subscription.resumed':
                    $this->handleSubscriptionResumed($payload);
                    break;

                case 'subscription.updated':
                    $this->handleSubscriptionUpdated($payload);
                    break;

                case 'subscription.authenticated':
                    $this->handleSubscriptionAuthenticated($payload);
                    break;

                case 'subscription.pending':
                    $this->handleSubscriptionPending($payload);
                    break;

                case 'subscription.halted':
                    $this->handleSubscriptionHalted($payload);
                    break;

                case 'subscription.paused':
                    $this->handleSubscriptionPaused($payload);
                    break;
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function handleSubscriptionCharged($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $payment = $payload['payload']['payment']['entity'];
        $notes = $subscription['notes'] ?? [];
        $userId = $notes['user_id'] ?? null;

        // Check if existing subscription belongs to a deleted restaurant
        $existingSub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        $targetRestaurantId = $userId ?? ($existingSub ? $existingSub->user_id : null);

        if ($targetRestaurantId) {
            $restaurant = RestaurantMaster::find($targetRestaurantId);
            if ($restaurant && $restaurant->status === 'D') {
                Log::warning("Ignored subscription.charged webhook for deleted restaurant ID: {$targetRestaurantId}");
                return;
            }
        }

        $plan = Plan::where('razorpay_plan_id', $subscription['plan_id'])->first();
        if (!$plan) return;

        // Create or update subscription
        $sub = Subscription::updateOrCreate(
            ['razorpay_subscription_id' => $subscription['id']],
            [
                'user_id' => $targetRestaurantId,
                'plan_id' => $plan->id,
                'razorpay_plan_id' => $subscription['plan_id'],
                'status' => 'active',
                'start_date' => now()->startOfDay(),
                'end_date' => now()->addDays($plan->duration_days - 1),
                'renewal_date' => now()->addDays($plan->duration_days),
                'auto_renew' => 1
            ]
        );

        // Create payment record
        Payment::updateOrCreate(
            ['razorpay_payment_id' => $payment['id']],
            [
                'subscription_id' => $sub->id,
                'user_id' => $sub->user_id,
                'plan_id' => $plan->id,
                'razorpay_order_id' => $subscription['id'],
                'amount' => $payment['amount'] / 100,
                'currency' => $payment['currency'],
                'status' => 'success',
                'description' => 'Recurring payment',
                'razorpay_response' => json_encode($payload)
            ]
        );
    }

    private function handleSubscriptionCancelled($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        
        Subscription::where('razorpay_subscription_id', $subscription['id'])
            ->update([
                'status' => 'cancelled',
                'renewal_date' => null,
                'auto_renew' => 0
            ]);
    }

    private function handleSubscriptionCompleted($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        
        Subscription::where('razorpay_subscription_id', $subscription['id'])
            ->update([
                'status' => 'completed',
                'auto_renew' => 0
            ]);
    }

    private function handleSubscriptionActivated($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        
        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if ($sub && $sub->user_id) {
            $restaurant = RestaurantMaster::find($sub->user_id);
            if ($restaurant && $restaurant->status === 'D') {
                Log::warning("Ignored subscription.activated webhook for deleted restaurant ID: {$sub->user_id}");
                return;
            }
        }

        Subscription::where('razorpay_subscription_id', $subscription['id'])
            ->update(['status' => 'active']);
    }

    private function handleSubscriptionResumed($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        Log::info('Subscription Resumed via webhook: ' . $subscription['id']);

        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if ($sub && $sub->user_id) {
            $restaurant = RestaurantMaster::find($sub->user_id);
            if ($restaurant && $restaurant->status === 'D') {
                Log::warning("Ignored subscription.resumed webhook for deleted restaurant ID: {$sub->user_id}");
                return;
            }
        }

        Subscription::where('razorpay_subscription_id', $subscription['id'])
            ->update([
                'status' => 'active',
                'auto_renew' => 1
            ]);
    }

    private function handleSubscriptionUpdated($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        Log::info('Subscription Updated via webhook: ' . $subscription['id']);

        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if (!$sub) return;

        $updateData = [];
        if (isset($subscription['status'])) {
            $status = $subscription['status'];
            if (in_array($status, ['authenticated', 'active'])) {
                $status = 'active';
            }
            $updateData['status'] = $status;
        }

        if (isset($subscription['charge_at']) && $subscription['charge_at'] > 0) {
            $updateData['renewal_date'] = date('Y-m-d H:i:s', $subscription['charge_at']);
        }

        if (!empty($updateData)) {
            $sub->update($updateData);
        }
    }

    private function handleSubscriptionAuthenticated($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        Log::info('Subscription Authenticated (Mandate/Payment Method Verified): ' . $subscription['id']);

        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if ($sub) {
            $sub->update([
                'status' => 'active',
                'auto_renew' => 1
            ]);
        }
    }

    private function handleSubscriptionPending($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        Log::warning('Subscription Payment Pending / Failed retry: ' . $subscription['id']);

        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if ($sub) {
            $sub->update([
                'status' => 'pending'
            ]);
        }
    }

    private function handleSubscriptionHalted($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        Log::error('Subscription Halted (Retries exhausted / Bank issue): ' . $subscription['id']);

        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if ($sub) {
            $sub->update([
                'status' => 'halted'
            ]);
        }
    }

    private function handleSubscriptionPaused($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        Log::info('Subscription Paused: ' . $subscription['id']);

        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if ($sub) {
            $sub->update([
                'status' => 'paused'
            ]);
        }
    }
}
