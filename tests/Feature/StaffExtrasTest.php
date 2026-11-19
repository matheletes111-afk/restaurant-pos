<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\StaffExtra;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StaffExtrasTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $adminUser;
    protected $staffMember;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::first();
        if (!$this->restaurant) {
            $this->restaurant = RestaurantMaster::create([
                'name' => 'Extras Test Kitchen',
                'address' => 'Test Street 10',
                'status' => 'A',
            ]);
        }

        $this->adminUser = User::firstOrCreate(
            ['email' => 'extras_admin@example.com'],
            [
                'name' => 'Extras Admin',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'RES',
                'role_type' => 'ADMIN',
                'status' => 'A',
            ]
        );

        $this->staffMember = User::firstOrCreate(
            ['email' => 'extras_waiter@example.com'],
            [
                'name' => 'John Waiter',
                'password' => bcrypt('password123'),
                'restaurant_id' => $this->restaurant->id,
                'role' => 'STAFF',
                'role_type' => 'WAITER',
                'status' => 'A',
            ]
        );
    }

    public function test_unauthenticated_user_cannot_access_staff_extras()
    {
        $response = $this->get(route('restaurant.staff.extras.index'));
        $this->assertContains($response->status(), [302, 401]);
    }

    public function test_admin_can_view_staff_extras_dashboard()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.staff.extras.index'));

        $response->assertStatus(200);
        $response->assertViewIs('restaurant.staff_extras.index');
        $response->assertViewHas(['fromDate', 'toDate', 'staffList', 'extras', 'stats']);
        $response->assertSee('Staff Extras');
        $response->assertSee('Total Earnings');
        $response->assertSee('Total Deductions');
        $response->assertSee('Total Advances');
        $response->assertSee('openAddExtraModal');
        $response->assertSee('getExtraModal');
    }

    public function test_can_create_earning_extra_entry()
    {
        $payload = [
            'user_id' => $this->staffMember->id,
            'type' => 'Earning',
            'amount' => 1500.50,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'Monthly performance festival bonus',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.staff.extras.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('staff_extras', [
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Earning',
            'amount' => 1500.50,
            'remarks' => 'Monthly performance festival bonus',
            'created_by' => $this->adminUser->id,
        ]);
    }

    public function test_can_create_deduction_extra_entry()
    {
        $payload = [
            'user_id' => $this->staffMember->id,
            'type' => 'Deduction',
            'amount' => 250.00,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'Broken glassware penalty',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.staff.extras.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('staff_extras', [
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Deduction',
            'amount' => 250.00,
            'remarks' => 'Broken glassware penalty',
        ]);
    }

    public function test_can_create_advance_extra_entry()
    {
        $payload = [
            'user_id' => $this->staffMember->id,
            'type' => 'Advance',
            'amount' => 5000.00,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'Emergency advance salary payment',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.staff.extras.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('staff_extras', [
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Advance',
            'amount' => 5000.00,
            'remarks' => 'Emergency advance salary payment',
        ]);
    }

    public function test_validation_fails_on_invalid_type_or_negative_amount()
    {
        $payload = [
            'user_id' => $this->staffMember->id,
            'type' => 'InvalidType',
            'amount' => -100,
            'entry_date' => date('Y-m-d'),
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.staff.extras.store'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'amount']);
    }

    public function test_can_fetch_extra_entry_for_modal_edit()
    {
        $extra = StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Advance',
            'amount' => 2000.00,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'Pre-trip advance',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('restaurant.staff.extras.show', $extra->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $extra->id,
                    'user_id' => $this->staffMember->id,
                    'type' => 'Advance',
                    'amount' => '2000.00',
                    'remarks' => 'Pre-trip advance',
                ]
            ]);

        // Also test root route alias /staff-extras/show/{id}
        $rootResponse = $this->actingAs($this->adminUser)
            ->getJson('/staff-extras/show/' . $extra->id);
        $rootResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $extra->id,
                ]
            ]);
    }

    public function test_can_update_existing_extra_entry()
    {
        $extra = StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Advance',
            'amount' => 2000.00,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'Initial remarks',
            'created_by' => $this->adminUser->id,
        ]);

        $updatePayload = [
            'user_id' => $this->staffMember->id,
            'type' => 'Earning',
            'amount' => 2500.00,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'Converted to performance bonus',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('restaurant.staff.extras.update', $extra->id), $updatePayload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('staff_extras', [
            'id' => $extra->id,
            'type' => 'Earning',
            'amount' => 2500.00,
            'remarks' => 'Converted to performance bonus',
        ]);
    }

    public function test_can_filter_by_type_and_staff()
    {
        // Create 1 Earning and 1 Deduction
        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Earning',
            'amount' => 1200.00,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'Filterable Earning',
            'created_by' => $this->adminUser->id,
        ]);

        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Deduction',
            'amount' => 300.00,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'Filterable Deduction',
            'created_by' => $this->adminUser->id,
        ]);

        // Filter for Deduction only
        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.staff.extras.index', [
                'type' => 'Deduction',
                'staff_id' => $this->staffMember->id,
            ]));

        $response->assertStatus(200);
        $response->assertSee('Filterable Deduction');
        $response->assertDontSee('Filterable Earning');
    }

    public function test_can_delete_extra_entry()
    {
        $extra = StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Earning',
            'amount' => 800.00,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'To be deleted',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson(route('restaurant.staff.extras.destroy', $extra->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('staff_extras', [
            'id' => $extra->id,
        ]);
    }

    public function test_can_export_extras_to_csv()
    {
        StaffExtra::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staffMember->id,
            'type' => 'Earning',
            'amount' => 777.00,
            'entry_date' => date('Y-m-d'),
            'remarks' => 'Exportable test record',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('restaurant.staff.extras.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Exportable test record', $response->streamedContent());
    }
}
