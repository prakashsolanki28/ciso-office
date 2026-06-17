<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('quiz.index') }}"
                    class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight line-clamp-1">
                        {{ $quiz->title }}
                    </h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $quiz->status_badge_class }}">
                            {{ ucfirst($quiz->status) }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $quiz->questions->count() }} {{ Str::plural('question', $quiz->questions->count()) }}
                            · Last saved {{ $quiz->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>

            <form id="delete-quiz-form" method="POST" action="{{ route('quiz.destroy', $quiz) }}" x-data
                @submit.prevent="if(confirm('Permanently delete this quiz and all questions?')) $el.submit()">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-red-600 border border-red-200 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <form method="POST" action="{{ route('quiz.update', $quiz) }}" enctype="multipart/form-data" id="quiz-form">
            @csrf
            @method('PATCH')

            {{-- Flash --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    <div
                        class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    <div class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                        <p class="font-medium mb-1">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex gap-6 items-start">

                    {{-- ── Main Column ── --}}
                    <div class="flex-1 min-w-0 space-y-5">

                        {{-- Banner --}}
                        <div x-data="{
                            dragging: false,
                            preview: '{{ $quiz->banner_url }}',
                            uploading: false,
                            async upload(file) {
                                if (!file || !file.type.startsWith('image/')) return;
                                this.uploading = true;
                                const fd = new FormData();
                                fd.append('banner', file);
                                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                                const res = await fetch('{{ route('quiz.banner.upload', $quiz) }}', { method: 'POST', body: fd });
                                const json = await res.json();
                                this.preview = json.url;
                                this.uploading = false;
                            }
                        }"
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div
                                class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Quiz Banner</h3>
                                <label class="cursor-pointer text-xs text-primary hover:underline font-medium">
                                    {{ $quiz->banner ? 'Change Image' : 'Upload Image' }}
                                    <input type="file" name="banner" accept="image/*" class="hidden"
                                        @change="upload($event.target.files[0])">
                                </label>
                            </div>
                            <div class="relative min-h-44 flex items-center justify-center cursor-pointer"
                                :class="dragging ? 'bg-primary/5 border-2 border-dashed border-primary' :
                                    'bg-gray-50 dark:bg-gray-900/30'"
                                @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; upload($event.dataTransfer.files[0])">
                                <template x-if="preview">
                                    <img :src="preview" alt="Quiz Banner" class="w-full max-h-56 object-cover">
                                </template>
                                <template x-if="!preview">
                                    <div class="text-center p-8">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm text-gray-400">Drag &amp; drop or click <span
                                                class="text-primary">Upload Image</span></p>
                                        <p class="text-xs text-gray-300 mt-1">PNG, JPG, WebP — up to 5 MB</p>
                                    </div>
                                </template>
                                <div x-show="uploading"
                                    class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 flex items-center justify-center">
                                    <svg class="animate-spin w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Title + Description --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5"
                            x-data="{ showHindi: {{ ($quiz->title_hi || $quiz->description_hi) ? 'true' : 'false' }} }">
                            <div class="space-y-4">
                                {{-- English fields --}}
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded">🇬🇧 English</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                        Quiz Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="title" value="{{ old('title', $quiz->title) }}"
                                        required placeholder="Quiz title…"
                                        class="w-full text-lg font-semibold border-0 border-b border-gray-200 dark:border-gray-600 pb-2 bg-transparent text-gray-900 dark:text-gray-100 focus:outline-none focus:border-primary placeholder-gray-300 dark:placeholder-gray-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1.5">Description</label>
                                    <textarea name="description" rows="3" placeholder="What will participants learn or be tested on?…"
                                        class="w-full text-sm border-0 bg-transparent text-gray-700 dark:text-gray-300 focus:outline-none resize-none placeholder-gray-300 dark:placeholder-gray-600">{{ old('description', $quiz->description) }}</textarea>
                                </div>

                                {{-- Hindi translation toggle --}}
                                <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <button type="button" @click="showHindi = !showHindi"
                                        class="flex items-center gap-2 text-sm font-medium transition"
                                        :class="showHindi ? 'text-orange-500 dark:text-orange-400' : 'text-gray-400 hover:text-orange-500 dark:hover:text-orange-400'">
                                        <span>🇮🇳</span>
                                        <span x-text="showHindi ? 'Hide Hindi Translation' : 'Add Hindi Translation (Optional)'"></span>
                                        <svg class="w-4 h-4 transition-transform" :class="showHindi ? 'rotate-180' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="showHindi" x-cloak class="mt-4 space-y-4 p-4 rounded-lg bg-orange-50/50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-900/30">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-semibold text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/30 px-2 py-0.5 rounded">हिंदी अनुवाद</span>
                                            <span class="text-xs text-gray-400">Optional — all Hindi fields</span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-orange-600 dark:text-orange-400 mb-1.5">Quiz Title (हिंदी)</label>
                                            <input type="text" name="title_hi"
                                                value="{{ old('title_hi', $quiz->title_hi) }}"
                                                placeholder="क्विज़ का शीर्षक हिंदी में…"
                                                dir="auto"
                                                class="w-full text-base font-semibold border-0 border-b border-orange-200 dark:border-orange-700/50 pb-2 bg-transparent text-gray-900 dark:text-gray-100 focus:outline-none focus:border-orange-400 placeholder-orange-200 dark:placeholder-orange-800">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-orange-500 dark:text-orange-400 mb-1.5">Description (हिंदी)</label>
                                            <textarea name="description_hi" rows="3"
                                                placeholder="हिंदी में विवरण लिखें… (वैकल्पिक)"
                                                dir="auto"
                                                class="w-full text-sm border-0 bg-transparent text-gray-700 dark:text-gray-300 focus:outline-none resize-none placeholder-orange-200 dark:placeholder-orange-800">{{ old('description_hi', $quiz->description_hi) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Questions List ── --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div
                                class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Questions</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $quiz->questions->count() }} {{ Str::plural('question', $quiz->questions->count()) }} added
                                    </p>
                                </div>
                                <a href="{{ route('quiz.questions.create', $quiz) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary text-xs font-semibold rounded-lg hover:bg-primary/20 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Question
                                </a>
                            </div>

                            @if ($quiz->questions->isEmpty())
                                {{-- Empty state --}}
                                <div class="py-16 text-center">
                                    <div
                                        class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">No questions yet</p>
                                    <p class="text-xs text-gray-400 mt-1">Click "Add Question" to start building your
                                        quiz.</p>
                                    <a href="{{ route('quiz.questions.create', $quiz) }}"
                                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:opacity-90 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add First Question
                                    </a>
                                </div>
                            @else
                                {{-- Question rows --}}
                                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($quiz->questions as $index => $question)
                                        <div
                                            class="flex items-start gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/20 transition group">
                                            {{-- Index badge --}}
                                            <div
                                                class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">
                                                {{ $index + 1 }}
                                            </div>

                                            {{-- Content --}}
                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-sm font-medium text-gray-800 dark:text-gray-200 line-clamp-2">
                                                    {{ $question->question_text }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                    @php
                                                        $typeConfig = match ($question->question_type) {
                                                            'single' => ['label' => 'Single Choice', 'class' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
                                                            'multiple' => ['label' => 'Multiple Choice', 'class' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300'],
                                                            'true_false' => ['label' => 'True / False', 'class' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
                                                            'text' => ['label' => 'Text Answer', 'class' => 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300'],
                                                            default => ['label' => $question->question_type, 'class' => 'bg-gray-100 text-gray-600'],
                                                        };
                                                    @endphp
                                                    <span
                                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $typeConfig['class'] }}">
                                                        {{ $typeConfig['label'] }}
                                                    </span>
                                                    <span class="text-xs text-gray-400">
                                                        {{ $question->marks ?? $quiz->marks_per_question }}
                                                        {{ Str::plural('mark', $question->marks ?? $quiz->marks_per_question) }}
                                                    </span>
                                                    @if ($question->question_type === 'text' || $question->options->where('is_correct', true)->count() > 0)
                                                        <span
                                                            class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400">
                                                            <svg class="w-3 h-3" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Answer set
                                                        </span>
                                                    @endif
                                                    @if ($question->question_text_hi)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400">
                                                            🇮🇳 हिंदी
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Actions (visible on hover) --}}
                                            <div
                                                class="flex items-center gap-0.5 shrink-0 opacity-0 group-hover:opacity-100 transition">
                                                {{-- Move up --}}
                                                @unless ($loop->first)
                                                    <form
                                                        action="{{ route('quiz.questions.move', [$quiz, $question]) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="direction" value="up">
                                                        <button type="submit"
                                                            class="p-1 text-gray-300 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                                            title="Move up">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round"
                                                                    stroke-linejoin="round" stroke-width="2"
                                                                    d="M5 15l7-7 7 7" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="w-6 h-6 p-1 shrink-0"></span>
                                                @endunless
                                                {{-- Move down --}}
                                                @unless ($loop->last)
                                                    <form
                                                        action="{{ route('quiz.questions.move', [$quiz, $question]) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="direction" value="down">
                                                        <button type="submit"
                                                            class="p-1 text-gray-300 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                                            title="Move down">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round"
                                                                    stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="w-6 h-6 p-1 shrink-0"></span>
                                                @endunless
                                                {{-- Edit --}}
                                                <a href="{{ route('quiz.questions.edit', [$quiz, $question]) }}"
                                                    class="p-1 text-gray-400 hover:text-primary transition"
                                                    title="Edit question">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                {{-- Delete --}}
                                                <form
                                                    action="{{ route('quiz.questions.destroy', [$quiz, $question]) }}"
                                                    method="POST" x-data
                                                    @submit.prevent="if(confirm('Delete this question?')) $el.submit()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-1 text-gray-300 hover:text-red-500 transition"
                                                        title="Delete question">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                                {{-- Add question footer --}}
                                <div
                                    class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/10">
                                    <a href="{{ route('quiz.questions.create', $quiz) }}"
                                        class="w-full py-2 border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-400 hover:border-primary hover:text-primary transition font-medium flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Another Question
                                    </a>
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- ── Sidebar ── --}}
                    <div class="w-72 shrink-0 space-y-4 sticky top-6">

                        {{-- Publish / Save --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                            x-data="{ status: '{{ old('status', $quiz->status) }}' }">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Publish</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                                    <select name="status" x-model="status"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1.5">
                                            <span class="flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                                Start Time
                                            </span>
                                        </label>
                                        <input type="datetime-local" name="start_time"
                                            value="{{ old('start_time', $quiz->start_time?->format('Y-m-d\TH:i')) }}"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1.5">
                                            <span class="flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                                End Time
                                            </span>
                                        </label>
                                        <input type="datetime-local" name="end_time"
                                            value="{{ old('end_time', $quiz->end_time?->format('Y-m-d\TH:i')) }}"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <button type="submit" form="quiz-form"
                                        class="w-full py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:opacity-90 transition flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                        Save Quiz
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Language --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                            x-data="{ lang: '{{ old('language', $quiz->language) }}' }">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Language</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Primary language for this quiz</p>
                                </div>
                                <span class="text-lg" x-text="lang === 'hi' ? '🇮🇳' : '🇬🇧'"></span>
                            </div>
                            <div class="p-4 space-y-3">
                                <input type="hidden" name="language" :value="lang">
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="lang='en'"
                                        :class="lang === 'en'
                                            ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                                            : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-blue-400 hover:text-blue-600'"
                                        class="flex items-center justify-center gap-1.5 py-2 rounded-lg border-2 text-xs font-semibold transition">
                                        🇬🇧 English
                                    </button>
                                    <button type="button" @click="lang='hi'"
                                        :class="lang === 'hi'
                                            ? 'bg-orange-500 text-white border-orange-500 shadow-sm'
                                            : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-orange-400 hover:text-orange-500'"
                                        class="flex items-center justify-center gap-1.5 py-2 rounded-lg border-2 text-xs font-semibold transition">
                                        🇮🇳 हिंदी
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400">Hindi translations for title, description &amp; questions are always optional.</p>
                            </div>
                        </div>

                        {{-- Quiz Settings --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                            x-data="{ durType: '{{ old('duration_type', $quiz->duration_type ?? '') }}' }">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Quiz Settings</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                {{-- Marks per question --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Default Marks per
                                        Question</label>
                                    <input type="number" name="marks_per_question" step="0.5" min="0"
                                        value="{{ old('marks_per_question', $quiz->marks_per_question) }}"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    <p class="text-xs text-gray-400 mt-1">Can be overridden per question</p>
                                </div>

                                {{-- Duration type --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Duration Mode</label>
                                    <select name="duration_type" x-model="durType"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                        <option value="">No time limit</option>
                                        <option value="full_paper">Total Paper Duration</option>
                                        <option value="per_question">Per Question Duration</option>
                                    </select>
                                </div>

                                {{-- Full paper duration --}}
                                <div x-show="durType === 'full_paper'" x-cloak>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Total Duration
                                        (minutes)</label>
                                    <input type="number" name="duration_minutes" min="1"
                                        value="{{ old('duration_minutes', $quiz->duration_minutes) }}"
                                        placeholder="e.g. 60" :required="durType === 'full_paper'"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                </div>

                                {{-- Per question duration --}}
                                <div x-show="durType === 'per_question'" x-cloak>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Time per Question
                                        (seconds)</label>
                                    <input type="number" name="duration_per_question" min="5"
                                        value="{{ old('duration_per_question', $quiz->duration_per_question) }}"
                                        placeholder="e.g. 60" :required="durType === 'per_question'"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                </div>

                                {{-- Attempts allowed --}}
                                <div x-data="{ unlimited: {{ (int) old('attempts_allowed', $quiz->attempts_allowed) === 0 ? 'true' : 'false' }} }">
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Attempts Allowed</label>
                                    <input type="number" name="attempts_allowed" min="1"
                                        value="{{ old('attempts_allowed', $quiz->attempts_allowed) ?: 1 }}"
                                        x-show="!unlimited" x-cloak :disabled="unlimited"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    <input type="hidden" name="attempts_allowed" value="0" :disabled="!unlimited">
                                    <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                        <input type="checkbox" x-model="unlimited"
                                            class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary/30">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Unlimited attempts</span>
                                    </label>
                                </div>

                                {{-- Pass percentage --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Pass Percentage (%)</label>
                                    <input type="number" name="pass_percentage" min="0" max="100" step="0.5"
                                        value="{{ old('pass_percentage', $quiz->pass_percentage) }}"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    <p class="text-xs text-gray-400 mt-1">Minimum score to pass the quiz</p>
                                </div>
                            </div>
                        </div>

                        {{-- Access Settings --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Access Settings</h3>
                            </div>
                            <div class="p-4 space-y-4">

                                {{-- Can review paper --}}
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="relative mt-0.5">
                                        <input type="hidden" name="can_review_paper" value="0">
                                        <input type="checkbox" name="can_review_paper" value="1"
                                            {{ old('can_review_paper', $quiz->can_review_paper) ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-9 h-5 rounded-full border-2 transition peer-checked:bg-primary peer-checked:border-primary border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-gray-400 peer-checked:after:bg-white after:rounded-full after:w-3.5 after:h-3.5 after:transition after:peer-checked:translate-x-4">
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Allow Paper
                                            Review</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Participants can review their answers
                                            after submitting</p>
                                    </div>
                                </label>

                                {{-- Can view result --}}
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="relative mt-0.5">
                                        <input type="hidden" name="can_view_result" value="0">
                                        <input type="checkbox" name="can_view_result" value="1"
                                            {{ old('can_view_result', $quiz->can_view_result) ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-9 h-5 rounded-full border-2 transition peer-checked:bg-primary peer-checked:border-primary border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-gray-400 peer-checked:after:bg-white after:rounded-full after:w-3.5 after:h-3.5 after:transition after:peer-checked:translate-x-4">
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Show Result
                                            After Exam</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Participants can see their score and
                                            result immediately</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Stats summary --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Summary</h3>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500">Total Questions</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $quiz->questions->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500">Questions Answered</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $quiz->questions->filter(fn($q) => $q->question_type === 'text' || $q->options->where('is_correct', true)->count() > 0)->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500">Created By</span>
                                    <span
                                        class="font-semibold text-gray-800 dark:text-gray-200">{{ $quiz->creator->name }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500">Last Updated</span>
                                    <span
                                        class="font-semibold text-gray-800 dark:text-gray-200">{{ $quiz->updated_at->format('M d, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            .peer:checked~div {
                background-color: var(--color-primary, #4f46e5);
                border-color: var(--color-primary, #4f46e5);
            }

            .peer:checked~div::after {
                background-color: white;
                transform: translateX(1rem);
            }
        </style>
    @endpush
</x-app-layout>
