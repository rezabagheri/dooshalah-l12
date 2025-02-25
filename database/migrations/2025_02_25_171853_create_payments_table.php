<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the payments table.
 *
 * This migration sets up the 'payments' table to store payment records for user subscriptions,
 * including payment methods, amounts, and statuses.
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
     * Creates the 'payments' table with columns for payment details and relationships.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each payment');
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade')
                ->comment('Foreign key referencing the subscription being paid for');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')
                ->comment('Foreign key referencing the user who made the payment');
            $table->timestamp('payment_date')->comment('Timestamp when the payment was made');
            $table->decimal('amount', 10, 2)->comment('Payment amount (up to 10 digits, 2 decimal places)');
            $table->enum('payment_method', ['paypal', 'credit_card', 'bank_transfer'])
                ->comment('Method of payment: PayPal, credit card, or bank transfer');
            $table->enum('payment_status', ['paid', 'unpaid', 'pending', 'failed'])->index()
                ->comment('Status of the payment: paid, unpaid, pending, or failed');
            $table->string('transaction_id')->nullable()
                ->comment('Transaction ID from the payment gateway (e.g., PayPal Transaction ID)');
            $table->string('receipt_number')->nullable()->comment('Receipt number for the payment (optional)');
            $table->string('invoice_link')->nullable()->comment('Link to the invoice (optional)');
            $table->json('gateway_response')->nullable()->comment('Raw response from the payment gateway');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'payments' table if it exists.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
