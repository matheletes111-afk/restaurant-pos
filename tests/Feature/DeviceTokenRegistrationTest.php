<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeviceTokenRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_store_fcm_token_via_web_endpoint()
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->postJson('/device-token/store', [
            'token' => 'sample_fcm_token_' . uniqid(),
            'device_type' => 'web'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('user_fcm_tokens', [
            'user_id' => $user->id,
            'device_type' => 'web'
        ]);
    }

    public function test_shared_device_token_reassigns_to_new_user()
    {
        $users = User::take(2)->get();
        if ($users->count() < 2) {
            $this->markTestSkipped('Need at least two users');
        }

        $user1 = $users[0];
        $user2 = $users[1];
        $sharedToken = 'shared_fcm_device_token_' . uniqid();

        // User 1 logs in and registers device
        $this->actingAs($user1)->postJson('/device-token/store', [
            'token' => $sharedToken,
            'device_type' => 'web'
        ])->assertStatus(200);

        $this->assertDatabaseHas('user_fcm_tokens', [
            'user_id' => $user1->id,
            'fcm_token' => $sharedToken
        ]);

        // User 2 logs in on the same device
        $this->actingAs($user2)->postJson('/device-token/store', [
            'token' => $sharedToken,
            'device_type' => 'web'
        ])->assertStatus(200);

        // Previous user's association must be gone
        $this->assertDatabaseMissing('user_fcm_tokens', [
            'user_id' => $user1->id,
            'fcm_token' => $sharedToken
        ]);

        // Current user must have the token
        $this->assertDatabaseHas('user_fcm_tokens', [
            'user_id' => $user2->id,
            'fcm_token' => $sharedToken
        ]);
    }
}
