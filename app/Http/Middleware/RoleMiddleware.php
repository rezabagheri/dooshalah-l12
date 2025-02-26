<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to restrict access based on user roles.
 *
 * This middleware checks if the authenticated user has one of the specified roles.
 *
 * @category Middleware
 * @package  App\Http\Middleware
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request and check user roles.
     *
     * @param  Request  $request The incoming HTTP request.
     * @param  Closure  $next The next middleware or request handler.
     * @param  string[]  ...$roles The roles allowed to access the route (e.g., 'admin', 'super_admin').
     * @return Response The HTTP response, either proceeding or denying access.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Authentication required.');
        }

        if (!in_array($user->role->value, $roles)) {
            abort(403, 'You do not have the required role to perform this action.');
        }

        return $next($request);
    }
}
