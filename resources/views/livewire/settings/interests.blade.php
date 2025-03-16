<?php

use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
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
            if (is_string($answer)) {
                $decodedAnswer = json_decode($answer, true);
            } else {
                $decodedAnswer = $answer;
            }

            $question = Question::find($questionId);
            if ($question && $question->answer_type === 'multiple' && !is_array($decodedAnswer)) {
                $this->answers[$questionId] = [$decodedAnswer];
                $this->originalAnswers[$questionId] = [$decodedAnswer];
                $this->tempAnswers[$questionId] = [$decodedAnswer];
            } else {
                $this->answers[$questionId] = $decodedAnswer;
                $this->originalAnswers[$questionId] = $decodedAnswer;
                $this->tempAnswers[$questionId] = $decodedAnswer;
            }
        }

        \Log::info('Mounted answers', ['answers' => $this->answers]);
    }

    public function updateAnswer($questionId, $value): void
    {
        $question = Question::find($questionId);
        if ($question) {
            if ($question->answer_type === 'multiple') {
                $this->tempAnswers[$questionId] = is_array($value) ? $value : explode(',', $value);
            } else {
                $this->tempAnswers[$questionId] = $value;
            }
        }
        \Log::info("Updated answer for question: {$questionId}", ['value' => $this->tempAnswers[$questionId]]);
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
        $this->answers = $this->tempAnswers;
        \Log::info('Saving answers', ['answers' => $this->answers]);

        try {
            $user = Auth::user();

            $changedAnswers = array_filter(
                $this->answers,
                function ($answer, $questionId) {
                    $original = $this->originalAnswers[$questionId] ?? null;
                    $question = Question::find($questionId);
                    return (!$question || $question->is_editable || is_null($original)) && json_encode($answer) !== json_encode($original);
                },
                ARRAY_FILTER_USE_BOTH,
            );

            foreach ($changedAnswers as $questionId => $answer) {
                $question = Question::find($questionId);
                if (!$question) {
                    continue;
                }

                $options = $question->options->pluck('option_value')->toArray();
                \Log::info("Processing question: {$questionId}", [
                    'question' => $question->question,
                    'answer_type' => $question->answer_type,
                    'answer' => $answer,
                    'allowed_options' => $options,
                ]);

                $rules = match ($question->answer_type) {
                    'string' => ['required', 'string', 'max:255'],
                    'number' => ['required', 'numeric'],
                    'boolean' => ['required', 'string', 'in:0,1'],
                    'single' => ['required', 'string', 'in:' . implode(',', $options)],
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
                    "answers.$questionId.array" => "The answer for '{$question->question}' must be a selection of options.",
                    "answers.$questionId.min" => "You must select at least one option for '{$question->question}'.",
                ];

                if ($question->answer_type === 'multiple') {
                    \Log::info("Validating multiple question: {$questionId}", [
                        'answer' => $this->answers[$questionId],
                        'allowed_options' => $options,
                    ]);
                    $this->validate(
                        [
                            "answers.$questionId" => $rules,
                            "answers.$questionId.*" => ['in:' . implode(',', $options)],
                        ],
                        $messages,
                    );
                } else {
                    \Log::info("Validating question: {$questionId}", [
                        'answer' => $this->answers[$questionId],
                        'allowed_options' => $question->answer_type === 'single' ? $options : null,
                    ]);
                    $this->validate(
                        [
                            "answers.$questionId" => $rules,
                        ],
                        $messages,
                    );
                }

                UserAnswer::updateOrCreate(['user_id' => $user->id, 'question_id' => $questionId], ['answer' => json_encode($answer)]);
            }

            $this->dispatch('interests-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            foreach ($errors as $error) {
                $this->dispatch('error', $error);
            }
        }
    }

    public function isReadonly($questionId): bool
    {
        $question = Question::find($questionId);
        return $question && !$question->is_editable && isset($this->originalAnswers[$questionId]);
    }
}; ?>

<x-settings.layout heading="Interested" subheading="">
    <div class="card card-primary card-outline card-hover-effect">
        <div class="card-header">
            <h5 class="card-title">
                {{ __('Page :current of :total', ['current' => $currentPage, 'total' => count($questionsByPage)]) }}
            </h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="saveAnswers" class="form-horizontal">
                @foreach ($questionsByPage[$this->currentPage] ?? [] as $question)
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">{{ $question['question'] }}</label>
                        <div class="col-sm-9">
                            @php
                                $isReadonly = $this->isReadonly($question['id']);
                            @endphp
                            @switch($question['answer_type'])
                                @case('string')
                                    <input wire:model.defer="tempAnswers.{{ $question['id'] }}" type="text"
                                        class="form-control" @if ($isReadonly) readonly @endif />
                                @break

                                @case('number')
                                    <input wire:model.defer="tempAnswers.{{ $question['id'] }}" type="number"
                                        class="form-control" @if ($isReadonly) readonly @endif />
                                @break

                                @case('boolean')
                                    <select wire:change="updateAnswer('{{ $question['id'] }}', $event.target.value)"
                                        class="form-control" @if ($isReadonly) disabled @endif>
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="1"
                                            {{ isset($tempAnswers[$question['id']]) && $tempAnswers[$question['id']] === '1' ? 'selected' : '' }}>
                                            {{ __('Yes') }}</option>
                                        <option value="0"
                                            {{ isset($tempAnswers[$question['id']]) && $tempAnswers[$question['id']] === '0' ? 'selected' : '' }}>
                                            {{ __('No') }}</option>
                                    </select>
                                @break

                                @case('single')
                                    <select wire:change="updateAnswer('{{ $question['id'] }}', $event.target.value)"
                                        class="form-control" @if ($isReadonly) disabled @endif>
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach ($question['options'] as $option)
                                            <option value="{{ $option['option_value'] }}"
                                                {{ isset($tempAnswers[$question['id']]) && $tempAnswers[$question['id']] === $option['option_value'] ? 'selected' : '' }}>
                                                {{ $option['option_value'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small>Selected: {{ $tempAnswers[$question['id']] ?? 'None' }}</small>
                                @break

                                @case('multiple')
                                    <select class="form-control" multiple @if ($isReadonly) disabled @endif
                                        onchange="updateMultipleAnswer('{{ $question['id'] }}', this)">
                                        @foreach ($question['options'] as $option)
                                            <option value="{{ $option['option_value'] }}"
                                                {{ isset($tempAnswers[$question['id']]) && is_array($tempAnswers[$question['id']]) && in_array($option['option_value'], $tempAnswers[$question['id']]) ? 'selected' : '' }}>
                                                {{ $option['option_value'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small>Selected: {{ json_encode($tempAnswers[$question['id']] ?? []) }}</small>
                                @break
                            @endswitch
                            @if (!$question['is_editable'])
                                <small class="text-warning d-block mt-1">
                                    {{ __('This question can only be answered once and cannot be edited later.') }}
                                </small>
                            @endif
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
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-settings.layout>
<script>
    function updateMultipleAnswer(questionId, selectElement) {
        const selectedOptions = Array.from(selectElement.selectedOptions).map(option => option.value);
        @this.call('updateAnswer', questionId, selectedOptions);
    }
</script>
