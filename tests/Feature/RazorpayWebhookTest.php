<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;

class RazorpayWebhookTest extends TestCase
{
    use DatabaseTransactions;
    public function test_webhook_endpoints_are_publicly_accessible_without_auth_or_csrf()
    {
        $response1 = $this->get('/razorpay/webhook');
        $response1->assertStatus(200);

        $response2 = $this->get('/admin/razorpay/webhook');
        $response2->assertStatus(200);

        $response3 = $this->get('/api/razorpay/webhook');
        $response3->assertStatus(200);
    }

    public function test_webhook_handles_unhandled_event_gracefully()
    {
        $payload = [
            'event' => 'payment.authorized',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_test123456',
                        'amount' => 50000,
                        'currency' => 'INR',
                        'status' => 'authorized'
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/razorpay/webhook', $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_webhook_handles_subscription_charged()
    {
        $user = User::create([
            'name' => 'Webhook User',
            'email' => 'wh_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'status' => 'A'
        ]);

        $plan = Plan::create([
            'name' => 'Pro Webhook Plan',
            'price' => 500,
            'duration_days' => 30,
            'razorpay_plan_id' => 'plan_webhook_test_1',
            'plan_status' => 'A'
        ]);

        $payload = [
            'event' => 'subscription.charged',
            'payload' => [
                'subscription' => [
                    'entity' => [
                        'id' => 'sub_webhook_test_1',
                        'plan_id' => 'plan_webhook_test_1',
                        'notes' => [
                            'user_id' => $user->id
                        ]
                    ]
                ],
                'payment' => [
                    'entity' => [
                        'id' => 'pay_webhook_test_1',
                        'amount' => 50000,
                        'currency' => 'INR',
                        'method' => 'card'
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/razorpay/webhook', $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('subscriptions', [
            'razorpay_subscription_id' => 'sub_webhook_test_1',
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('payments', [
            'razorpay_payment_id' => 'pay_webhook_test_1',
            'amount' => 500.00,
            'status' => 'success'
        ]);
    }
}
