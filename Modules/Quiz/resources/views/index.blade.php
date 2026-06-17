<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Cyber Awareness Quizzes
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage and publish security awareness quizzes</p>
            </div>
            <button
                x-data
                @click="$dispatch('open-modal', 'create-quiz')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Quiz
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <input
                            type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search quizzes…"
                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                            <option value="">All statuses</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                            <option value="published" @selected(request('status') === 'published')>Published</option>
                            <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Language</label>
                        <select name="language" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                            <option value="">All languages</option>
                            <option value="en" @selected(request('language') === 'en')>🇬🇧 English</option>
                            <option value="hi" @selected(request('language') === 'hi')>🇮🇳 Hindi</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 text-sm bg-gray-900 dark:bg-gray-100 dark:text-gray-900 text-white rounded-lg hover:opacity-80 transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'status', 'language']))
                        <a href="{{ route('quiz.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Clear</a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($quizzes->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 font-semibold text-lg">No quizzes yet</p>
                        <p class="text-sm text-gray-400 mt-1 max-w-xs">Create your first cyber awareness quiz to start testing your team's security knowledge.</p>
                        <button
                            x-data
                            @click="$dispatch('open-modal', 'create-quiz')"
                            class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Create First Quiz
                        </button>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Quiz</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Schedule</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Questions</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($quizzes as $quiz)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($quiz->banner_url)
                                                <img src="{{ $quiz->banner_url }}" alt="" class="w-14 h-10 object-cover rounded-lg shrink-0">
                                            @else
                                                <div class="w-14 h-10 bg-gradient-to-br from-primary/20 to-primary/5 rounded-lg shrink-0 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-primary/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 line-clamp-1">{{ $quiz->title }}</p>
                                                @if ($quiz->description)
                                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $quiz->description }}</p>
                                                @endif
                                                <div class="flex items-center gap-1.5 mt-1">
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold {{ $quiz->language_badge['class'] }}">
                                                        {{ $quiz->language === 'hi' ? '🇮🇳' : '🇬🇧' }} {{ $quiz->language_badge['text'] }}
                                                    </span>
                                                    @if ($quiz->has_hindi_content && $quiz->language === 'en')
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400">
                                                            + हिंदी
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                                            @if ($quiz->start_time)
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                                    {{ $quiz->start_time->format('M d, Y H:i') }}
                                                </div>
                                            @endif
                                            @if ($quiz->end_time)
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                                    {{ $quiz->end_time->format('M d, Y H:i') }}
                                                </div>
                                            @endif
                                            @if (!$quiz->start_time && !$quiz->end_time)
                                                <span class="text-gray-300 dark:text-gray-600">No schedule</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            {{ $quiz->questions_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $quiz->status_badge_class }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            {{ ucfirst($quiz->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('quiz.edit', $quiz) }}"
                                               class="p-1.5 text-gray-400 hover:text-primary dark:hover:text-primary transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                               title="Edit quiz">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('quiz.destroy', $quiz) }}"
                                                  x-data
                                                  @submit.prevent="if(confirm('Delete this quiz and all its questions?')) $el.submit()">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 transition rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20"
                                                        title="Delete quiz">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($quizzes->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $quizzes->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ── Create Quiz Modal ── --}}
    <x-modal name="create-quiz" max-width="lg">
        <div x-data="{ loading: false }">
            <form method="POST" action="{{ route('quiz.store') }}" @submit="loading = true">
                @csrf

                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Quiz</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Fill in the basics — you'll add questions next.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Quiz Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text" name="title"
                            placeholder="e.g. Phishing Awareness Quiz Q1 2025"
                            required autofocus
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                        @error('title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                        <textarea
                            name="description" rows="3"
                            placeholder="Brief description of what this quiz covers…"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"
                        ></textarea>
                    </div>

                    {{-- Language --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Primary Language</label>
                        <div class="grid grid-cols-2 gap-2" x-data="{ lang: 'en' }">
                            <input type="hidden" name="language" :value="lang">
                            <button type="button" @click="lang='en'"
                                :class="lang === 'en' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-blue-400 hover:text-blue-600'"
                                class="flex items-center justify-center gap-2 py-2 rounded-lg border-2 text-sm font-semibold transition">
                                🇬🇧 English
                            </button>
                            <button type="button" @click="lang='hi'"
                                :class="lang === 'hi' ? 'bg-orange-500 text-white border-orange-500 shadow-sm' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-orange-400 hover:text-orange-600'"
                                class="flex items-center justify-center gap-2 py-2 rounded-lg border-2 text-sm font-semibold transition">
                                🇮🇳 हिंदी
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">English is the default. Hindi translations can be added after creation.</p>
                    </div>

                    {{-- Schedule --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                    Start Time
                                </span>
                            </label>
                            <input
                                type="datetime-local" name="start_time"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                    End Time
                                </span>
                            </label>
                            <input
                                type="datetime-local" name="end_time"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            >
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-end gap-3 rounded-b-lg">
                    <button type="button" @click="$dispatch('close-modal', 'create-quiz')"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" :disabled="loading"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 disabled:opacity-50 transition">
                        <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Create &amp; Configure
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    @push('styles')
    <style>[x-cloak] { display: none !important; }</style>
    @endpush
</x-app-layout>
