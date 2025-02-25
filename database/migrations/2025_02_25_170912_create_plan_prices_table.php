<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the plan_prices table.
 *
 * This migration sets up the 'plan_prices' table to store pricing details for subscription plans,
 * including duration, price, and validity periods.
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
     * Creates the 'plan_prices' table with columns for plan pricing and validity.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each plan price');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade')->comment('Foreign key referencing the plans table');
            $table->enum('duration', ['1_month', '3_months', '6_months', '1_year'])->index()->comment('Duration of the plan: 1 month, 3 months, 6 months, or 1 year');
            $table->decimal('price', 10, 2)->comment('Price of the plan (up to 10 digits, 2 decimal places)');
            $table->timestamp('valid_from')->comment('Timestamp when the price becomes valid');
            $table->timestamp('valid_to')->nullable()->comment('Timestamp when the price expires (optional)');
            $table->boolean('is_active')->default(true)->comment('Indicates if the price is currently active');
            $table->timestamps();

            $table->index(['plan_id', 'duration'], 'plan_prices_plan_duration_index')->comment('Index for faster lookups by plan and duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'plan_prices' table if it exists.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};
