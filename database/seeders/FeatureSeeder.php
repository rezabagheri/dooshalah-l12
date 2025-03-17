<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

/**
 * Seeder for populating the features table with sample data.
 *
 * This seeder creates initial features available in the application's subscription plans.
 *
 * @category Database
 * @package  Seeders
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates sample features with names and descriptions.
     *
     * @return void
     */
    public function run(): void
    {
        $features = [
            [
                'name' => 'view_suggestions',
                'description' => 'View friend or content suggestions based on user activity.',
            ],
            [
                'name' => 'send_request',
                'description' => 'Send friend requests to other users.',
            ],
            [
                'name' => 'accept_request',
                'description' => 'Accept friend requests from other users.',
            ],
            [
                'name' => 'remove_friend',
                'description' => 'Remove users from your friends list.',
            ],
            [
                'name' => 'block_user',
                'description' => 'Block other users from interacting with you.',
            ],
            [
                'name' => 'unblock_user',
                'description' => 'Unblock users to allow interaction again.',
            ],
            [
                'name' => 'report_user',
                'description' => 'Report users for inappropriate behavior.',
            ],
            [
                'name' => 'message_inbox',
                'description' => 'View Inbox.',
            ],
            [
                'name' => 'send_message',
                'description' => 'Send private messages to other users.',
            ],
            [
                'name' => 'read_message',
                'description' => 'Read private messages from other users.',
            ],
            [
                'name' => 'use_chat',
                'description' => 'Using real-time chat messages.',
            ],
            [
                'name' => 'send_chat_message',
                'description' => 'Send real-time chat messages to other users.',
            ],
            [
                'name' => 'read_chat_message',
                'description' => 'Read real-time chat messages from other users.',
            ],
            [
                'name' => 'view_profile',
                'description' => 'View the profiles of other users.',
            ],
            [
                'name' => 'edit_profile',
                'description' => 'Edit your own profile information and media.',
            ],
            [
                'name' => 'upload_media',
                'description' => 'Upload photos or other media to your profile.',
            ],
            [
                'name' => 'view_notifications',
                'description' => 'View notifications about friend requests, messages, etc.',
            ],
        ];

        foreach ($features as $featureData) {
            Feature::firstOrCreate(
                ['name' => $featureData['name']],
                ['description' => $featureData['description']]
            );
        }

        $this->command->info("FeatureSeeder completed: " . count($features) . " features created.");
    }
}
