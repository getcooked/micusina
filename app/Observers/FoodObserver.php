<?php

namespace App\Observers;

use App\Models\Food;
use App\Services\LowStockNotifier;

class FoodObserver
{
    public function saved(Food $food): void
    {
        $threshold = (int) config('services.low_stock.threshold', 5);

        if ($food->stock > $threshold) {
            if (
                $food->low_stock_notified_at !== null
                || $food->low_stock_email_sent_at !== null
                || $food->low_stock_sms_sent_at !== null
            ) {
                $food->forceFill([
                    'low_stock_notified_at' => null,
                    'low_stock_email_sent_at' => null,
                    'low_stock_sms_sent_at' => null,
                ])->saveQuietly();
            }

            return;
        }

        $notifier = app(LowStockNotifier::class);
        $updates = [];

        if ($food->low_stock_email_sent_at === null && $notifier->sendEmail($food, $threshold)) {
            $updates['low_stock_email_sent_at'] = now();
        }

        if ($food->low_stock_sms_sent_at === null && $notifier->sendSmsFor($food, $threshold)) {
            $updates['low_stock_sms_sent_at'] = now();
        }

        $emailSent = $food->low_stock_email_sent_at !== null || isset($updates['low_stock_email_sent_at']);
        $smsSent = $food->low_stock_sms_sent_at !== null || isset($updates['low_stock_sms_sent_at']);

        if ($emailSent && $smsSent) {
            $updates['low_stock_notified_at'] = now();
        }

        if ($updates !== []) {
            $food->forceFill($updates)->saveQuietly();
        }
    }
}
