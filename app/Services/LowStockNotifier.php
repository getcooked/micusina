<?php

namespace App\Services;

use App\Mail\LowStockAlert;
use App\Models\Food;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class LowStockNotifier
{
    public function send(Food $food): bool
    {
        $threshold = (int) config('services.low_stock.threshold', 5);
        $message = sprintf(
            'Mi Cusina low stock: %s has %d item(s) left (threshold: %d). Please restock soon.',
            $food->title,
            $food->stock,
            $threshold,
        );

        $emailSent = $this->sendEmail($food, $threshold);
        $smsSent = $this->sendSms($message);

        return $emailSent && $smsSent;
    }

    public function sendEmail(Food $food, ?int $threshold = null): bool
    {
        $threshold ??= (int) config('services.low_stock.threshold', 5);
        $recipient = config('services.low_stock.email');

        if (!$recipient || config('mail.default') === 'log') {
            Log::warning('Low-stock email skipped because real email delivery is not configured.');

            return false;
        }

        try {
            Mail::to($recipient)->send(new LowStockAlert($food, $threshold));

            return true;
        } catch (Throwable $exception) {
            Log::error('Low-stock email could not be sent.', [
                'food_id' => $food->id,
                'recipient' => $recipient,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function sendSmsFor(Food $food, ?int $threshold = null): bool
    {
        $threshold ??= (int) config('services.low_stock.threshold', 5);

        return $this->sendSms(sprintf(
            'Mi Cusina low stock: %s has %d item(s) left (threshold: %d). Please restock soon.',
            $food->title,
            $food->stock,
            $threshold,
        ));
    }

    private function sendSms(string $message): bool
    {
        if (filled(config('services.infobip.base_url')) && filled(config('services.infobip.api_key'))) {
            return $this->sendInfobipSms($message);
        }

        return $this->sendTwilioSms($message);
    }

    private function sendInfobipSms(string $message): bool
    {
        $baseUrl = rtrim((string) config('services.infobip.base_url'), '/');
        $apiKey = config('services.infobip.api_key');
        $from = config('services.infobip.from', 'ServiceSMS');
        $to = config('services.low_stock.phone');

        if (!str_starts_with($baseUrl, 'http://') && !str_starts_with($baseUrl, 'https://')) {
            $baseUrl = 'https://'.$baseUrl;
        }

        try {
            Http::withHeaders([
                'Authorization' => 'App '.$apiKey,
                'Accept' => 'application/json',
            ])->post($baseUrl.'/sms/3/messages', [
                'messages' => [[
                    'sender' => $from,
                    'destinations' => [['to' => ltrim((string) $to, '+')]],
                    'content' => ['text' => $message],
                ]],
            ])->throw();

            return true;
        } catch (Throwable $exception) {
            Log::error('Low-stock SMS could not be sent through Infobip.', [
                'recipient' => $to,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function sendTwilioSms(string $message): bool
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');
        $to = config('services.low_stock.phone');

        if (!$sid || !$token || !$from || !$to) {
            Log::warning('Low-stock SMS skipped because Twilio is not configured.');

            return false;
        }

        try {
            Http::asForm()
                ->withBasicAuth($sid, $token)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To' => $to,
                    'Body' => $message,
                ])
                ->throw();

            return true;
        } catch (Throwable $exception) {
            Log::error('Low-stock SMS could not be sent.', [
                'recipient' => $to,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
