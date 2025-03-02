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

    public function mount(): void
    {
        $this->questionsByPage = Question::with('options')
            ->orderBy('page')
            ->orderBy('order_in_page')
            ->get()
            ->groupBy('page')
            ->toArray();

        $userAnswers = UserAnswer::where('user_id', Auth::id())->get()->pluck('answer', 'question_id')->toArray();
        foreach ($userAnswers as $questionId => $answer) {
            $decodedAnswer = json_decode($answer, true);
            $this->answers[$questionId] = $decodedAnswer;
            $this->originalAnswers[$questionId] = $decodedAnswer;
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

            $changedAnswers = array_filter($this->answers, function ($answer, $questionId) {
                $original = $this->originalAnswers[$questionId] ?? null;
                $question = Question::find($questionId);
                return (!$question || $question->is_editable || is_null($original)) && json_encode($answer) !== json_encode($original);
            }, ARRAY_FILTER_USE_BOTH);

            foreach ($changedAnswers as $questionId => $answer) {
                $question = Question::find($questionId);
                if (!$question) {
                    continue;
                }

                $rules = match ($question->answer_type) {
                    'string' => ['required', 'string', 'max:255'],
                    'number' => ['required', 'numeric'],
                    'boolean' => ['required', 'string', 'in:0,1'],
                    'single' => ['required', 'string', 'in:' . implode(',', $question->options->pluck('option_value')->toArray())],
                    'multiple' => ['required', 'array'],
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
                ];

                if ($question->answer_type === 'multiple') {
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

    public function isReadonly($questionId): bool
    {
        $question = Question::find($questionId);
        return $question && !$question->is_editable && isset($this->originalAnswers[$questionId]);
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
            @foreach ($questionsByPage[$this->currentPage] ?? [] as $question)
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">{{ $question['question'] }}</label>
                    <div class="col-sm-9">
                        @php
                            $isReadonly = $this->isReadonly($question['id']);
                        @endphp
                        @switch($question['answer_type'])
                            @case('string')
                                <input wire:model="answers.{{ $question['id'] }}" type="text" class="form-control" @if($isReadonly) readonly @endif />
                                @break
                            @case('number')
                                <input wire:model="answers.{{ $question['id'] }}" type="number" class="form-control" @if($isReadonly) readonly @endif />
                                @break
                            @case('boolean')
                                <select wire:model="answers.{{ $question['id'] }}" class="form-control" @if($isReadonly) disabled @endif>
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                    <option value="0">{{ __('No') }}</option>
                                </select>
                                @break
                            @case('single')
                                <select wire:model="answers.{{ $question['id'] }}" class="form-control" @if($isReadonly) disabled @endif>
                                    <option value="">{{ __('Select') }}</option>
                                    @foreach ($question['options'] as $option)
                                        <option value="{{ $option['option_value'] }}">{{ $option['option_value'] }}</option>
                                    @endforeach
                                </select>
                                @break
                            @case('multiple')
                                <select wire:model="answers.{{ $question['id'] }}" class="form-control" multiple @if($isReadonly) disabled @endif>
                                    @foreach ($question['options'] as $option)
                                        <option value="{{ $option['option_value'] }}">{{ $option['option_value'] }}</option>
                                    @endforeach
                                </select>
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
