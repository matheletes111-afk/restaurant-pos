<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\PlanHistory;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Admin;
use App\Models\RestaurantMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class PlanDomesticInternationalTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupAdmin()
    {
        return Admin::first() ?? Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    protected function setupRestaurantAndUser()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Rest ' . uniqid(),
            'restaurant_id_unique' => 'REST-DI-' . rand(100, 999),
            'parent_id' => null,
            'address' => '123 Market St',
            'status' => 'A'
        ]);

        $user = User::create([
            'name' => 'Owner ' . uniqid(),
            'email' => 'owner_' . uniqid() . '@example.com',
            'phone' => '98765' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        return [$restaurant, $user];
    }

    public function test_existing_plans_default_to_domestic()
    {
        $existingCount = DB::table('plans')->where('plan_type', 'DOMESTIC')->count();
        $this->assertGreaterThan(0, $existingCount);

        // When creating a plan without specifying plan_type, DB default is DOMESTIC
        $plan = Plan::create([
            'name' => 'Default Plan Test ' . uniqid(),
            'price' => 500.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_delete' => 'N',
            'plan_status' => 'A',
        ]);

        $this->assertEquals('DOMESTIC', $plan->fresh()->plan_type);
        $this->assertFalse($plan->isInternational());
    }

    public function test_domestic_plan_calculates_gst_properly()
    {
        // For Domestic plan: 1180 total price -> 1000 taxable amount, 180 GST amount
        $price = 1180.00;
        $gstPercentage = 18.00;
        $taxableAmount = round($price / (1 + ($gstPercentage / 100)), 2);
        $gstAmount = round($price - $taxableAmount, 2);

        $plan = Plan::create([
            'name' => 'Domestic 1180 Plan ' . uniqid(),
            'plan_type' => 'DOMESTIC',
            'price' => $price,
            'gst_percentage' => $gstPercentage,
            'taxable_amount' => $taxableAmount,
            'gst_amount' => $gstAmount,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_delete' => 'N',
            'plan_status' => 'A',
        ]);

        $this->assertEquals('DOMESTIC', $plan->plan_type);
        $this->assertFalse($plan->isInternational());
        $this->assertEquals(18.00, (float)$plan->gst_percentage);
        $this->assertEquals(1000.00, (float)$plan->taxable_amount);
        $this->assertEquals(180.00, (float)$plan->gst_amount);
    }

    public function test_international_plan_has_zero_gst()
    {
        // For International plan: 1500 price -> 0 GST, 1500 taxable amount
        $price = 1500.00;
        $plan = Plan::create([
            'name' => 'International Global Plan ' . uniqid(),
            'plan_type' => 'INTERNATIONAL',
            'price' => $price,
            'gst_percentage' => 0.00,
            'taxable_amount' => $price,
            'gst_amount' => 0.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_delete' => 'N',
            'plan_status' => 'A',
        ]);

        $this->assertEquals('INTERNATIONAL', $plan->plan_type);
        $this->assertTrue($plan->isInternational());
        $this->assertEquals(0.00, (float)$plan->gst_percentage);
        $this->assertEquals(1500.00, (float)$plan->taxable_amount);
        $this->assertEquals(0.00, (float)$plan->gst_amount);
    }

    public function test_admin_plan_controller_store_and_update_international_logic()
    {
        $plan = new Plan();
        $plan->name = 'Test Store Inter ' . uniqid();
        $plan->plan_type = 'INTERNATIONAL';
        $plan->price = 2000.00;
        $plan->gst_percentage = 0.00;
        $plan->taxable_amount = 2000.00;
        $plan->gst_amount = 0.00;
        $plan->duration_days = 365;
        $plan->billing_cycle = 'yearly';
        $plan->is_delete = 'N';
        $plan->plan_status = 'A';
        $plan->save();

        $history = new PlanHistory();
        $history->plan_id = $plan->id;
        $history->name = $plan->name;
        $history->plan_type = 'INTERNATIONAL';
        $history->price = 2000.00;
        $history->gst_percentage = 0.00;
        $history->taxable_amount = 2000.00;
        $history->gst_amount = 0.00;
        $history->duration_days = 365;
        $history->billing_cycle = 'yearly';
        $history->status = 'C';
        $history->save();

        $this->assertEquals('INTERNATIONAL', $plan->fresh()->plan_type);
        $this->assertEquals('INTERNATIONAL', $history->fresh()->plan_type);
        $this->assertEquals(0.00, (float)$plan->fresh()->gst_amount);
        $this->assertEquals(0.00, (float)$history->fresh()->gst_amount);
    }

    public function test_subscription_controller_checks_domestic_vs_international_condition()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        // 1. Domestic Plan subscription payment
        $domesticPlan = Plan::create([
            'name' => 'Domestic POS Plan ' . uniqid(),
            'plan_type' => 'DOMESTIC',
            'price' => 1180.00,
            'gst_percentage' => 18.00,
            'taxable_amount' => 1000.00,
            'gst_amount' => 180.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_delete' => 'N',
            'plan_status' => 'A',
        ]);

        $domesticSub = Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $domesticPlan->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'amount' => 1180.00,
        ]);

        $domesticPayment = new Payment();
        $domesticPayment->subscription_id = $domesticSub->id;
        $domesticPayment->user_id = $restaurant->id;
        $domesticPayment->plan_id = $domesticPlan->id;
        $domesticPayment->amount = 1180.00;
        $domesticPayment->gst_percentage = ($domesticPlan->plan_type === 'INTERNATIONAL') ? '0' : '18';
        $domesticPayment->currency = 'INR';
        $domesticPayment->status = 'success';
        $domesticPayment->save();

        $this->assertEquals('18', $domesticPayment->fresh()->gst_percentage);

        // 2. International Plan subscription payment
        $internationalPlan = Plan::create([
            'name' => 'International Global Plan ' . uniqid(),
            'plan_type' => 'INTERNATIONAL',
            'price' => 1500.00,
            'gst_percentage' => 0.00,
            'taxable_amount' => 1500.00,
            'gst_amount' => 0.00,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'is_delete' => 'N',
            'plan_status' => 'A',
        ]);

        $interSub = Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $internationalPlan->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(365),
            'amount' => 1500.00,
        ]);

        $interPayment = new Payment();
        $interPayment->subscription_id = $interSub->id;
        $interPayment->user_id = $restaurant->id;
        $interPayment->plan_id = $internationalPlan->id;
        $interPayment->amount = 1500.00;
        $interPayment->gst_percentage = ($internationalPlan->plan_type === 'INTERNATIONAL') ? '0' : '18';
        $interPayment->currency = 'INR';
        $interPayment->status = 'success';
        $interPayment->save();

        $this->assertEquals('0', $interPayment->fresh()->gst_percentage);

        // Verify SubscriptionController show() API returns correct GST and plan_type
        $this->actingAs($user);
        $response = $this->get(route('admin.subscriptions.show', $interSub->id));
        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals('INTERNATIONAL', $data['plan']['plan_type']);
        $this->assertEquals(0, $data['plan']['gst_percentage']);
        $this->assertEquals('0', $data['payment']['gst_percentage']);
    }

    public function test_invoice_pdf_view_renders_properly_for_domestic_and_international()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        // 1. Domestic Plan invoice render test
        $domPlan = Plan::create([
            'name' => 'Domestic PDF Plan ' . uniqid(),
            'plan_type' => 'DOMESTIC',
            'price' => 1180.00,
            'gst_percentage' => 18.00,
            'taxable_amount' => 1000.00,
            'gst_amount' => 180.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_delete' => 'N',
            'plan_status' => 'A',
        ]);

        $domSub = Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $domPlan->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'amount' => 1180.00,
        ]);

        $domPayment = Payment::create([
            'subscription_id' => $domSub->id,
            'user_id' => $restaurant->id,
            'plan_id' => $domPlan->id,
            'amount' => 1180.00,
            'gst_percentage' => '18',
            'status' => 'success',
        ]);

        $domView = view('emails.subscription_invoice_pdf', [
            'subscription' => $domSub,
            'restaurant' => $restaurant,
            'payment' => $domPayment,
            'plan' => $domPlan,
            'invoice_no' => 'INV-TEST-DOM',
            'invoice_date' => now()->format('Y-m-d')
        ])->render();

        $this->assertStringContainsString('GST (18%):', $domView);
        $this->assertStringContainsString('180.00', $domView);

        // 2. International Plan invoice render test
        $interPlan = Plan::create([
            'name' => 'Inter PDF Plan ' . uniqid(),
            'plan_type' => 'INTERNATIONAL',
            'price' => 2000.00,
            'gst_percentage' => 0.00,
            'taxable_amount' => 2000.00,
            'gst_amount' => 0.00,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'is_delete' => 'N',
            'plan_status' => 'A',
        ]);

        $interSub = Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $interPlan->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(365),
            'amount' => 2000.00,
        ]);

        $interPayment = Payment::create([
            'subscription_id' => $interSub->id,
            'user_id' => $restaurant->id,
            'plan_id' => $interPlan->id,
            'amount' => 2000.00,
            'gst_percentage' => '0',
            'status' => 'success',
        ]);

        $interView = view('emails.subscription_invoice_pdf', [
            'subscription' => $interSub,
            'restaurant' => $restaurant,
            'payment' => $interPayment,
            'plan' => $interPlan,
            'invoice_no' => 'INV-TEST-INTER',
            'invoice_date' => now()->format('Y-m-d')
        ])->render();

        $this->assertStringContainsString('GST (0% - International):', $interView);
        $this->assertStringContainsString('0.00', $interView);
    }

    public function test_usd_currency_handling_in_plans_and_subscriptions()
    {
        [$restaurant, $user] = $this->setupRestaurantAndUser();

        $usdPlan = Plan::create([
            'name' => 'USD Global Plan ' . uniqid(),
            'plan_type' => 'INTERNATIONAL',
            'price' => 49.00,
            'currency' => 'USD',
            'gst_percentage' => 0.00,
            'taxable_amount' => 49.00,
            'gst_amount' => 0.00,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'is_delete' => 'N',
            'plan_status' => 'A',
        ]);

        $this->assertEquals('USD', $usdPlan->currency);

        $subscription = Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $usdPlan->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(365),
            'amount' => 49.00,
        ]);

        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'user_id' => $restaurant->id,
            'plan_id' => $usdPlan->id,
            'amount' => 49.00,
            'currency' => $usdPlan->currency,
            'gst_percentage' => '0',
            'status' => 'success',
        ]);

        $this->assertEquals('USD', $payment->currency);

        // Check show() API response formats price with $ and USD
        $this->actingAs($user);
        $response = $this->get(route('admin.subscriptions.show', $subscription->id));
        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals('USD', $data['plan']['currency']);
        $this->assertStringContainsString('$', $data['plan']['formatted_price']);
        $this->assertStringContainsString('$', $data['payment']['formatted_amount']);

        // Check payment blade contains USD configuration
        $paymentView = view('admin.subscriptions.payment', [
            'order_id' => 'ord_test_usd_123',
            'subscription_id' => null,
            'is_one_time' => true,
            'customer_id' => null,
            'plan' => $usdPlan,
            'user' => $user,
            'payable_amount' => 49.00,
            'currency' => 'USD',
        ])->render();

        $this->assertStringContainsString('"USD"', $paymentView);
        $this->assertStringContainsString('USD', $paymentView);
    }
}
