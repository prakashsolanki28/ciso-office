<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Available Quizzes') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-300">{{ session('error') }}</div>
            @endif
            @if (session('info'))
                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 px-4 py-3 text-sm text-blue-700 dark:text-blue-300">{{ session('info') }}</div>
            @endif

            @if ($quizzes->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No quizzes are running right now. Check back later.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($quizzes as $quiz)
                        @php $s = $stats[$quiz->id]; @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                            @if ($quiz->banner_url)
                                <img src="{{ $quiz->banner_url }}" alt="" class="h-32 w-full object-cover">
                            @else
                                <div class="h-32 w-full bg-gradient-to-br from-primary to-secondary"></div>
                            @endif

                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">{{ $quiz->title }}</h3>
                                @if ($quiz->description)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $quiz->description }}</p>
                                @endif

                                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $quiz->questions_count }} questions</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        @if ($quiz->duration_type === 'full_paper'){{ $quiz->duration_minutes }} min
                                        @elseif ($quiz->duration_type === 'per_question'){{ $quiz->duration_per_question }}s / question
                                        @else No time limit @endif
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">Pass {{ rtrim(rtrim(number_format($quiz->pass_percentage, 2), '0'), '.') }}%</span>
                                </div>

                                {{-- Status / best score --}}
                                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                    @if (! is_null($s['best']))
                                        Best score: <span class="font-semibold {{ $s['best_passed'] ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-200' }}">{{ rtrim(rtrim(number_format($s['best'], 2), '0'), '.') }}%</span>
                                        @if ($s['best_passed']) <span class="text-green-600 dark:text-green-400">· Passed</span> @endif
                                    @endif
                                    @if (! is_null($s['attempts_left']))
                                        <span class="block mt-0.5">{{ $s['attempts_left'] }} attempt(s) left</span>
                                    @else
                                        <span class="block mt-0.5">Unlimited attempts</span>
                                    @endif
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    @if ($s['in_progress'])
                                        <a href="{{ route('quiz.take.show', $s['in_progress']) }}"
                                           class="block text-center px-4 py-2 bg-alert-amber text-white text-sm font-medium rounded-lg hover:opacity-90 transition">Resume</a>
                                    @else
                                        <a href="{{ route('quiz.take.intro', $quiz) }}"
                                           class="block text-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
                                            {{ $s['completed_count'] > 0 ? 'View / Retake' : 'Start Quiz' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div>{{ $quizzes->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
