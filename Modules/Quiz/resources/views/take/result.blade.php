<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quiz Result') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center space-y-6">

                <div>
                    <p class="text-sm text-gray-400">{{ $quiz->title }}</p>
                    <h3 class="mt-1 text-lg font-semibold text-gray-800 dark:text-gray-100">
                        {{ $attempt->status === 'expired' ? 'Time up — attempt submitted' : 'Attempt submitted' }}
                    </h3>
                </div>

                @if ($quiz->can_view_result)
                    <div class="flex flex-col items-center gap-3">
                        @if ($attempt->passed)
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                ✓ Passed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                ✕ Not passed
                            </span>
                        @endif

                        <div class="text-5xl font-bold text-gray-800 dark:text-gray-100">
                            {{ rtrim(rtrim(number_format($attempt->percentage, 2), '0'), '.') }}<span class="text-2xl text-gray-400">%</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Score: {{ rtrim(rtrim(number_format($attempt->score, 2), '0'), '.') }} / {{ rtrim(rtrim(number_format($attempt->total_marks, 2), '0'), '.') }}
                            · Pass mark {{ rtrim(rtrim(number_format($quiz->pass_percentage, 2), '0'), '.') }}%
                        </p>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Your responses have been recorded. Results are not available for this quiz.
                    </p>
                @endif

                <div class="flex items-center justify-center gap-3 pt-2">
                    <a href="{{ route('quiz.take.index') }}" class="px-5 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition">Back to quizzes</a>
                    @if ($quiz->can_review_paper)
                        <a href="{{ route('quiz.take.review', $attempt) }}" class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Review answers</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
