<?php

namespace App\Traits;

use App\Models\Subscription;

trait HasFeatureAccess
{
    public function hasFeatureAccess($feature): bool
    {
        $activeSubscription = Subscription::where('user_id', auth()->id())
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        return $activeSubscription && $activeSubscription->plan->features()->where('name', $feature)->exists();
    }
}
