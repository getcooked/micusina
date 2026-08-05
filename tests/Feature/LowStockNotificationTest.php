<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Services\LowStockNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class LowStockNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_alerts_once_until_the_item_is_restocked(): void
    {
        $this->mock(LowStockNotifier::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendEmail')->twice()->andReturnTrue();
            $mock->shouldReceive('sendSmsFor')->twice()->andReturnTrue();
        });

        $food = new Food;
        $food->title = 'Chicken Adobo';
        $food->detail = 'Test item';
        $food->price = 100;
        $food->stock = 10;
        $food->image = 'adobo.jpg';
        $food->save();

        $food->update(['stock' => 5]);
        $this->assertNotNull($food->fresh()->low_stock_notified_at);
        $this->assertNotNull($food->fresh()->low_stock_email_sent_at);
        $this->assertNotNull($food->fresh()->low_stock_sms_sent_at);

        $food->update(['stock' => 4]);

        $food->update(['stock' => 8]);
        $this->assertNull($food->fresh()->low_stock_notified_at);
        $this->assertNull($food->fresh()->low_stock_email_sent_at);
        $this->assertNull($food->fresh()->low_stock_sms_sent_at);

        $food->update(['stock' => 5]);
        $this->assertNotNull($food->fresh()->low_stock_notified_at);
    }
}
