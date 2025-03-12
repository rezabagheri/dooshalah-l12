<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Payment Invoice - Successful Transaction',
            from: new \Illuminate\Mail\Mailables\Address('info@doosh-chat.maloons.com', 'Doosh Chat'),
        );
    }

    public function content(): Content
    {
        $subscription = $this->payment->subscription;

        return new Content(
            view: 'emails.payment-success',
            with: [
                'userName' => $this->payment->user->display_name,
                'plan' => $subscription->plan->name,
                'duration' => ucfirst(str_replace('_', ' ', $subscription->planPrice->duration)),
                'amount' => number_format($this->payment->amount, 2),
                'transactionId' => $this->payment->transaction_id,
                'paymentDate' => $this->payment->payment_date->format('Y-m-d H:i:s'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
