@php
    /** @var \Modules\Quiz\Models\Quiz $quiz */
    /** @var \Modules\Quiz\Models\QuizQuestion $question */
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('quiz.edit', $quiz) }}"
                class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="min-w-0">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">Edit Question
                    #{{ $quiz->questions->search(fn($q) => $q->id === $question->id) + 1 }}</h2>
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $quiz->title }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                    <p class="font-medium mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @php
                $existingOptions = $question->options
                    ->map(fn($o) => ['option_text' => $o->option_text, 'option_text_hi' => $o->option_text_hi ?? '', 'is_correct' => $o->is_correct])
                    ->values()
                    ->toArray();
                $currentType = old('question_type', $question->question_type);
            @endphp

            <form method="POST"
                action="{{ route('quiz.questions.update', [$quiz, $question]) }}"
                x-data="questionForm('{{ $currentType }}', {{ json_encode($existingOptions) }})"
                @submit.prevent="submitWith('save_back')">
                @csrf
                @method('PATCH')

                <div class="space-y-5">

                    {{-- Question Type --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <label
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Question
                            Type</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <button type="button" @click="setType('single')"
                                :class="type === 'single' ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-primary hover:text-primary dark:hover:text-primary'"
                                class="flex flex-col items-center gap-2 px-3 py-3.5 rounded-xl border-2 transition text-xs font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                                    <circle cx="12" cy="12" r="4" fill="currentColor" stroke="none" />
                                </svg>
                                Single Choice
                            </button>
                            <button type="button" @click="setType('multiple')"
                                :class="type === 'multiple' ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-primary hover:text-primary dark:hover:text-primary'"
                                class="flex flex-col items-center gap-2 px-3 py-3.5 rounded-xl border-2 transition text-xs font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="3" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M7 12l3 3 7-7" />
                                </svg>
                                Multiple Choice
                            </button>
                            <button type="button" @click="setType('true_false')"
                                :class="type === 'true_false' ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-primary hover:text-primary dark:hover:text-primary'"
                                class="flex flex-col items-center gap-2 px-3 py-3.5 rounded-xl border-2 transition text-xs font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                True / False
                            </button>
                            <button type="button" @click="setType('text')"
                                :class="type === 'text' ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-primary hover:text-primary dark:hover:text-primary'"
                                class="flex flex-col items-center gap-2 px-3 py-3.5 rounded-xl border-2 transition text-xs font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                                Text Answer
                            </button>
                        </div>
                        <input type="hidden" name="question_type" :value="type">
                    </div>

                    {{-- Question Text --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Question <span class="text-red-500">*</span>
                            </label>
                            {{-- Language Tab --}}
                            <div class="flex items-center gap-0.5 p-1 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <button type="button" @click="langTab = 'en'"
                                    :class="langTab === 'en'
                                        ? 'bg-white dark:bg-gray-800 shadow-sm text-gray-800 dark:text-gray-100'
                                        : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                                    class="flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold transition">
                                    🇬🇧 English
                                </button>
                                <button type="button" @click="langTab = 'hi'"
                                    :class="langTab === 'hi'
                                        ? 'bg-white dark:bg-gray-800 shadow-sm text-orange-600 dark:text-orange-400'
                                        : 'text-gray-500 hover:text-orange-500 dark:hover:text-orange-400'"
                                    class="flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold transition">
                                    🇮🇳 हिंदी
                                    <span class="text-xs font-normal text-gray-400">(Optional)</span>
                                </button>
                            </div>
                        </div>

                        {{-- English question text --}}
                        <div x-show="langTab === 'en'">
                            <textarea name="question_text" rows="4" required
                                placeholder="Type your question here…"
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none">{{ old('question_text', $question->question_text) }}</textarea>
                        </div>

                        {{-- Hindi question text --}}
                        <div x-show="langTab === 'hi'" x-cloak>
                            <textarea name="question_text_hi" rows="4"
                                placeholder="हिंदी में प्रश्न लिखें… (वैकल्पिक)"
                                dir="auto"
                                class="w-full text-sm rounded-lg border-orange-200 dark:border-orange-700/60 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-orange-300/40 focus:border-orange-400 resize-none">{{ old('question_text_hi', $question->question_text_hi) }}</textarea>
                            <p class="text-xs text-orange-500 dark:text-orange-400 mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                Hindi translation is optional — leave blank to use English only.
                            </p>
                        </div>
                    </div>

                    {{-- Options: Single / Multiple --}}
                    <div x-show="type === 'single' || type === 'multiple'"
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <label
                                class="text-sm font-semibold text-gray-700 dark:text-gray-300">Answer Options</label>
                            <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full"
                                x-text="type === 'single' ? 'Pick one correct answer' : 'Pick all correct answers'"></span>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(option, i) in options" :key="i">
                                <div class="flex items-center gap-2.5">
                                    <button type="button"
                                        @click="type === 'single' ? setSingleCorrect(i) : (option.is_correct = !option.is_correct)"
                                        :class="option.is_correct
                                            ? 'bg-green-500 border-green-500 text-white'
                                            : 'border-gray-300 dark:border-gray-500 text-gray-300 hover:border-green-400 hover:text-green-400'"
                                        :title="option.is_correct ? 'Correct answer' : 'Mark as correct'"
                                        class="w-7 h-7 shrink-0 rounded-full border-2 flex items-center justify-center transition">
                                        <svg x-show="option.is_correct" class="w-3.5 h-3.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span x-show="!option.is_correct"
                                            class="text-xs font-bold text-gray-400 dark:text-gray-500"
                                            x-text="i + 1"></span>
                                    </button>
                                    {{-- English option --}}
                                    <input type="text" x-show="langTab === 'en'" x-model="option.option_text"
                                        :placeholder="'Option ' + (i + 1)"
                                        class="flex-1 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                        :class="option.is_correct ? 'border-green-300 dark:border-green-600 bg-green-50 dark:bg-green-900/20' : ''">
                                    {{-- Hindi option --}}
                                    <input type="text" x-show="langTab === 'hi'" x-cloak x-model="option.option_text_hi"
                                        :placeholder="'विकल्प ' + (i + 1) + ' (वैकल्पिक)'"
                                        dir="auto"
                                        class="flex-1 text-sm rounded-lg border-orange-200 dark:border-orange-700/60 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-orange-300/40 focus:border-orange-400"
                                        :class="option.is_correct ? 'border-green-300 dark:border-green-600 bg-green-50 dark:bg-green-900/20' : ''">
                                    <button type="button" @click="removeOption(i)"
                                        :disabled="options.length <= 2"
                                        class="p-1 text-gray-300 hover:text-red-400 disabled:opacity-20 disabled:cursor-not-allowed transition shrink-0"
                                        title="Remove option">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addOption()"
                            class="mt-3 text-xs text-primary hover:underline font-medium flex items-center gap-1 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Option
                        </button>
                    </div>

                    {{-- True / False --}}
                    <div x-show="type === 'true_false'"
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Correct Answer</label>
                        {{-- English: button-style selector --}}
                        <div x-show="langTab === 'en'" class="grid grid-cols-2 gap-3">
                            <template x-for="(option, i) in options" :key="i">
                                <button type="button" @click="setSingleCorrect(i)"
                                    :class="option.is_correct
                                        ? 'bg-green-500 border-green-500 text-white shadow-sm'
                                        : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-green-400 hover:text-green-600 dark:hover:text-green-400'"
                                    class="py-4 rounded-xl border-2 text-sm font-semibold transition flex items-center justify-center gap-2">
                                    <svg x-show="option.is_correct" class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span x-text="option.option_text"></span>
                                </button>
                            </template>
                        </div>
                        {{-- Hindi: show editable labels for True/False --}}
                        <div x-show="langTab === 'hi'" x-cloak class="space-y-2">
                            <p class="text-xs text-orange-500 dark:text-orange-400 mb-2">Optionally provide Hindi labels for True / False:</p>
                            <template x-for="(option, i) in options" :key="i">
                                <div class="flex items-center gap-2.5">
                                    <button type="button" @click="setSingleCorrect(i)"
                                        :class="option.is_correct ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 dark:border-gray-500 text-gray-400 hover:border-green-400'"
                                        class="w-7 h-7 shrink-0 rounded-full border-2 flex items-center justify-center transition">
                                        <svg x-show="option.is_correct" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span x-show="!option.is_correct" class="text-xs font-bold text-gray-400" x-text="option.option_text.charAt(0)"></span>
                                    </button>
                                    <input type="text" x-model="option.option_text_hi"
                                        :placeholder="i === 0 ? 'सच (True)' : 'झूठ (False)'"
                                        dir="auto"
                                        class="flex-1 text-sm rounded-lg border-orange-200 dark:border-orange-700/60 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-orange-300/40 focus:border-orange-400">
                                    <span class="text-xs text-gray-400 shrink-0" x-text="option.option_text"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Text Answer --}}
                    <div x-show="type === 'text'"
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Model Answer
                            <span class="font-normal text-gray-400 text-xs">(optional — shown after quiz)</span>
                        </label>
                        <textarea name="correct_text_answer" rows="3"
                            placeholder="Enter the expected answer or key points…"
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none">{{ old('correct_text_answer', $question->correct_text_answer) }}</textarea>
                    </div>

                    {{-- Marks & Notes --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                Marks
                                <span class="font-normal text-gray-400 text-xs">(default:
                                    {{ $quiz->marks_per_question }})</span>
                            </label>
                            <input type="number" name="marks" step="0.5" min="0"
                                value="{{ old('marks', $question->marks) }}"
                                placeholder="{{ $quiz->marks_per_question }}"
                                class="w-full sm:w-36 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        </div>

                        <div x-data="{ showNotes: {{ $question->notes ? 'true' : 'false' }} }">
                            <button type="button" @click="showNotes = !showNotes"
                                class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition font-medium">
                                <svg class="w-4 h-4 transition-transform" :class="showNotes ? 'rotate-180' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                <span x-text="showNotes ? 'Hide Notes' : 'Add Notes / Explanation'"></span>
                            </button>
                            <div x-show="showNotes" x-cloak class="mt-2">
                                <textarea name="notes" rows="3"
                                    placeholder="Explanation or notes shown to participants after the quiz (optional)…"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none">{{ old('notes', $question->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-3 pb-2">
                        <button type="button" @click="submitWith('save_back')"
                            class="flex-1 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:opacity-90 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                        <a href="{{ route('quiz.edit', $quiz) }}"
                            class="py-2.5 px-5 text-sm font-medium text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition flex items-center justify-center rounded-lg border border-transparent hover:border-gray-200 dark:hover:border-gray-600">
                            Cancel
                        </a>
                    </div>

                    {{-- Delete --}}
                    <div class="pb-6">
                        <form method="POST"
                            action="{{ route('quiz.questions.destroy', [$quiz, $question]) }}"
                            x-data
                            @submit.prevent="if(confirm('Delete this question permanently?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-sm text-red-500 hover:text-red-700 dark:hover:text-red-400 transition flex items-center gap-1.5 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete this question
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Serialized options injected here on submit --}}
                <div id="options-container"></div>
                <input type="hidden" name="action" id="action-input" value="save_back">
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function questionForm(initialType, initialOptions) {
                return {
                    type: initialType || 'single',
                    langTab: '{{ $question->question_text_hi ? 'hi' : 'en' }}',
                    options: [],

                    init() {
                        if (initialOptions && initialOptions.length) {
                            this.options = initialOptions.map(o => ({
                                option_text: o.option_text,
                                option_text_hi: o.option_text_hi || '',
                                is_correct: !!o.is_correct,
                            }));
                        } else {
                            this.resetOptions(this.type);
                        }
                    },

                    resetOptions(type) {
                        if (type === 'true_false') {
                            this.options = [
                                { option_text: 'True', option_text_hi: '', is_correct: false },
                                { option_text: 'False', option_text_hi: '', is_correct: false },
                            ];
                        } else if (type === 'text') {
                            this.options = [];
                        } else {
                            this.options = [
                                { option_text: '', option_text_hi: '', is_correct: false },
                                { option_text: '', option_text_hi: '', is_correct: false },
                                { option_text: '', option_text_hi: '', is_correct: false },
                                { option_text: '', option_text_hi: '', is_correct: false },
                            ];
                        }
                    },

                    setType(newType) {
                        this.type = newType;
                        this.resetOptions(newType);
                    },

                    addOption() {
                        this.options.push({ option_text: '', option_text_hi: '', is_correct: false });
                    },

                    removeOption(index) {
                        if (this.options.length <= 2) return;
                        this.options.splice(index, 1);
                    },

                    setSingleCorrect(index) {
                        this.options = this.options.map((o, i) => ({ ...o, is_correct: i === index }));
                    },

                    submitWith(action) {
                        const form = this.$el;
                        form.querySelector('#action-input').value = action;

                        const container = form.querySelector('#options-container');
                        container.innerHTML = '';

                        this.options.forEach((opt, i) => {
                            const t = document.createElement('input');
                            t.type = 'hidden';
                            t.name = `options[${i}][option_text]`;
                            t.value = opt.option_text;
                            container.appendChild(t);

                            const th = document.createElement('input');
                            th.type = 'hidden';
                            th.name = `options[${i}][option_text_hi]`;
                            th.value = opt.option_text_hi || '';
                            container.appendChild(th);

                            const c = document.createElement('input');
                            c.type = 'hidden';
                            c.name = `options[${i}][is_correct]`;
                            c.value = opt.is_correct ? '1' : '0';
                            container.appendChild(c);
                        });

                        form.submit();
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
