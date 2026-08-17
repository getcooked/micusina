<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_paid_sales_without_canceled_orders(): void
    {
        $admin = User::factory()->create(['usertype' => 'admin']);

        Order::create([
            'name' => 'Paid customer', 'title' => 'Chicken Meal', 'quantity' => 2,
            'price' => 240, 'delivery_status' => 'Delivered',
            'payment_status' => 'Paid', 'payment_method' => 'Cash on Delivery',
        ]);
        Order::create([
            'name' => 'Canceled customer', 'title' => 'Canceled Meal', 'quantity' => 1,
            'price' => 120, 'delivery_status' => 'Canceled', 'payment_status' => 'Paid',
        ]);

        $this->actingAs($admin)
            ->get('/sales-report')
            ->assertOk()
            ->assertSee('Sales Report')
            ->assertSee('Chicken Meal')
            ->assertDontSee('Canceled Meal')
            ->assertSee('240.00');
    }

    public function test_non_admin_cannot_view_sales_report(): void
    {
        $user = User::factory()->create(['usertype' => 'user']);

        $this->actingAs($user)->get('/sales-report')->assertForbidden();
    }

    public function test_admin_can_view_transaction_history(): void
    {
        $admin = User::factory()->create(['usertype' => 'admin']);

        Order::create([
            'name' => 'History customer', 'title' => 'Burger Meal', 'quantity' => 1,
            'price' => 120, 'delivery_status' => 'In Progress', 'payment_status' => 'Pending Verification',
        ]);

        $this->actingAs($admin)
            ->get('/transaction-history')
            ->assertOk()
            ->assertSee('Transaction History')
            ->assertSee('History customer')
            ->assertSee('Burger Meal x 1');
    }
}
