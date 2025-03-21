<?php

namespace App\Livewire\Settings;

use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Interests extends Component
{
    public int $currentPage = 1;
    public array $answers = [];
    public array $originalAnswers = [];
    public array $questionsByPage = [];
    public array $tempAnswers = [];

    public function mount(): void
    {
        $this->questionsByPage = Question::with('options')->orderBy('page')->orderBy('order_in_page')->get()->groupBy('page')->toArray();

        $userAnswers = UserAnswer::where('user_id', Auth::id())->get()->pluck('answer', 'question_id')->toArray();
        foreach ($userAnswers as $questionId => $answer) {
            $question = Question::find($questionId);
            if (!$question) {
                continue;
            }

            $processedAnswer = is_string($answer) ? json_decode($answer, true) : $answer;
            if ($processedAnswer === null) {
                $processedAnswer = $answer;
            }

            switch ($question->answer_type) {
                case 'number':
                    $processedAnswer = is_numeric($processedAnswer) ? (int) $processedAnswer : $processedAnswer;
                    break;
                case 'multiple':
                    $processedAnswer = is_array($processedAnswer) ? $processedAnswer : (empty($processedAnswer) ? [] : [$processedAnswer]);
                    break;
                case 'boolean':
                    $processedAnswer = $processedAnswer === '1' || $processedAnswer === 1 || $processedAnswer === true ? '1' : '0';
                    break;
                case 'single':
                    $processedAnswer = is_string($processedAnswer) ? trim($processedAnswer) : $processedAnswer;
                    break;
            }

            $this->answers[$questionId] = $processedAnswer;
            $this->originalAnswers[$questionId] = $processedAnswer;
            $this->tempAnswers[$questionId] = $processedAnswer;

            \Log::info('Loaded answer for question', [
                'question_id' => $questionId,
                'answer' => $processedAnswer,
                'type' => gettype($processedAnswer),
            ]);
        }
    }

    public function updateAnswer($questionId, $value): void
    {
        $question = Question::find($questionId);
        if ($question) {
            if ($question->answer_type === 'multiple') {
                $this->tempAnswers[$questionId] = is_array($value) ? $value : (empty($value) ? [] : [$value]);
            } else {
                $this->tempAnswers[$questionId] = $value;
            }
        }
    }

    public function nextPage(): void
    {
        if ($this->currentPage < count($this->questionsByPage)) {
            $this->currentPage++;
        }
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function saveAnswers(): void
    {
        \Log::info('Temp answers before copy', [
            'tempAnswers' => $this->tempAnswers,
        ]);

        $this->answers = $this->tempAnswers;

        $user = Auth::user();
        $changedAnswers = array_filter($this->answers, fn($answer, $questionId) => !$this->isReadonly($questionId) && json_encode($answer) !== json_encode($this->originalAnswers[$questionId] ?? null), ARRAY_FILTER_USE_BOTH);

        try {
            foreach ($changedAnswers as $questionId => $answer) {
                $question = Question::find($questionId);
                if (!$question) {
                    continue;
                }

                $options = $question->options->pluck('option_value')->toArray();
                $rules = match ($question->answer_type) {
                    'string' => ['required', 'string', 'max:255'],
                    'number' => ['required', 'numeric'],
                    'boolean' => ['required', 'in:0,1'],
                    'single' => ['required', 'in:' . implode(',', $options)],
                    'multiple' => ['required', 'array', 'min:1'],
                    default => ['nullable'],
                };

                if ($question->is_required) {
                    $rules[] = 'required';
                }

                $messages = [
                    "answers.$questionId.required" => "The answer for '{$question->question}' is required.",
                    "answers.$questionId.string" => "The answer for '{$question->question}' must be a string.",
                    "answers.$questionId.max" => "The answer for '{$question->question}' must not exceed 255 characters.",
                    "answers.$questionId.numeric" => "The answer for '{$question->question}' must be a number.",
                    "answers.$questionId.in" => "The selected answer for '{$question->question}' is invalid.",
                    "answers.$questionId.array" => "The answer for '{$question->question}' must be an array.",
                    "answers.$questionId.min" => "You must select at least one option for '{$question->question}'.",
                    "answers.$questionId.*.in" => "One or more selected options for '{$question->question}' are invalid.",
                ];

                \Log::info('Before validation', [
                    'question_id' => $questionId,
                    'question' => $question->question,
                    'answer' => $answer,
                    'type' => gettype($answer),
                ]);

                if ($question->answer_type === 'multiple') {
                    $this->validate(
                        [
                            "answers.$questionId" => $rules,
                            "answers.$questionId.*" => ['in:' . implode(',', $options)],
                        ],
                        $messages,
                    );
                } else {
                    $this->validate(["answers.$questionId" => $rules], $messages);
                }

                \Log::info('Saving answer', [
                    'question_id' => $questionId,
                    'answer' => $answer,
                ]);

                UserAnswer::updateOrCreate(['user_id' => $user->id, 'question_id' => $questionId], ['answer' => json_encode($answer)]);
            }

            $this->dispatch('interests-updated');
            $this->dispatch('show-toast', [
                'message' => 'Interests saved successfully!',
                'type' => 'success',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            foreach ($errors as $error) {
                $this->dispatch('show-toast', [
                    'message' => $error,
                    'type' => 'danger',
                ]);
            }
        }
    }
    public function isReadonly($questionId): bool
    {
        $question = Question::find($questionId);
        return $question && !$question->is_editable && isset($this->originalAnswers[$questionId]);
    }

    public function render()
    {
        return view('livewire.settings.interests');
    }
}
