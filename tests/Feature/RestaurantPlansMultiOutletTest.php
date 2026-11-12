<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\RestaurantMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantPlansMultiOutletTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndUser()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Plan Test Rest ' . uniqid(),
            'restaurant_id_unique' => 'REST-PT-' . rand(100, 999),
            'parent_id' => null,
            'address' => '123 Test Ave',
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

    public function test_restaurant_plans_displays_multiple_restaurant_support_and_count()
    {
        [, $user] = $this->setupRestaurantAndUser();

        $limitedPlan = Plan::create([
            'name' => 'MULTI 5 PLAN ' . uniqid(),
            'price' => 2999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'multi_outlet_checkbox' => 'Y',
            'total_number_of_outlets' => 5,
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        $unlimitedPlan = Plan::create([
            'name' => 'MULTI UNLIMITED PLAN ' . uniqid(),
            'price' => 4999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'multi_outlet_checkbox' => 'Y',
            'total_number_of_outlets' => 0,
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        $singlePlan = Plan::create([
            'name' => 'SINGLE REST PLAN ' . uniqid(),
            'price' => 999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'multi_outlet_checkbox' => 'N',
            'total_number_of_outlets' => 1,
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        $response = $this->actingAs($user)->get('/admin/restaurant/plans');
        $response->assertStatus(200);

        $response->assertSee($limitedPlan->name);
        $response->assertSee($unlimitedPlan->name);
        $response->assertSee($singlePlan->name);

        $response->assertSee('<li><i class="fas fa-check-circle text-success"></i> Multiple Restaurant Support (Up to 5 Restaurants)</li>', false);
        $response->assertSee('<li><i class="fas fa-check-circle text-success"></i> Multiple Restaurant Support (Unlimited)</li>', false);
        $response->assertSee('<li><i class="fas fa-times-circle text-danger"></i> Multiple Restaurant Support</li>', false);
    }
}
