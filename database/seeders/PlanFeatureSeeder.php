<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanFeatureSeeder extends Seeder
{
    public function run(): void
    {
        // Get all plans
        $planA = Plan::where('name', 'Plan A')->first();
        $planB = Plan::where('name', 'Plan B')->first();
        $planC = Plan::where('name', 'Plan C')->first();

        // Define feature sets
        $allFeatures = Feature::pluck('id', 'name')->toArray();
        $basicFeatures = [
            'view_suggestions',
            'send_request',
            'accept_request',
            'remove_friend',
            'block_user',
            'unblock_user',
            'report_user',
            'send_message',
            'read_message',
            'message_inbox',
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
