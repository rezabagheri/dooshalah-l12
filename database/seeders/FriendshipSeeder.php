<?php

namespace Database\Seeders;

use App\Enums\FriendshipStatus;
use App\Enums\Gender;
use App\Enums\Severity;
use App\Models\Block;
use App\Models\Friendship;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class FriendshipSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        $output = new ConsoleOutput();
        $progressBar = new ProgressBar($output, $users->count());
        $progressBar->start();

        DB::transaction(function () use ($users, $progressBar) {
            foreach ($users as $user) {
                $this->seedFriendships($user);
                $this->seedBlocks($user);
                $this->seedReports($user);
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $output->writeln("\nFriendships, blocks, and reports seeded successfully.");
    }

    private function seedFriendships(User $user): void
    {
        $friendCount = rand(5, 15);
        $potentialFriends = User::where('id', '!=', $user->id)
            ->where('gender', $user->gender === Gender::Male ? Gender::Female : Gender::Male)
            ->get()
            ->filter(function ($target) use ($user) {
                $userAge = now()->diffInYears($user->birth_date);
                $targetAge = now()->diffInYears($target->birth_date);
                return $user->gender === Gender::Male
                    ? ($userAge >= $targetAge && $userAge - $targetAge <= 10)
                    : ($targetAge >= $userAge && $targetAge - $userAge <= 10);
            });

        $friendsAdded = 0;
        foreach ($potentialFriends->shuffle()->take($friendCount) as $target) {
            if (!Friendship::where('user_id', $user->id)->where('target_id', $target->id)->exists() &&
                !Friendship::where('user_id', $target->id)->where('target_id', $user->id)->exists()) {
                $statusWeights = [
                    FriendshipStatus::Accepted->value => 50,
                    FriendshipStatus::Pending->value => 30,
                    FriendshipStatus::Rejected->value => 20,
                ];
                $status = $this->weightedRandomElement($statusWeights);

                Friendship::create([
                    'user_id' => $user->id,
                    'target_id' => $target->id,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $friendsAdded++;
            }
            if ($friendsAdded >= $friendCount) {
                break;
            }
        }
    }

    private function seedBlocks(User $user): void
    {
        $blockCount = rand(0, 10);
        $potentialBlocks = User::where('id', '!=', $user->id)
            ->whereDoesntHave('friendships', function ($query) use ($user) {
                $query->where('target_id', $user->id)->where('status', FriendshipStatus::Accepted->value);
            })
            ->inRandomOrder()
            ->take($blockCount)
            ->get();

        foreach ($potentialBlocks as $target) {
            if (!Block::where('user_id', $user->id)->where('target_id', $target->id)->exists()) {
                Block::create([
                    'user_id' => $user->id,
                    'target_id' => $target->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedReports(User $user): void
    {
        $reportCount = rand(0, 10);
        $potentialReports = User::where('id', '!=', $user->id)->inRandomOrder()->take($reportCount)->get();

        $reportReasons = [
            Severity::Low->value => [
                'Minor inappropriate comment.',
                'Slightly off-topic profile content.',
            ],
            Severity::Medium->value => [
                'Inappropriate behavior in profile comments.',
                'Posting mildly offensive content in photos.',
                'Sending unwanted messages.',
            ],
            Severity::High->value => [
                'Suspicious activity detected.',
                'Fake profile suspected.',
                'Harassment or abusive behavior.',
            ],
        ];

        foreach ($potentialReports as $target) {
            if (!Report::where('user_id', $user->id)->where('target_id', $target->id)->exists()) {
                $severity = fake()->randomElement([
                    Severity::Low->value,
                    Severity::Medium->value,
                    Severity::High->value
                ]);
                $reason = fake()->randomElement($reportReasons[$severity]);

                Report::create([
                    'user_id' => $user->id,
                    'target_id' => $target->id,
                    'report' => $reason,
                    'page_url' => fake()->url(),
                    'user_agent' => fake()->userAgent(),
                    'status' => fake()->randomElement([
                        FriendshipStatus::Pending->value,
                        FriendshipStatus::Accepted->value,
                        FriendshipStatus::Rejected->value
                    ]),
                    'severity' => $severity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Select a random element based on weights.
     *
     * @param array $weightedElements Array of elements with their weights (e.g., ['accepted' => 50, 'pending' => 30, 'rejected' => 20])
     * @return string The selected element
     */
    private function weightedRandomElement(array $weightedElements): string
    {
        $totalWeight = array_sum($weightedElements);
        $rand = rand(1, $totalWeight);
        $current = 0;

        foreach ($weightedElements as $element => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $element;
            }
        }

        // در صورتی که به هر دلیلی به اینجا برسه (نباید بشه)، یه مقدار پیش‌فرض برگردون
        return array_key_first($weightedElements);
    }
}
