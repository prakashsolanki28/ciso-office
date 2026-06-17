<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            {{-- Left: back + title --}}
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('casestudy.index') }}"
                    class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight line-clamp-1">
                        {{ $caseStudy->title }}
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Last saved {{ $caseStudy->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Right: delete --}}
            <form method="POST" action="{{ route('casestudy.destroy', $caseStudy) }}" x-data
                @submit.prevent="if(confirm('Permanently delete this case study?')) $el.submit()">
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

    <div x-data="tiptapEditor({{ json_encode($caseStudy->content ?? '') }}, 'Write the case study…')" class="py-6">
        <form method="POST" action="{{ route('casestudy.update', $caseStudy) }}" enctype="multipart/form-data"
            id="case-study-form" @submit="syncContent()">
            @csrf
            @method('PATCH')
            <input type="hidden" name="content" x-ref="contentInput" value="{{ $caseStudy->content }}">

            {{-- Flash --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex gap-6 items-start">

                    {{-- ── Main Content Column ── --}}
                    <div class="flex-1 min-w-0 space-y-5">

                        {{-- Banner --}}
                        <div x-data="{
                            dragging: false,
                            preview: '{{ $caseStudy->image_url }}',
                            uploading: false,
                            async upload(file) {
                                if (!file || !file.type.startsWith('image/')) return;
                                this.uploading = true;
                                const fd = new FormData();
                                fd.append('image', file);
                                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                                const res = await fetch('{{ route('casestudy.banner.upload', $caseStudy) }}', { method: 'POST', body: fd });
                                const json = await res.json();
                                this.preview = json.url;
                                this.uploading = false;
                            }
                        }"
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Cover Image</h3>
                                <label class="cursor-pointer text-xs text-primary hover:underline font-medium">
                                    <span x-text="preview ? 'Change' : 'Upload'"></span>
                                    <input type="file" name="image" accept="image/*" class="hidden"
                                        @change="upload($event.target.files[0])">
                                </label>
                            </div>

                            <div class="relative min-h-48 flex items-center justify-center"
                                :class="dragging ? 'bg-primary/5 border-2 border-dashed border-primary' : 'bg-gray-50 dark:bg-gray-900/30'"
                                @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; upload($event.dataTransfer.files[0])">
                                <template x-if="preview">
                                    <img :src="preview" alt="Cover" class="w-full max-h-72 object-cover">
                                </template>
                                <template x-if="!preview">
                                    <div class="text-center p-8">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm text-gray-400">Drag &amp; drop or click Upload</p>
                                        <p class="text-xs text-gray-300 mt-1">PNG, JPG, WebP — up to 5 MB</p>
                                    </div>
                                </template>
                                <div x-show="uploading" class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 flex items-center justify-center">
                                    <svg class="animate-spin w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Title + Slug --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title', $caseStudy->title) }}" required
                                class="w-full text-2xl font-bold border-0 border-b border-gray-100 dark:border-gray-700 pb-3 bg-transparent text-gray-900 dark:text-gray-100 focus:outline-none focus:border-primary placeholder-gray-200 dark:placeholder-gray-700"
                                placeholder="Case study title…">
                            @error('title') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-xs text-gray-400 font-mono">/case-studies/</span>
                                <input type="text" name="slug" value="{{ old('slug', $caseStudy->slug) }}" required
                                    class="flex-1 text-xs font-mono border-0 bg-transparent text-gray-400 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 placeholder-gray-300"
                                    placeholder="case-study-slug">
                            </div>
                            @error('slug') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Short Description --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Short Description</label>
                            <textarea name="short_description" rows="2" maxlength="1000"
                                placeholder="A brief one-liner shown on the listing and detail header…"
                                class="w-full text-sm border-0 bg-transparent text-gray-700 dark:text-gray-300 focus:outline-none resize-none placeholder-gray-300 dark:placeholder-gray-600">{{ old('short_description', $caseStudy->short_description) }}</textarea>
                            @error('short_description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Body (TipTap) --}}
                        <x-tiptap-editor label="Case Study Body" />

                        {{-- Results / Outcomes --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Results / Outcomes</label>
                            <textarea name="results" rows="4" maxlength="2000"
                                placeholder="Summarise the measurable outcomes and impact…"
                                class="w-full text-sm border-0 bg-transparent text-gray-700 dark:text-gray-300 focus:outline-none resize-none placeholder-gray-300 dark:placeholder-gray-600">{{ old('results', $caseStudy->results) }}</textarea>
                            @error('results') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- ── Sidebar ── --}}
                    <div class="w-72 shrink-0 space-y-4 sticky top-6">
                        {{-- Publish Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Publish</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                {{-- Status --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                                    <select name="status"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                        @foreach (['draft', 'published', 'archived'] as $st)
                                            <option value="{{ $st }}" @selected(old('status', $caseStudy->status) === $st)>{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                {{-- Client --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Client</label>
                                    <input type="text" name="client" value="{{ old('client', $caseStudy->client) }}"
                                        placeholder="e.g. HRRL Refinery"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    @error('client') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                {{-- Publish at --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Publish at</label>
                                    <input type="datetime-local" name="published_at"
                                        value="{{ old('published_at', optional($caseStudy->published_at)->format('Y-m-d\TH:i')) }}"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    @error('published_at') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <button type="submit" form="case-study-form"
                                    class="w-full py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:opacity-90 transition text-center">
                                    Save Case Study
                                </button>
                            </div>
                        </div>

                        {{-- Meta Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Details</h3>
                            </div>
                            <div class="p-4 space-y-3 text-xs text-gray-500">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Created</span>
                                    <span class="font-medium text-gray-600 dark:text-gray-300">{{ $caseStudy->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Updated</span>
                                    <span class="font-medium text-gray-600 dark:text-gray-300">{{ $caseStudy->updated_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Words</span>
                                    <span class="font-medium text-gray-600 dark:text-gray-300" x-text="wordCount"></span>
                                </div>
                                @if ($caseStudy->status === 'published')
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                        <a href="{{ route('casestudies.public.show', $caseStudy->slug) }}" target="_blank"
                                            class="text-primary hover:underline font-medium inline-flex items-center gap-1">
                                            View live
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
