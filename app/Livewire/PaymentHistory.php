<?php

namespace App\Livewire;

use App\Models\Payment;
use Livewire\Component;

class PaymentHistory extends Component
{
    public $selectedPayment;

    public function showDetails($paymentId)
    {
        $this->selectedPayment = Payment::with('subscription.plan', 'subscription.planPrice')->findOrFail($paymentId);
    }

    public function render()
    {
        $payments = Payment::where('user_id', auth()->id())
            ->orderBy('payment_date', 'desc')
            ->get();

        return view('livewire.payment-history', [
            'payments' => $payments,
        ]);
    }
}
