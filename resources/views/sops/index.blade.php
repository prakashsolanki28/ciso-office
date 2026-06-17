@extends('layouts.public')

@section('title', 'Standard Operating Procedures — HRRL CISO Office')

@section('content')

    {{-- ══════════ HERO ══════════ --}}
    <section class="relative bg-primary text-white overflow-hidden border-b border-white/10">
        <div class="absolute inset-0 bg-cover bg-center opacity-[0.12]"
            style="background-image: url('/images/banner.png')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/95 to-primary/70"></div>

        <div class="max-w-container-max mx-auto px-margin-edge py-24 lg:py-28 relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-px w-10 bg-alert-amber"></div>
                <span class="font-label-md text-alert-amber tracking-widest uppercase text-xs font-semibold">Security Governance</span>
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold tracking-tight leading-[1.05] mb-6 max-w-4xl">
                Standard Operating
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-alert-amber to-[#f5a623]">Procedures</span>
            </h1>
            <p class="font-body-lg text-white/60 font-light max-w-2xl text-lg leading-relaxed">
                Official security procedures published by the HRRL CISO Office. Browse, view, and
                download the standard operating procedures that govern our security practices.
            </p>
        </div>
    </section>

    {{-- ══════════ LIST ══════════ --}}
    <section class="py-section-gap bg-surface"
        x-data="{
            query: '',
            items: @js($sops->map(fn ($s) => strtolower(trim($s->title . ' ' . ($s->description ?? ''))))->values()),
            matches(text) {
                const q = this.query.toLowerCase().trim();
                return !q || text.includes(q);
            },
            get hasResults() {
                const q = this.query.toLowerCase().trim();
                return !q || this.items.some(t => t.includes(q));
            },
        }">
        <div class="max-w-container-max mx-auto px-margin-edge">

            @if ($sops->isEmpty())
                <div class="text-center py-24 max-w-md mx-auto">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-white border border-border-gray/30 shadow-sm flex items-center justify-center text-primary/30">
                        <span class="material-symbols-outlined text-4xl">description</span>
                    </div>
                    <h2 class="font-title-md text-primary mb-2">No procedures published yet</h2>
                    <p class="font-body-md text-on-surface-variant font-light">
                        Published standard operating procedures from the CISO Office will appear here soon.
                    </p>
                </div>
            @else
                {{-- Search --}}
                <div class="mb-10 max-w-md">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 text-xl">search</span>
                        <input
                            type="text" x-model="query"
                            placeholder="Search procedures…"
                            class="w-full bg-white border border-border-gray/30 rounded-full pl-12 pr-4 py-3 font-body-md text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($sops as $sop)
                        <article
                            x-show="matches(@js(strtolower(trim($sop->title . ' ' . ($sop->description ?? '')))))"
                            x-transition.opacity
                            class="group bg-white/70 backdrop-blur-lg border border-border-gray/30 rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex flex-col">
                            <div class="p-6 flex flex-col flex-1">
                                {{-- Icon + file meta --}}
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                                        <i data-lucide="{{ $sop->icon ?: 'file-text' }}" style="width:24px;height:24px;"></i>
                                    </div>
                                    @if ($sop->file_size_human)
                                        <span class="font-label-sm text-on-surface-variant/70">{{ $sop->file_size_human }}</span>
                                    @endif
                                </div>

                                <h2 class="font-title-md text-primary tracking-tight mb-2 group-hover:text-secondary transition-colors line-clamp-2">
                                    {{ $sop->title }}
                                </h2>

                                @if ($sop->description)
                                    <p class="font-body-md text-on-surface-variant font-light line-clamp-3 mb-5">
                                        {{ $sop->description }}
                                    </p>
                                @endif

                                {{-- Actions --}}
                                <div class="mt-auto pt-5 border-t border-border-gray/20 flex items-center gap-3">
                                    <a href="{{ $sop->file_url }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 font-label-md text-secondary hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                        View
                                    </a>
                                    <span class="h-4 w-px bg-border-gray/40" aria-hidden="true"></span>
                                    <a href="{{ $sop->file_url }}" download="{{ $sop->file_name }}"
                                        class="inline-flex items-center gap-1.5 font-label-md text-on-surface-variant hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-lg">download</span>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- No search results --}}
                <div x-show="!hasResults" x-cloak class="text-center py-20 max-w-md mx-auto">
                    <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-white border border-border-gray/30 shadow-sm flex items-center justify-center text-primary/30">
                        <span class="material-symbols-outlined text-3xl">search_off</span>
                    </div>
                    <h2 class="font-title-md text-primary mb-2">No matching procedures</h2>
                    <p class="font-body-md text-on-surface-variant font-light">
                        No procedures match “<span x-text="query"></span>”. Try a different search.
                    </p>
                </div>
            @endif
        </div>
    </section>

@endsection

@push('scripts')
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Render the Lucide icon chosen for each SOP card.
        window.addEventListener('DOMContentLoaded', () => {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    </script>
@endpush
