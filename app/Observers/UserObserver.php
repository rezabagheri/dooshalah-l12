<?php

namespace App\Observers;

use App\Enums\FriendshipStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        $planC = Plan::where('name', 'Plan C')->first();
        $planCPrice = $planC->prices()->where('duration', '1_month')
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('valid_to')
                      ->orWhere('valid_to', '>=', now());
            })
            ->first();

        if ($planC && $planCPrice) {
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $planC->id,
                'plan_price_id' => $planCPrice->id,
                'amount' => $planCPrice->price,
                'start_date' => now(),
                'end_date' => now()->addMonth(), // 1 ماه پیش‌فرض
                'status' => 'active',
            ]);
        }
    }
}
