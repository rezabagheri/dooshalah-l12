<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * The authentication and authorization service provider.
 *
 * Registers policies and defines custom gates for feature-based access control.
 *
 * @category Providers
 * @package  App\Providers
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Plan::class => \App\Policies\PlanPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * Defines gates for feature-specific access based on user subscriptions.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate عمومی برای چک کردن هر feature
        Gate::define('can-use-feature', function (User $user, string $feature) {
            if ($user->role === UserRole::SuperAdmin) {
                return true;
            }

            $subscription = $user->subscriptions()->where('status', 'active')
                ->where('end_date', '>=', now())->first();

            return $subscription && $subscription->plan->features->contains('name', $feature);
        });

        // Gateهای خاص برای هر feature
        $features = [
            'view_suggestions',
            'send_request',
            'accept_request',
            'remove_friend',
            'block_user',
            'report_user',
            'send_message',
            'read_message',
            'send_chat_message',
            'read_chat_message',
            'view_profile',
            'edit_profile',
            'upload_media',
            'view_notifications',
        ];

        foreach ($features as $feature) {
            Gate::define($feature, function (User $user) use ($feature) {
                return Gate::allows('can-use-feature', $feature);
            });
        }
    }
}
