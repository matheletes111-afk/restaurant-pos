<?php

namespace Tests\Feature;

use App\Models\RestaurantMaster;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RestaurantProfileLogoTest extends TestCase
{
    use DatabaseTransactions;

    protected function getRestaurantOwner()
    {
        $restaurant = RestaurantMaster::first();
        if (!$restaurant) {
            $restaurant = RestaurantMaster::create([
                'name' => 'Logo Restro',
                'email' => 'logo@example.com',
                'phone' => '9876543210',
                'status' => 'A'
            ]);
        }

        $plan = Plan::first() ?? Plan::create(['name' => 'Diamond', 'price' => 2999, 'duration_days' => 365, 'billing_cycle' => 'monthly', 'inventory_checkbox' => 'Y', 'plan_status' => 'A', 'is_delete' => 'N']);
        Subscription::firstOrCreate(
            ['user_id' => $restaurant->id, 'status' => 'active'],
            ['plan_id' => $plan->id, 'start_date' => now()->subDay(), 'end_date' => now()->addYear()]
        );

        $user = User::where('restaurant_id', $restaurant->id)->first();
        if (!$user) {
            $user = new User();
            $user->name = 'Test Owner';
            $user->email = 'owner_test_' . uniqid() . '@example.com';
            $user->password = bcrypt('123456');
            $user->phone = '9876543210';
            $user->role = 'RES';
            $user->role_type = 'ADMIN';
            $user->restaurant_id = $restaurant->id;
            $user->save();
        }
        return $user;
    }

    public function test_profile_page_renders_successfully()
    {
        $user = $this->getRestaurantOwner();
        $response = $this->actingAs($user)->get(route('restaurant.profile.index'));
        $response->assertStatus(200);
        $response->assertSee('Restaurant Logo');
    }

    public function test_restaurant_profile_update_accepts_logo_upload()
    {
        Storage::fake('public');
        $user = $this->getRestaurantOwner();
        $restaurant = RestaurantMaster::find($user->restaurant_id);

        $logoFile = UploadedFile::fake()->image('brand_logo.png', 400, 400);

        $response = $this->actingAs($user)->post(route('restaurant.profile.update'), [
            'restaurant_name' => $restaurant->name ?? 'Test Restro',
            'phone' => '9876543210',
            'address' => $restaurant->address ?? '123 Food Street',
            'pincode' => '400001',
            'logo' => $logoFile,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $restaurant->refresh();
        $this->assertNotNull($restaurant->logo);
        $this->assertStringStartsWith('logo_', $restaurant->logo);
    }
}
