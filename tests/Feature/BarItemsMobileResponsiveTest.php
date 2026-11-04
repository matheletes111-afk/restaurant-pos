<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BarItemsMobileResponsiveTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_bar_items_page_renders_with_responsive_classes()
    {
        $user = User::whereNotNull('restaurant_id')->first();
        if (!$user) {
            $user = User::factory()->create();
        }
        $user->role = 'SA'; // Super admin bypasses subscription check

        $response = $this->actingAs($user)->get('/admin/bar/items');
        $response->assertStatus(200);

        // Verify responsive layout elements exist
        $response->assertSee('bar-header-actions', false);
        $response->assertSee('bar-toolbar-container', false);
        $response->assertSee('bar-filter-chips-wrap', false);
        $response->assertSee('bar-toolbar-search', false);
        $response->assertSee('card-action-bar', false);
        $response->assertSee('@media (max-width: 768px)', false);
    }
}
