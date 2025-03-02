<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class UserAnswerSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $questions = Question::with('options')->get();

        if ($users->isEmpty() || $questions->isEmpty()) {
            $this->command->warn('No users or questions found. Please seed users and questions first.');
            return;
        }

        $output = new ConsoleOutput();
        $progressBar = new ProgressBar($output, $users->count() * $questions->count());
        $progressBar->start();

        DB::transaction(function () use ($users, $questions, $progressBar) {
            foreach ($users as $user) {
                foreach ($questions as $question) {
                    try {
                        $answer = $this->generateAnswer($question);

                        UserAnswer::create([
                            'user_id' => $user->id,
                            'question_id' => $question->id,
                            'answer' => $answer,
                        ]);

                        $progressBar->advance();
                    } catch (\Exception $e) {
                        Log::error('Failed to seed user answer for user_id: ' . $user->id . ', question_id: ' . $question->id . ' - Error: ' . $e->getMessage());
                        throw $e;
                    }
                }
            }
        });

        $progressBar->finish();
        $output->writeln("\nUser answers seeding completed successfully.");
    }

    private function generateAnswer(Question $question): string
    {
        switch ($question->answer_type) {
            case 'string':
                return json_encode(match ($question->search_label) {
                    'Father Name' => fake()->firstName('male') . ' ' . fake()->lastName(),
                    'Mother Name' => fake()->firstName('female') . ' ' . fake()->lastName(),
                    default => fake()->sentence(),
                });
            case 'number':
                return json_encode(match ($question->search_label) {
                    'Sibling' => fake()->numberBetween(0, 6),
                    'Height' => fake()->numberBetween(150, 200), // قد به سانتیمتر
                    default => fake()->numberBetween(1, 100),
                });
            case 'boolean':
                return json_encode(fake()->boolean() ? "1" : "0");
            case 'single':
                return json_encode(
                    $question->options->isNotEmpty() ? $question->options->random()->option_value : 'N/A'
                );
            case 'multiple':
                return json_encode(
                    $question->options->isNotEmpty()
                        ? $question->options->random(rand(1, min(3, $question->options->count())))->pluck('option_value')->toArray()
                        : ['N/A']
                );
            default:
                return json_encode(null);
        }
    }
}
