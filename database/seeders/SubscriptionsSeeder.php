<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeder for populating the subscriptions table with sample data.
 *
 * This seeder creates subscriptions for users with no overlapping time periods.
 *
 * @category Database
 * @package  Seeders
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class SubscriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates at least one subscription per user with random plans and durations.
     *
     * @return void
     */
    public function run(): void
    {
        $users = User::all();
        $plans = Plan::all();
        $progressBar = $this->command->getOutput()->createProgressBar($users->count());

        $this->command->info("\nSubscriptions seeding started!");

        foreach ($users as $user) {
            // برای هر کاربر حداقل یه اشتراک بسازیم
            $startDate = Carbon::now()->subMonths(rand(6, 12)); // شروع تصادفی توی 6-12 ماه گذشته
            $subscriptionsCount = rand(1, 3); // 1 تا 3 اشتراک برای هر کاربر

            for ($i = 0; $i < $subscriptionsCount; $i++) {
                $plan = $plans->random();
                $planPrice = PlanPrice::where('plan_id', $plan->id)->inRandomOrder()->first();

                $durationDays = match ($planPrice->duration) {
                    '1_month' => 30,
                    '3_months' => 90,
                    '6_months' => 180,
                    '1_year' => 365,
                };

                $endDate = $startDate->copy()->addDays($durationDays);
                $status = now()->greaterThan($endDate) ? 'expired' : 'active';

                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'plan_price_id' => $planPrice->id,
                    'amount' => $planPrice->price,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status,
                ]);

                // شروع اشتراک بعدی بعد از پایان اشتراک قبلی
                $startDate = $endDate->copy()->addDay();
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->info("\nSubscriptionsSeeder completed: Subscriptions created for " . $users->count() . " users.");
    }
}
