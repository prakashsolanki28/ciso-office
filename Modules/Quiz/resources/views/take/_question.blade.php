@php
    /**
     * Expects:
     *   $question    - QuizQuestion (with options loaded)
     *   $saved       - QuizAttemptAnswer|null (for prefilling)
     *   $namePrefix  - '' for per-question mode, "answers[ID]" for full-paper mode
     *   $number      - display number (1-based)
     */
    $namePrefix = $namePrefix ?? '';
    $optName  = $namePrefix !== '' ? $namePrefix.'[options][]' : 'options[]';
    $textName = $namePrefix !== '' ? $namePrefix.'[text]' : 'text';
    $selected = collect($saved?->selected_option_ids ?? [])->map(fn ($id) => (int) $id)->all();
    $marks = $question->marks ?? $quiz->marks_per_question;
@endphp

<div class="space-y-4">
    <div class="flex items-start justify-between gap-4">
        <p class="text-base font-medium text-gray-800 dark:text-gray-100">
            @isset($number)<span class="text-gray-400 mr-1">{{ $number }}.</span>@endisset
            {{ $question->question_text }}
        </p>
        <span class="shrink-0 text-xs text-gray-400 whitespace-nowrap">
            {{ rtrim(rtrim(number_format($marks, 2), '0'), '.') }} {{ \Illuminate\Support\Str::plural('mark', $marks) }}
        </span>
    </div>
    @if ($question->question_text_hi)
        <p class="text-sm text-gray-500 dark:text-gray-400 -mt-2">{{ $question->question_text_hi }}</p>
    @endif

    @if ($question->hasOptions())
        @php $inputType = $question->question_type === 'multiple' ? 'checkbox' : 'radio'; @endphp
        <div class="space-y-2">
            @foreach ($question->options as $option)
                <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-primary/50 has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition">
                    <input type="{{ $inputType }}" name="{{ $optName }}" value="{{ $option->id }}"
                           @checked(in_array((int) $option->id, $selected, true))
                           class="mt-0.5 text-primary focus:ring-primary/30 {{ $inputType === 'radio' ? 'rounded-full' : 'rounded' }} border-gray-300 dark:border-gray-600">
                    <span class="text-sm text-gray-700 dark:text-gray-200">
                        {{ $option->option_text }}
                        @if ($option->option_text_hi)
                            <span class="block text-xs text-gray-400">{{ $option->option_text_hi }}</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>
    @else
        <textarea name="{{ $textName }}" rows="4" placeholder="Type your answer…"
                  class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">{{ $saved?->text_answer }}</textarea>
    @endif
</div>
