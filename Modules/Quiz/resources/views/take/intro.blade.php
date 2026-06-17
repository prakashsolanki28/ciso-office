<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quiz Instructions') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('error'))
                <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-300">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($quiz->banner_url)
                    <img src="{{ $quiz->banner_url }}" alt="" class="h-40 w-full object-cover">
                @endif
                <div class="p-6 space-y-5">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $quiz->title }}</h3>
                        @if ($quiz->description)
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $quiz->description }}</p>
                        @endif
                    </div>

                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-400">Questions</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $questionCount }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400">Total marks</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-200">{{ rtrim(rtrim(number_format($quiz->total_marks, 2), '0'), '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400">Duration</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-200">
                                @if ($quiz->duration_type === 'full_paper')
                                    {{ $quiz->duration_minutes }} minutes (whole paper)
                                @elseif ($quiz->duration_type === 'per_question')
                                    {{ $quiz->duration_per_question }} seconds per question
                                @else
                                    No time limit
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-400">Pass mark</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-200">{{ rtrim(rtrim(number_format($quiz->pass_percentage, 2), '0'), '.') }}%</dd>
                        </div>
                    </dl>

                    @if ($quiz->duration_type === 'per_question')
                        <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-4 py-3 text-sm text-amber-700 dark:text-amber-300">
                            This quiz is timed <strong>per question</strong>. Each question auto-advances when its timer runs out — you cannot go back.
                        </div>
                    @elseif ($quiz->duration_type === 'full_paper')
                        <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-4 py-3 text-sm text-amber-700 dark:text-amber-300">
                            You have <strong>{{ $quiz->duration_minutes }} minutes</strong> for the whole paper. It submits automatically when time runs out.
                        </div>
                    @endif

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        @if (is_null($stats['attempts_left']))
                            Attempts: <span class="font-medium">Unlimited</span>
                        @else
                            Attempts remaining: <span class="font-medium">{{ $stats['attempts_left'] }}</span> of {{ $quiz->attempts_allowed }}
                        @endif
                        @if (! is_null($stats['best']))
                            · Best so far: <span class="font-medium">{{ rtrim(rtrim(number_format($stats['best'], 2), '0'), '.') }}%</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-2">
                        <a href="{{ route('quiz.take.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">&larr; Back</a>

                        @if ($stats['in_progress'])
                            <a href="{{ route('quiz.take.show', $stats['in_progress']) }}"
                               class="px-5 py-2.5 bg-alert-amber text-white text-sm font-medium rounded-lg hover:opacity-90 transition">Resume Attempt</a>
                        @elseif (! is_null($stats['attempts_left']) && $stats['attempts_left'] <= 0)
                            <span class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">No attempts left</span>
                        @else
                            <form method="POST" action="{{ route('quiz.take.start', $quiz) }}">
                                @csrf
                                <button class="px-5 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition">Start Quiz</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
