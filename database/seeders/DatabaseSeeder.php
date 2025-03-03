<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Main seeder for running all database seeds.
 *
 * @category Database
 * @package  Seeders
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            UserSeeder::class,
            MediaSeeder::class,
            PlanSeeder::class,
            PlanPriceSeeder::class,
            FeatureSeeder::class,
            PlanFeatureSeeder::class,
            SubscriptionsSeeder::class,
            PaymentSeeder::class,
            QuestionSeeder::class,
            UserAnswerSeeder::class,
            MenuSeeder::class,
            FriendshipSeeder::class,
            MessageSeeder::class,

        ]);
    }
}
