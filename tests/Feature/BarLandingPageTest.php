<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BarLandingPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_bar_landing_page_renders_cleanly_when_no_bar_plans_exist()
    {
        // Ensure no active bar plans exist for this test
        Plan::where('bar_management_checkbox', 'Y')->update(['is_delete' => 'Y']);

        $response = $this->get('/bar');
        $response->assertStatus(200);

        // Core bar facilities and hero content render
        $response->assertSeeText('Built for Bars, Pubs, Lounges', false);
        $response->assertSeeText('Zero Liquor Shrinkage', false);

        // Pricing section and switcher tabs are not displayed when no plans exist
        $response->assertDontSee('id="pricing"', false);
        $response->assertDontSeeText('Monthly Billing', false);
        $response->assertDontSeeText('Annual Billing', false);
    }

    public function test_bar_landing_page_shows_only_database_bar_plans_and_dynamic_timeframe()
    {
        // 1. Create a single Yearly Bar Plan
        $yearlyPlan = Plan::create([
            'name' => 'BAR TEST ANNUAL EXCLUSIVE',
            'slug' => 'bar-test-annual-exclusive',
            'price' => 19999,
            'cross_price' => 29999,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'bar_management_checkbox' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
            'category_number' => 0,
            'total_number_of_dishes' => 0,
            'total_number_of_table' => 0,
        ]);

        $response = $this->get('/bar');
        $response->assertStatus(200);

        // Plan is displayed
        $response->assertSeeText('BAR TEST ANNUAL EXCLUSIVE', false);
        $response->assertSeeText('Unlimited Bar Categories', false);
        $response->assertSeeText('Unlimited Bar Brands', false);
        $response->assertSeeText('Unlimited Bar Items & Variations', false);
        $response->assertSeeText('Installing & Training (Online)', false);
        $response->assertSeeText('Support 24 x 7 (Online)', false);

        // Since only yearly exists in DB, Monthly Billing tab must NOT be shown
        $response->assertDontSeeText('Monthly Billing', false);

        // 2. Now create a Monthly Bar Plan
        $monthlyPlan = Plan::create([
            'name' => 'BAR TEST MONTHLY EXCLUSIVE',
            'slug' => 'bar-test-monthly-exclusive',
            'price' => 1999,
            'cross_price' => 2999,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'bar_management_checkbox' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
            'category_number' => 0,
            'total_number_of_dishes' => 0,
            'total_number_of_table' => 0,
        ]);

        $response2 = $this->get('/bar');
        $response2->assertStatus(200);

        // Now both plans and both switcher tabs are visible
        $response2->assertSeeText('BAR TEST ANNUAL EXCLUSIVE', false);
        $response2->assertSeeText('BAR TEST MONTHLY EXCLUSIVE', false);
        $response2->assertSeeText('Annual Billing', false);
        $response2->assertSeeText('Monthly Billing', false);
    }

    public function test_restaurant_home_page_excludes_bar_plans_and_has_support_training()
    {
        // Create a bar plan
        $barPlan = Plan::create([
            'name' => 'BAR DEDICATED PLAN',
            'slug' => 'bar-dedicated-plan',
            'price' => 5000,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'bar_management_checkbox' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify Training and Support in restaurant plans
        $response->assertSeeText('Installing & Training (Online)', false);
        $response->assertSeeText('Support 24 x 7 (Online)', false);

        // Verify Bar-enabled plans are EXCLUDED from the restaurant landing page
        $response->assertDontSeeText('BAR DEDICATED PLAN', false);
    }

    public function test_bar_facilities_render_root_images()
    {
        $response = $this->get('/bar');
        $response->assertStatus(200);

        // Verify the 3 root images are rendered in SMART BAR FACILITIES
        $response->assertSee('01.png', false);
        $response->assertSee('02.png', false);
        $response->assertSee('03.png', false);

        // Verify root image routes resolve properly
        $this->get('/01.png')->assertStatus(200);
        $this->get('/02.png')->assertStatus(200);
        $this->get('/03.png')->assertStatus(200);
    }

    public function test_bar_welcome_navbar_and_footer_match_welcome_page()
    {
        $response = $this->get('/bar');
        $response->assertStatus(200);

        // Navbar elements from welcome.blade.php
        $response->assertSee('class="navbar"', false);
        $response->assertSee('<a href="#features">Features</a>', false);
        $response->assertSee('Bar POS', false);
        $response->assertSee('<a href="#pricing">Pricing</a>', false);
        $response->assertSee('<a href="#how-it-works">How It Works</a>', false);
        $response->assertSee('<a href="#platform-benefits">Benefits</a>', false);
        $response->assertSee('<a href="#faq">FAQ</a>', false);
        $response->assertSee(route('blog.index'), false);
        $response->assertSee('Book A Demo', false);

        // Footer elements from welcome.blade.php
        $response->assertSee('class="footer"', false);
        $response->assertSee('+91 7001769472', false);
        $response->assertSee('info@billnbite.com', false);
        $response->assertSee('Siliguri, India', false);
        $response->assertSee(route('login'), false);
        $response->assertSee('Restaurant Login', false);
        $response->assertSee('© 2026 Bill & Bite. All rights reserved.', false);
    }
}
