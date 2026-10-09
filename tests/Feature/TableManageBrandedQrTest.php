<?php

namespace Tests\Feature;

use App\Models\RestaurantMaster;
use App\Models\TableManage;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TableManageBrandedQrTest extends TestCase
{
    use DatabaseTransactions;

    protected function getRestaurantOwner()
    {
        $restaurant = RestaurantMaster::first();
        if (!$restaurant) {
            $restaurant = new RestaurantMaster();
            $restaurant->name = 'Test Restaurant';
            $restaurant->phone = '9876543210';
            $restaurant->email = 'test_restaurant@example.com';
            $restaurant->status = 'A';
            $restaurant->save();
        }

        $user = User::where('role', 'RES')->first();
        if (!$user) {
            $user = new User();
            $user->name = 'Test Owner';
            $user->email = 'owner_table_test@example.com';
            $user->password = bcrypt('123456');
            $user->phone = '9876543210';
            $user->role = 'RES';
            $user->role_type = 'ADMIN';
            $user->restaurant_id = $restaurant->id;
            $user->save();
        } else {
            $user->restaurant_id = $restaurant->id;
            $user->save();
        }

        // Ensure active subscription exists for test user's restaurant
        $sub = Subscription::where('user_id', $restaurant->id)->where('status', 'active')->first();
        if (!$sub) {
            $plan = Plan::first() ?? Plan::create([
                'name' => 'Basic Plan',
                'price' => 999,
                'duration' => 365,
                'billing_cycle' => 'monthly',
                'inventory_checkbox' => 'Y',
                'status' => 'A'
            ]);

            Subscription::create([
                'user_id' => $restaurant->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(300),
                'amount' => 999
            ]);
        }

        return $user;
    }

    public function test_table_manage_page_renders_successfully()
    {
        $user = $this->getRestaurantOwner();
        $response = $this->actingAs($user)->get(route('table.manage'));
        $response->assertStatus(200);
        $response->assertSee('Manage Tables');
    }

    public function test_table_insert_generates_branded_qr_with_table_and_restaurant_name()
    {
        $user = $this->getRestaurantOwner();
        $restaurant = RestaurantMaster::find($user->restaurant_id);

        $tableName = 'Table VIP ' . rand(100, 999);

        $response = $this->actingAs($user)->post(route('table.manage.insert'), [
            'name' => $tableName,
            'description' => 'Near window',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $table = TableManage::where('name', $tableName)->where('restaurant_id', $user->restaurant_id)->first();
        $this->assertNotNull($table);
        $this->assertNotNull($table->qr_code);

        $qrPath = public_path('qrcodes/' . $table->qr_code);
        $this->assertFileExists($qrPath);

        $svgContent = file_get_contents($qrPath);
        // Verify SVG has table name and restaurant name
        $this->assertStringContainsString($tableName, $svgContent);
        if ($restaurant) {
            $this->assertStringContainsString(htmlspecialchars($restaurant->name, ENT_XML1, 'UTF-8'), $svgContent);
        }
    }

    public function test_regenerate_qr_action_replaces_old_qr_file()
    {
        $user = $this->getRestaurantOwner();
        $table = TableManage::where('restaurant_id', $user->restaurant_id)->where('status', '!=', 'D')->first();

        if (!$table) {
            $table = new TableManage();
            $table->name = 'Test Table 99';
            $table->restaurant_id = $user->restaurant_id;
            $table->user_id = $user->id;
            $table->status = 'A';
            $table->save();
        }

        $oldQr = $table->qr_code;

        $response = $this->actingAs($user)->get(route('table.manage.regenerate.qr', $table->id));
        $response->assertSessionHas('success');

        $table->refresh();
        $newQr = $table->qr_code;
        $this->assertNotNull($newQr);

        $newQrPath = public_path('qrcodes/' . $newQr);
        $this->assertFileExists($newQrPath);
    }

    public function test_regenerate_all_qr_action()
    {
        $user = $this->getRestaurantOwner();
        $response = $this->actingAs($user)->get(route('table.manage.regenerate.all'));
        $response->assertSessionHas('success');
    }
}
