<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInventoryAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_the_dashboard(): void
    {
        $admin = User::factory()->create(['usertype' => 'admin']);

        $this->actingAs($admin)
            ->get('/home')
            ->assertRedirect('/dashboard');
    }

    public function test_admin_dashboard_lists_out_of_stock_and_low_stock_items(): void
    {
        $admin = User::factory()->create(['usertype' => 'admin']);

        Food::withoutEvents(function (): void {
            Food::create([
                'title' => 'Unavailable Meal',
                'detail' => 'Test item',
                'price' => 100,
                'stock' => 0,
                'image' => 'unavailable.jpg',
            ]);

            Food::create([
                'title' => 'Nearly Sold Out Meal',
                'detail' => 'Test item',
                'price' => 100,
                'stock' => 3,
                'image' => 'low.jpg',
            ]);
        });

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSeeText('Low-stock alert')
            ->assertSeeText('Nearly Sold Out Meal: 3 left')
            ->assertSeeText('Out of Stock');
    }
}
