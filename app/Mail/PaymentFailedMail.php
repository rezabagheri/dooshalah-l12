<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $reason;

    public function __construct(Payment $payment, $reason)
    {
        $this->payment = $payment;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Failed - Action Required',
            from: new \Illuminate\Mail\Mailables\Address('info@doosh-chat.maloons.com', 'Doosh Chat'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-failed',
            with: [
                'userName' => $this->payment->user->display_name,
                'reason' => $this->reason,
                'supportLink' => route('support'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
