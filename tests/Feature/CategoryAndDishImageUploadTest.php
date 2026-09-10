<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryAndDishImageUploadTest extends TestCase
{
    protected function getRestaurantUser()
    {
        $user = new User();
        $user->id = 1;
        $user->restaurant_id = 1;
        $user->role = 'RES';
        $user->role_type = 'ADMIN';
        $user->permissions = ['menu_master'];
        return $user;
    }

    protected function createCategory($name = 'Test Category')
    {
        $category = new Category();
        $category->name = $name . ' ' . time() . '_' . rand(100, 999);
        $category->user_id = 1;
        $category->restaurant_id = 1;
        $category->status = 'A';
        $category->save();
        return $category;
    }

    protected function createSubCategory($categoryId, $name = 'Test Dish')
    {
        $subCategory = new SubCategory();
        $subCategory->name = $name . ' ' . time() . '_' . rand(100, 999);
        $subCategory->price = 150;
        $subCategory->food_type = 'VEG';
        $subCategory->category_id = $categoryId;
        $subCategory->user_id = 1;
        $subCategory->restaurant_id = 1;
        $subCategory->status = 'A';
        $subCategory->save();
        return $subCategory;
    }

    public function test_category_insert_accepts_valid_image()
    {
        Storage::fake('public');
        $user = $this->getRestaurantUser();

        $image = UploadedFile::fake()->image('category_sample.png', 200, 200);

        $response = $this->actingAs($user)->post(route('manage.category.insert'), [
            'name' => 'Desserts ' . time(),
            'image' => $image,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
    }

    public function test_category_insert_rejects_non_image_file()
    {
        Storage::fake('public');
        $user = $this->getRestaurantUser();

        $nonImage = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route('manage.category.insert'), [
            'name' => 'Invalid Category ' . time(),
            'image' => $nonImage,
        ]);

        $response->assertSessionHasErrors(['image']);
    }

    public function test_category_update_rejects_non_image_file()
    {
        Storage::fake('public');
        $user = $this->getRestaurantUser();

        $category = $this->createCategory('Existing Cat');

        $nonImage = UploadedFile::fake()->create('test.txt', 50, 'text/plain');

        $response = $this->actingAs($user)->post(route('manage.category.update'), [
            'id' => $category->id,
            'name' => 'Existing Cat Updated',
            'image' => $nonImage,
        ]);

        $response->assertSessionHasErrors(['image']);
    }

    public function test_food_item_insert_accepts_valid_image()
    {
        Storage::fake('public');
        $user = $this->getRestaurantUser();

        $category = $this->createCategory('Beverages');

        $image = UploadedFile::fake()->image('cold_coffee.webp', 300, 300);

        $response = $this->actingAs($user)->post(route('manage.subcategory.category.insert'), [
            'name' => 'Cold Coffee',
            'price' => 120,
            'food_type' => 'VEG',
            'category_id' => $category->id,
            'image' => $image,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
    }

    public function test_food_item_insert_rejects_non_image_file()
    {
        Storage::fake('public');
        $user = $this->getRestaurantUser();

        $category = $this->createCategory('Beverages');

        $nonImage = UploadedFile::fake()->create('malicious.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route('manage.subcategory.category.insert'), [
            'name' => 'Cold Coffee',
            'price' => 120,
            'food_type' => 'VEG',
            'category_id' => $category->id,
            'image' => $nonImage,
        ]);

        $response->assertSessionHasErrors(['image']);
    }

    public function test_food_item_update_rejects_non_image_file()
    {
        Storage::fake('public');
        $user = $this->getRestaurantUser();

        $category = $this->createCategory('Beverages');
        $subCategory = $this->createSubCategory($category->id, 'Mocktail');

        $nonImage = UploadedFile::fake()->create('script.php', 10, 'text/x-php');

        $response = $this->actingAs($user)->post(route('manage.subcategory.category.update'), [
            'id' => $subCategory->id,
            'name' => 'Mocktail Updated',
            'price' => 160,
            'food_type' => 'VEG',
            'image' => $nonImage,
        ]);

        $response->assertSessionHasErrors(['image']);
    }
}
