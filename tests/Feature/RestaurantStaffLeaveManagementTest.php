<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\StaffLeavePolicy;
use App\Models\StaffLeave;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantStaffLeaveManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndAdmin()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Leave Restro ' . uniqid(),
            'restaurant_id_unique' => 'REST-LV-' . rand(100, 999),
            'parent_id' => null,
            'address' => '500 Leave Avenue',
            'status' => 'A'
        ]);

        $admin = User::create([
            'name' => 'Leave Admin ' . uniqid(),
            'email' => 'leave_admin_' . uniqid() . '@example.com',
            'phone' => '98765' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $plan = Plan::create([
            'name' => 'Staff Plan ' . uniqid(),
            'price' => 999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(29),
            'amount' => 999.00,
            'currency' => 'INR',
        ]);

        return [$restaurant, $admin];
    }

    protected function createStaffMember($restaurant, $role = 'Waiter')
    {
        return User::create([
            'name' => 'Staff ' . uniqid(),
            'email' => 'staff_lv_' . uniqid() . '@example.com',
            'phone' => '91234' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'STAFF',
            'role_type' => $role,
            'restaurant_id' => $restaurant->id,
            'status' => 'A',
            'permissions' => json_encode(['staff' => ['view', 'create', 'edit', 'delete']])
        ]);
    }

    public function test_can_view_leave_management_page_defaulting_to_current_month()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        $response = $this->actingAs($admin)
            ->get(route('restaurant.leaves.index'));

        $response->assertStatus(200);
        $response->assertSee('Staff Leave Management');
        $response->assertSee('Apply / Record Staff Leave');
        $response->assertSee($staff->name);
        $response->assertSee(date('Y-m-01'));
        $response->assertSee(date('Y-m-t'));
    }

    public function test_can_change_from_date_and_to_date_filter()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $targetFrom = '2026-10-01';
        $targetTo = '2026-10-31';

        $response = $this->actingAs($admin)
            ->get(route('restaurant.leaves.index', [
                'from_date' => $targetFrom,
                'to_date' => $targetTo
            ]));

        $response->assertStatus(200);
        $response->assertSee($targetFrom);
        $response->assertSee($targetTo);
    }

    public function test_cannot_take_leave_when_requested_days_exceed_unused_quota()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        // Policy has 2 days total
        StaffLeavePolicy::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'year' => 2026,
            'leave_type' => 'sick_leave',
            'annual_quota' => 2.00,
            'used_count' => 0.00,
            'carried_forward' => 0.00,
            'is_carry_forward' => false
        ]);

        // Attempt requesting 3 days (10th to 12th)
        $response = $this->actingAs($admin)
            ->post(route('restaurant.leaves.store'), [
                'user_id' => $staff->id,
                'leave_type' => 'sick_leave',
                'start_date' => '2026-11-10',
                'end_date' => '2026-11-12',
                'reason' => 'Viral fever recovery',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $errorMessage = session('error');
        $this->assertStringContainsString('Cannot be done', $errorMessage);
        $this->assertStringContainsString('Requested 3 day(s)', $errorMessage);
        $this->assertStringContainsString('only 2 day(s) unused leave available', $errorMessage);

        // Database should have NO leave records created
        $this->assertDatabaseMissing('staff_leaves', [
            'user_id' => $staff->id,
            'leave_type' => 'sick_leave',
        ]);
    }

    public function test_can_successfully_take_leave_and_deducts_quota()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        // Policy has 5 days
        $policy = StaffLeavePolicy::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'year' => 2026,
            'leave_type' => 'paid_leave',
            'annual_quota' => 5.00,
            'used_count' => 0.00,
            'carried_forward' => 0.00,
            'is_carry_forward' => false
        ]);

        // Request 2 days leave (18th to 19th)
        $response = $this->actingAs($admin)
            ->post(route('restaurant.leaves.store'), [
                'user_id' => $staff->id,
                'leave_type' => 'paid_leave',
                'start_date' => '2026-11-18',
                'end_date' => '2026-11-19',
                'reason' => 'Annual family vacation',
            ]);

        $response->assertRedirect(route('restaurant.leaves.index', [
            'from_date' => '2026-11-01',
            'to_date' => '2026-11-30'
        ]));
        $response->assertSessionHas('success');

        // Check leave record
        $this->assertDatabaseHas('staff_leaves', [
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'leave_type' => 'paid_leave',
            'start_date' => '2026-11-18',
            'end_date' => '2026-11-19',
            'days_count' => 2.00,
            'status' => 'approved',
            'created_by' => $admin->id,
        ]);

        // Check quota deducted
        $policy->refresh();
        $this->assertEquals(2.00, $policy->used_count);
        $this->assertEquals(3.00, $policy->remaining_balance);
    }

    public function test_can_cancel_leave_with_remarks_restores_quota_and_keeps_in_list()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        $policy = StaffLeavePolicy::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'year' => 2026,
            'leave_type' => 'sick_leave',
            'annual_quota' => 10.00,
            'used_count' => 3.00,
            'carried_forward' => 0.00,
            'is_carry_forward' => false
        ]);

        $leave = StaffLeave::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'leave_type' => 'sick_leave',
            'start_date' => '2026-11-20',
            'end_date' => '2026-11-22',
            'days_count' => 3.00,
            'reason' => 'Scheduled rest',
            'status' => 'approved',
            'created_by' => $admin->id,
        ]);

        // Cancel with remarks
        $cancelRemarks = 'Staff recovered early and reported to duty';
        $response = $this->actingAs($admin)
            ->post(route('restaurant.leaves.cancel', $leave->id), [
                'cancellation_remarks' => $cancelRemarks,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check record still exists but is marked cancelled with remarks
        $leave->refresh();
        $this->assertEquals('cancelled', $leave->status);
        $this->assertEquals($cancelRemarks, $leave->cancellation_remarks);
        $this->assertEquals($admin->id, $leave->cancelled_by);
        $this->assertNotNull($leave->cancelled_at);

        // Check quota restored
        $policy->refresh();
        $this->assertEquals(0.00, $policy->used_count);
        $this->assertEquals(10.00, $policy->remaining_balance);

        // Check page view displays the cancelled entry and remarks
        $viewResponse = $this->actingAs($admin)
            ->get(route('restaurant.leaves.index', [
                'from_date' => '2026-11-01',
                'to_date' => '2026-11-30'
            ]));

        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Cancelled / Deleted');
        $viewResponse->assertSee($cancelRemarks);
    }

    public function test_super_admin_can_view_and_manage_leaves_across_restaurants()
    {
        [$restaurant1, ] = $this->setupRestaurantAndAdmin();
        [$restaurant2, ] = $this->setupRestaurantAndAdmin();

        $staff1 = $this->createStaffMember($restaurant1, 'Cashier');
        $staff2 = $this->createStaffMember($restaurant2, 'Chef');

        $superAdmin = User::create([
            'name' => 'Super Admin LV ' . uniqid(),
            'email' => 'superadmin_lv_' . uniqid() . '@example.com',
            'phone' => '90000' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'SA',
            'role_type' => 'SUPER_ADMIN',
            'restaurant_id' => null,
            'status' => 'A'
        ]);

        // Super Admin views restaurant 1
        $response1 = $this->actingAs($superAdmin)
            ->get(route('restaurant.leaves.index', ['restaurant_id' => $restaurant1->id]));

        $response1->assertStatus(200);
        $response1->assertSee($staff1->name);
        $response1->assertDontSee($staff2->name);

        // Switch to restaurant 2
        $response2 = $this->actingAs($superAdmin)
            ->get(route('restaurant.leaves.index', ['restaurant_id' => $restaurant2->id]));

        $response2->assertStatus(200);
        $response2->assertSee($staff2->name);
        $response2->assertDontSee($staff1->name);
    }

    public function test_can_export_leaves_csv()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        StaffLeave::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staff->id,
            'leave_type' => 'sick_leave',
            'start_date' => '2026-11-05',
            'end_date' => '2026-11-06',
            'days_count' => 2.00,
            'reason' => 'Export test',
            'status' => 'approved',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('restaurant.leaves.export', [
                'from_date' => '2026-11-01',
                'to_date' => '2026-11-30',
            ]));

        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('content-disposition'));
        $this->assertStringContainsString('leave_management_', $response->headers->get('content-disposition'));
    }
}

