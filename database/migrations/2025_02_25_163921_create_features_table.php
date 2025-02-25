<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the features and plan_features tables.
 *
 * This migration sets up the 'features' table to store available features and the 'plan_features'
 * pivot table to associate features with subscription plans in the application.
 *
 * @category Database
 * @package  Migrations
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the 'features' table for feature definitions and the 'plan_features' table
     * for associating features with plans.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each feature');
            $table->string('name')->unique()->index()->comment('Name of the feature (e.g., "friend_request", "messaging", "online_chat")');
            $table->string('description')->nullable()->comment('Description of the feature (optional)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
