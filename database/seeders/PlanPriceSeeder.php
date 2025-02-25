<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeder for populating the plan_prices table with sample data.
 *
 * This seeder creates pricing options for each plan with different durations.
 *
 * @category Database
 * @package  Seeders
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class PlanPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates pricing options (1 month, 3 months, 6 months, 1 year) for each plan.
     *
     * @return void
     */
    public function run(): void
    {
        $plans = Plan::all();
        $durations = [
            ['duration' => '1_month', 'days' => 30, 'price_factor' => 1],
            ['duration' => '3_months', 'days' => 90, 'price_factor' => 2.8],
            ['duration' => '6_months', 'days' => 180, 'price_factor' => 5.5],
            ['duration' => '1_year', 'days' => 365, 'price_factor' => 10],
        ];

        $basePrices = [
            'Plan A' => 39.99, // Base Price For Plan A
            'Plan B' => 29.00,  // Base Price For Plan B
            'Plan C' => 19.00,  // Base Price For  Plan C
        ];

        foreach ($plans as $plan) {
            foreach ($durations as $duration) {
                PlanPrice::firstOrCreate(
                    [
                        'plan_id' => $plan->id,
                        'duration' => $duration['duration'],
                    ],
                    [
                        'price' => $basePrices[$plan->name] * $duration['price_factor'],
                        'valid_from' => Carbon::now()->subMonth(),
                        'valid_to' => Carbon::now()->addYear(),
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info("PlanPriceSeeder completed: " . (count($plans) * count($durations)) . " plan prices created.");
    }
}
