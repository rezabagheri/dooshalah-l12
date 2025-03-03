<?php

namespace Database\Seeders;

use App\Enums\MessageStatus;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();

        foreach ($users as $sender) {
            // پیدا کردن کاربرانی که جنسیت مخالف دارن
            $receivers = User::where('gender', $sender->gender === 'male' ? 'female' : 'male')
                ->where('id', '!=', $sender->id)
                ->get();

            if ($receivers->isEmpty()) {
                continue; // اگه گیرنده‌ای نبود، رد می‌شیم
            }

            // 1. پیام‌های ارسال‌شده (1 تا 20)
            $sentCount = rand(1, 20);
            for ($i = 0; $i < $sentCount; $i++) {
                $receiver = $receivers->random();
                $message = Message::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'subject' => $faker->sentence(5),
                    'message' => $faker->paragraph(),
                    'sent_at' => $faker->dateTimeBetween('-1 month', 'now'),
                    'read_at' => $faker->boolean(70) ? $faker->dateTimeBetween('-1 month', 'now') : null,
                    'status' => MessageStatus::Sent->value,
                    'is_deleted' => false,
                    'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                    'updated_at' => $faker->dateTimeBetween('-1 month', 'now'),
                ]);

                // 3. اضافه کردن پاسخ (50% شانس)
                if ($faker->boolean(50)) {
                    $reply = Message::create([
                        'sender_id' => $receiver->id,
                        'receiver_id' => $sender->id,
                        'subject' => 'Re: ' . $message->subject,
                        'message' => $faker->paragraph(),
                        'sent_at' => $faker->dateTimeBetween($message->sent_at, 'now'),
                        'read_at' => $faker->boolean(50) ? $faker->dateTimeBetween($message->sent_at, 'now') : null,
                        'parent_id' => $message->id,
                        'status' => MessageStatus::Sent->value,
                        'is_deleted' => false,
                        'created_at' => $faker->dateTimeBetween($message->sent_at, 'now'),
                        'updated_at' => $faker->dateTimeBetween($message->sent_at, 'now'),
                    ]);

                    // پاسخ به پاسخ (30% شانس)
                    if ($faker->boolean(30)) {
                        Message::create([
                            'sender_id' => $sender->id,
                            'receiver_id' => $receiver->id,
                            'subject' => 'Re: ' . $reply->subject,
                            'message' => $faker->paragraph(),
                            'sent_at' => $faker->dateTimeBetween($reply->sent_at, 'now'),
                            'read_at' => $faker->boolean(30) ? $faker->dateTimeBetween($reply->sent_at, 'now') : null,
                            'parent_id' => $reply->id,
                            'status' => MessageStatus::Sent->value,
                            'is_deleted' => false,
                            'created_at' => $faker->dateTimeBetween($reply->sent_at, 'now'),
                            'updated_at' => $faker->dateTimeBetween($reply->sent_at, 'now'),
                        ]);
                    }
                }
            }

            // 2. پیام‌های Draft (0 تا 5)
            $draftCount = rand(0, 5);
            for ($i = 0; $i < $draftCount; $i++) {
                $receiver = $receivers->random();
                Message::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'subject' => $faker->sentence(5),
                    'message' => $faker->paragraph(),
                    'sent_at' => null,
                    'read_at' => null,
                    'status' => MessageStatus::Draft->value,
                    'is_deleted' => false,
                    'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                    'updated_at' => $faker->dateTimeBetween('-1 month', 'now'),
                ]);
            }
        }

        $this->command->info('Messages seeded successfully!');
    }
};
