<?php

use App\Enums\AnswerType;
use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public int $currentPage = 1;
    public array $answers = [];
    public array $originalAnswers = []; // برای نگه‌داری مقادیر اولیه
    public array $questionsByPage = [];

    public function mount(): void
    {
        $this->questionsByPage = Question::with('options')
            ->orderBy('page')
            ->orderBy('order_in_page')
            ->get()
            ->groupBy('page')
            ->toArray();

        // بارگذاری پاسخ‌های فعلی کاربر
        $userAnswers = UserAnswer::where('user_id', Auth::id())->get()->pluck('answer', 'question_id')->toArray();
        foreach ($userAnswers as $questionId => $answer) {
            $decodedAnswer = json_decode($answer, true);
            $this->answers[$questionId] = $decodedAnswer;
            $this->originalAnswers[$questionId] = $decodedAnswer; // ذخیره نسخه اولیه
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
        try {
            $user = Auth::user();

            // فقط پرسش‌هایی که تغییر کردن رو اعتبارسنجی و ذخیره کن
            $changedAnswers = array_filter($this->answers, function ($answer, $questionId) {
                $original = $this->originalAnswers[$questionId] ?? null;
                return json_encode($answer) !== json_encode($original); // مقایسه JSON برای دقت بیشتر
            }, ARRAY_FILTER_USE_BOTH);

            foreach ($changedAnswers as $questionId => $answer) {
                $question = Question::find($questionId);
                if (!$question) {
                    continue;
                }

                $rules = match ($question->answer_type) {
                    AnswerType::String->value => ['required', 'string', 'max:255'],
                    AnswerType::Boolean->value => ['required', 'string', 'in:0,1'],
                    AnswerType::Single->value => ['required', 'string', 'in:' . implode(',', $question->options->pluck('option_value')->toArray())],
                    AnswerType::Multiple->value => ['required', 'array'],
                    default => ['nullable'],
                };

                if ($question->is_required) {
                    $rules[] = 'required';
                }

                $messages = [
                    "answers.$questionId.required" => "The answer for '{$question->question}' is required.",
                    "answers.$questionId.string" => "The answer for '{$question->question}' must be a string.",
                    "answers.$questionId.max" => "The answer for '{$question->question}' must not exceed 255 characters.",
                    "answers.$questionId.in" => "The selected answer for '{$question->question}' is invalid.",
                    "answers.$questionId.array" => "The answer for '{$question->question}' must be a selection of options.",
                ];

                if ($question->answer_type === AnswerType::Multiple->value) {
                    $this->validate([
                        "answers.$questionId" => $rules,
                        "answers.$questionId.*" => ['in:' . implode(',', $question->options->pluck('option_value')->toArray())],
                    ], $messages);
                } else {
                    $this->validate([
                        "answers.$questionId" => $rules,
                    ], $messages);
                }

                UserAnswer::updateOrCreate(
                    ['user_id' => $user->id, 'question_id' => $questionId],
                    ['answer' => json_encode($answer)]
                );
            }

            $this->dispatch('interests-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            foreach ($errors as $error) {
                $this->dispatch('error', $error);
            }
        }
    }
}; ?>

<div class="card card-primary card-outline card-hover-effect">
    <div class="card-header">
        <h5 class="card-title">
            {{ __('Page :current of :total', ['current' => $currentPage, 'total' => count($questionsByPage)]) }}
        </h5>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="saveAnswers" class="form-horizontal">
            @foreach ($questionsByPage[$currentPage] ?? [] as $question)
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">{{ $question['question'] }}</label>
                    <div class="col-sm-9">
                        @switch($question['answer_type'])
                            @case(AnswerType::String->value)
                                <input wire:model="answers.{{ $question['id'] }}" type="text" class="form-control" />
                                @break
                            @case(AnswerType::Boolean->value)
                                <select wire:model="answers.{{ $question['id'] }}" class="form-control">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                    <option value="0">{{ __('No') }}</option>
                                </select>
                                @break
                            @case(AnswerType::Single->value)
                                <select wire:model="answers.{{ $question['id'] }}" class="form-control">
                                    <option value="">{{ __('Select') }}</option>
                                    @foreach ($question['options'] as $option)
                                        <option value="{{ $option['option_value'] }}">{{ $option['option_value'] }}</option>
                                    @endforeach
                                </select>
                                @break
                            @case(AnswerType::Multiple->value)
                                <select wire:model="answers.{{ $question['id'] }}" class="form-control" multiple>
                                    @foreach ($question['options'] as $option)
                                        <option value="{{ $option['option_value'] }}">{{ $option['option_value'] }}</option>
                                    @endforeach
                                </select>
                                @break
                        @endswitch
                    </div>
                </div>
            @endforeach

            <div class="row">
                <div class="col-sm-9 offset-sm-3 d-flex gap-2">
                    @if ($currentPage > 1)
                        <button type="button" wire:click="previousPage" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('Previous') }}
                        </button>
                    @endif
                    @if ($currentPage < count($questionsByPage))
                        <button type="button" wire:click="nextPage" class="btn btn-primary">
                            {{ __('Next') }} <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    @else
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> {{ __('Save') }}
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
