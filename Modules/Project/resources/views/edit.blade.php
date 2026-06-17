<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            {{-- Left: back + title --}}
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('project.index') }}"
                    class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight line-clamp-1">
                        {{ $project->name }}
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Last saved {{ $project->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Right: delete --}}
            <form method="POST" action="{{ route('project.destroy', $project) }}" x-data
                @submit.prevent="if(confirm('Permanently delete this project?')) $el.submit()">
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

    <div x-data="tiptapEditor({{ json_encode($project->description ?? '') }}, 'Describe this project…')" class="py-6">
        <form method="POST" action="{{ route('project.update', $project) }}" enctype="multipart/form-data"
            id="project-form" @submit="syncContent()">
            @csrf
            @method('PATCH')
            <input type="hidden" name="description" x-ref="contentInput" value="{{ $project->description }}">

            {{-- Flash --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    <div
                        class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
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
                            preview: '{{ $project->banner_url }}',
                            uploading: false,
                            async upload(file) {
                                if (!file || !file.type.startsWith('image/')) return;
                                this.uploading = true;
                                const fd = new FormData();
                                fd.append('banner', file);
                                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                                const res = await fetch('{{ route('project.banner.upload', $project) }}', { method: 'POST', body: fd });
                                const json = await res.json();
                                this.preview = json.url;
                                this.uploading = false;
                            }
                        }"
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div
                                class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Banner Image</h3>
                                <label class="cursor-pointer text-xs text-primary hover:underline font-medium">
                                    <span x-text="preview ? 'Change' : 'Upload'"></span>
                                    <input type="file" name="banner" accept="image/*" class="hidden"
                                        @change="upload($event.target.files[0])">
                                </label>
                            </div>

                            <div class="relative min-h-48 flex items-center justify-center"
                                :class="dragging ? 'bg-primary/5 border-2 border-dashed border-primary' :
                                    'bg-gray-50 dark:bg-gray-900/30'"
                                @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; upload($event.dataTransfer.files[0])">
                                <template x-if="preview">
                                    <img :src="preview" alt="Banner" class="w-full max-h-72 object-cover">
                                </template>
                                <template x-if="!preview">
                                    <div class="text-center p-8">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm text-gray-400">Drag &amp; drop or click Upload</p>
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

                        {{-- Name + Slug --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                            <label
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Project
                                Name</label>
                            <input type="text" name="name" value="{{ old('name', $project->name) }}" required
                                class="w-full text-2xl font-bold border-0 border-b border-gray-100 dark:border-gray-700 pb-3 bg-transparent text-gray-900 dark:text-gray-100 focus:outline-none focus:border-primary placeholder-gray-200 dark:placeholder-gray-700"
                                placeholder="Project name…">
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-xs text-gray-400 font-mono">/projects/</span>
                                <input type="text" name="slug" value="{{ old('slug', $project->slug) }}"
                                    required
                                    class="flex-1 text-xs font-mono border-0 bg-transparent text-gray-400 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 placeholder-gray-300"
                                    placeholder="project-slug">
                            </div>
                        </div>
                        {{-- Short Description --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                            <label
                                class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Short
                                Description</label>
                            <textarea name="short_description" rows="2" maxlength="500"
                                placeholder="A brief one-liner about this project…"
                                class="w-full text-sm border-0 bg-transparent text-gray-700 dark:text-gray-300 focus:outline-none resize-none placeholder-gray-300 dark:placeholder-gray-600">{{ old('short_description', $project->short_description) }}</textarea>
                        </div>
                        <x-tiptap-editor label="Description" />

                        {{-- Specifications / Features --}}
                        <div x-data="specsManager({{ Js::from($project->specifications ?? []) }})">

                            <input type="hidden" name="specifications" :value="JSON.stringify(items)">

                            {{-- Click-outside overlay: closes any open icon picker --}}
                            <div x-show="activePickerIndex !== null"
                                 @click="closePicker()"
                                 class="fixed inset-0 z-40"
                                 style="display:none;"></div>

                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">

                                {{-- Header --}}
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Specifications / Features</h3>
                                        <p class="text-xs text-gray-400 mt-0.5">Highlight key features or technical specs.</p>
                                    </div>
                                    <button type="button" @click="add()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-primary text-white rounded-lg hover:opacity-90 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Item
                                    </button>
                                </div>

                                {{-- Column Labels (shown only when items exist) --}}
                                <template x-if="items.length > 0">
                                    <div class="flex items-center gap-2 px-4 py-1.5 bg-gray-50 dark:bg-gray-700/40 border-b border-gray-100 dark:border-gray-700">
                                        <span class="w-[72px] shrink-0 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Icon</span>
                                        <span class="w-44 shrink-0 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Title</span>
                                        <span class="flex-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Description</span>
                                        <span class="w-7 shrink-0"></span>
                                    </div>
                                </template>

                                {{-- Empty State --}}
                                <template x-if="items.length === 0">
                                    <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-sm text-gray-400">No specifications yet.</p>
                                        <p class="text-xs text-gray-300 mt-0.5">Click "Add Item" to begin.</p>
                                    </div>
                                </template>

                                {{-- Item Rows --}}
                                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                    <template x-for="(item, index) in items" :key="index">

                                        <div class="flex items-center gap-2 px-4 py-2.5">

                                            {{-- ── Custom Icon Picker ── --}}
                                            <div class="relative shrink-0 w-[72px]">
                                                {{-- Trigger button --}}
                                                <button type="button"
                                                    @click="togglePicker(index, $event)"
                                                    class="w-full flex items-center justify-between gap-1 pl-2 pr-1.5 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 hover:border-primary/50 hover:bg-gray-50 dark:hover:bg-gray-600 transition text-gray-600 dark:text-gray-300"
                                                    :class="activePickerIndex === index ? 'border-primary/60 ring-2 ring-primary/20' : ''">
                                                    <span class="flex items-center justify-center w-4 h-4"
                                                          x-html="iconTag(item.icon, 16)"></span>
                                                    <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform"
                                                         :class="activePickerIndex === index ? 'rotate-180' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>

                                                {{-- Dropdown panel: flips up or down based on available viewport space --}}
                                                <div x-show="activePickerIndex === index"
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-75"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="absolute left-0 z-50 w-64 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl p-2.5 origin-top-left"
                                                     :class="pickerOpensUp ? 'bottom-full mb-1.5 origin-bottom-left' : 'top-full mt-1.5 origin-top-left'"
                                                     style="display:none;">

                                                    {{-- Search --}}
                                                    <div class="relative mb-2">
                                                        <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.3-4.3M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/>
                                                        </svg>
                                                        <input type="text" x-model="iconSearch"
                                                            placeholder="Search icons…"
                                                            class="w-full pl-7 pr-2 py-1.5 text-xs border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                                    </div>

                                                    {{-- Icon grid --}}
                                                    {{-- Only the open picker instantiates its icon grid. Otherwise every (hidden) picker would render the full ~1,650-icon set into the DOM, freezing/crashing the page. --}}
                                                    <template x-if="activePickerIndex === index">
                                                        <div class="grid grid-cols-8 gap-0.5 max-h-44 overflow-y-auto">
                                                            <template x-for="name in filteredIcons()" :key="name">
                                                                <button type="button"
                                                                    @click.stop="selectIcon(item, name)"
                                                                    :title="name"
                                                                    :class="item.icon === name
                                                                        ? 'bg-primary/10 text-primary ring-1 ring-inset ring-primary/30'
                                                                        : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700'"
                                                                    class="p-1.5 rounded-md flex items-center justify-center transition">
                                                                    <i :data-lucide="name"
                                                                       style="width:15px;height:15px;display:block;pointer-events:none;"></i>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <template x-if="activePickerIndex === index && filteredIconsTruncated()">
                                                        <p class="text-center text-[11px] text-gray-400 pt-1.5">Showing first <span x-text="iconLimit"></span> — type to search.</p>
                                                    </template>

                                                    <template x-if="activePickerIndex === index && filteredIcons().length === 0">
                                                        <p class="text-center text-xs text-gray-400 py-3">No icons found.</p>
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- Title input --}}
                                            <input type="text" x-model="item.title"
                                                placeholder="Feature title"
                                                class="w-44 shrink-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">

                                            {{-- Description input --}}
                                            <input type="text" x-model="item.description"
                                                placeholder="Brief description…"
                                                class="flex-1 min-w-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">

                                            {{-- Remove --}}
                                            <button type="button" @click="remove(index)"
                                                class="shrink-0 p-1.5 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition" title="Remove">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>

                                        </div>
                                    </template>
                                </div>

                                {{-- Footer --}}
                                <template x-if="items.length > 0">
                                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 rounded-b-xl flex items-center justify-between">
                                        <span class="text-xs text-gray-400" x-text="items.length + ' item' + (items.length !== 1 ? 's' : '')"></span>
                                        <button type="button" @click="add()"
                                            class="text-xs text-primary hover:underline font-medium">+ Add another</button>
                                    </div>
                                </template>

                            </div>
                        </div>

                        {{-- Statistics --}}
                        <div x-data="statsManager({{ Js::from($project->statistics ?? []) }})">

                            <input type="hidden" name="statistics" :value="JSON.stringify(items)">

                            {{-- Click-outside overlay --}}
                            <div x-show="activePickerIndex !== null"
                                 @click="closePicker()"
                                 class="fixed inset-0 z-40"
                                 style="display:none;"></div>

                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">

                                {{-- Header --}}
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Statistics</h3>
                                        <p class="text-xs text-gray-400 mt-0.5">Key metrics and data points.</p>
                                    </div>
                                    <button type="button" @click="add()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-primary text-white rounded-lg hover:opacity-90 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Stat
                                    </button>
                                </div>

                                {{-- Column labels --}}
                                <template x-if="items.length > 0">
                                    <div class="flex items-center gap-2 px-4 py-1.5 bg-gray-50 dark:bg-gray-700/40 border-b border-gray-100 dark:border-gray-700">
                                        <span class="w-[72px] shrink-0 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Icon</span>
                                        <span class="w-44 shrink-0 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Key</span>
                                        <span class="flex-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Value</span>
                                        <span class="w-7 shrink-0"></span>
                                    </div>
                                </template>

                                {{-- Empty state --}}
                                <template x-if="items.length === 0">
                                    <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        <p class="text-sm text-gray-400">No statistics yet.</p>
                                        <p class="text-xs text-gray-300 mt-0.5">Click "Add Stat" to begin.</p>
                                    </div>
                                </template>

                                {{-- Stat rows --}}
                                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="flex items-center gap-2 px-4 py-2.5">

                                            {{-- Icon picker --}}
                                            <div class="relative shrink-0 w-[72px]">
                                                <button type="button"
                                                    @click="togglePicker(index, $event)"
                                                    class="w-full flex items-center justify-between gap-1 pl-2 pr-1.5 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 hover:border-primary/50 hover:bg-gray-50 dark:hover:bg-gray-600 transition text-gray-600 dark:text-gray-300"
                                                    :class="activePickerIndex === index ? 'border-primary/60 ring-2 ring-primary/20' : ''">
                                                    <span class="flex items-center justify-center w-4 h-4"
                                                          x-html="iconTag(item.icon, 16)"></span>
                                                    <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform"
                                                         :class="activePickerIndex === index ? 'rotate-180' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>

                                                {{-- Dropdown --}}
                                                <div x-show="activePickerIndex === index"
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-75"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="absolute left-0 z-50 w-64 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl p-2.5"
                                                     :class="pickerOpensUp ? 'bottom-full mb-1.5 origin-bottom-left' : 'top-full mt-1.5 origin-top-left'"
                                                     style="display:none;">

                                                    <div class="relative mb-2">
                                                        <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.3-4.3M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/>
                                                        </svg>
                                                        <input type="text" x-model="iconSearch"
                                                            placeholder="Search icons…"
                                                            class="w-full pl-7 pr-2 py-1.5 text-xs border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                                    </div>

                                                    {{-- Only the open picker instantiates its icon grid. Otherwise every (hidden) picker would render the full ~1,650-icon set into the DOM, freezing/crashing the page. --}}
                                                    <template x-if="activePickerIndex === index">
                                                        <div class="grid grid-cols-8 gap-0.5 max-h-44 overflow-y-auto">
                                                            <template x-for="name in filteredIcons()" :key="name">
                                                                <button type="button"
                                                                    @click.stop="selectIcon(item, name)"
                                                                    :title="name"
                                                                    :class="item.icon === name
                                                                        ? 'bg-primary/10 text-primary ring-1 ring-inset ring-primary/30'
                                                                        : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700'"
                                                                    class="p-1.5 rounded-md flex items-center justify-center transition">
                                                                    <i :data-lucide="name"
                                                                       style="width:15px;height:15px;display:block;pointer-events:none;"></i>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <template x-if="activePickerIndex === index && filteredIconsTruncated()">
                                                        <p class="text-center text-[11px] text-gray-400 pt-1.5">Showing first <span x-text="iconLimit"></span> — type to search.</p>
                                                    </template>

                                                    <template x-if="activePickerIndex === index && filteredIcons().length === 0">
                                                        <p class="text-center text-xs text-gray-400 py-3">No icons found.</p>
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- Key --}}
                                            <input type="text" x-model="item.key"
                                                placeholder="e.g. Uptime"
                                                class="w-44 shrink-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">

                                            {{-- Value --}}
                                            <input type="text" x-model="item.value"
                                                placeholder="e.g. 99.9%"
                                                class="flex-1 min-w-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">

                                            {{-- Remove --}}
                                            <button type="button" @click="remove(index)"
                                                class="shrink-0 p-1.5 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition" title="Remove">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>

                                        </div>
                                    </template>
                                </div>

                                {{-- Footer --}}
                                <template x-if="items.length > 0">
                                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 rounded-b-xl flex items-center justify-between">
                                        <span class="text-xs text-gray-400" x-text="items.length + ' stat' + (items.length !== 1 ? 's' : '')"></span>
                                        <button type="button" @click="add()"
                                            class="text-xs text-primary hover:underline font-medium">+ Add another</button>
                                    </div>
                                </template>

                            </div>
                        </div>

                        {{-- Charts --}}
                        <div x-data="chartsManager({{ Js::from($project->charts ?? []) }})">

                            <input type="hidden" name="charts" :value="JSON.stringify(charts)">

                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">

                                {{-- Header --}}
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Charts</h3>
                                        <p class="text-xs text-gray-400 mt-0.5">Graphs shown on the public page (e.g. Support, Implementation…).</p>
                                    </div>
                                    <button type="button" @click="addChart()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-primary text-white rounded-lg hover:opacity-90 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Chart
                                    </button>
                                </div>

                                {{-- Empty state --}}
                                <template x-if="charts.length === 0">
                                    <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        <p class="text-sm text-gray-400">No charts yet.</p>
                                        <p class="text-xs text-gray-300 mt-0.5">Click "Add Chart" to begin.</p>
                                    </div>
                                </template>

                                {{-- Chart cards --}}
                                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                    <template x-for="(chart, ci) in charts" :key="ci">
                                        <div class="p-4 space-y-3">

                                            {{-- Chart title + type + remove --}}
                                            <div class="flex items-center gap-2">
                                                <input type="text" x-model="chart.title"
                                                    placeholder="Chart title — e.g. Coverage"
                                                    class="flex-1 min-w-0 text-sm font-medium border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">

                                                <select x-model="chart.type"
                                                    class="w-32 shrink-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                                    <option value="bar">Bar / Column</option>
                                                    <option value="line">Line</option>
                                                    <option value="area">Area</option>
                                                    <option value="radar">Radar</option>
                                                    <option value="pie">Pie</option>
                                                </select>

                                                <button type="button" @click="removeChart(ci)"
                                                    class="shrink-0 p-1.5 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition" title="Remove chart">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- Data points --}}
                                            <div class="rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-700/20">
                                                <div class="flex items-center gap-2 px-3 py-1.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="flex-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Label</span>
                                                    <span class="w-32 shrink-0 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Value</span>
                                                    <span class="w-9 shrink-0 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Color</span>
                                                    <span class="w-7 shrink-0"></span>
                                                </div>

                                                <template x-if="chart.points.length === 0">
                                                    <p class="px-3 py-3 text-xs text-gray-400">No data points. Add one below.</p>
                                                </template>

                                                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                                    <template x-for="(point, pi) in chart.points" :key="pi">
                                                        <div class="flex items-center gap-2 px-3 py-2">
                                                            <input type="text" x-model="point.label"
                                                                placeholder="e.g. Support"
                                                                class="flex-1 min-w-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                                            <input type="number" step="any" x-model="point.value"
                                                                placeholder="e.g. 80"
                                                                class="w-32 shrink-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                                            <input type="color" x-model="point.color"
                                                                class="w-9 h-9 shrink-0 p-0.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer"
                                                                title="Bar color">
                                                            <button type="button" @click="removePoint(chart, pi)"
                                                                class="shrink-0 p-1.5 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition" title="Remove data point">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>

                                                <div class="px-3 py-2 border-t border-gray-100 dark:border-gray-700">
                                                    <button type="button" @click="addPoint(chart)"
                                                        class="text-xs text-primary hover:underline font-medium">+ Add data point</button>
                                                </div>
                                            </div>

                                        </div>
                                    </template>
                                </div>

                                {{-- Footer --}}
                                <template x-if="charts.length > 0">
                                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 rounded-b-xl flex items-center justify-between">
                                        <span class="text-xs text-gray-400" x-text="charts.length + ' chart' + (charts.length !== 1 ? 's' : '')"></span>
                                        <button type="button" @click="addChart()"
                                            class="text-xs text-primary hover:underline font-medium">+ Add another</button>
                                    </div>
                                </template>

                            </div>
                        </div>

                        {{-- Before / After grid --}}
                        <div class="grid grid-cols-2 gap-5">

                            {{-- ── Before ── --}}
                            <div x-data="pointsManager({{ Js::from($project->before_points ?? []) }})"
                                 class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

                                <input type="hidden" name="before_points" :value="JSON.stringify(items)">

                                {{-- Header --}}
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Before</h3>
                                            <p class="text-xs text-gray-400 mt-0.5">State before implementation.</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="add()"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 rounded-lg hover:bg-amber-100 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add
                                    </button>
                                </div>

                                {{-- Empty state --}}
                                <template x-if="items.length === 0">
                                    <div class="flex flex-col items-center justify-center py-8 text-center px-4">
                                        <svg class="w-7 h-7 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <p class="text-xs text-gray-400">No before points yet.</p>
                                    </div>
                                </template>

                                {{-- Point rows --}}
                                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="flex items-center gap-2 px-3 py-2">
                                            <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-amber-400 mt-0.5"></span>
                                            <input type="text" x-model="item.text"
                                                placeholder="Describe the before state…"
                                                class="flex-1 min-w-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-amber-300/50 focus:border-amber-400">
                                            <button type="button" @click="remove(index)"
                                                class="shrink-0 p-1 rounded text-gray-300 hover:text-red-500 transition" title="Remove">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Footer --}}
                                <template x-if="items.length > 0">
                                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between rounded-b-xl">
                                        <span class="text-xs text-gray-400" x-text="items.length + ' point' + (items.length !== 1 ? 's' : '')"></span>
                                        <button type="button" @click="add()" class="text-xs text-amber-600 hover:underline font-medium">+ Add</button>
                                    </div>
                                </template>
                            </div>

                            {{-- ── After ── --}}
                            <div x-data="pointsManager({{ Js::from($project->after_points ?? []) }})"
                                 class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

                                <input type="hidden" name="after_points" :value="JSON.stringify(items)">

                                {{-- Header --}}
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">After</h3>
                                            <p class="text-xs text-gray-400 mt-0.5">Outcome after implementation.</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="add()"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded-lg hover:bg-emerald-100 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add
                                    </button>
                                </div>

                                {{-- Empty state --}}
                                <template x-if="items.length === 0">
                                    <div class="flex flex-col items-center justify-center py-8 text-center px-4">
                                        <svg class="w-7 h-7 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-xs text-gray-400">No after points yet.</p>
                                    </div>
                                </template>

                                {{-- Point rows --}}
                                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="flex items-center gap-2 px-3 py-2">
                                            <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-emerald-400 mt-0.5"></span>
                                            <input type="text" x-model="item.text"
                                                placeholder="Describe the after state…"
                                                class="flex-1 min-w-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-300/50 focus:border-emerald-400">
                                            <button type="button" @click="remove(index)"
                                                class="shrink-0 p-1 rounded text-gray-300 hover:text-red-500 transition" title="Remove">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Footer --}}
                                <template x-if="items.length > 0">
                                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between rounded-b-xl">
                                        <span class="text-xs text-gray-400" x-text="items.length + ' point' + (items.length !== 1 ? 's' : '')"></span>
                                        <button type="button" @click="add()" class="text-xs text-emerald-600 hover:underline font-medium">+ Add</button>
                                    </div>
                                </template>
                            </div>

                        </div>{{-- /before-after grid --}}

                        {{-- Onboard Accounts --}}
                        @php
                            $accountsData = collect($project->onboard_accounts ?? [])->map(fn($a) => array_merge($a, [
                                'logo_url'  => !empty($a['logo']) ? asset('storage/' . $a['logo']) : null,
                                'uploading' => false,
                            ]))->all();
                        @endphp

                        <div x-data="accountsManager(
                                {{ Js::from($accountsData) }},
                                '{{ route('project.account-logo.upload', $project) }}'
                             )"
                             class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

                            <input type="hidden" name="onboard_accounts" :value="serialized()">

                            {{-- Header --}}
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Onboard Accounts</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Accounts linked to this project.</p>
                                </div>
                                <button type="button" @click="add()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-primary text-white rounded-lg hover:opacity-90 transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Account
                                </button>
                            </div>

                            {{-- Empty state --}}
                            <template x-if="items.length === 0">
                                <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-sm text-gray-400">No accounts linked yet.</p>
                                    <p class="text-xs text-gray-300 mt-0.5">Click "Add Account" to begin.</p>
                                </div>
                            </template>

                            {{-- Account rows --}}
                            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex items-center gap-3 px-4 py-3">

                                        {{-- Logo upload --}}
                                        <div class="relative shrink-0 w-10 h-10 group cursor-pointer">
                                            {{-- Preview / placeholder --}}
                                            <div class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 overflow-hidden flex items-center justify-center">
                                                <template x-if="item.logo_url">
                                                    <img :src="item.logo_url" alt="logo" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!item.logo_url">
                                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </template>
                                            </div>

                                            {{-- Upload spinner --}}
                                            <div x-show="item.uploading"
                                                 class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 rounded-lg flex items-center justify-center">
                                                <svg class="animate-spin w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                            </div>

                                            {{-- Hover overlay --}}
                                            <div class="absolute inset-0 rounded-lg bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center pointer-events-none">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                            </div>

                                            {{-- Hidden file input --}}
                                            <input type="file" accept="image/*"
                                                   @change="uploadLogo(item, $event)"
                                                   class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                                   :disabled="item.uploading">
                                        </div>

                                        {{-- Name --}}
                                        <input type="text" x-model="item.name"
                                            placeholder="Account name *"
                                            class="w-40 shrink-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">

                                        {{-- Description --}}
                                        <input type="text" x-model="item.description"
                                            placeholder="Short description…"
                                            class="flex-1 min-w-0 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">

                                        {{-- Remove --}}
                                        <button type="button" @click="remove(index)"
                                            class="shrink-0 p-1.5 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition" title="Remove">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>

                                    </div>
                                </template>
                            </div>

                            {{-- Footer --}}
                            <template x-if="items.length > 0">
                                <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 rounded-b-xl flex items-center justify-between">
                                    <span class="text-xs text-gray-400" x-text="items.length + ' account' + (items.length !== 1 ? 's' : '')"></span>
                                    <button type="button" @click="add()" class="text-xs text-primary hover:underline font-medium">+ Add another</button>
                                </div>
                            </template>

                        </div>{{-- /onboard-accounts --}}

                        {{-- Gallery --}}
                        @php
                            $galleryData = collect($project->gallery ?? [])->map(fn($g) => array_merge($g, [
                                'image_url' => !empty($g['image']) ? asset('storage/' . $g['image']) : null,
                                'uploading' => false,
                            ]))->all();
                        @endphp

                        <div x-data="galleryManager(
                                {{ Js::from($galleryData) }},
                                '{{ route('project.gallery-image.upload', $project) }}'
                             )"
                             @dragover.prevent="dragging = true"
                             @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; handleDrop($event)"
                             class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors"
                             :class="dragging ? 'border-primary/50 bg-primary/5' : ''">

                            <input type="hidden" name="gallery" :value="serialized()">

                            {{-- Hidden multi-file input --}}
                            <input type="file" x-ref="fileInput" accept="image/*" multiple
                                   @change="handleFiles($event)"
                                   class="hidden">

                            {{-- Header --}}
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Gallery</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Project images with captions.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span x-show="uploading" class="text-xs text-gray-400 animate-pulse">Uploading…</span>
                                    <button type="button" @click="$refs.fileInput.click()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-primary text-white rounded-lg hover:opacity-90 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Add Images
                                    </button>
                                </div>
                            </div>

                            {{-- Empty / drop-zone state --}}
                            <template x-if="items.length === 0">
                                <div @click="$refs.fileInput.click()"
                                     class="flex flex-col items-center justify-center py-14 cursor-pointer select-none">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3"
                                         :class="dragging ? 'bg-primary/10 text-primary' : 'text-gray-300'">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                        <span :class="dragging ? 'text-primary' : ''">Drop images here</span>
                                        or <span class="text-primary underline underline-offset-2">browse</span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, WebP — up to 5 MB each</p>
                                </div>
                            </template>

                            {{-- Grid --}}
                            <template x-if="items.length > 0">
                                <div class="p-4 grid grid-cols-3 gap-3">

                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="relative group rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 flex flex-col">

                                            {{-- Image area --}}
                                            <div class="relative aspect-video bg-gray-100 dark:bg-gray-700 overflow-hidden">

                                                {{-- Preview --}}
                                                <template x-if="item.image_url && !item.uploading">
                                                    <img :src="item.image_url" :alt="item.caption || 'gallery'"
                                                         class="w-full h-full object-cover">
                                                </template>

                                                {{-- Upload placeholder --}}
                                                <template x-if="item.uploading">
                                                    <div class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                                        <svg class="animate-spin w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                        </svg>
                                                    </div>
                                                </template>

                                                {{-- Remove button --}}
                                                <button type="button" @click="remove(index)"
                                                    class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-black/50 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition hover:bg-red-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>

                                                {{-- Image index badge --}}
                                                <div class="absolute bottom-1.5 left-1.5 w-5 h-5 rounded bg-black/40 text-white text-[10px] font-medium flex items-center justify-center"
                                                     x-text="index + 1"></div>
                                            </div>

                                            {{-- Caption --}}
                                            <div class="px-2 py-1.5">
                                                <input type="text" x-model="item.caption"
                                                    placeholder="Add caption…"
                                                    class="w-full text-xs border-0 bg-transparent text-gray-600 dark:text-gray-300 placeholder-gray-300 dark:placeholder-gray-600 focus:ring-0 focus:outline-none p-0">
                                            </div>

                                        </div>
                                    </template>

                                    {{-- Inline add-more tile --}}
                                    <button type="button" @click="$refs.fileInput.click()"
                                        class="aspect-video rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 hover:border-primary/50 hover:bg-primary/5 transition flex flex-col items-center justify-center gap-1.5 text-gray-400 hover:text-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span class="text-xs font-medium">Add more</span>
                                    </button>

                                </div>
                            </template>

                            {{-- Footer --}}
                            <template x-if="items.length > 0">
                                <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 rounded-b-xl flex items-center justify-between">
                                    <span class="text-xs text-gray-400" x-text="items.filter(i => i.image).length + ' image' + (items.filter(i => i.image).length !== 1 ? 's' : '')"></span>
                                    <span class="text-xs text-gray-400">Drag &amp; drop supported</span>
                                </div>
                            </template>

                        </div>{{-- /gallery --}}

                    </div>
                    {{-- ── Sidebar ── --}}
                    <div class="w-72 shrink-0 space-y-4 sticky top-6">
                        {{-- Save Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Save</h3>
                            </div>
                            <div class="p-4 space-y-3">
                                <p class="text-xs text-gray-400 leading-relaxed">
                                    Changes are saved manually. Click <strong
                                        class="text-gray-600 dark:text-gray-300">Save Project</strong> to persist your
                                    edits.
                                </p>
                                <button type="submit" form="project-form"
                                    class="w-full py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:opacity-90 transition text-center">
                                    Save Project
                                </button>
                            </div>
                        </div>
                        {{-- Meta Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Details</h3>
                            </div>
                            <div class="p-4 space-y-3 text-xs text-gray-500">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Created</span>
                                    <span
                                        class="font-medium text-gray-600 dark:text-gray-300">{{ $project->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Updated</span>
                                    <span
                                        class="font-medium text-gray-600 dark:text-gray-300">{{ $project->updated_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Words</span>
                                    <span class="font-medium text-gray-600 dark:text-gray-300"
                                        x-text="wordCount"></span>
                                </div>
                                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <p class="text-gray-400 mb-1">Slug</p>
                                    <p class="font-mono text-gray-600 dark:text-gray-300 break-all">
                                        {{ $project->slug }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

@php
    // Read icon names from public/js/icons.js (the single source of truth).
    $iconsJs      = file_get_contents(public_path('js/icons.js'));
    preg_match_all('/"([a-z][a-z0-9-]+)"/', $iconsJs, $iconsMatch);
    $specIconNames = $iconsMatch[1] ?? [];
@endphp

@push('scripts')
<script>
(function () {
    // Icon names sourced from public/js/icons.js (injected server-side).
    const ICON_NAMES = {!! json_encode($specIconNames) !!};

    // Shared picker behaviour — spread into any Alpine component that needs an icon picker.
    // `itemFactory` defines the blank item shape for that component.
    function pickerMixin(itemFactory) {
        return {
            activePickerIndex: null,
            pickerOpensUp: false,
            iconSearch: '',

            init() {
                this.$nextTick(() => window._lucideCreate?.());
                this.$watch('iconSearch', () => this.$nextTick(() => window._lucideCreate?.()));
            },

            add() {
                this.items.push(itemFactory());
                this.$nextTick(() => window._lucideCreate?.());
            },

            remove(index) {
                if (this.activePickerIndex === index) this.activePickerIndex = null;
                this.items.splice(index, 1);
            },

            togglePicker(index, event) {
                if (this.activePickerIndex === index) {
                    this.activePickerIndex = null;
                    this.iconSearch = '';
                    return;
                }
                const rect = event.currentTarget.getBoundingClientRect();
                this.pickerOpensUp = (window.innerHeight - rect.bottom) < 260;
                this.activePickerIndex = index;
                this.iconSearch = '';
                this.$nextTick(() => window._lucideCreate?.());
            },

            closePicker() {
                this.activePickerIndex = null;
                this.iconSearch = '';
            },

            selectIcon(item, name) {
                item.icon = name;
                this.closePicker();
                this.$nextTick(() => window._lucideCreate?.());
            },

            // Cap how many icons render at once. The full Lucide set is ~1,650
            // icons; rendering them all (in every picker, even while closed) used
            // to freeze the page and crash the tab. 80 = 10 rows in the 8-col grid.
            iconLimit: 80,

            filteredIcons() {
                const q = this.iconSearch.trim().toLowerCase();
                const matches = q ? ICON_NAMES.filter(n => n.includes(q)) : ICON_NAMES;
                return matches.slice(0, this.iconLimit);
            },

            // True when matches were trimmed by iconLimit, so we can nudge the
            // user to narrow their search instead of silently hiding icons.
            filteredIconsTruncated() {
                const q = this.iconSearch.trim().toLowerCase();
                const total = q ? ICON_NAMES.filter(n => n.includes(q)).length : ICON_NAMES.length;
                return total > this.iconLimit;
            },

            iconTag(name, size = 16) {
                return `<i data-lucide="${name || 'circle-dashed'}" style="width:${size}px;height:${size}px;display:block;"></i>`;
            },
        };
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('specsManager', (initial) => ({
            ...pickerMixin(() => ({ icon: '', title: '', description: '' })),
            items: Array.isArray(initial) ? initial : [],
        }));

        Alpine.data('statsManager', (initial) => ({
            ...pickerMixin(() => ({ icon: '', key: '', value: '' })),
            items: Array.isArray(initial) ? initial : [],
        }));

        // Charts — nested repeater (chart → data points). No icon picker.
        // Brand palette (matches resources/views/projects/show.blade.php) used as the
        // default per-point color, so charts never look randomly coloured.
        const CHART_PALETTE = ['#236390', '#E6930A', '#0a2342', '#5b8fb0', '#94ccff', '#105783', '#b2c7ef'];
        Alpine.data('chartsManager', (initial) => ({
            charts: (Array.isArray(initial) ? initial : []).map(c => ({
                title: c.title || '',
                type: c.type || 'bar',
                points: Array.isArray(c.points)
                    ? c.points.map((p, i) => ({ label: p.label || '', value: p.value ?? '', color: p.color || CHART_PALETTE[i % CHART_PALETTE.length] }))
                    : [],
            })),
            addChart() {
                this.charts.push({ title: '', type: 'bar', points: [{ label: '', value: '', color: CHART_PALETTE[0] }] });
            },
            removeChart(index) {
                this.charts.splice(index, 1);
            },
            addPoint(chart) {
                chart.points.push({ label: '', value: '', color: CHART_PALETTE[chart.points.length % CHART_PALETTE.length] });
            },
            removePoint(chart, index) {
                chart.points.splice(index, 1);
            },
        }));

        // Gallery — images with captions, AJAX upload, drag-and-drop.
        Alpine.data('galleryManager', (initial, uploadUrl) => ({
            items: (Array.isArray(initial) ? initial : []).map(g => ({
                image:     g.image     || '',
                image_url: g.image_url || null,
                caption:   g.caption   || '',
                uploading: false,
            })),
            uploadUrl,
            dragging: false,
            uploading: false,

            remove(index) {
                this.items.splice(index, 1);
            },

            handleFiles(event) {
                this._uploadFiles(Array.from(event.target.files));
                event.target.value = '';
            },

            handleDrop(event) {
                const files = Array.from(event.dataTransfer.files).filter(f => f.type.startsWith('image/'));
                this._uploadFiles(files);
            },

            async _uploadFiles(files) {
                if (!files.length) return;
                this.uploading = true;
                await Promise.all(files.map(async (file) => {
                    // Push placeholder into the reactive array first.
                    this.items.push({ image: '', image_url: null, caption: '', uploading: true });
                    // IMPORTANT: read back the item through this.items so we get Alpine's
                    // reactive proxy, not the plain object we just created.  Any mutation
                    // through the proxy will correctly trigger a re-render.
                    const reactiveItem = this.items[this.items.length - 1];
                    await this._upload(reactiveItem, file);
                }));
                this.uploading = false;
            },

            async _upload(item, file) {
                const fd = new FormData();
                fd.append('image', file);
                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                try {
                    const res = await fetch(this.uploadUrl, { method: 'POST', body: fd });
                    if (!res.ok) {
                        const err = await res.json().catch(() => ({}));
                        throw new Error(err.message || `Upload failed (${res.status})`);
                    }
                    const json = await res.json();
                    item.image     = json.path;
                    item.image_url = json.url;
                } catch (e) {
                    console.error('Gallery upload failed:', e);
                    const idx = this.items.indexOf(item);
                    if (idx > -1) this.items.splice(idx, 1);
                } finally {
                    item.uploading = false;
                }
            },

            serialized() {
                return JSON.stringify(
                    this.items
                        .filter(i => i.image)
                        .map(({ image, caption }) => ({ image, caption }))
                );
            },
        }));

        // Onboard accounts — name, description, logo (AJAX upload per item).
        Alpine.data('accountsManager', (initial, uploadUrl) => ({
            items: (Array.isArray(initial) ? initial : []).map(a => ({
                name: a.name || '',
                description: a.description || '',
                logo: a.logo || '',
                logo_url: a.logo_url || null,
                uploading: false,
            })),
            uploadUrl,

            add() {
                this.items.push({ name: '', description: '', logo: '', logo_url: null, uploading: false });
            },

            remove(index) {
                this.items.splice(index, 1);
            },

            async uploadLogo(item, event) {
                const file = event.target.files[0];
                if (!file || !file.type.startsWith('image/')) return;
                item.uploading = true;
                const fd = new FormData();
                fd.append('logo', file);
                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                try {
                    const res  = await fetch(this.uploadUrl, { method: 'POST', body: fd });
                    const json = await res.json();
                    item.logo     = json.path;
                    item.logo_url = json.url;
                } catch (e) {
                    console.error('Account logo upload failed', e);
                } finally {
                    item.uploading = false;
                    event.target.value = '';
                }
            },

            // Strip UI-only fields before submitting.
            serialized() {
                return JSON.stringify(
                    this.items.map(({ name, description, logo }) => ({ name, description, logo }))
                );
            },
        }));

        // Simple repeatable-text-point list (no icon picker).
        Alpine.data('pointsManager', (initial) => ({
            items: Array.isArray(initial) ? initial : [],

            add() {
                this.items.push({ text: '' });
                this.$nextTick(() => {
                    const inputs = this.$el.querySelectorAll('input[type="text"]');
                    inputs[inputs.length - 1]?.focus();
                });
            },

            remove(index) {
                this.items.splice(index, 1);
            },
        }));
    });
}());
</script>
@endpush

</x-app-layout>
