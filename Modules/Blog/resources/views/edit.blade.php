<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            {{-- Left: back + title --}}
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('blog.index') }}" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight line-clamp-1">
                        {{ $blog->title }}
                    </h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        @php
                            $statusClass = match($blog->status) {
                                'published' => 'bg-green-100 text-green-700',
                                'scheduled' => 'bg-amber-100 text-amber-700',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $statusClass }}">{{ ucfirst($blog->status) }}</span>
                        <span class="text-xs text-gray-400">Last saved {{ $blog->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            {{-- Right: delete button (standalone form, completely outside blog-form) --}}
            <form id="delete-blog-form" method="POST" action="{{ route('blog.destroy', $blog) }}"
                  x-data @submit.prevent="if(confirm('Are you sure you want to permanently delete this post?')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-red-600 border border-red-200 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </x-slot>

    <div
        x-data="tiptapEditor({{ json_encode($blog->content ?? '') }}, 'Start writing your blog post here…')"
        class="py-6"
    >
        <form
            method="POST"
            action="{{ route('blog.update', $blog) }}"
            enctype="multipart/form-data"
            id="blog-form"
            @submit="syncContent()"
        >
            @csrf
            @method('PATCH')
            <input type="hidden" name="content" x-ref="contentInput" value="{{ $blog->content }}">

            {{-- Flash --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex gap-6 items-start">

                    {{-- ── Main Content Column ── --}}
                    <div class="flex-1 min-w-0 space-y-5">

                        {{-- Banner --}}
                        <div
                            x-data="{
                                dragging: false,
                                preview: '{{ $blog->banner_url }}',
                                uploading: false,
                                async upload(file) {
                                    if (!file || !file.type.startsWith('image/')) return;
                                    this.uploading = true;
                                    const fd = new FormData();
                                    fd.append('banner', file);
                                    fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                                    const res = await fetch('{{ route('blog.banner.upload', $blog) }}', { method: 'POST', body: fd });
                                    const json = await res.json();
                                    this.preview = json.url;
                                    this.uploading = false;
                                }
                            }"
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                        >
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Banner Image</h3>
                                <label class="cursor-pointer text-xs text-primary hover:underline font-medium">
                                    {{ $blog->banner ? 'Change' : 'Upload' }}
                                    <input type="file" name="banner" accept="image/*" class="hidden"
                                           @change="upload($event.target.files[0])">
                                </label>
                            </div>

                            <div
                                class="relative min-h-48 flex items-center justify-center"
                                :class="dragging ? 'bg-primary/5 border-2 border-dashed border-primary' : 'bg-gray-50 dark:bg-gray-900/30'"
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; upload($event.dataTransfer.files[0])"
                            >
                                <template x-if="preview">
                                    <img :src="preview" alt="Banner" class="w-full max-h-64 object-cover">
                                </template>
                                <template x-if="!preview">
                                    <div class="text-center p-8">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-sm text-gray-400">Drag &amp; drop or click Upload</p>
                                        <p class="text-xs text-gray-300 mt-1">PNG, JPG, WebP up to 5MB</p>
                                    </div>
                                </template>
                                <div x-show="uploading" class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 flex items-center justify-center">
                                    <svg class="animate-spin w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Title --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Title</label>
                            <input
                                type="text" name="title"
                                value="{{ old('title', $blog->title) }}"
                                required
                                class="w-full text-xl font-semibold border-0 border-b border-gray-200 dark:border-gray-600 pb-2 bg-transparent text-gray-900 dark:text-gray-100 focus:outline-none focus:border-primary placeholder-gray-300 dark:placeholder-gray-600"
                                placeholder="Post title…"
                            >
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-xs text-gray-400">/blog/</span>
                                <input
                                    type="text" name="slug"
                                    value="{{ old('slug', $blog->slug) }}"
                                    required
                                    class="flex-1 text-xs font-mono border-0 bg-transparent text-gray-500 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200"
                                    placeholder="post-slug"
                                >
                            </div>
                        </div>

                        {{-- Excerpt --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Excerpt</label>
                            <textarea
                                name="excerpt" rows="2" maxlength="500"
                                placeholder="A short summary shown in blog listings…"
                                class="w-full text-sm border-0 bg-transparent text-gray-700 dark:text-gray-300 focus:outline-none resize-none placeholder-gray-300 dark:placeholder-gray-600"
                            >{{ old('excerpt', $blog->excerpt) }}</textarea>
                        </div>

                        <x-tiptap-editor label="Content" :read-time="true" />
                    </div>

                    {{-- ── Sidebar ── --}}
                    <div class="w-72 shrink-0 space-y-4 sticky top-6">

                        {{-- Publish Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Publish</h3>
                            </div>
                            <div class="p-4 space-y-4" x-data="{ status: '{{ old('status', $blog->status) }}' }">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                                    <select name="status" x-model="status"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="scheduled">Scheduled</option>
                                    </select>
                                </div>
                                <div x-show="status === 'scheduled'" x-cloak>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Publish At</label>
                                    <input type="datetime-local" name="published_at"
                                           value="{{ old('published_at', $blog->published_at?->format('Y-m-d\TH:i')) }}"
                                           :required="status === 'scheduled'"
                                           class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                </div>
                                @if ($blog->published_at && $blog->status === 'published')
                                    <p class="text-xs text-gray-400">
                                        Published {{ $blog->published_at->format('M d, Y \a\t H:i') }}
                                    </p>
                                @endif
                                <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <button type="submit" form="blog-form"
                                            class="flex-1 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition text-center">
                                        Save
                                    </button>
                                    @if ($blog->status === 'published')
                                        <a href="{{ route('blog.public.show', $blog->slug) }}" target="_blank"
                                           class="p-2 text-gray-400 hover:text-blue-600 transition border border-gray-200 dark:border-gray-600 rounded-lg" title="View live">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Category Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                             x-data="{
                                 adding: false,
                                 newCat: '',
                                 async createCategory() {
                                     if (!this.newCat.trim()) return;
                                     const res = await fetch('{{ route('blog.categories.store') }}', {
                                         method: 'POST',
                                         headers: {
                                             'Content-Type': 'application/json',
                                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                         },
                                         body: JSON.stringify({ name: this.newCat }),
                                     });
                                     const cat = await res.json();
                                     const sel = document.getElementById('category_id');
                                     const opt = new Option(cat.name, cat.id, true, true);
                                     sel.add(opt);
                                     this.newCat = '';
                                     this.adding = false;
                                 }
                             }">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Category</h3>
                                <button type="button" @click="adding = !adding" class="text-xs text-primary hover:underline">
                                    + New
                                </button>
                            </div>
                            <div class="p-4 space-y-3">
                                <select id="category_id" name="category_id"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    <option value="">No category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected($blog->category_id == $cat->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <div x-show="adding" x-cloak class="flex gap-2">
                                    <input type="text" x-model="newCat" placeholder="Category name"
                                           @keydown.enter.prevent="createCategory()"
                                           class="flex-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    <button type="button" @click="createCategory()" class="px-2 py-1 bg-primary text-white text-xs rounded-lg">Add</button>
                                </div>
                            </div>
                        </div>

                        {{-- Tags Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                             x-data="{
                                 tags: {{ json_encode($blog->tags->pluck('name')) }},
                                 input: '',
                                 add() {
                                     const val = this.input.trim();
                                     if (val && !this.tags.includes(val)) this.tags.push(val);
                                     this.input = '';
                                 },
                                 remove(tag) {
                                     this.tags = this.tags.filter(t => t !== tag);
                                 }
                             }">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tags</h3>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="tag in tags" :key="tag">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">
                                            <input type="hidden" name="tags[]" :value="tag">
                                            <span x-text="tag"></span>
                                            <button type="button" @click="remove(tag)" class="text-gray-400 hover:text-red-500 ml-0.5">×</button>
                                        </span>
                                    </template>
                                </div>
                                <input type="text" x-model="input"
                                       placeholder="Add tag…"
                                       @keydown.enter.prevent="add()"
                                       @keydown.comma.prevent="add()"
                                       class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                <p class="text-xs text-gray-400">Press Enter or comma to add</p>
                            </div>
                        </div>

                        {{-- SEO Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <button type="button"
                                    x-data="{ open: false }" @click="open = !open"
                                    class="w-full p-4 flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300"
                                    :class="open ? 'border-b border-gray-100 dark:border-gray-700' : ''"
                                    x-on:click.stop="$refs.seoPanel.style.display = $refs.seoPanel.style.display === 'none' ? 'block' : 'none'">
                                SEO Settings
                                <svg class="w-4 h-4 text-gray-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-ref="seoPanel" style="display:none" class="p-4 space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Meta Title</label>
                                    <input type="text" name="meta_title"
                                           value="{{ old('meta_title', $blog->meta_title) }}"
                                           placeholder="SEO title (60 chars)"
                                           maxlength="255"
                                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Meta Description</label>
                                    <textarea name="meta_description" rows="3" maxlength="500"
                                              placeholder="SEO description (160 chars)"
                                              class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"
                                    >{{ old('meta_description', $blog->meta_description) }}</textarea>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </form>
    </div>

</x-app-layout>
