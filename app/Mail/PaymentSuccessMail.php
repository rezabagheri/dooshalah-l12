<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        $subscription = $this->payment->subscription;

        return $this->subject('Your Payment Invoice - Successful Transaction')
                    ->view('emails.payment-success')
                    ->with([
                        'userName' => $this->payment->user->display_name,
                        'plan' => $subscription->plan->name,
                        'duration' => ucfirst(str_replace('_', ' ', $subscription->planPrice->duration)),
                        'amount' => number_format($this->payment->amount, 2),
                        'transactionId' => $this->payment->transaction_id,
                        'paymentDate' => $this->payment->payment_date->format('Y-m-d H:i:s'),
                    ]);
    }
}
