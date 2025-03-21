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
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                            @if ($isReadonly) disabled @endif
                                            onchange="updateBooleanAnswer('{{ $question['id'] }}', this)"
                                            {{ isset($tempAnswers[$question['id']]) && $tempAnswers[$question['id']] === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label">Yes</label>
                                    </div>
                                @break

                                <script>
                                    function updateBooleanAnswer(questionId, checkbox) {
                                        const value = checkbox.checked ? '1' : '0';
                                        console.log('Boolean value for ' + questionId + ':', value);
                                        @this.set('tempAnswers.' + questionId, value);
                                    }
                                </script>
                                @case('single')
                                    <select wire:change="updateAnswer('{{ $question['id'] }}', $event.target.value)"
                                        class="form-control" @if ($isReadonly) disabled @endif>
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach ($question['options'] as $option)
                                            <option value="{{ $option['option_value'] }}"
                                                {{ trim($tempAnswers[$question['id']] ?? '') == trim($option['option_value']) ? 'selected' : '' }}>
                                                {{ $option['option_value'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                @break

                                @case('multiple')
                                    <select multiple class="form-control" @if ($isReadonly) disabled @endif
                                        onchange="updateMultipleAnswer('{{ $question['id'] }}', this)">
                                        @foreach ($question['options'] as $option)
                                            <option value="{{ $option['option_value'] }}"
                                                {{ in_array($option['option_value'], (array) ($tempAnswers[$question['id']] ?? [])) ? 'selected' : '' }}>
                                                {{ $option['option_value'] }}
                                            </option>
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

    <script>
        function updateMultipleAnswer(questionId, selectElement) {
            const selectedOptions = Array.from(selectElement.selectedOptions).map(option => option.value);
            @this.set('tempAnswers.' + questionId, selectedOptions);
        }

        function updateBooleanAnswer(questionId, checkbox) {
            const value = checkbox.checked ? '1' : '0';
            console.log('Boolean value for ' + questionId + ':', value); // دیباگ
            @this.set('tempAnswers.' + questionId, value);
        }
    </script>

</x-settings.layout>
