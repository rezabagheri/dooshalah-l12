<?php

namespace App\Livewire;

use App\Models\Plan;
use Livewire\Component;

class PlansUpgrade extends Component
{
    public function render()
    {
        $plans = Plan::with(['prices' => function ($query) {
            $query->where('is_active', true)
                  ->where('valid_from', '<=', now())
                  ->where(function ($query) {
                      $query->whereNull('valid_to')
                            ->orWhere('valid_to', '>=', now());
                  });
        }])
        ->where('name', '!=', 'Plan C') // حذف Plan C
        ->get();

        return view('livewire.plans-upgrade', [
            'plans' => $plans,
        ])->layout('layouts.app');
    }
}
