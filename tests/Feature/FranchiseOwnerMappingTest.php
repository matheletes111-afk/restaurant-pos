<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\FranchiseRestaurantMapping;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FranchiseOwnerMappingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_view_map_and_detach_restaurant_for_franchise_owner()
    {
        // 1. Create Admin user
        $admin = new User();
        $admin->id = 88881;
        $admin->role = 'SA';
        $admin->restaurant_id = null;
        $this->actingAs($admin);

        // 2. Create FSO Owner
        $fso = User::create([
            'name' => 'Test Franchise Owner',
            'email' => 'fso_test_' . uniqid() . '@example.com',
            'phone' => '9876543210',
            'password' => bcrypt('password123'),
            'role' => 'FSO',
            'status' => 'A'
        ]);

        // 3. Create 2 test restaurants
        $rest1 = RestaurantMaster::create([
            'name' => 'Downtown Cafe Branch',
            'restaurant_id_unique' => 'REST-TEST-001',
            'address' => '123 Main Street',
            'status' => 'A'
        ]);

        $rest2 = RestaurantMaster::create([
            'name' => 'Uptown Bistro Branch',
            'restaurant_id_unique' => 'REST-TEST-002',
            'address' => '456 High Street',
            'status' => 'A'
        ]);

        // 4. Test GET mapping list (initially empty)
        $response = $this->getJson(route('admin.fso.mapping.list', $fso->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'owner' => [
                'id' => $fso->id,
                'count' => 0
            ],
            'mapped' => []
        ]);

        // 5. Attach Restaurant 1 to FSO
        $attachResponse = $this->postJson(route('admin.fso.mapping.attach'), [
            'user_id' => $fso->id,
            'restaurant_id' => $rest1->id
        ]);

        $attachResponse->assertStatus(200);
        $attachResponse->assertJson([
            'success' => true,
            'total_mapped' => 1
        ]);

        $this->assertDatabaseHas('franchise_restaurant_mappings', [
            'user_id' => $fso->id,
            'restaurant_id' => $rest1->id
        ]);

        // 6. Test duplicate prevention
        $dupResponse = $this->postJson(route('admin.fso.mapping.attach'), [
            'user_id' => $fso->id,
            'restaurant_id' => $rest1->id
        ]);
        $dupResponse->assertStatus(422);

        // 7. Attach Restaurant 2
        $attachResponse2 = $this->postJson(route('admin.fso.mapping.attach'), [
            'user_id' => $fso->id,
            'restaurant_id' => $rest2->id
        ]);
        $attachResponse2->assertStatus(200);
        $attachResponse2->assertJson(['total_mapped' => 2]);

        // 8. Verify mapping list has both restaurants
        $listResponse = $this->getJson(route('admin.fso.mapping.list', $fso->id));
        $listResponse->assertStatus(200);
        $this->assertCount(2, $listResponse->json('mapped'));

        // 9. Detach Restaurant 1
        $detachResponse = $this->postJson(route('admin.fso.mapping.detach'), [
            'user_id' => $fso->id,
            'restaurant_id' => $rest1->id
        ]);
        $detachResponse->assertStatus(200);
        $detachResponse->assertJson(['total_mapped' => 1]);

        $this->assertDatabaseMissing('franchise_restaurant_mappings', [
            'user_id' => $fso->id,
            'restaurant_id' => $rest1->id
        ]);

        $this->assertDatabaseHas('franchise_restaurant_mappings', [
            'user_id' => $fso->id,
            'restaurant_id' => $rest2->id
        ]);
    }
}

