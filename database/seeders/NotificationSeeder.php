<?php

namespace Database\Seeders;

use App\Enums\NotificationType;
use App\Models\Friendship;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();
        $friendships = Friendship::all();
        $payments = Payment::all();

        foreach ($friendships->take(3) as $friendship) {
            Notification::create([
                'user_id' => $friendship->target_id,
                'sender_id' => $friendship->user_id,
                'type' => NotificationType::FriendRequest->value,
                'title' => \Illuminate\Support\Str::limit(User::find($friendship->user_id)->display_name . ' sent you a friend request', 100, '...'),
                'content' => 'Click to view the request.',
                'action_url' => route('friends.received'),
                'is_read' => $faker->boolean(30),
                'read_at' => $faker->boolean(30) ? now()->subDays(rand(1, 5)) : null,
                'related_id' => $friendship->id,
                'related_type' => 'Friendship',
                'priority' => 2,
                'created_at' => now()->subDays(rand(1, 10)),
                'updated_at' => now()->subDays(rand(1, 10)),
            ]);
        }

        foreach ($friendships->where('status', 'accepted')->take(2) as $friendship) {
            Notification::create([
                'user_id' => $friendship->user_id,
                'sender_id' => $friendship->target_id,
                'type' => NotificationType::FriendAccepted->value,
                'title' => \Illuminate\Support\Str::limit(User::find($friendship->target_id)->display_name . ' accepted your friend request', 100, '...'),
                'content' => 'You are now friends!',
                'action_url' => route('friends.my-friends'),
                'is_read' => $faker->boolean(50),
                'read_at' => $faker->boolean(50) ? now()->subDays(rand(1, 3)) : null,
                'related_id' => $friendship->id,
                'related_type' => 'Friendship',
                'priority' => 2,
                'created_at' => now()->subDays(rand(1, 7)),
                'updated_at' => now()->subDays(rand(1, 7)),
            ]);
        }

        foreach ($payments->where('payment_status', 'paid')->take(2) as $payment) {
            $subscription = $payment->subscription;
            Notification::create([
                'user_id' => $payment->user_id,
                'type' => NotificationType::PaymentSuccess->value,
                'title' => \Illuminate\Support\Str::limit("Your payment for {$subscription->plan->name} ({$subscription->planPrice->duration}) was successful", 100, '...'),
                'content' => "Your payment for {$subscription->plan->name} ({$subscription->planPrice->duration}) was successful.",
                'action_url' => route('payments.history'),
                'is_read' => $faker->boolean(50),
                'read_at' => $faker->boolean(50) ? now()->subDays(rand(1, 2)) : null,
                'related_id' => $payment->id,
                'related_type' => 'Payment',
                'priority' => 2,
                'created_at' => $payment->payment_date,
                'updated_at' => $payment->payment_date,
            ]);
        }

        foreach ($payments->where('payment_status', 'failed')->take(2) as $payment) {
            Notification::create([
                'user_id' => $payment->user_id,
                'type' => NotificationType::PaymentFailed->value,
                'title' => \Illuminate\Support\Str::limit('Payment Failed', 100, '...'),
                'content' => 'Your payment attempt failed. Please try again.',
                'action_url' => route('plans.upgrade'),
                'is_read' => $faker->boolean(20),
                'read_at' => $faker->boolean(20) ? now()->subDays(rand(1, 5)) : null,
                'related_id' => $payment->id,
                'related_type' => 'Payment',
                'priority' => 3,
                'created_at' => $payment->payment_date,
                'updated_at' => $payment->payment_date,
            ]);
        }

        foreach ($users->take(3) as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => NotificationType::AdminMessage->value,
                'title' => \Illuminate\Support\Str::limit('Welcome to Your App!', 100, '...'),
                'content' => 'We’re excited to have you here. Check out our latest updates!',
                'action_url' => route('dashboard'),
                'is_read' => $faker->boolean(40),
                'read_at' => $faker->boolean(40) ? now()->subDays(rand(1, 5)) : null,
                'priority' => 2,
                'created_at' => now()->subDays(rand(1, 15)),
                'updated_at' => now()->subDays(rand(1, 15)),
            ]);
        }
    }
}
