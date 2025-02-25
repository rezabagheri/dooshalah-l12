<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * Seeder for populating the plans table with sample data.
 *
 * This seeder creates initial subscription plans for the application.
 *
 * @category Database
 * @package  Seeders
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates sample plans (Plan A, Plan B, Plan C) with descriptions.
     *
     * @return void
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Plan A',
                'description' => 'The premium plan with full access to all features.',
            ],
            [
                'name' => 'Plan B',
                'description' => 'The standard plan with essential messaging and social features.',
            ],
            [
                'name' => 'Plan C',
                'description' => 'The basic plan with limited features for casual users.',
            ],
        ];

        foreach ($plans as $planData) {
            Plan::firstOrCreate(
                ['name' => $planData['name']],
                ['description' => $planData['description']]
            );
        }

        $this->command->info("PlanSeeder completed: " . count($plans) . " plans created.");
    }
}
