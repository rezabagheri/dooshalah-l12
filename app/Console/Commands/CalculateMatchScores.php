<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserMatchScore;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateMatchScores extends Command
{
    protected $signature = 'matches:calculate {--full : Calculate for all users}';
    protected $description = 'Calculate and update match scores for users with recent changes';

    public function handle()
    {
        if ($this->option('full')) {
            $users = User::all();
            foreach ($users as $user) {
                $this->calculateForUser($user);
                $this->info("Processed match calculation for user ID: {$user->id}");
            }
        } else {
            $lastRun = \Illuminate\Support\Facades\Cache::get('last_match_calculation', now()->subDays(1));
            $changedUsers = UserAnswer::where('updated_at', '>=', $lastRun)
                ->orWhere('created_at', '>=', $lastRun)
                ->distinct('user_id')
                ->pluck('user_id')
                ->map(fn ($userId) => User::find($userId));

            foreach ($changedUsers as $user) {
                $this->calculateForUser($user);
                $this->info("Processed match calculation for user ID: {$user->id}");
            }

            \Illuminate\Support\Facades\Cache::put('last_match_calculation', now());
        }

        $this->info('Match score calculations completed successfully!');
    }

    private function calculateForUser($user)
    {
        UserMatchScore::where('user_id', $user->id)->delete();

        DB::statement("
            INSERT INTO user_match_scores (user_id, target_id, match_score, created_at, updated_at)
            SELECT
                ?,
                u2.user_id AS target_id,
                ROUND(SUM(CASE WHEN u1.answer = u2.answer THEN u1.weight ELSE 0 END) / SUM(u1.weight) * 100, 1) AS match_score,
                NOW(),
                NOW()
            FROM user_answer_pivot_cache u1
            JOIN user_answer_pivot_cache u2 ON u1.question_id = u2.question_id
            WHERE u1.user_id = ? AND u2.user_id != ?
            GROUP BY u2.user_id
        ", [$user->id, $user->id, $user->id]);
    }
}
