<?php

use App\Http\Middleware\FeatureMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\UpdateLastSeen;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Bootstrap the Laravel application.
 *
 * Configures the application's middleware, routing, and exception handling.
 *
 * @category Bootstrap
 * @package  App
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // اضافه کردن به گروه web
        $middleware->web(append: [
            UpdateLastSeen::class,
        ]);

        // ثبت Middlewareها به‌عنوان alias
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'feature' => FeatureMiddleware::class,
            'update.last.seen' => UpdateLastSeen::class, // اختیاری، برای استفاده دستی
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
