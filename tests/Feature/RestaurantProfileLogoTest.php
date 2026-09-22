<?php

namespace Tests\Feature;

use App\Models\RestaurantMaster;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RestaurantProfileLogoTest extends TestCase
{
    protected function getRestaurantOwner()
    {
        $user = User::where('role', 'RES')->first();
        if (!$user) {
            $user = new User();
            $user->name = 'Test Owner';
            $user->email = 'owner_test@example.com';
            $user->password = bcrypt('123456');
            $user->phone = '9876543210';
            $user->role = 'RES';
            $user->role_type = 'ADMIN';
            $user->restaurant_id = 1;
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
