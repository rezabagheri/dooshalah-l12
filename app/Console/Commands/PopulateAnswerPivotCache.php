<?php

namespace App\Console\Commands;

use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateAnswerPivotCache extends Command
{
    protected $signature = 'answers:cache';
    protected $description = 'Populate the user_answer_pivot_cache table';

    public function handle()
    {
        $this->info('Populating user_answer_pivot_cache...');

        $answers = UserAnswer::with('question')->get();

        foreach ($answers as $answer) {
            DB::table('user_answer_pivot_cache')->updateOrInsert(
                ['user_id' => $answer->user_id, 'question_id' => $answer->question_id],
                [
                    'answer' => is_array($answer->answer) ? implode(',', $answer->answer) : $answer->answer,
                    'weight' => $answer->question->weight ?? 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('User answer pivot cache populated successfully!');
    }
}
