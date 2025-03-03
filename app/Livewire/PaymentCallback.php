<?php

namespace App\Livewire;

use App\Models\Payment;
use Livewire\Component;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentCallback extends Component
{
    public $paymentId;
    public $payerId;
    public $paymentDetails;

    public function mount()
    {
        $this->paymentId = request()->route('payment_id');
        $this->payerId = request('PayerID');

        // اگه PayerID نباشه، یعنی لغو شده
        if (!$this->payerId) {
            $this->paymentDetails = [
                'status' => 'canceled',
                'message' => 'Payment was canceled.',
                'reason' => 'You canceled the payment process.',
            ];
            return;
        }

        $this->processPayment();
    }

    private function processPayment()
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $payment = Payment::findOrFail($this->paymentId);
        $response = $provider->capturePaymentOrder($payment->transaction_id);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $payment->update([
                'payment_status' => 'paid',
                'gateway_response' => json_encode($response),
            ]);

            $subscription = $payment->subscription;
            $subscription->update(['status' => 'active']);

            $this->paymentDetails = [
                'status' => 'success',
                'message' => 'Payment completed successfully!',
                'plan' => $subscription->plan->name,
                'duration' => $subscription->planPrice->duration,
                'amount' => $payment->amount,
                'transaction_id' => $payment->transaction_id,
                'payment_date' => $payment->payment_date->format('Y-m-d H:i:s'),
            ];
        } else {
            $payment->update([
                'payment_status' => 'failed',
                'gateway_response' => json_encode($response),
            ]);

            $this->paymentDetails = [
                'status' => 'failed',
                'message' => 'Payment failed.',
                'reason' => $response['error']['message'] ?? 'An unknown error occurred during payment processing.',
            ];
        }
    }

    public function render()
    {
        return view('livewire.payment-callback', [
            'paymentDetails' => $this->paymentDetails,
        ]);
    }
}
