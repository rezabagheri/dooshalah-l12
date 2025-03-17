<?php

namespace App\Livewire;

use App\Enums\NotificationType;
use App\Mail\PaymentFailedMail;
use App\Mail\PaymentSuccessMail;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        if (!$this->payerId) {
            $this->paymentDetails = [
                'status' => 'canceled',
                'message' => 'Payment was canceled.',
                'reason' => 'You canceled the payment process.',
            ];
            $payment = Payment::find($this->paymentId);
            if ($payment) {
                try {
                    Mail::to($payment->user->email)->send(new PaymentFailedMail($payment, $this->paymentDetails['reason']));
                } catch (\Exception $e) {
                    Log::error('Failed to send payment failed email', [
                        'user_id' => $payment->user->id,
                        'email' => $payment->user->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            return;
        }

        $this->processPayment();
    }

    private function processPayment()
    {
        $provider = new PayPalClient();
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

            // ارسال ایمیل با try-catch
            try {
                Mail::to($payment->user->email)->send(new PaymentSuccessMail($payment));
            } catch (\Exception $e) {
                Log::error('Failed to send payment success email', [
                    'user_id' => $payment->user->id,
                    'email' => $payment->user->email,
                    'error' => $e->getMessage(),
                ]);
                // می‌تونیم یه پیام به paymentDetails اضافه کنیم
                $this->paymentDetails['email_error'] = 'We couldn’t send you a confirmation email. Please check your email address.';
            }

            Notification::create([
                'user_id' => auth()->user()->id,
                'type' => NotificationType::PaymentSuccess->value,
                'title' => \Illuminate\Support\Str::limit("Your payment for {$subscription->plan->name} ({$subscription->planPrice->duration}) was successful", 100, '...'),
                'content' => "Your payment for {$subscription->plan->name} ({$subscription->planPrice->duration}) was successful.",
                'action_url' => route('payments.history'),
                'related_id' => $payment->id,
                'related_type' => 'Payment',
                'priority' => 2,
            ]);
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

            try {
                Mail::to($payment->user->email)->send(new PaymentFailedMail($payment, $this->paymentDetails['reason']));
            } catch (\Exception $e) {
                Log::error('Failed to send payment failed email', [
                    'user_id' => $payment->user->id,
                    'email' => $payment->user->email,
                    'error' => $e->getMessage(),
                ]);
            }

            Notification::create([
                'user_id' => auth()->user()->id,
                'type' => NotificationType::PaymentFailed->value,
                'title' => \Illuminate\Support\Str::limit("Payment attempt failed: {$this->paymentDetails['reason']}", 100, '...'),
                'content' => "Your payment attempt failed: {$this->paymentDetails['reason']}",
                'action_url' => route('plans.upgrade'),
                'related_id' => $payment->id,
                'related_type' => 'Payment',
                'priority' => 3,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.payment-callback', [
            'paymentDetails' => $this->paymentDetails,
        ]);
    }
}
