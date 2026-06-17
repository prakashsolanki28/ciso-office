<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                SOPs
            </h2>
            <button type="button" x-data
               @click="$dispatch('open-sop-create')"
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New SOP
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
                            placeholder="Search SOPs…"
                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                    </div>
                    <button type="submit" class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">
                        Filter
                    </button>
                    @if (request()->filled('search'))
                        <a href="{{ route('sop.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($sops->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">No SOPs yet</p>
                        <p class="text-sm text-gray-400 mt-1">Click "New SOP" to add your first one.</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SOP</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($sops as $sop)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start gap-3">
                                            <span class="shrink-0 mt-0.5 flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                                                <i data-lucide="{{ $sop->icon ?: 'file-text' }}" style="width:18px;height:18px;"></i>
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 line-clamp-1">{{ $sop->title }}</p>
                                                @if ($sop->description)
                                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $sop->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                            <div class="min-w-0">
                                                <p class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[14rem]">{{ $sop->file_name ?? 'document.pdf' }}</p>
                                                @if ($sop->file_size_human)
                                                    <p class="text-xs text-gray-400">{{ $sop->file_size_human }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-gray-500">
                                        {{ $sop->created_at?->format('M d, Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('sop.download', $sop) }}"
                                               class="p-1.5 text-gray-400 hover:text-blue-600 transition" title="Download PDF">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                            <button type="button" x-data
                                               @click="$dispatch('open-sop-edit', {
                                                   action: '{{ route('sop.update', $sop) }}',
                                                   title: @js($sop->title),
                                                   description: @js($sop->description),
                                                   icon: @js($sop->icon),
                                                   fileName: @js($sop->file_name),
                                                   fileSize: @js($sop->file_size_human),
                                                   downloadUrl: '{{ route('sop.download', $sop) }}',
                                                   isPublic: {{ $sop->is_public ? 'true' : 'false' }},
                                               })"
                                               class="p-1.5 text-gray-400 hover:text-primary transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <form method="POST" action="{{ route('sop.destroy', $sop) }}"
                                                  x-data
                                                  @submit.prevent="if(confirm('Delete this SOP?')) $el.submit()">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($sops->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $sops->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Create / Edit modal (shared) --}}
    <x-modal name="sop-form" focusable maxWidth="lg">
        <div
            x-data="{
                mode: @js(old('form_mode', 'create')),
                storeUrl: @js(route('sop.store')),
                actionUrl: @js(old('action_url', route('sop.store'))),
                title: @js(old('title', '')),
                description: @js(old('description', '')),
                icon: @js(old('icon', '')),
                isPublic: {{ old('is_public') ? 'true' : 'false' }},
                existingFile: {
                    name: @js(old('existing_file_name', '')),
                    size: @js(old('existing_file_size', '')),
                    downloadUrl: @js(old('download_url', '')),
                },
                newFileName: '',
                dragging: false,

                {{-- ── Icon picker (single value; mirrors Project's Lucide picker) ── --}}
                iconPickerOpen: false,
                iconSearch: '',
                iconLimit: 80,
                toggleIconPicker() {
                    this.iconPickerOpen = !this.iconPickerOpen;
                    this.iconSearch = '';
                    if (this.iconPickerOpen) this.$nextTick(() => window._lucideCreate?.());
                },
                selectIcon(name) {
                    this.icon = name;
                    this.iconPickerOpen = false;
                    this.$nextTick(() => window._lucideCreate?.());
                },
                filteredIcons() {
                    const all = window.SOP_ICON_NAMES || [];
                    const q = this.iconSearch.trim().toLowerCase();
                    const matches = q ? all.filter(n => n.includes(q)) : all;
                    return matches.slice(0, this.iconLimit);
                },
                filteredIconsTruncated() {
                    const all = window.SOP_ICON_NAMES || [];
                    const q = this.iconSearch.trim().toLowerCase();
                    const total = q ? all.filter(n => n.includes(q)).length : all.length;
                    return total > this.iconLimit;
                },
                iconTag(name, size = 16) {
                    return `<i data-lucide='${name || 'file-text'}' style='width:${size}px;height:${size}px;display:block;'></i>`;
                },

                openCreate() {
                    this.mode = 'create';
                    this.actionUrl = this.storeUrl;
                    this.title = '';
                    this.description = '';
                    this.icon = '';
                    this.iconPickerOpen = false;
                    this.isPublic = false;
                    this.existingFile = { name: '', size: '', downloadUrl: '' };
                    this.newFileName = '';
                    this.$dispatch('open-modal', 'sop-form');
                    this.$nextTick(() => window._lucideCreate?.());
                },
                openEdit(detail) {
                    this.mode = 'edit';
                    this.actionUrl = detail.action;
                    this.title = detail.title ?? '';
                    this.description = detail.description ?? '';
                    this.icon = detail.icon ?? '';
                    this.iconPickerOpen = false;
                    this.isPublic = detail.isPublic ?? false;
                    this.existingFile = {
                        name: detail.fileName ?? '',
                        size: detail.fileSize ?? '',
                        downloadUrl: detail.downloadUrl ?? '',
                    };
                    this.newFileName = '';
                    this.$dispatch('open-modal', 'sop-form');
                    this.$nextTick(() => window._lucideCreate?.());
                },
                setFile(files) { this.newFileName = files && files.length ? files[0].name : ''; },
            }"
            x-on:open-sop-create.window="openCreate()"
            x-on:open-sop-edit.window="openEdit($event.detail)"
            x-init="$nextTick(() => { @if($errors->any()) $dispatch('open-modal', 'sop-form'); @endif window._lucideCreate?.(); })"
        >
            <form :action="actionUrl" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" :value="mode === 'edit' ? 'PATCH' : 'POST'">
                <input type="hidden" name="form_mode" :value="mode">
                <input type="hidden" name="action_url" :value="actionUrl">
                <input type="hidden" name="existing_file_name" :value="existingFile.name">
                <input type="hidden" name="existing_file_size" :value="existingFile.size">
                <input type="hidden" name="download_url" :value="existingFile.downloadUrl">
                <input type="hidden" name="icon" :value="icon">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200"
                        x-text="mode === 'edit' ? 'Edit SOP' : 'New SOP'"></h2>
                    <button type="button" @click="$dispatch('close-modal', 'sop-form')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition" title="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-5">
                    {{-- Icon + Title --}}
                    <div class="flex items-start gap-3">
                        {{-- Icon picker (single value; mirrors Project's Lucide picker) --}}
                        <div class="shrink-0">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Icon</label>
                            <div class="relative" @click.outside="iconPickerOpen = false">
                                {{-- Trigger --}}
                                <button type="button"
                                    @click="toggleIconPicker()"
                                    class="flex items-center justify-center gap-1.5 w-[64px] h-[42px] border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 hover:border-primary/50 hover:bg-gray-50 dark:hover:bg-gray-600 transition text-gray-600 dark:text-gray-300"
                                    :class="iconPickerOpen ? 'border-primary/60 ring-2 ring-primary/20' : ''">
                                    <span class="flex items-center justify-center w-5 h-5" x-html="iconTag(icon, 18)"></span>
                                    <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform"
                                         :class="iconPickerOpen ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                {{-- Dropdown panel --}}
                                <div x-show="iconPickerOpen"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute left-0 top-full mt-1.5 z-50 w-64 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl p-2.5 origin-top-left"
                                     style="display:none;">

                                    {{-- Search --}}
                                    <div class="relative mb-2">
                                        <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.3-4.3M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/>
                                        </svg>
                                        <input type="text" x-model="iconSearch"
                                            @input="$nextTick(() => window._lucideCreate?.())"
                                            placeholder="Search icons…"
                                            class="w-full pl-7 pr-2 py-1.5 text-xs border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    </div>

                                    {{-- Icon grid (only built while open, to avoid rendering ~1,650 icons) --}}
                                    <template x-if="iconPickerOpen">
                                        <div class="grid grid-cols-8 gap-0.5 max-h-44 overflow-y-auto">
                                            <template x-for="name in filteredIcons()" :key="name">
                                                <button type="button"
                                                    @click.stop="selectIcon(name)"
                                                    :title="name"
                                                    :class="icon === name
                                                        ? 'bg-primary/10 text-primary ring-1 ring-inset ring-primary/30'
                                                        : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700'"
                                                    class="p-1.5 rounded-md flex items-center justify-center transition">
                                                    <i :data-lucide="name" style="width:15px;height:15px;display:block;pointer-events:none;"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="iconPickerOpen && filteredIconsTruncated()">
                                        <p class="text-center text-[11px] text-gray-400 pt-1.5">Showing first <span x-text="iconLimit"></span> — type to search.</p>
                                    </template>
                                    <template x-if="iconPickerOpen && filteredIcons().length === 0">
                                        <p class="text-center text-xs text-gray-400 py-3">No icons found.</p>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Title --}}
                        <div class="flex-1 min-w-0">
                            <label for="sop-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text" id="sop-title" name="title" x-model="title"
                                placeholder="e.g. Incident Response Procedure" required
                                class="w-full h-[42px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            >
                            @error('title')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="sop-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Description
                        </label>
                        <textarea
                            id="sop-description" name="description" x-model="description" rows="4"
                            placeholder="Briefly describe what this SOP covers…"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        ></textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PDF File --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            PDF File <span class="text-red-500" x-show="mode === 'create'">*</span>
                        </label>

                        {{-- Current file (edit only) --}}
                        <template x-if="mode === 'edit' && existingFile.name">
                            <div class="mb-3">
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/40 px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-text="existingFile.name"></p>
                                            <p class="text-xs text-gray-400" x-show="existingFile.size" x-text="existingFile.size"></p>
                                        </div>
                                    </div>
                                    <a :href="existingFile.downloadUrl" class="text-xs font-medium text-primary hover:underline shrink-0">Download</a>
                                </div>
                                <p class="mt-2 text-xs text-gray-400">Upload a new PDF to replace the current file, or leave empty to keep it.</p>
                            </div>
                        </template>

                        <label
                            @dragover.prevent="dragging = true"
                            @dragleave.prevent="dragging = false"
                            @drop="dragging = false"
                            :class="dragging ? 'border-primary bg-primary/5' : 'border-gray-300 dark:border-gray-600'"
                            class="flex flex-col items-center justify-center w-full px-6 py-8 border-2 border-dashed rounded-lg cursor-pointer hover:border-primary transition"
                        >
                            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-medium text-primary">Click to upload</span> or drag &amp; drop
                            </p>
                            <p class="text-xs text-gray-400 mt-1">PDF only, up to 20&nbsp;MB</p>
                            <p x-show="newFileName" x-text="newFileName" class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-200" x-cloak></p>
                            <input
                                type="file" name="file" accept="application/pdf,.pdf"
                                class="hidden"
                                @change="setFile($event.target.files)"
                                :required="mode === 'create'"
                            >
                        </label>
                        @error('file')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Public visibility --}}
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/40 px-4 py-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox" name="is_public" value="1" x-model="isPublic"
                                class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary/30"
                            >
                            <span>
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visible on public site</span>
                                <span class="block text-xs text-gray-400 mt-0.5">When enabled, this SOP is listed publicly at <code>/sops</code> and anyone can view or download the PDF.</span>
                            </span>
                        </label>
                        @error('is_public')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="$dispatch('close-modal', 'sop-form')"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">Cancel</button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition"
                            x-text="mode === 'edit' ? 'Save Changes' : 'Create SOP'"></button>
                </div>
            </form>
        </div>
    </x-modal>

    @php
        // Icon names sourced from public/js/icons.js (the single source of truth, same as the Project picker).
        $iconsJs   = file_get_contents(public_path('js/icons.js'));
        preg_match_all('/"([a-z][a-z0-9-]+)"/', $iconsJs, $iconsMatch);
        $iconNames = $iconsMatch[1] ?? [];
    @endphp

    @push('scripts')
        <script>
            window.SOP_ICON_NAMES = {!! json_encode($iconNames) !!};
        </script>
    @endpush
</x-app-layout>
