<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the plan_features table.
 *
 * This migration sets up the 'plan_features' pivot table to associate features with subscription plans.
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
     * Creates the 'plan_features' table with unique foreign key constraints.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each plan-feature relationship');
            $table->unsignedBigInteger('plan_id')->index()->comment('Foreign key referencing the plans table');
            $table->unsignedBigInteger('feature_id')->index()->comment('Foreign key referencing the features table');
            $table->timestamps();
            $table->unique(['plan_id', 'feature_id'], 'plan_feature_unique')
                ->comment('Ensures no duplicate feature assignments per plan');

            // تعریف دستی کلیدهای خارجی با نام‌های منحصربه‌فرد
            $table->foreign('plan_id', 'plan_features_plan_id_foreign')
                ->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('feature_id', 'plan_features_feature_id_foreign')
                ->references('id')->on('features')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'plan_features' table if it exists.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};
