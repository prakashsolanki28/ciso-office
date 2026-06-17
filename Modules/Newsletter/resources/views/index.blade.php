<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                Newsletters
            </h2>
            <button
                x-data
                @click="$dispatch('newsletter-open', null)"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Newsletter
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

            {{-- Search + status filter --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <input
                            type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search newsletters…"
                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                    </div>
                    <div class="w-44">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status"
                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary">
                            <option value="">All statuses</option>
                            @foreach (['draft', 'published', 'archived'] as $st)
                                <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">
                        Filter
                    </button>
                    @if (request('search') || request('status'))
                        <a href="{{ route('newsletter.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($newsletters->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm0 0l9 6 9-6"/>
                        </svg>
                        <p class="text-gray-500 font-medium">No newsletters yet</p>
                        <p class="text-sm text-gray-400 mt-1">Click "New Newsletter" to get started.</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Newsletter</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Publish at</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($newsletters as $n)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($n->image_url)
                                                <img src="{{ $n->image_url }}" alt="" class="w-12 h-12 object-cover rounded-lg shrink-0">
                                            @else
                                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg shrink-0 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 line-clamp-1">{{ $n->title }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 max-w-md">{{ $n->short_description ?? '—' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $statusClass = match($n->status) {
                                                'published' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                'archived'  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                                default     => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold {{ $statusClass }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            {{ ucfirst($n->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-gray-500">
                                        {{ $n->published_at?->format('M d, Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4 text-xs text-gray-500">
                                        {{ $n->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        @php
                                            $payload = [
                                                'id'                => $n->id,
                                                'title'             => $n->title,
                                                'short_description' => $n->short_description,
                                                'status'            => $n->status,
                                                'published_at'      => optional($n->published_at)->format('Y-m-d\TH:i'),
                                                'image_url'         => $n->image_url,
                                            ];
                                        @endphp
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                x-data
                                                @click="$dispatch('newsletter-open', {{ Js::from($payload) }})"
                                                class="p-1.5 text-gray-400 hover:text-primary transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <form method="POST" action="{{ route('newsletter.destroy', $n) }}"
                                                  x-data
                                                  @submit.prevent="if(confirm('Delete this newsletter?')) $el.submit()">
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

                    @if ($newsletters->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $newsletters->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Create / Edit Modal (shared) --}}
    <x-modal name="newsletter-form" max-width="lg">
        <div x-data="newsletterForm()" @newsletter-open.window="open($event.detail)">
            <form method="POST" enctype="multipart/form-data" :action="action" @submit="loading = true">
                @csrf
                <input type="hidden" name="_method" :value="mode === 'edit' ? 'PUT' : 'POST'">
                <input type="hidden" name="form_mode" :value="mode">
                <input type="hidden" name="newsletter_id" :value="id">

                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                        x-text="mode === 'edit' ? 'Edit Newsletter' : 'New Newsletter'"></h3>
                    <p class="mt-1 text-sm text-gray-500">Fill in the details below.</p>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" x-model="title" placeholder="Newsletter title" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @error('title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Short description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Short description</label>
                        <textarea name="short_description" x-model="short_description" rows="3" maxlength="1000"
                            placeholder="A brief summary of this newsletter…"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"></textarea>
                        @error('short_description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                            <select name="status" x-model="status"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Publish at --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Publish at</label>
                            <input type="datetime-local" name="published_at" x-model="published_at"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary">
                            @error('published_at')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Image --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Image</label>
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 overflow-hidden flex items-center justify-center shrink-0">
                                <template x-if="newPreview || existingImage">
                                    <img :src="newPreview || existingImage" alt="" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!(newPreview || existingImage)">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </template>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="image" accept="image/*" x-ref="imageInput" @change="onFile"
                                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                <p class="mt-1 text-xs text-gray-400">PNG, JPG, WebP — up to 5 MB.</p>
                            </div>
                        </div>
                        @error('image')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-end gap-3 rounded-b-lg">
                    <button type="button" @click="$dispatch('close-modal', 'newsletter-form')"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" :disabled="loading || !title"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 disabled:opacity-50 transition">
                        <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="mode === 'edit' ? 'Save Changes' : 'Create Newsletter'"></span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('newsletterForm', () => ({
                    mode: 'create',
                    id: null,
                    title: '',
                    short_description: '',
                    status: 'draft',
                    published_at: '',
                    existingImage: null,
                    newPreview: null,
                    loading: false,
                    storeUrl: @js(route('newsletter.store')),
                    baseUrl: @js(url('newsletters')),

                    get action() {
                        return this.mode === 'edit' ? this.baseUrl + '/' + this.id : this.storeUrl;
                    },

                    open(data) {
                        if (data) {
                            this.mode = 'edit';
                            this.id = data.id ?? null;
                            this.title = data.title ?? '';
                            this.short_description = data.short_description ?? '';
                            this.status = data.status ?? 'draft';
                            this.published_at = data.published_at ?? '';
                            this.existingImage = data.image_url ?? null;
                        } else {
                            this.reset();
                        }
                        this.newPreview = null;
                        this.loading = false;
                        if (this.$refs.imageInput) this.$refs.imageInput.value = '';
                        this.$dispatch('open-modal', 'newsletter-form');
                    },

                    reset() {
                        this.mode = 'create';
                        this.id = null;
                        this.title = '';
                        this.short_description = '';
                        this.status = 'draft';
                        this.published_at = '';
                        this.existingImage = null;
                    },

                    onFile(e) {
                        const file = e.target.files[0];
                        this.newPreview = file ? URL.createObjectURL(file) : null;
                    },

                    init() {
                        @if ($errors->any())
                            this.mode = @js(old('form_mode', 'create'));
                            this.id = @js(old('newsletter_id'));
                            this.title = @js(old('title', ''));
                            this.short_description = @js(old('short_description', ''));
                            this.status = @js(old('status', 'draft'));
                            this.published_at = @js(old('published_at', ''));
                            this.$nextTick(() => this.$dispatch('open-modal', 'newsletter-form'));
                        @endif
                    },
                }));
            });
        </script>
    @endpush

</x-app-layout>
