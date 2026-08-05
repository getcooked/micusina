<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class RegistrationOtpSender
{
    public function sendEmail(string $email, string $code): bool
    {
        try {
            Mail::raw(
                "Your Mi Cusina registration verification code is {$code}. It expires in 10 minutes. If you did not request this, ignore this email.",
                fn ($message) => $message->to($email)->subject('Mi Cusina registration verification')
            );

            return true;
        } catch (Throwable $exception) {
            Log::error('Registration verification email failed.', [
                'recipient' => $email,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function sendSms(string $phone, string $code): bool
    {
        $message = "Your Mi Cusina registration code is {$code}. It expires in 10 minutes. Do not share this code.";

        if (filled(config('services.infobip.base_url')) && filled(config('services.infobip.api_key'))) {
            return $this->sendInfobip($phone, $message);
        }

        return $this->sendTwilio($phone, $message);
    }

    private function sendInfobip(string $phone, string $message): bool
    {
        $baseUrl = rtrim((string) config('services.infobip.base_url'), '/');

        if (!str_starts_with($baseUrl, 'http://') && !str_starts_with($baseUrl, 'https://')) {
            $baseUrl = 'https://'.$baseUrl;
        }

        try {
            Http::withHeaders([
                'Authorization' => 'App '.config('services.infobip.api_key'),
                'Accept' => 'application/json',
            ])->post($baseUrl.'/sms/3/messages', [
                'messages' => [[
                    'sender' => config('services.infobip.from', 'ServiceSMS'),
                    'destinations' => [['to' => ltrim($phone, '+')]],
                    'content' => ['text' => $message],
                ]],
            ])->throw();

            return true;
        } catch (Throwable $exception) {
            Log::error('Registration SMS failed through Infobip.', [
                'recipient' => $phone,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function sendTwilio(string $phone, string $message): bool
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        if (!$sid || !$token || !$from) {
            Log::warning('Registration SMS skipped because no SMS provider is configured.');

            return false;
        }

        try {
            Http::asForm()
                ->withBasicAuth($sid, $token)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To' => $phone,
                    'Body' => $message,
                ])
                ->throw();

            return true;
        } catch (Throwable $exception) {
            Log::error('Registration SMS failed through Twilio.', [
                'recipient' => $phone,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
