<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="quizTaker({{ $secondsLeft ?? 'null' }})">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Sticky timer / progress bar --}}
            <div class="sticky top-0 z-10 bg-white/90 dark:bg-gray-800/90 backdrop-blur rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-3 flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    @if ($quiz->duration_type === 'per_question')
                        Question <span class="font-semibold text-gray-800 dark:text-gray-200">{{ ($position === false ? 0 : $position) + 1 }}</span> of {{ $quiz->questions->count() }}
                    @else
                        {{ $quiz->questions->count() }} questions
                    @endif
                </div>
                @if (! is_null($secondsLeft))
                    <div class="flex items-center gap-2 text-sm font-mono font-semibold"
                         :class="remaining <= 30 ? 'text-critical-red' : 'text-gray-700 dark:text-gray-200'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="display"></span>
                    </div>
                @endif
            </div>

            @if ($quiz->duration_type === 'per_question')
                {{-- ---------------- Per-question mode ---------------- --}}
                @php $current = $currentQuestion; @endphp
                <form method="POST" action="{{ route('quiz.take.answer', $attempt) }}" x-ref="form" @submit="submitting = true">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $current->id }}">

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        @include('quiz::take._question', [
                            'question' => $current,
                            'saved' => $answers->get($current->id),
                            'namePrefix' => '',
                            'number' => ($position === false ? 0 : $position) + 1,
                        ])
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit" x-bind:disabled="submitting"
                                class="px-6 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition disabled:opacity-50">
                            @php $isLast = ($position !== false) && ($position + 1) >= $quiz->questions->count(); @endphp
                            {{ $isLast ? 'Finish Quiz' : 'Next Question' }} &rarr;
                        </button>
                    </div>
                </form>
            @else
                {{-- ---------------- Full-paper / no-timer mode ---------------- --}}
                <form method="POST" action="{{ route('quiz.take.submit', $attempt) }}" x-ref="form" @submit="submitting = true">
                    @csrf
                    <div class="space-y-5">
                        @foreach ($quiz->questions as $i => $question)
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                @include('quiz::take._question', [
                                    'question' => $question,
                                    'saved' => $answers->get($question->id),
                                    'namePrefix' => 'answers['.$question->id.']',
                                    'number' => $i + 1,
                                ])
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" x-bind:disabled="submitting"
                                @click="confirmSubmit($event)"
                                class="px-6 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition disabled:opacity-50">
                            Submit Quiz
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function quizTaker(seconds) {
            return {
                remaining: seconds,
                display: '',
                submitting: false,
                init() {
                    if (this.remaining === null) return;
                    this.render();
                    const id = setInterval(() => {
                        this.remaining--;
                        this.render();
                        if (this.remaining <= 0) {
                            clearInterval(id);
                            this.timeUp();
                        }
                    }, 1000);
                },
                render() {
                    let r = Math.max(0, this.remaining);
                    const m = Math.floor(r / 60);
                    const s = r % 60;
                    this.display = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                },
                timeUp() {
                    if (this.submitting) return;
                    this.submitting = true;
                    this.$refs.form.submit();
                },
                confirmSubmit(e) {
                    if (!confirm('Submit your answers? You will not be able to change them.')) {
                        e.preventDefault();
                        this.submitting = false;
                        return false;
                    }
                    return true;
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
