<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Plan;
use Razorpay\Api\Api;

class WebhookController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
    }

    public function handle(Request $request)
    {
        try {
            $this->razorpay->utility->verifyWebhookSignature(
                $request->getContent(),
                $request->header('X-Razorpay-Signature'),
                env('RAZORPAY_WEBHOOK_SECRET')
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

        $plan = Plan::where('razorpay_plan_id', $subscription['plan_id'])->first();
        if (!$plan) return;

        // Create or update subscription
        $sub = Subscription::updateOrCreate(
            ['razorpay_subscription_id' => $subscription['id']],
            [
                'user_id' => $notes['user_id'] ?? null,
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
            ->update(['status' => 'expired']);
    }

    private function handleSubscriptionActivated($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        
        Subscription::where('razorpay_subscription_id', $subscription['id'])
            ->update(['status' => 'active']);
    }
}