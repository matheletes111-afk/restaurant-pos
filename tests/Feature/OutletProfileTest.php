<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OutletProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected $restaurant;
    protected $ownerUser;
    protected $outlet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::create([
            'name' => 'Main Test Restaurant',
            'address' => '100 Main Street',
            'pincode' => '700001',
            'gstin' => '19AAAAA0000A1Z5',
            'status' => 'A',
        ]);

        $this->ownerUser = User::create([
            'name' => 'Outlet Owner',
            'email' => 'owner_outlet_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $this->restaurant->id,
            'status' => 'A'
        ]);

        $this->restaurant->owner_id = $this->ownerUser->id;
        $this->restaurant->save();

        $plan = Plan::create([
            'name' => 'Enterprise Multi-Outlet',
            'price' => 4999,
            'duration_days' => 365,
            'billing_cycle' => 'monthly',
            'multi_outlet_checkbox' => 'Y',
            'total_number_of_outlets' => 5,
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        Subscription::create([
            'user_id' => $this->restaurant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addYear()
        ]);

        $this->outlet = RestaurantMaster::create([
            'parent_id' => $this->restaurant->id,
            'name' => 'North Branch Outlet',
            'address' => '200 North Avenue',
            'pincode' => '700002',
            'status' => 'A',
            'owner_id' => $this->ownerUser->id,
            'created_by' => $this->ownerUser->id,
        ]);
    }

    public function test_outlets_index_displays_edit_links_to_profile_page()
    {
        $this->actingAs($this->ownerUser);

        $response = $this->get(route('restaurant.outlets.index'));
        $response->assertStatus(200);

        // Assert links to profile page with outlet_id exist
        $response->assertSee(route('restaurant.profile.index', ['outlet_id' => $this->restaurant->id]));
        $response->assertSee(route('restaurant.profile.index', ['outlet_id' => $this->outlet->id]));
    }

    public function test_profile_page_loads_and_switches_context_when_outlet_id_is_passed()
    {
        $this->actingAs($this->ownerUser);

        $response = $this->get(route('restaurant.profile.index', ['outlet_id' => $this->outlet->id]));
        $response->assertStatus(200);
        $response->assertSee('North Branch Outlet');
        $response->assertSee('200 North Avenue');

        $this->ownerUser->refresh();
        $this->assertEquals($this->outlet->id, $this->ownerUser->restaurant_id);
    }

    public function test_profile_page_updates_outlet_details_successfully()
    {
        $this->actingAs($this->ownerUser);

        $payload = [
            'outlet_id' => $this->outlet->id,
            'name' => 'Outlet Owner',
            'restaurant_name' => 'Updated North Branch Outlet',
            'phone' => '9876543210',
            'address' => '250 New North Avenue',
            'pincode' => '700005',
            'gstin' => '19BBBBB0000B1Z5',
            'fssai_number' => '12345678901234',
            'gst_percentage' => 18,
            'upi_id' => 'northbranch@upi',
        ];

        $response = $this->post(route('restaurant.profile.update'), $payload);
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->outlet->refresh();
        $this->assertEquals('Updated North Branch Outlet', $this->outlet->name);
        $this->assertEquals('250 New North Avenue', $this->outlet->address);
        $this->assertEquals('700005', $this->outlet->pincode);
        $this->assertEquals('19BBBBB0000B1Z5', $this->outlet->gstin);
        $this->assertEquals(18, (float) $this->outlet->gst_percentage);
        $this->assertEquals('northbranch@upi', $this->outlet->upi_id);
    }

    public function test_profile_page_displays_location_map_and_geofence_controls()
    {
        $this->actingAs($this->ownerUser);

        $this->restaurant->update([
            'latitude' => 22.57264600,
            'longitude' => 88.36389500,
            'geofence_radius_meters' => 75,
            'geofence_enabled' => true,
        ]);

        $response = $this->get(route('restaurant.profile.index'));
        $response->assertStatus(200);

        $response->assertSee('restaurantProfileMap');
        $response->assertSee('Restaurant Physical Location');
        $response->assertSee('22.572646');
        $response->assertSee('88.363895');
        $response->assertSee('75');
        $response->assertSee('Enforce GPS Attendance Geofencing for Staff');
        $response->assertSee('btnDetectProfileLocation');
        $response->assertSee('btnClearProfileLocation');
    }

    public function test_profile_page_updates_location_and_geofence_settings()
    {
        $this->actingAs($this->ownerUser);

        $payload = [
            'outlet_id' => $this->restaurant->id,
            'name' => 'Owner Name',
            'restaurant_name' => $this->restaurant->name,
            'phone' => '9876543210',
            'address' => $this->restaurant->address,
            'pincode' => $this->restaurant->pincode,
            'latitude' => 22.57264600,
            'longitude' => 88.36389500,
            'geofence_radius_meters' => 100,
            'geofence_enabled' => 1,
        ];

        $response = $this->post(route('restaurant.profile.update'), $payload);
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->restaurant->refresh();
        $this->assertEquals(22.57264600, (float) $this->restaurant->latitude);
        $this->assertEquals(88.36389500, (float) $this->restaurant->longitude);
        $this->assertEquals(100, $this->restaurant->geofence_radius_meters);
        $this->assertTrue((bool) $this->restaurant->geofence_enabled);

        // Test clearing location
        $clearPayload = [
            'outlet_id' => $this->restaurant->id,
            'name' => 'Owner Name',
            'restaurant_name' => $this->restaurant->name,
            'phone' => '9876543210',
            'address' => $this->restaurant->address,
            'pincode' => $this->restaurant->pincode,
            'latitude' => '',
            'longitude' => '',
            'clear_location' => 1,
            'geofence_radius_meters' => 50,
        ];

        $response2 = $this->post(route('restaurant.profile.update'), $clearPayload);
        $response2->assertStatus(302);
        $this->restaurant->refresh();
        $this->assertNull($this->restaurant->latitude);
        $this->assertNull($this->restaurant->longitude);
        $this->assertFalse((bool) $this->restaurant->geofence_enabled);
    }
}
