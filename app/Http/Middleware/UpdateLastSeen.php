<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_seen = now();
            $user->saveQuietly(); // بدون تریگر کردن eventها ذخیره می‌کنه
        }

        return $next($request);
    }
}
