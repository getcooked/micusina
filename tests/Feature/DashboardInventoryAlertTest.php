<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInventoryAlertTest extends TestCase
{
    use RefreshDatabase;

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
            ->get('/home')
            ->assertOk()
            ->assertSee('Inventory needs attention')
            ->assertSee('1 out of stock and 1 low-stock items')
            ->assertSee('Unavailable Meal: Out of stock')
            ->assertSee('Nearly Sold Out Meal: 3 left')
            ->assertSee('Review Inventory');
    }
}
