<?php

namespace App\Observers;

use App\Models\UserAnswer;
use App\Models\UserMatchScore;
use Illuminate\Support\Facades\DB;

class UserAnswerObserver
{
    public function created(UserAnswer $userAnswer)
    {
        $this->updatePivotCache($userAnswer);
        $this->updateMatchScores($userAnswer->user_id);
    }

    public function updated(UserAnswer $userAnswer)
    {
        $this->updatePivotCache($userAnswer);
        $this->updateMatchScores($userAnswer->user_id);
    }

    private function updatePivotCache(UserAnswer $userAnswer)
    {
        DB::table('user_answer_pivot_cache')->updateOrInsert(
            ['user_id' => $userAnswer->user_id, 'question_id' => $userAnswer->question_id],
            [
                'answer' => is_array($userAnswer->answer) ? implode(',', $userAnswer->answer) : $userAnswer->answer,
                'weight' => $userAnswer->question->weight ?? 1,
                'updated_at' => now(),
            ]
        );
    }

    private function updateMatchScores($userId)
    {
        $user = \App\Models\User::find($userId);
        if (!$user) return;

        UserMatchScore::where('user_id', $userId)->delete();

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
        ", [$userId, $userId, $userId]);
    }
}
