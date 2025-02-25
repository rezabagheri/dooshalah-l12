<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Subscription;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

/**
 * Seeder for populating the payments table with sample data.
 *
 * This seeder creates payments for each subscription, including successful and failed payments with realistic gateway responses.
 *
 * @category Database
 * @package  Seeders
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates one payment per subscription with a mix of statuses and PayPal-like responses.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create();
        $subscriptions = Subscription::all();
        $progressBar = $this->command->getOutput()->createProgressBar($subscriptions->count());

        $this->command->info("\nPayments seeding started!");

        foreach ($subscriptions as $subscription) {
            $status = $faker->randomElement(['paid', 'pending', 'failed']); // ترکیبی از وضعیت‌ها
            $paymentDate = $subscription->start_date->copy()->addHours(rand(1, 24)); // پرداخت نزدیک شروع اشتراک
            $transactionId = $status === 'paid' || $status === 'pending' ? $faker->uuid : null;

            $gatewayResponse = match ($status) {
                'paid' => [
                    'id' => $transactionId,
                    'status' => 'COMPLETED',
                    'amount' => [
                        'value' => (string) $subscription->amount,
                        'currency_code' => 'USD',
                    ],
                    'payer' => [
                        'email_address' => $faker->email,
                        'payer_id' => $faker->uuid,
                    ],
                    'create_time' => $paymentDate->toIso8601String(),
                    'update_time' => $paymentDate->toIso8601String(),
                ],
                'pending' => [
                    'id' => $transactionId,
                    'status' => 'PENDING',
                    'amount' => [
                        'value' => (string) $subscription->amount,
                        'currency_code' => 'USD',
                    ],
                    'reason' => 'PAYER_ACTION_REQUIRED',
                    'create_time' => $paymentDate->toIso8601String(),
                ],
                'failed' => [
                    'status' => 'FAILED',
                    'error' => [
                        'name' => 'PAYMENT_DENIED',
                        'message' => 'The payment was denied by the payer’s bank.',
                        'debug_id' => $faker->uuid,
                    ],
                    'create_time' => $paymentDate->toIso8601String(),
                ],
            };

            Payment::create([
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'payment_date' => $paymentDate,
                'amount' => $subscription->amount,
                'payment_method' => $faker->randomElement(['paypal', 'credit_card', 'bank_transfer']),
                'payment_status' => $status,
                'transaction_id' => $transactionId,
                'receipt_number' => $faker->boolean(70) ? 'REC-' . $faker->numberBetween(1000, 9999) : null,
                'invoice_link' => $faker->boolean(50) ? $faker->url : null,
                'gateway_response' => json_encode($gatewayResponse),
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->info("\nPaymentSeeder completed: Payments created for " . $subscriptions->count() . " subscriptions.");
    }
}
