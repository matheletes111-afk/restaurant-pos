<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BarLandingPlanTest extends TestCase
{
    use DatabaseTransactions;

    public function test_bar_landing_only_shows_default_bar_plan_and_cross_price_on_top()
    {
        // 1. Create a Default Bar Plan with cross price
        $defaultBarPlan = Plan::create([
            'name' => 'BAR PRO DEFAULT ' . uniqid(),
            'price' => 2999.00,
            'cross_price' => 4999.00,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'bar_management_checkbox' => 'Y',
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        // 2. Create a Non-Default Bar Plan (MUST NOT BE SHOWN)
        $nonDefaultBarPlan = Plan::create([
            'name' => 'BAR NON-DEFAULT CUSTOM ' . uniqid(),
            'price' => 9999.00,
            'cross_price' => 12999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'bar_management_checkbox' => 'Y',
            'is_default_plan' => 'N',
            'is_default_free' => 'N',
            'is_default_paid' => 'N',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        // 3. Create a Restaurant-Only Default Plan (bar_management_checkbox = 'N') (MUST NOT BE SHOWN)
        $restaurantOnlyPlan = Plan::create([
            'name' => 'RESTAURANT ONLY ' . uniqid(),
            'price' => 1999.00,
            'cross_price' => 2999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'bar_management_checkbox' => 'N',
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        $response = $this->get('/bar');
        $response->assertStatus(200);

        // Verify Default Bar Plan is shown
        $response->assertSee($defaultBarPlan->name);
        $response->assertSee('₹2,999.00');
        $response->assertSee('₹4,999.00');
        $response->assertDontSee('cross-discount-pill');
        $response->assertDontSee('% OFF');

        // Verify Non-Default Bar Plan is NOT shown
        $response->assertDontSee($nonDefaultBarPlan->name);

        // Verify Restaurant-Only Plan is NOT shown
        $response->assertDontSee($restaurantOnlyPlan->name);

        // Verify Cross price is positioned before active price in the HTML (on top of main price)
        $content = $response->getContent();
        $crossPos = strpos($content, 'cross-price-box');
        $mainPos = strpos($content, 'main-price-box');

        $this->assertNotFalse($crossPos, 'cross-price-box must be present');
        $this->assertNotFalse($mainPos, 'main-price-box must be present');
        $this->assertLessThan($mainPos, $crossPos, 'cross-price-box must appear before main-price-box');

        // Verify Responsive CSS declarations are rendered
        $response->assertSee('@media (max-width: 768px)', false);
        $response->assertSee('@media (max-width: 480px)', false);
    }
}
