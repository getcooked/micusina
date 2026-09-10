<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use App\Services\PayMongoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use RuntimeException;
use Tests\TestCase;

class PayMongoCallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_uses_temporary_signed_return_urls(): void
    {
        config([
            'services.paymongo.base_url' => 'https://api.paymongo.test/v1',
            'services.paymongo.secret_key' => 'sk_test_example',
        ]);
        Http::fake([
            'https://api.paymongo.test/v1/checkout_sessions' => Http::response([
                'data' => ['id' => 'cs_test_signed_callbacks'],
            ], 201),
        ]);
        $booking = $this->booking();

        app(PayMongoService::class)->createCheckout($booking);

        $recorded = Http::recorded()->first();
        $this->assertNotNull($recorded);
        $attributes = $recorded[0]->data()['data']['attributes'];
        $successUrl = $attributes['success_url'];
        $cancelUrl = $attributes['cancel_url'];

        $this->assertTrue(URL::hasValidSignature(Request::create($successUrl)));
        $this->assertTrue(URL::hasValidSignature(Request::create($cancelUrl)));

        parse_str((string) parse_url($successUrl, PHP_URL_QUERY), $successQuery);
        parse_str((string) parse_url($cancelUrl, PHP_URL_QUERY), $cancelQuery);
        $this->assertSame($successQuery['expires'], $cancelQuery['expires']);
        $this->assertGreaterThan(now()->timestamp, (int) $successQuery['expires']);
        $this->assertLessThanOrEqual(now()->addDay()->timestamp, (int) $successQuery['expires']);
    }

    public function test_web_booking_persists_the_checkout_url_for_resumption(): void
    {
        $user = User::factory()->create();
        $checkoutUrl = 'https://checkout.paymongo.test/cs_web_resume';
        $payMongo = $this->mock(PayMongoService::class);
        $payMongo->shouldReceive('createCheckout')
            ->once()
            ->withArgs(fn (Book $booking) => $booking->user_id === $user->id)
            ->andReturn([
                'id' => 'cs_web_resume',
                'attributes' => ['checkout_url' => $checkoutUrl],
            ]);

        $response = $this->actingAs($user)->post('/book_table', [
            'first_name' => 'Web',
            'last_name' => 'Customer',
            'email' => $user->email,
            'phone' => '09171234567',
            'n_guest' => 2,
            'date' => now()->addDays(2)->toDateString(),
            'time' => '6:00 PM',
            'payment_method' => 'GCash',
        ]);

        $response->assertRedirect($checkoutUrl);
        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'paymongo_checkout_id' => 'cs_web_resume',
            'paymongo_checkout_url' => $checkoutUrl,
            'payment_status' => 'Pending',
            'status' => 'Awaiting Payment',
        ]);
    }

    public function test_guest_can_complete_payment_with_a_valid_signed_url(): void
    {
        $booking = $this->booking(['paymongo_checkout_id' => 'cs_test_paid']);
        $payMongo = $this->mock(PayMongoService::class);
        $payMongo->shouldReceive('retrieveCheckout')
            ->once()
            ->with('cs_test_paid')
            ->andReturn(['id' => 'cs_test_paid']);
        $payMongo->shouldReceive('paidPayment')
            ->once()
            ->andReturn([
                'id' => 'pay_test_paid',
                'attributes' => [
                    'status' => 'paid',
                    'amount' => 12500,
                    'currency' => 'PHP',
                ],
            ]);

        $response = $this->get($this->signedUrl('booking.payment.return', $booking));

        $this->assertGuest();
        $response->assertRedirect('/?section=book')
            ->assertSessionHas('message')
            ->assertSessionHas('booking_receipt');
        $this->assertDatabaseHas('books', [
            'id' => $booking->id,
            'payment_status' => 'Paid',
            'paymongo_payment_id' => 'pay_test_paid',
            'status' => 'Pending',
        ]);
    }

    public function test_guest_can_follow_a_valid_signed_cancel_url(): void
    {
        $booking = $this->booking();

        $response = $this->get($this->signedUrl('booking.payment.cancel', $booking));

        $this->assertGuest();
        $response->assertRedirect('/?section=book')
            ->assertSessionHasErrors([
                'payment' => 'Payment was cancelled. Your booking has not been confirmed.',
            ]);
    }

    public function test_unsigned_expired_and_tampered_callback_urls_are_rejected(): void
    {
        $booking = $this->booking();
        $otherBooking = $this->booking();

        $this->get(route('booking.payment.return', $booking))->assertForbidden();
        $this->get(URL::temporarySignedRoute(
            'booking.payment.return',
            now()->subMinute(),
            ['booking' => $booking],
        ))->assertForbidden();

        $validUrl = $this->signedUrl('booking.payment.return', $booking);
        $tamperedUrl = preg_replace(
            '#/booking/payment/return/'.$booking->id.'(?=\?)#',
            '/booking/payment/return/'.$otherBooking->id,
            $validUrl,
        );
        $this->assertIsString($tamperedUrl);
        $this->get($tamperedUrl)->assertForbidden();
    }

    public function test_verification_outage_returns_a_safe_browser_fallback(): void
    {
        $booking = $this->booking(['paymongo_checkout_id' => 'cs_test_unavailable']);
        $payMongo = $this->mock(PayMongoService::class);
        $payMongo->shouldReceive('retrieveCheckout')
            ->once()
            ->andThrow(new RuntimeException('Provider unavailable'));

        $response = $this->get($this->signedUrl('booking.payment.return', $booking));

        $response->assertRedirect('/?section=book')
            ->assertSessionHasErrors([
                'payment' => 'We could not verify the payment right now. Your reservation will update automatically once PayMongo confirms it.',
            ]);
        $this->assertDatabaseHas('books', [
            'id' => $booking->id,
            'payment_status' => 'Pending',
            'status' => 'Awaiting Payment',
        ]);
    }

    private function signedUrl(string $route, Book $booking): string
    {
        return URL::temporarySignedRoute($route, now()->addMinutes(10), ['booking' => $booking]);
    }

    private function booking(array $overrides = []): Book
    {
        $user = User::factory()->create();

        return Book::create(array_merge([
            'user_id' => $user->id,
            'first_name' => 'Mobile',
            'last_name' => 'Customer',
            'name' => 'Mobile Customer',
            'email' => $user->email,
            'phone' => '09171234567',
            'guest' => 2,
            'date' => now()->addDays(2)->toDateString(),
            'time' => '6:00 PM',
            'reservation_price' => 250,
            'deposit_amount' => 125,
            'payment_method' => 'GCash',
            'gcash_reference' => 'BK-TEST',
            'payment_status' => 'Pending',
            'status' => 'Awaiting Payment',
        ], $overrides));
    }
}
