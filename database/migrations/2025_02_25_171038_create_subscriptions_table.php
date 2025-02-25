<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the subscriptions table.
 *
 * This migration sets up the 'subscriptions' table to store user subscriptions to plans,
 * including the specific price purchased and subscription details.
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
     * Creates the 'subscriptions' table with columns for user subscriptions.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each subscription');
            $table->unsignedBigInteger('user_id')->comment('Foreign key referencing the user who subscribed');
            $table->unsignedBigInteger('plan_id')->index()->comment('Foreign key referencing the subscribed plan');
            $table->unsignedBigInteger('plan_price_id')->comment('Foreign key referencing the price of the plan at purchase');
            $table->decimal('amount', 10, 2)->comment('Amount paid for the subscription');
            $table->timestamp('start_date')->index()->comment('Timestamp when the subscription starts');
            $table->timestamp('end_date')->index()->comment('Timestamp when the subscription ends');
            $table->enum('status', ['active', 'expired', 'canceled'])->default('active')
                ->comment('Status of the subscription: active, expired, or canceled');
            $table->timestamps();

            $table->index(['user_id', 'status', 'end_date'], 'subscription_active_index')
                ->comment('Index for faster lookups of active subscriptions');

            // تعریف دستی کلیدهای خارجی با نام‌های منحصربه‌فرد
            $table->foreign('user_id', 'subscriptions_user_id_foreign')
                ->references('id')->on('users')->onDelete('cascade');
            $table->foreign('plan_id', 'subscriptions_plan_id_foreign')
                ->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('plan_price_id', 'subscriptions_plan_price_id_foreign')
                ->references('id')->on('plan_prices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'subscriptions' table if it exists.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
