<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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

    public function build()
    {
        return $this->subject('Payment Failed - Action Required')
                    ->view('emails.payment-failed')
                    ->with([
                        'userName' => $this->payment->user->display_name,
                        'reason' => $this->reason,
                        'supportLink' => route('support'), // فرض بر اینکه صفحه پشتیبانی داری
                    ]);
    }
}
