<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/csv/questions.csv');

        if (!file_exists($filePath)) {
            Log::error('CSV file not found at: ' . $filePath);
            throw new \Exception('CSV file not found at: ' . $filePath);
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        DB::transaction(function () use ($records) {
            foreach ($records as $record) {
                try {
                    $questionId = DB::table('questions')->insertGetId([
                        'page' => $record['page'],
                        'order_in_page' => $record['order_in_page'],
                        'question' => $record['question'],
                        'answer_type' => $record['answer_type'],
                        'search_label' => $record['search_label'],
                        'answer_label' => $record['answer_label'],
                        'is_required' => filter_var($record['is_required'], FILTER_VALIDATE_BOOLEAN),
                        'is_editable' => filter_var($record['is_editable'], FILTER_VALIDATE_BOOLEAN),
                        'category' => $record['category'],
                        'is_visible' => filter_var($record['is_visible'], FILTER_VALIDATE_BOOLEAN),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (!empty($record['options'])) {
                        $options = explode(',', $record['options']);
                        foreach ($options as $index => $option) {
                            $option = trim($option);
                            if (!empty($option)) {
                                DB::table('question_options')->insert([
                                    'question_id' => $questionId,
                                    'option_value' => $option,
                                    'option_key' => strtolower(str_replace(' ', '_', $option)),
                                    'order_in_question' => $index + 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to seed question: ' . $record['question'] . ' - Error: ' . $e->getMessage());
                    throw $e;
                }
            }
        });

        $this->command->info('Questions and options seeded successfully!');
    }
}
