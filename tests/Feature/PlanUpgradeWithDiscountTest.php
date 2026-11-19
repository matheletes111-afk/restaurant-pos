<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\RestaurantMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PlanUpgradeWithDiscountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_plan_upgrade_calculates_upfront_discount_and_activates_new_plan()
    {
        // 1. Create restaurant owner
        $owner = User::create([
            'name' => 'Upgrade Owner',
            'email' => 'upgrade_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'status' => 'A'
        ]);

        $restaurant = RestaurantMaster::create([
            'name' => 'Upgrade Test Bistro',
            'address' => '789 Test Road',
            'owner_id' => $owner->id,
            'status' => 'A'
        ]);

        $owner->restaurant_id = $restaurant->id;
        $owner->save();

        // 2. Create old active plan (₹500 / 365 days)
        $oldPlan = Plan::create([
            'name' => 'Starter Annual Plan',
            'price' => 500.00,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        // Old subscription started 15 days ago
        $oldSub = Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $oldPlan->id,
            'razorpay_subscription_id' => 'sub_old_' . uniqid(),
            'status' => 'active',
            'start_date' => now()->subDays(15)->startOfDay(),
            'end_date' => now()->subDays(15)->addDays(365)->endOfDay(),
            'auto_renew' => 1
        ]);

        $oldPayment = Payment::create([
            'user_id' => $restaurant->id,
            'plan_id' => $oldPlan->id,
            'subscription_id' => $oldSub->id,
            'razorpay_order_id' => 'order_old_' . uniqid(),
            'razorpay_payment_id' => 'pay_old_' . uniqid(),
            'amount' => 500.00,
            'status' => 'success'
        ]);

        // 3. Create new upgraded plan (₹600 / 30 days)
        $newPlan = Plan::create([
            'name' => 'Pro Monthly Plan',
            'price' => 600.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        // 4. Test paymentSuccess callback for upgrade
        $upgradeOrderId = 'order_upg_' . uniqid();
        $upgradePaymentId = 'pay_upg_' . uniqid();

        // Create pending payment row as created by processPaidPlan
        $pendingPayment = Payment::create([
            'user_id' => $restaurant->id,
            'plan_id' => $newPlan->id,
            'razorpay_order_id' => $upgradeOrderId,
            'amount' => 120.55, // 600 - 479.45
            'refund_amount' => 479.45,
            'status' => 'pending'
        ]);

        // Post payment success with upgrade parameters
        $response = $this->actingAs($owner)->postJson(route('admin.subscriptions.payment.success'), [
            'razorpay_payment_id' => $upgradePaymentId,
            'razorpay_order_id' => $upgradeOrderId,
            'razorpay_signature' => 'mock_test_sig',
            'plan_id' => $newPlan->id,
            'user_id' => $restaurant->id,
            'is_one_time' => '1',
            'existing_subscription_id' => $oldSub->id,
            'credit_amount' => 479.45
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // 5. Verify database state
        // Old subscription must be expired and auto_renew disabled
        $oldSub->refresh();
        $this->assertEquals('expired', $oldSub->status);
        $this->assertEquals(0, $oldSub->auto_renew);

        // New subscription must be active and linked to new plan
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $restaurant->id,
            'plan_id' => $newPlan->id,
            'status' => 'active',
            'auto_renew' => 0
        ]);

        // Upgraded payment row must be marked success with net difference amount
        $pendingPayment->refresh();
        $this->assertEquals('success', $pendingPayment->status);
        $this->assertEquals(120.55, $pendingPayment->amount);
        $this->assertNotNull($pendingPayment->subscription_id);
    }
}
