<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PlanTermsAndConditionsTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $ownerUser;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::create([
            'name' => 'Terms Test Restaurant ' . uniqid(),
            'status' => 'A',
            'phone' => '9999999999',
            'email' => 'terms_' . uniqid() . '@restaurant.com'
        ]);

        $this->ownerUser = User::create([
            'name' => 'Terms Owner',
            'email' => 'terms_owner_' . uniqid() . '@example.com',
            'phone' => '9876543210',
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $this->restaurant->id,
            'status' => 'A'
        ]);

        $this->restaurant->owner_id = $this->ownerUser->id;
        $this->restaurant->save();

        $this->plan = Plan::create([
            'name' => 'Gold Starter Plan ' . uniqid(),
            'price' => 1499.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);
    }

    public function test_select_plans_page_displays_terms_checkbox_and_links_and_disabled_button()
    {
        $this->actingAs($this->ownerUser);

        $response = $this->get(route('select.plan.page'));
        $response->assertStatus(200);

        // Check for terms checkbox and links
        $response->assertSee('terms-plan-checkbox');
        $response->assertSee(route('terms.conditions'));
        $response->assertSee(route('privacy.policy'));
        $response->assertSee('Terms &amp; Conditions', false);
        $response->assertSee('Privacy Policy');

        // Check that purchase button starts in disabled state
        $response->assertSee('btn_plan_' . $this->plan->id);
        $response->assertSee('class="btn-select disabled"', false);
        $response->assertSee('aria-disabled="true"', false);
    }

    public function test_restaurant_plans_page_displays_terms_checkbox_and_disabled_button()
    {
        // Give active subscription to allow accessing restaurant/plans route through middleware
        Subscription::create([
            'user_id' => $this->restaurant->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(29),
            'amount' => 1499.00,
            'currency' => 'INR',
        ]);

        // Create a higher tier upgrade plan so it shows in the list
        $upgradePlan = Plan::create([
            'name' => 'Platinum Upgrade Plan ' . uniqid(),
            'price' => 2999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        $this->actingAs($this->ownerUser);

        $response = $this->get(route('restaurant.plans'));
        $response->assertStatus(200);

        // Check for terms checkbox and links
        $response->assertSee('terms_rest_plan_' . $upgradePlan->id);
        $response->assertSee(route('terms.conditions'));
        $response->assertSee(route('privacy.policy'));

        // Check that upgrade button starts in disabled state
        $response->assertSee('btn_rest_plan_' . $upgradePlan->id);
        $response->assertSee('aria-disabled="true"', false);
    }

    public function test_checkout_subscription_create_page_displays_terms_checkbox_and_disabled_button()
    {
        $this->actingAs($this->ownerUser);

        $response = $this->get(route('admin.subscriptions.create', $this->plan->id));
        $response->assertStatus(200);

        // Check terms checkbox on checkout
        $response->assertSee('checkout_terms_checkbox');
        $response->assertSee(route('terms.conditions'));
        $response->assertSee(route('privacy.policy'));
        $response->assertSee('I have read and agree to');

        // Check submit button is disabled initially
        $response->assertSee('id="btn_submit_checkout"', false);
        $response->assertSee('disabled', false);
    }
}
