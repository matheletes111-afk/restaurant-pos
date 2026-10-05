<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\DishAddon;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DishAddonMasterTest extends TestCase
{
    use DatabaseTransactions;

    private $restaurant;
    private $user;
    private $otherRestaurant;
    private $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = RestaurantMaster::create([
            'name' => 'Burger Haven ' . uniqid(),
            'email' => 'haven_' . uniqid() . '@example.com',
            'phone_number' => '9876543210',
            'address' => 'Food Street 1',
            'status' => 'A'
        ]);

        $plan = Plan::first() ?? Plan::create([
            'name' => 'Gold Plan',
            'price' => 999,
            'duration_days' => 365,
            'billing_cycle' => 'yearly',
            'plan_status' => 'A',
            'is_delete' => 'N'
        ]);

        Subscription::create([
            'user_id' => $this->restaurant->id,
            'plan_id' => $plan->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addYear(),
            'status' => 'active'
        ]);

        $this->user = User::create([
            'name' => 'Restaurant Manager',
            'email' => 'mgr_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $this->restaurant->id,
            'role' => 'RES',
            'role_type' => 'ADMIN',
        ]);

        // Second restaurant for multi-tenant checks
        $this->otherRestaurant = RestaurantMaster::create([
            'name' => 'Other Cafe ' . uniqid(),
            'email' => 'other_' . uniqid() . '@example.com',
            'phone_number' => '9876543211',
            'status' => 'A'
        ]);

        Subscription::create([
            'user_id' => $this->otherRestaurant->id,
            'plan_id' => $plan->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addYear(),
            'status' => 'active'
        ]);

        $this->otherUser = User::create([
            'name' => 'Other Manager',
            'email' => 'other_mgr_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'restaurant_id' => $this->otherRestaurant->id,
            'role' => 'RES',
            'role_type' => 'ADMIN',
        ]);
    }

    public function test_restaurant_user_can_view_dish_addon_index()
    {
        DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Extra Melted Cheese',
            'price' => 35.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $response = $this->actingAs($this->user)->get(route('addon.index'));

        $response->assertStatus(200);
        $response->assertSee('Dish Addon Master');
        $response->assertSee('Extra Melted Cheese');
        $response->assertSee('35.00');
    }

    public function test_can_create_dish_addon()
    {
        $payload = [
            'name' => 'Truffle Mayo Dip',
            'price' => 45.00,
            'food_type' => 'VEG',
            'description' => 'Rich creamy truffle infused mayonnaise',
            'status' => 'A'
        ];

        $response = $this->actingAs($this->user)->post(route('addon.store'), $payload);

        $response->assertRedirect(route('addon.index'));
        $this->assertDatabaseHas('dish_addons', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Truffle Mayo Dip',
            'price' => 45.00,
            'food_type' => 'VEG',
        ]);
    }

    public function test_can_update_dish_addon()
    {
        $addon = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Bacon Strip',
            'price' => 50.00,
            'food_type' => 'NON-VEG',
            'status' => 'A'
        ]);

        $payload = [
            'name' => 'Crispy Smoked Bacon',
            'price' => 65.00,
            'food_type' => 'NON-VEG',
            'description' => 'Double smoked crispy bacon strips',
            'status' => 'A'
        ];

        $response = $this->actingAs($this->user)->post(route('addon.update', $addon->id), $payload);

        $response->assertRedirect(route('addon.index'));
        $this->assertDatabaseHas('dish_addons', [
            'id' => $addon->id,
            'name' => 'Crispy Smoked Bacon',
            'price' => 65.00,
        ]);
    }

    public function test_can_toggle_addon_status()
    {
        $addon = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Jalapeno Slices',
            'price' => 20.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $response = $this->actingAs($this->user)->postJson(route('addon.toggle.status', $addon->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'status' => 'I']);
        $this->assertEquals('I', $addon->fresh()->status);
    }

    public function test_can_delete_addon()
    {
        $addon = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Old Addon',
            'price' => 10.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $response = $this->actingAs($this->user)->deleteJson(route('addon.destroy', $addon->id));

        $response->assertStatus(200);
        $this->assertEquals('D', $addon->fresh()->status);
    }

    public function test_can_download_excel_template()
    {
        $response = $this->actingAs($this->user)->get(route('addon.template.download'));

        $response->assertStatus(200);
        $this->assertStringContainsString('dish_addons_template.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_can_bulk_upload_addons_via_excel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Addon Name');
        $sheet->setCellValue('B1', 'Base Price (₹)');
        $sheet->setCellValue('C1', 'Food Type (VEG/NON-VEG)');
        $sheet->setCellValue('D1', 'Description');

        $sheet->setCellValue('A2', 'Cheddar Dip');
        $sheet->setCellValue('B2', 35);
        $sheet->setCellValue('C2', 'VEG');
        $sheet->setCellValue('D2', 'Creamy cheddar cheese');

        $sheet->setCellValue('A3', 'Grilled Chicken Chunk');
        $sheet->setCellValue('B3', 60);
        $sheet->setCellValue('C3', 'NON-VEG');
        $sheet->setCellValue('D3', 'Spiced chicken');

        $sheet->setCellValue('A4', 'Bacon Bits');
        $sheet->setCellValue('B4', 45);
        $sheet->setCellValue('C4', 'NON-VEG');
        $sheet->setCellValue('D4', 'Crispy bacon bits');

        $tempFile = tempnam(sys_get_temp_dir(), 'addon_upload_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'dish_addons.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->user)->post(route('addon.bulk.upload'), [
            'bulk_file' => $uploadedFile
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('dish_addons', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Cheddar Dip',
            'price' => 35.00,
            'food_type' => 'VEG'
        ]);

        $this->assertDatabaseHas('dish_addons', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Grilled Chicken Chunk',
            'price' => 60.00,
            'food_type' => 'NON-VEG'
        ]);

        $this->assertDatabaseHas('dish_addons', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Bacon Bits',
            'price' => 45.00,
            'food_type' => 'NON-VEG'
        ]);

        @unlink($tempFile);
    }

    public function test_cannot_create_addon_with_invalid_food_type()
    {
        $payload = [
            'name' => 'Invalid Food Type Addon',
            'price' => 50.00,
            'food_type' => 'EGG',
            'status' => 'A'
        ];

        $response = $this->actingAs($this->user)->post(route('addon.store'), $payload);
        $response->assertSessionHasErrors('food_type');
    }

    public function test_multi_tenant_isolation_between_restaurants()
    {
        $addon = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Private Addon',
            'price' => 99.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        // Other user from another restaurant cannot access or edit it
        $response = $this->actingAs($this->otherUser)->get(route('addon.edit', $addon->id));
        $response->assertStatus(404);

        $responseUpdate = $this->actingAs($this->otherUser)->post(route('addon.update', $addon->id), [
            'name' => 'Hacked Name',
            'price' => 1.00,
            'food_type' => 'VEG'
        ]);
        $responseUpdate->assertStatus(404);
        $this->assertEquals('Private Addon', $addon->fresh()->name);
    }

    public function test_dish_can_be_created_with_mapped_addons()
    {
        $category = \App\Models\Category::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Burgers',
            'status' => 'A'
        ]);

        $addon1 = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Extra Cheese Slice',
            'price' => 20.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $addon2 = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Bacon Jam',
            'price' => 40.00,
            'food_type' => 'NON-VEG',
            'status' => 'A'
        ]);

        $payload = [
            'name' => 'Signature Beef Burger',
            'price' => 250.00,
            'food_type' => 'NON-VEG',
            'category_id' => $category->id,
            'addon_ids' => [$addon1->id, $addon2->id]
        ];

        $response = $this->actingAs($this->user)->post(route('manage.subcategory.category.insert'), $payload);
        $response->assertRedirect();

        $dish = \App\Models\SubCategory::where('name', 'Signature Beef Burger')->first();
        $this->assertNotNull($dish);
        $this->assertEquals(2, $dish->addons()->count());
        $this->assertTrue($dish->addons->contains($addon1->id));
        $this->assertTrue($dish->addons->contains($addon2->id));
    }

    public function test_dish_can_be_updated_with_synced_addons()
    {
        $category = \App\Models\Category::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Pizzas',
            'status' => 'A'
        ]);

        $addon1 = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Extra Mozzarella',
            'price' => 50.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $addon2 = DishAddon::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Olives & Jalapenos',
            'price' => 30.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $dish = \App\Models\SubCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $category->id,
            'name' => 'Margherita Supreme',
            'price' => 300.00,
            'food_type' => 'VEG',
            'status' => 'A'
        ]);

        $dish->addons()->sync([$addon1->id]);
        $this->assertEquals(1, $dish->fresh()->addons()->count());

        // Update to swap addon1 with addon2
        $updatePayload = [
            'id' => $dish->id,
            'name' => 'Margherita Supreme Deluxe',
            'price' => 320.00,
            'food_type' => 'VEG',
            'addon_ids' => [$addon2->id]
        ];

        $response = $this->actingAs($this->user)->post(route('manage.subcategory.category.update'), $updatePayload);
        $response->assertRedirect();

        $dish = $dish->fresh();
        $this->assertEquals('Margherita Supreme Deluxe', $dish->name);
        $this->assertEquals(1, $dish->addons()->count());
        $this->assertFalse($dish->addons->contains($addon1->id));
        $this->assertTrue($dish->addons->contains($addon2->id));
    }
}
