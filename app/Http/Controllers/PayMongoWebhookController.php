<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\PayMongoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PayMongoWebhookController extends Controller
{
    public function complete(Book $booking, PayMongoService $payMongo): RedirectResponse
    {
        abort_unless(auth()->id() === $booking->user_id, 403);

        if ($booking->payment_status !== 'Paid' && $booking->paymongo_checkout_id) {
            $checkout = $payMongo->retrieveCheckout($booking->paymongo_checkout_id);
            $payment = $payMongo->paidPayment($checkout);

            if ($payment) {
                $this->markPaid($booking, $payment);
            }
        }

        if ($booking->fresh()->payment_status !== 'Paid') {
            return redirect('/?section=book')->withErrors([
                'payment' => 'Payment has not been confirmed by PayMongo yet. Please do not submit another payment.',
            ]);
        }

        return redirect('/?section=book')
            ->with('message', 'Payment confirmed. Your reservation was sent to admin/staff records.')
            ->with('booking_receipt', $this->receipt($booking->fresh()));
    }

    public function cancel(Book $booking): RedirectResponse
    {
        abort_unless(auth()->id() === $booking->user_id, 403);

        return redirect('/?section=book')->withErrors([
            'payment' => 'Payment was cancelled. Your booking has not been confirmed.',
        ]);
    }

    public function webhook(Request $request, PayMongoService $payMongo): JsonResponse
    {
        $payload = $request->getContent();

        if (!$payMongo->validWebhookSignature($payload, $request->header('Paymongo-Signature'))) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $event = json_decode($payload, true);
        $eventType = data_get($event, 'data.attributes.type');
        $payment = data_get($event, 'data.attributes.data');

        if (in_array($eventType, ['payment.paid', 'checkout_session.payment.paid'], true)) {
            $checkoutId = data_get($payment, 'attributes.checkout_session_id');
            $reference = data_get($payment, 'attributes.external_reference_number')
                ?? data_get($payment, 'attributes.reference_number');

            $booking = $checkoutId ? Book::where('paymongo_checkout_id', $checkoutId)->first() : null;
            if (!$booking && is_string($reference) && preg_match('/^BK-0*(\d+)$/', $reference, $matches)) {
                $booking = Book::find((int) $matches[1]);
            }

            if ($booking) {
                $this->markPaid($booking, $payment);
            }
        }

        return response()->json(['received' => true]);
    }

    private function markPaid(Book $booking, array $payment): void
    {
        $status = data_get($payment, 'attributes.status');
        $amount = (int) data_get($payment, 'attributes.amount', 0);
        $expected = (int) round(((float) $booking->deposit_amount) * 100);

        if ($status !== 'paid' || $amount !== $expected || data_get($payment, 'attributes.currency') !== 'PHP') {
            return;
        }

        $booking->payment_status = 'Paid';
        $booking->paymongo_payment_id = data_get($payment, 'id');
        $booking->paid_at = now();
        $booking->status = 'Pending';
        $booking->save();
    }

    private function receipt(Book $booking): array
    {
        return [
            'reference' => $booking->gcash_reference,
            'name' => $booking->name,
            'guests' => (int) $booking->guest,
            'date' => $booking->date,
            'time' => $booking->time,
            'payment_method' => $booking->payment_method,
            'payment_reference' => $booking->paymongo_payment_id,
            'total' => (float) $booking->reservation_price,
            'deposit' => (float) $booking->deposit_amount,
            'balance' => (float) $booking->reservation_price - (float) $booking->deposit_amount,
        ];
    }
}
