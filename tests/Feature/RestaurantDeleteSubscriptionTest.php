<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\RestaurantMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantDeleteSubscriptionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_restaurant_delete_immediately_expires_subscription_and_disables_autopay()
    {
        // 1. Create/Get Super Admin user
        $admin = User::find(1);
        if (!$admin) {
            $admin = User::create([
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin_test_' . time() . '@example.com',
                'password' => bcrypt('password'),
                'role' => 'SA',
                'role_type' => 'SUPERADMIN',
                'status' => 'A'
            ]);
        } else {
            $admin->role = 'SA';
            $admin->save();
        }

        // 2. Create restaurant owner
        $owner = User::create([
            'name' => 'Restaurant Owner',
            'email' => 'owner_test_' . time() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'status' => 'A'
        ]);

        // 3. Create restaurant
        $restaurant = RestaurantMaster::create([
            'name' => 'Delete Test Restaurant',
            'address' => '123 Test Street',
            'owner_id' => $owner->id,
            'status' => 'A'
        ]);

        $owner->restaurant_id = $restaurant->id;
        $owner->save();

        // 4. Create Plan
        $plan = Plan::create([
            'name' => 'AutoPay Test Plan',
            'label_name' => 'Popular',
            'price' => 999.00,
            'cross_price' => '1499.00',
            'billing_cycle' => 'monthly',
            'duration_days' => 30,
            'description' => 'Test description',
            'category_number' => 10,
            'total_number_of_dishes' => 100,
            'total_number_of_table' => 20,
            'inventory_checkbox' => 'Y',
            'is_delete' => 'N',
            'plan_status' => 'A'
        ]);

        // 5. Create active subscription with autopay enabled
        $subscription = Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'renewal_date' => now()->addDays(30),
            'auto_renew' => 1,
            'razorpay_subscription_id' => 'sub_test_mock_123',
            'refund_amount' => 0.00
        ]);

        // 6. Delete restaurant as admin
        $response = $this->actingAs($admin)
            ->from(route('manage.restaurant'))
            ->get(route('manage.restaurant.delete', $restaurant->id));
        
        $response->assertRedirect(route('manage.restaurant'));
        $response->assertSessionHas('success');

        // 7. Verify restaurant soft-deleted
        $restaurant->refresh();
        $this->assertEquals('D', $restaurant->status);

        // 8. Verify owner user soft-deleted
        $owner->refresh();
        $this->assertEquals('D', $owner->status);

        // 9. Verify subscription immediately expired and autopay disabled
        $subscription->refresh();
        $this->assertEquals('expired', $subscription->status);
        $this->assertEquals(0, $subscription->auto_renew);
        $this->assertNull($subscription->renewal_date);
    }
}
