<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\RestaurantMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubscriptionDetailsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_fetch_subscription_details_json()
    {
        // 1. Create dummy restaurant owner & restaurant
        $owner = User::create([
            'name' => 'Sub Test User',
            'email' => 'subtest_' . time() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => 1
        ]);

        $restaurant = RestaurantMaster::find(1);
        if (!$restaurant) {
            $restaurant = RestaurantMaster::create([
                'id' => 1,
                'name' => 'Test POS Restaurant',
                'owner_id' => $owner->id,
                'status' => 'A'
            ]);
        }

        // 2. Create dummy plan
        $plan = Plan::create([
            'name' => 'Pro Test Plan',
            'label_name' => 'Popular',
            'price' => 599.00,
            'cross_price' => '999.00',
            'billing_cycle' => 'monthly',
            'duration_days' => 30,
            'description' => 'Great plan with all features.',
            'category_number' => 10,
            'total_number_of_dishes' => 100,
            'total_number_of_table' => 20,
            'inventory_checkbox' => 'Y',
            'is_delete' => 'N',
            'plan_status' => 'A'
        ]);

        // 3. Create dummy subscription
        $subscription = Subscription::create([
            'user_id' => $owner->restaurant_id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'renewal_date' => now()->addDays(30),
            'auto_renew' => 1,
            'razorpay_subscription_id' => 'sub_test123456',
            'refund_amount' => 0.00
        ]);

        // 4. Create dummy payment
        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'user_id' => $owner->restaurant_id,
            'plan_id' => $plan->id,
            'razorpay_payment_id' => 'pay_test123456',
            'amount' => 599.00,
            'currency' => 'INR',
            'status' => 'success',
            'payment_method' => 'upi',
            'description' => 'Payment for Pro Test Plan'
        ]);

        // 5. Test AJAX details route
        $response = $this->actingAs($owner)->getJson(route('admin.subscriptions.show', $subscription->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $subscription->id,
                'status' => 'active',
                'plan' => [
                    'name' => 'Pro Test Plan',
                    'label_name' => 'Popular',
                    'inventory_enabled' => true,
                    'category_number' => 10,
                    'dish_number' => 100,
                    'table_number' => 20
                ],
                'payment' => [
                    'status' => 'Success',
                    'payment_method' => 'UPI',
                    'razorpay_payment_id' => 'pay_test123456'
                ]
            ]
        ]);
    }
}
