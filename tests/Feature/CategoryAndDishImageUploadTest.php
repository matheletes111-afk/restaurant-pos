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
    protected $testPlan;
    protected $testSubscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testPlan = new \App\Models\Plan();
        $this->testPlan->name = 'Test Unlimited Plan';
        $this->testPlan->total_number_of_dishes = 0; // Unlimited
        $this->testPlan->duration_days = 30;
        $this->testPlan->price = 0;
        $this->testPlan->save();

        $this->testSubscription = new \App\Models\Subscription();
        $this->testSubscription->user_id = 8888;
        $this->testSubscription->plan_id = $this->testPlan->id;
        $this->testSubscription->status = 'active';
        $this->testSubscription->save();
    }

    protected function tearDown(): void
    {
        if ($this->testSubscription) {
            $this->testSubscription->delete();
        }
        if ($this->testPlan) {
            $this->testPlan->delete();
        }

        parent::tearDown();
    }

    protected function getRestaurantUser($restaurantId = 8888)
    {
        $user = User::find(1) ?? new User();
        $user->id = 1;
        $user->restaurant_id = $restaurantId;
        $user->role = 'RES';
        $user->role_type = 'ADMIN';
        $user->permissions = ['menu_master'];
        return $user;
    }

    protected function createCategory($name = 'Test Category', $restaurantId = 8888)
    {
        $category = new Category();
        $category->name = $name . ' ' . time() . '_' . rand(100, 999);
        $category->user_id = 1;
        $category->restaurant_id = $restaurantId;
        $category->status = 'A';
        $category->save();
        return $category;
    }

    protected function createSubCategory($categoryId, $name = 'Test Dish', $restaurantId = 8888)
    {
        $subCategory = new SubCategory();
        $subCategory->name = $name . ' ' . time() . '_' . rand(100, 999);
        $subCategory->price = 150;
        $subCategory->food_type = 'VEG';
        $subCategory->category_id = $categoryId;
        $subCategory->user_id = 1;
        $subCategory->restaurant_id = $restaurantId;
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

    public function test_bulk_upload_food_items_three_column_format()
    {
        $user = $this->getRestaurantUser();
        $category = $this->createCategory('Main Course');

        $csvContent = "Product Name,Price,Food Type\n";
        $csvContent .= "Shahi Paneer,280,VEG\n";
        $csvContent .= "Mutton Curry,450,NON-VEG\n";

        $file = UploadedFile::fake()->createWithContent('dishes.csv', $csvContent);

        $response = $this->actingAs($user)->post(route('manage.subcategory.category.bulk.upload'), [
            'category_id' => $category->id,
            'bulk_file' => $file,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sub_category', [
            'name' => 'Shahi Paneer',
            'price' => 280,
            'food_type' => 'VEG',
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('sub_category', [
            'name' => 'Mutton Curry',
            'price' => 450,
            'food_type' => 'NON-VEG',
            'category_id' => $category->id,
        ]);
    }

    public function test_category_bulk_upload_single_column_format()
    {
        $restaurantId = 8888;
        Category::where('restaurant_id', $restaurantId)->delete();
        $user = $this->getRestaurantUser($restaurantId);

        $csvContent = "Category Name\n";
        $csvContent .= "Appetizers\n";
        $csvContent .= "Continental\n";
        $csvContent .= "Mocktails\n";

        $file = UploadedFile::fake()->createWithContent('categories.csv', $csvContent);

        $response = $this->actingAs($user)->post(route('manage.category.bulk.upload'), [
            'bulk_file' => $file,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('category', [
            'name' => 'Appetizers',
            'restaurant_id' => $restaurantId,
        ]);

        $this->assertDatabaseHas('category', [
            'name' => 'Continental',
            'restaurant_id' => $restaurantId,
        ]);

        $this->assertDatabaseHas('category', [
            'name' => 'Mocktails',
            'restaurant_id' => $restaurantId,
        ]);

        Category::where('restaurant_id', $restaurantId)->delete();
    }

    public function test_category_bulk_upload_respects_plan_category_limits()
    {
        $restaurantId = 7772;
        Category::where('restaurant_id', $restaurantId)->delete();
        $user = $this->getRestaurantUser($restaurantId);

        // Create plan with limit of 2 categories
        $plan = new \App\Models\Plan();
        $plan->name = 'Category Quota Plan';
        $plan->category_number = 2;
        $plan->duration_days = 30;
        $plan->price = 0;
        $plan->save();

        $subscription = new \App\Models\Subscription();
        $subscription->user_id = $restaurantId;
        $subscription->plan_id = $plan->id;
        $subscription->status = 'active';
        $subscription->save();

        // 1 category already exists
        $this->createCategory('Soups', $restaurantId);

        // Upload CSV with 3 categories (only 1 should fit, 2 skipped due to limit of 2)
        $csvContent = "Category Name\n";
        $csvContent .= "Salads\n";
        $csvContent .= "Desserts\n";
        $csvContent .= "Beverages\n";

        $file = UploadedFile::fake()->createWithContent('categories_quota.csv', $csvContent);

        $response = $this->actingAs($user)->post(route('manage.category.bulk.upload'), [
            'bulk_file' => $file,
        ]);

        $response->assertSessionHas('warning');

        $totalCategories = Category::where('restaurant_id', $restaurantId)->where('status', '!=', 'D')->count();
        $this->assertEquals(2, $totalCategories);

        Category::where('restaurant_id', $restaurantId)->delete();
        $subscription->delete();
        $plan->delete();
    }

    public function test_dishes_bulk_upload_four_column_format()
    {
        $restaurantId = 8888;
        SubCategory::where('restaurant_id', $restaurantId)->delete();
        Category::where('restaurant_id', $restaurantId)->delete();

        $user = $this->getRestaurantUser($restaurantId);
        
        $cat1 = new Category();
        $cat1->name = 'Starters';
        $cat1->restaurant_id = $restaurantId;
        $cat1->user_id = 1;
        $cat1->status = 'A';
        $cat1->save();

        $cat2 = new Category();
        $cat2->name = 'Main Course';
        $cat2->restaurant_id = $restaurantId;
        $cat2->user_id = 1;
        $cat2->status = 'A';
        $cat2->save();

        $csvContent = "Product Name,Price,Food Type,Category\n";
        $csvContent .= "Paneer Tikka,220,VEG,Starters\n";
        $csvContent .= "Butter Chicken,380,NON-VEG,Main Course\n";

        $file = UploadedFile::fake()->createWithContent('dishes_4col.csv', $csvContent);

        $response = $this->actingAs($user)->post(route('manage.subcategory.category.bulk.upload'), [
            'bulk_file' => $file,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sub_category', [
            'name' => 'Paneer Tikka',
            'price' => 220,
            'food_type' => 'VEG',
            'category_id' => $cat1->id,
            'restaurant_id' => $restaurantId,
        ]);

        $this->assertDatabaseHas('sub_category', [
            'name' => 'Butter Chicken',
            'price' => 380,
            'food_type' => 'NON-VEG',
            'category_id' => $cat2->id,
            'restaurant_id' => $restaurantId,
        ]);

        SubCategory::where('restaurant_id', $restaurantId)->delete();
        Category::where('restaurant_id', $restaurantId)->delete();
    }

    public function test_dishes_bulk_upload_fails_and_inserts_nothing_if_category_name_not_matched()
    {
        $restaurantId = 8888;
        SubCategory::where('restaurant_id', $restaurantId)->delete();
        Category::where('restaurant_id', $restaurantId)->delete();

        $user = $this->getRestaurantUser($restaurantId);
        
        $cat1 = new Category();
        $cat1->name = 'Starters';
        $cat1->restaurant_id = $restaurantId;
        $cat1->user_id = 1;
        $cat1->status = 'A';
        $cat1->save();

        // Row 1 has valid category 'Starters', but Row 2 has non-existent category 'Unknown Category'
        $csvContent = "Product Name,Price,Food Type,Category\n";
        $csvContent .= "Paneer Tikka,220,VEG,Starters\n";
        $csvContent .= "Pizza Margherita,350,VEG,Unknown Category\n";

        $file = UploadedFile::fake()->createWithContent('dishes_unmatched.csv', $csvContent);

        $response = $this->actingAs($user)->post(route('manage.subcategory.category.bulk.upload'), [
            'bulk_file' => $file,
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Unknown Category', session('error'));

        // Strict Requirement: Nothing should be inserted!
        $totalDishes = SubCategory::where('restaurant_id', $restaurantId)->count();
        $this->assertEquals(0, $totalDishes, "No dishes should be inserted if any category does not match.");

        Category::where('restaurant_id', $restaurantId)->delete();
    }

    public function test_dish_insert_enforces_overall_restaurant_dish_limit()
    {
        $restaurantId = 8889;
        SubCategory::where('restaurant_id', $restaurantId)->delete();
        Category::where('restaurant_id', $restaurantId)->delete();

        $user = $this->getRestaurantUser($restaurantId);
        
        // Create plan with limit of 2 dishes total
        $plan = new \App\Models\Plan();
        $plan->name = 'Starter Test Plan';
        $plan->total_number_of_dishes = 2;
        $plan->duration_days = 30;
        $plan->price = 0;
        $plan->save();

        // Create active subscription for this restaurant
        $subscription = new \App\Models\Subscription();
        $subscription->user_id = $restaurantId;
        $subscription->plan_id = $plan->id;
        $subscription->status = 'active';
        $subscription->save();

        $cat1 = $this->createCategory('Starters', $restaurantId);
        $cat2 = $this->createCategory('Main Course', $restaurantId);

        // Dish 1 in Category 1
        $this->createSubCategory($cat1->id, 'Paneer Tikka', $restaurantId);
        // Dish 2 in Category 2
        $this->createSubCategory($cat2->id, 'Dal Makhani', $restaurantId);

        // Attempt to insert Dish 3 in Category 1 - Should be blocked by overall restaurant limit of 2
        $response = $this->actingAs($user)->post(route('manage.subcategory.category.insert'), [
            'category_id' => $cat1->id,
            'name' => 'Spring Rolls',
            'price' => 180,
            'food_type' => 'VEG',
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Dish limit reached', session('error'));

        // Clean up test plan & subscription & dishes
        SubCategory::where('restaurant_id', $restaurantId)->delete();
        Category::where('restaurant_id', $restaurantId)->delete();
        $subscription->delete();
        $plan->delete();
    }

    public function test_bulk_upload_respects_overall_restaurant_dish_limit()
    {
        $restaurantId = 8890;
        SubCategory::where('restaurant_id', $restaurantId)->delete();
        Category::where('restaurant_id', $restaurantId)->delete();

        $user = $this->getRestaurantUser($restaurantId);
        
        // Create plan with limit of 2 dishes total
        $plan = new \App\Models\Plan();
        $plan->name = 'Bulk Quota Test Plan';
        $plan->total_number_of_dishes = 2;
        $plan->duration_days = 30;
        $plan->price = 0;
        $plan->save();

        $subscription = new \App\Models\Subscription();
        $subscription->user_id = $restaurantId;
        $subscription->plan_id = $plan->id;
        $subscription->status = 'active';
        $subscription->save();

        $cat1 = $this->createCategory('Snacks', $restaurantId);

        // Pre-populate 1 dish
        $this->createSubCategory($cat1->id, 'Samosa', $restaurantId);

        // CSV with 3 items (Exceeds overall quota limit of 2, so should fail safely and not insert beyond limit)
        $csvContent = "Product Name,Price,Food Type,Category\n";
        $csvContent .= "Kachori,50,VEG,Snacks\n";
        $csvContent .= "Vada Pav,40,VEG,Snacks\n";
        $csvContent .= "Dhokla,60,VEG,Snacks\n";

        $file = UploadedFile::fake()->createWithContent('dishes_quota.csv', $csvContent);

        $response = $this->actingAs($user)->post(route('manage.subcategory.category.bulk.upload'), [
            'category_id' => $cat1->id,
            'bulk_file' => $file,
        ]);

        $response->assertSessionHas('error');

        // Total active dishes for this restaurant should still be 1
        $totalDishes = SubCategory::where('restaurant_id', $restaurantId)->where('status', '!=', 'D')->count();
        $this->assertEquals(1, $totalDishes);

        // Clean up
        SubCategory::where('restaurant_id', $restaurantId)->delete();
        Category::where('restaurant_id', $restaurantId)->delete();
        $subscription->delete();
        $plan->delete();
    }
}
