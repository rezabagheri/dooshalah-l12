<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    public function handle(Request $request, Closure $next, $feature): Response
    {
        $user = auth()->user();

        // چک کردن اینکه کاربر لاگین کرده یا نه
        if (!$user) {
            return redirect()->route('login');
        }

        // پیدا کردن پلن فعال کاربر
        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        // اگه پلن فعال نداره
        if (!$activeSubscription) {
            return redirect()->route('plans.upgrade')->with('error', "You need an active plan to access the '{$feature}' feature.");
        }

        // چک کردن دسترسی به فیچر
        $hasAccess = $activeSubscription->plan->features()
            ->where('name', $feature)
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('plans.upgrade')->with('error', "Your current plan does not include the '{$feature}' feature.");
        }

        return $next($request);
    }
}
