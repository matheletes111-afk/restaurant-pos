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
use Throwable;

class WebhookController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $keyId = config('services.razorpay.key_id') ?: env('RAZORPAY_KEY_ID');
        $keySecret = config('services.razorpay.key_secret') ?: env('RAZORPAY_KEY_SECRET');
        if ($keyId && $keySecret) {
            $this->razorpay = new Api($keyId, $keySecret);
        }
    }

    public function handle(Request $request)
    {
        // Health / verification check (GET requests)
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'active',
                'message' => 'Razorpay Webhook endpoint is active and listening.'
            ], 200);
        }

        $webhookSecret = config('services.razorpay.webhook_secret') ?: env('RAZORPAY_WEBHOOK_SECRET');
        $signature = $request->header('X-Razorpay-Signature');

        // Verify signature if webhook secret is configured
        if (!empty($webhookSecret) && !empty($signature)) {
            try {
                if ($this->razorpay) {
                    $this->razorpay->utility->verifyWebhookSignature(
                        $request->getContent(),
                        $signature,
                        $webhookSecret
                    );
                } else {
                    // Fallback HMAC SHA256 check
                    $expectedSignature = hash_hmac('sha256', $request->getContent(), $webhookSecret);
                    if (!hash_equals($expectedSignature, $signature)) {
                        Log::warning('Razorpay Webhook signature mismatch.');
                        return response()->json(['error' => 'Invalid webhook signature'], 400);
                    }
                }
            } catch (Throwable $e) {
                Log::warning('Razorpay Webhook signature verification failed: ' . $e->getMessage());
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        try {
            $payload = $request->all();
            $event = $payload['event'] ?? null;

            if (!$event) {
                Log::warning('Razorpay Webhook received without event name.', ['body' => $request->getContent()]);
                return response()->json(['status' => 'ignored', 'message' => 'No event specified'], 200);
            }

            Log::info('Razorpay Webhook Received: ' . $event, ['event' => $event]);

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

                case 'payment.captured':
                case 'payment.authorized':
                    $this->handlePaymentCaptured($payload);
                    break;

                case 'payment.failed':
                    $this->handlePaymentFailed($payload);
                    break;

                case 'refund.processed':
                case 'refund.created':
                    $this->handleRefundProcessed($payload);
                    break;

                default:
                    Log::info('Razorpay Webhook unhandled event: ' . $event);
                    break;
            }

            // Always return HTTP 200 OK so Razorpay records successful delivery and does not auto-disable
            return response()->json(['status' => 'success', 'event' => $event], 200);

        } catch (Throwable $e) {
            Log::error('Razorpay Webhook Processing Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Return 200 OK even on handled internal exceptions to prevent Razorpay from auto-disabling the endpoint
            return response()->json(['status' => 'error_handled', 'message' => 'Processed with errors logged'], 200);
        }
    }

    private function handleSubscriptionCharged($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        $payment = $payload['payload']['payment']['entity'] ?? null;

        if (!$subscription || !$payment) {
            Log::warning('Subscription or Payment entity missing in subscription.charged payload.');
            return;
        }

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
        $durationDays = $plan ? $plan->duration_days : 30;

        // Create or update subscription
        $sub = Subscription::updateOrCreate(
            ['razorpay_subscription_id' => $subscription['id']],
            [
                'user_id' => $targetRestaurantId,
                'plan_id' => $plan ? $plan->id : ($existingSub ? $existingSub->plan_id : null),
                'razorpay_plan_id' => $subscription['plan_id'],
                'status' => 'active',
                'start_date' => now()->startOfDay(),
                'end_date' => now()->addDays($durationDays - 1),
                'renewal_date' => now()->addDays($durationDays),
                'auto_renew' => 1
            ]
        );

        // Create payment record
        Payment::updateOrCreate(
            ['razorpay_payment_id' => $payment['id']],
            [
                'subscription_id' => $sub->id,
                'user_id' => $sub->user_id,
                'plan_id' => $plan ? $plan->id : null,
                'razorpay_order_id' => $subscription['id'],
                'amount' => isset($payment['amount']) ? ($payment['amount'] / 100) : 0,
                'currency' => $payment['currency'] ?? 'INR',
                'status' => 'success',
                'description' => 'Recurring payment via Webhook',
                'payment_method' => $payment['method'] ?? 'razorpay',
                'razorpay_response' => json_encode($payload)
            ]
        );
    }

    private function handleSubscriptionCancelled($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        if (!$subscription) return;

        Subscription::where('razorpay_subscription_id', $subscription['id'])
            ->update([
                'status' => 'cancelled',
                'renewal_date' => null,
                'auto_renew' => 0
            ]);
    }

    private function handleSubscriptionCompleted($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        if (!$subscription) return;

        Subscription::where('razorpay_subscription_id', $subscription['id'])
            ->update([
                'status' => 'completed',
                'auto_renew' => 0
            ]);
    }

    private function handleSubscriptionActivated($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        if (!$subscription) return;

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
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        if (!$subscription) return;

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
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        if (!$subscription) return;

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
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        if (!$subscription) return;

        Log::info('Subscription Authenticated: ' . $subscription['id']);

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
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        if (!$subscription) return;

        Log::warning('Subscription Payment Pending: ' . $subscription['id']);

        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if ($sub) {
            $sub->update([
                'status' => 'pending'
            ]);
        }
    }

    private function handleSubscriptionHalted($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        if (!$subscription) return;

        Log::error('Subscription Halted: ' . $subscription['id']);

        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if ($sub) {
            $sub->update([
                'status' => 'halted'
            ]);
        }
    }

    private function handleSubscriptionPaused($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'] ?? null;
        if (!$subscription) return;

        Log::info('Subscription Paused: ' . $subscription['id']);

        $sub = Subscription::where('razorpay_subscription_id', $subscription['id'])->first();
        if ($sub) {
            $sub->update([
                'status' => 'paused'
            ]);
        }
    }

    private function handlePaymentCaptured($payload)
    {
        $payment = $payload['payload']['payment']['entity'] ?? null;
        if (!$payment) return;

        Log::info('Razorpay Payment Captured/Authorized: ' . ($payment['id'] ?? 'unknown'));

        // Check if payment is already recorded
        if (isset($payment['id'])) {
            $existing = Payment::where('razorpay_payment_id', $payment['id'])->first();
            if ($existing) {
                $existing->update([
                    'status' => 'success',
                    'payment_method' => $payment['method'] ?? $existing->payment_method,
                    'razorpay_response' => json_encode($payload)
                ]);
            }
        }
    }

    private function handlePaymentFailed($payload)
    {
        $payment = $payload['payload']['payment']['entity'] ?? null;
        if (!$payment) return;

        Log::warning('Razorpay Payment Failed: ' . ($payment['id'] ?? 'unknown'), [
            'error_code' => $payment['error_code'] ?? null,
            'error_description' => $payment['error_description'] ?? null
        ]);

        if (isset($payment['id'])) {
            $existing = Payment::where('razorpay_payment_id', $payment['id'])->first();
            if ($existing) {
                $existing->update([
                    'status' => 'failed',
                    'description' => $payment['error_description'] ?? 'Payment failed',
                    'razorpay_response' => json_encode($payload)
                ]);
            }
        }
    }

    private function handleRefundProcessed($payload)
    {
        $refund = $payload['payload']['refund']['entity'] ?? null;
        $payment = $payload['payload']['payment']['entity'] ?? null;

        if (!$refund) return;

        Log::info('Razorpay Refund Processed: ' . ($refund['id'] ?? 'unknown'));

        $paymentId = $refund['payment_id'] ?? ($payment['id'] ?? null);
        if ($paymentId) {
            $paymentRecord = Payment::where('razorpay_payment_id', $paymentId)->first();
            if ($paymentRecord) {
                $refundAmount = isset($refund['amount']) ? ($refund['amount'] / 100) : 0;
                $paymentRecord->update([
                    'refund_amount' => $refundAmount,
                    'status' => 'refunded',
                    'description' => 'Refund processed: ' . ($refund['id'] ?? '')
                ]);
            }
        }
    }
}
