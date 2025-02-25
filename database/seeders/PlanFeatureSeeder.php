<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * Seeder for populating the plan_features table with sample data.
 *
 * This seeder assigns features to subscription plans based on their access levels.
 *
 * @category Database
 * @package  Seeders
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class PlanFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Assigns features to Plan A, Plan B, and Plan C according to predefined access levels.
     *
     * @return void
     */
    public function run(): void
    {
        // Get all plans
        $planA = Plan::where('name', 'Plan A')->first();
        $planB = Plan::where('name', 'Plan B')->first();
        $planC = Plan::where('name', 'Plan C')->first();

        // Define feature sets
        $allFeatures = Feature::pluck('id', 'name')->toArray(); // همه‌ی امکانات با نام و ID
        $basicFeatures = [
            'view_suggestions',
            'send_request',
            'accept_request',
            'remove_friend',
            'block_user',
            'report_user',
            'send_message',
            'read_message',
        ];
        $viewSuggestionsOnly = ['view_suggestions'];

        // Assign features to Plan C (view_suggestions only)
        $planC->features()->sync(array_map(fn($name) => $allFeatures[$name], $viewSuggestionsOnly));
        $this->command->info("Assigned " . count($viewSuggestionsOnly) . " features to Plan C.");

        // Assign features to Plan B (basic features)
        $planB->features()->sync(array_map(fn($name) => $allFeatures[$name], $basicFeatures));
        $this->command->info("Assigned " . count($basicFeatures) . " features to Plan B.");

        // Assign features to Plan A (all features)
        $planA->features()->sync($allFeatures);
        $this->command->info("Assigned " . count($allFeatures) . " features to Plan A.");
    }
}
