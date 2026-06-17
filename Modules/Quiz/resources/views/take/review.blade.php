<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Review') }} — {{ $quiz->title }}
            </h2>
            <a href="{{ route('quiz.take.result', $attempt) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">&larr; Back to result</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">
            @foreach ($quiz->questions as $i => $question)
                @php
                    $ans = $answers->get($question->id);
                    $selected = collect($ans?->selected_option_ids ?? [])->map(fn ($id) => (int) $id)->all();
                    $awarded = $ans?->marks_awarded ?? 0;
                    $maxMarks = $question->marks ?? $quiz->marks_per_question;
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <p class="text-base font-medium text-gray-800 dark:text-gray-100">
                            <span class="text-gray-400 mr-1">{{ $i + 1 }}.</span>{{ $question->question_text }}
                        </p>
                        <span class="shrink-0 text-xs font-semibold px-2 py-0.5 rounded {{ $ans?->is_correct ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                            {{ rtrim(rtrim(number_format($awarded, 2), '0'), '.') }} / {{ rtrim(rtrim(number_format($maxMarks, 2), '0'), '.') }}
                        </span>
                    </div>

                    @if ($question->hasOptions())
                        <div class="space-y-2">
                            @foreach ($question->options as $option)
                                @php
                                    $isChosen = in_array((int) $option->id, $selected, true);
                                    $isCorrect = $option->is_correct;
                                @endphp
                                <div class="flex items-center gap-3 p-3 rounded-lg border text-sm
                                    @if ($isCorrect) border-green-300 bg-green-50 dark:bg-green-900/20 dark:border-green-800
                                    @elseif ($isChosen) border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-800
                                    @else border-gray-200 dark:border-gray-700 @endif">
                                    <span class="w-5 text-center">
                                        @if ($isCorrect)<span class="text-green-600 dark:text-green-400">✓</span>
                                        @elseif ($isChosen)<span class="text-red-600 dark:text-red-400">✕</span>
                                        @endif
                                    </span>
                                    <span class="text-gray-700 dark:text-gray-200">{{ $option->option_text }}</span>
                                    @if ($isChosen)<span class="ml-auto text-xs text-gray-400">your answer</span>@endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="space-y-2 text-sm">
                            <div class="p-3 rounded-lg border {{ $ans?->is_correct ? 'border-green-300 bg-green-50 dark:bg-green-900/20 dark:border-green-800' : 'border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-800' }}">
                                <span class="text-xs text-gray-400 block mb-0.5">Your answer</span>
                                <span class="text-gray-700 dark:text-gray-200">{{ $ans?->text_answer ?: '—' }}</span>
                            </div>
                            @if ($question->correct_text_answer)
                                <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <span class="text-xs text-gray-400 block mb-0.5">Expected answer</span>
                                    <span class="text-gray-700 dark:text-gray-200">{{ $question->correct_text_answer }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($question->notes)
                        <p class="text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-3">{{ $question->notes }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
