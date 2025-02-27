<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Media;
use App\Models\UserMedia;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Seeder for populating the media and user_medias tables with sample data.
 */
class MediaSeeder extends Seeder
{
    private const SOURCE_IMAGE_PATH = 'images';
    private const DESTINATION_PATH = 'public/user-profiles';

    public function run(): void
    {
        $faker = Faker::create();
        $progressBar = $this->command->getOutput()->createProgressBar(User::count());

        if (!Storage::exists(self::DESTINATION_PATH)) {
            Storage::makeDirectory(self::DESTINATION_PATH);
            $this->command->info("Created directory: " . storage_path('app/' . self::DESTINATION_PATH));
        }

        $this->command->info("\nMedia seeding started!");

        User::with('media')->chunk(50, function ($users) use ($faker, $progressBar) {
            foreach ($users as $user) {
                // Skip if user already has media (including profile)
                if ($user->media->isNotEmpty()) {
                    $progressBar->advance();
                    continue;
                }

                $genderFolder = $user->gender === 'male' ? 'male' : 'female';
                $sourcePath = storage_path(self::SOURCE_IMAGE_PATH . '/' . $genderFolder);

                $images = collect(scandir($sourcePath))
                    ->filter(fn($file) => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                    ->values();

                if ($images->isEmpty()) {
                    $progressBar->advance();
                    continue;
                }

                // Select 5 to 10 images for the album
                $albumCount = rand(5, 10);
                $albumImages = $images->random(min($albumCount, $images->count()))->all();
                $albumMediaIds = [];

                foreach ($albumImages as $albumImage) {
                    $albumImageName = uniqid('album_') . '.' . pathinfo($albumImage, PATHINFO_EXTENSION);
                    $destinationFullPath = storage_path('app/' . self::DESTINATION_PATH . '/' . $albumImageName);

                    $destinationDir = dirname($destinationFullPath);
                    if (!file_exists($destinationDir)) {
                        mkdir($destinationDir, 0755, true);
                        $this->command->info("Created directory: {$destinationDir}");
                    }

                    if (!copy("{$sourcePath}/{$albumImage}", $destinationFullPath)) {
                        $this->command->error("Failed to copy {$albumImage} for user {$user->id}");
                        continue;
                    }

                    $media = Media::create([
                        'path' => 'user-profiles/' . $albumImageName, // فقط user-profiles/ و اسم فایل
                        'original_name' => $albumImage,
                        'type' => 'image',
                        'mime_type' => 'image/jpeg',
                        'size' => filesize($destinationFullPath),
                    ]);

                    UserMedia::create([
                        'user_id' => $user->id,
                        'media_id' => $media->id,
                        'is_profile' => false,
                        'is_approved' => $faker->boolean(70),
                        'order' => count($albumMediaIds),
                    ]);

                    $albumMediaIds[] = $media->id;
                }

                // Add profile picture only if none exists
                if (!empty($albumMediaIds) && !$user->media()->where('is_profile', true)->exists()) {
                    $profileMediaId = $faker->randomElement($albumMediaIds);
                    UserMedia::create([
                        'user_id' => $user->id,
                        'media_id' => $profileMediaId,
                        'is_profile' => true,
                        'is_approved' => true,
                        'order' => 0,
                    ]);
                }

                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->command->info("\nMedia seeding completed successfully!");
    }
}
