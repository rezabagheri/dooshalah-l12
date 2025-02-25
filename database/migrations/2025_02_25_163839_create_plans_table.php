<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the plans table.
 *
 * This migration sets up the 'plans' table to store subscription plans with their names and descriptions.
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
     * Creates the 'plans' table with columns for plan identification, name, and description.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each plan');
            $table->string('name')->index()->comment('Name of the plan (e.g., A, B, C)');
            $table->text('description')->nullable()->comment('Description of the plan (optional)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'plans' table if it exists.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
