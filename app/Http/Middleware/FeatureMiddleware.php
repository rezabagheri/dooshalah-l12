<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to restrict access based on subscription plan features.
 *
 * This middleware checks if the authenticated user's active subscription includes the specified feature.
 *
 * @category Middleware
 * @package  App\Http\Middleware
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class FeatureMiddleware
{
    /**
     * Handle an incoming request and check feature access.
     *
     * @param  Request  $request The incoming HTTP request.
     * @param  Closure  $next The next middleware or request handler.
     * @param  string  $feature The feature required to access the route (e.g., 'send_request').
     * @return Response The HTTP response, either proceeding or denying access.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Authentication required.');
        }

        // SuperAdmin همه‌ی امکانات رو داره
        if ($user->role === UserRole::SuperAdmin) {
            return $next($request);
        }

        $subscription = $user->subscriptions()->where('status', 'active')
            ->where('end_date', '>=', now())->first();

        if (!$subscription || !$subscription->plan->features->contains('name', $feature)) {
            abort(403, "Your plan does not include the '$feature' feature.");
        }

        return $next($request);
    }
}
