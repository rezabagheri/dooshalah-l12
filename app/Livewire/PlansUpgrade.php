<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Subscription;
use Livewire\Component;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PlansUpgrade extends Component
{
    public $selectedPlanId;
    public $selectedDuration = '1_month';
    public $prices = [];

    public function mount()
    {
        $this->loadPrices();
    }

    public function updatedSelectedPlanId($value)
    {
        $this->loadPrices();
    }

    public function updatedSelectedDuration($value)
    {
        $this->loadPrices();
    }

    private function loadPrices()
    {
        $plans = $this->getPlans();
        foreach ($plans as $plan) {
            $price = PlanPrice::where('plan_id', $plan->id)
                ->where('duration', $this->selectedDuration)
                ->where('is_active', true)
                ->where('valid_from', '<=', now())
                ->where(function ($query) {
                    $query->whereNull('valid_to')
                          ->orWhere('valid_to', '>=', now());
                })
                ->first();

            $this->prices[$plan->id] = $price ? $price->price : 0;
        }
    }

    public function checkout()
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $plan = Plan::findOrFail($this->selectedPlanId);
        $price = PlanPrice::where('plan_id', $this->selectedPlanId)
            ->where('duration', $this->selectedDuration)
            ->firstOrFail();

        $subscription = Subscription::create([
            'user_id' => auth()->id(),
            'plan_id' => $plan->id,
            'plan_price_id' => $price->id,
            'amount' => $price->price,
            'start_date' => now(),
            'end_date' => now()->addMonths(match($this->selectedDuration) {
                '1_month' => 1,
                '3_months' => 3,
                '6_months' => 6,
                '1_year' => 12,
            }),
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'user_id' => auth()->id(),
            'payment_date' => now(),
            'amount' => $price->price,
            'payment_method' => 'paypal',
            'payment_status' => 'pending',
        ]);

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $price->price,
                    ],
                    "description" => "Subscription to {$plan->name} for {$this->selectedDuration}",
                ]
            ],
            "application_context" => [
                "return_url" => route('plans.payment.callback', ['payment_id' => $payment->id]),
                "cancel_url" => route('plans.payment.cancel', ['payment_id' => $payment->id]),
            ]
        ]);

        if (isset($response['id']) && $response['status'] == 'CREATED') {
            $payment->update(['transaction_id' => $response['id']]);
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        $this->dispatch('error', 'Unable to initiate payment.');
    }

    private function getPlans()
    {
        return Plan::with(['prices' => function ($query) {
            $query->where('is_active', true)
                  ->where('valid_from', '<=', now())
                  ->where(function ($query) {
                      $query->whereNull('valid_to')
                            ->orWhere('valid_to', '>=', now());
                  });
        }])
        ->where('name', '!=', 'Plan C')
        ->get();
    }

    public function render()
    {
        $this->loadPrices();
        return view('livewire.plans-upgrade', [
            'plans' => $this->getPlans(),
        ]);
    }
}
