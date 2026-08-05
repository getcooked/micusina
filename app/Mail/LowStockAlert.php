<?php

namespace App\Mail;

use App\Models\Food;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Food $food, public int $threshold)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Low stock alert: '.$this->food->title);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock-alert',
            with: [
                'food' => $this->food,
                'threshold' => $this->threshold,
            ],
        );
    }
}
