@extends('layouts.public')

@section('title', 'Major Projects — HRRL CISO Office')

@section('content')

    @php
        $totalAccounts = $projects->sum(fn($p) => count($p->onboard_accounts ?? []));
        $totalGallery = $projects->sum(fn($p) => count($p->gallery ?? []));
    @endphp

    {{-- ══════════ HERO ══════════ --}}
    <section class="relative bg-primary text-white overflow-hidden border-b border-white/10">
        {{-- background image (subtle) --}}
        <div class="absolute inset-0 bg-cover bg-center opacity-[0.12]"
            style="background-image: url('/images/banner.png')"></div>
        {{-- legibility gradient --}}
        <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/95 to-primary/70"></div>

        <div class="max-w-container-max mx-auto px-margin-edge py-24 lg:py-28 relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-px w-10 bg-alert-amber"></div>
                <span class="font-label-md text-alert-amber tracking-widest uppercase text-xs font-semibold">Our Work</span>
            </div>

            <h1 class="text-5xl lg:text-6xl font-bold tracking-tight leading-[1.05] mb-6 max-w-4xl">
                Major
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-alert-amber to-[#f5a623]">Projects</span>
                that secure the enterprise
            </h1>
            <p class="font-body-lg text-white/60 font-light max-w-2xl text-lg leading-relaxed">
                A closer look at the security initiatives delivered by the HRRL CISO Office —
                the problems we set out to solve, how we engineered the solution, and the measurable impact.
            </p>
        </div>
    </section>

    {{-- ══════════ PROJECT SHOWCASE (zig-zag) ══════════ --}}
    <section class="py-section-gap bg-surface relative overflow-hidden">
        {{-- soft blue glow --}}
        <div class="absolute top-1/4 -left-32 w-96 h-96 rounded-full bg-secondary/5 blur-3xl pointer-events-none"></div>

        <div class="max-w-container-max mx-auto px-margin-edge relative z-10">

            @forelse ($projects as $project)
                @php
                    $topStats = array_slice($project->statistics ?? [], 0, 3);
                    $topSpecs = array_slice($project->specifications ?? [], 0, 3);
                @endphp

                <div
                    class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center {{ !$loop->last ? 'mb-24 lg:mb-32' : '' }}">

                    {{-- ── IMAGE ── --}}
                    <div class="relative {{ $loop->even ? 'lg:order-last' : '' }}">
                        {{-- decorative offset frame --}}
                        <div
                            class="absolute -inset-3 rounded-[2rem] bg-gradient-to-br from-secondary/10 to-alert-amber/10 -z-10 blur-[2px]">
                        </div>

                        <div class="relative group rounded-3xl overflow-hidden shadow-2xl ring-1 ring-black/5">
                            @if ($project->banner_url)
                                <img src="{{ $project->banner_url }}" alt="{{ $project->name }}"
                                    class="w-full aspect-[4/3] object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div
                                    class="w-full aspect-[4/3] bg-gradient-to-br from-primary/15 via-secondary/10 to-alert-amber/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary/25 text-7xl">shield</span>
                                </div>
                            @endif
                            {{-- gradient sheen --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/30 via-transparent to-transparent">
                            </div>
                            {{-- index badge --}}
                            <div
                                class="absolute top-5 left-5 flex items-center gap-2 bg-white/90 backdrop-blur-md text-primary font-label-sm font-bold pl-2.5 pr-4 py-1.5 rounded-full shadow-lg">
                                <span
                                    class="w-6 h-6 rounded-full bg-alert-amber text-white flex items-center justify-center text-[11px]">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                Project
                            </div>
                        </div>

                        {{-- floating stat card --}}
                        @if (!empty($topStats[0]))
                            <div
                                class="hidden md:block absolute -bottom-6 {{ $loop->even ? '-right-6' : '-left-6' }} bg-white rounded-2xl shadow-xl ring-1 ring-black/5 px-6 py-4">
                                <div class="font-headline-lg text-3xl font-bold text-primary tracking-tight leading-none">
                                    {{ $topStats[0]['value'] ?? '' }}
                                </div>
                                <div class="font-label-sm text-on-surface-variant mt-1.5">{{ $topStats[0]['key'] ?? '' }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- ── CONTENT ── --}}
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="h-px w-8 bg-alert-amber"></div>
                            <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">
                                Project {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>

                        <h2 class="font-headline-lg text-3xl lg:text-4xl font-bold text-primary tracking-tight mb-5 leading-tight">
                            {{ $project->name }}
                        </h2>

                        @if ($project->short_description)
                            <p class="font-body-lg text-on-surface-variant font-light mb-7 leading-relaxed">
                                {{ $project->short_description }}
                            </p>
                        @endif

                        {{-- spec highlights --}}
                        @if ($topSpecs)
                            <ul class="space-y-2.5 mb-8">
                                @foreach ($topSpecs as $spec)
                                    @if (!empty($spec['title']))
                                        <li class="flex items-start gap-3">
                                            <span
                                                class="mt-0.5 w-5 h-5 rounded-full bg-secondary/10 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-secondary text-sm">check</span>
                                            </span>
                                            <span class="font-body-md text-on-surface">{{ $spec['title'] }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif

                        {{-- inline mini-stats --}}
                        @if (count($topStats) > 1)
                            <div class="flex flex-wrap gap-x-10 gap-y-4 mb-8">
                                @foreach (array_slice($topStats, 0, 3) as $stat)
                                    <div>
                                        <div class="font-title-md font-bold text-primary">{{ $stat['value'] ?? '' }}</div>
                                        <div class="font-label-sm text-on-surface-variant">{{ $stat['key'] ?? '' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <a href="{{ route('projects.public.show', $project->slug) }}"
                            class="font-label-md inline-flex items-center gap-2 bg-alert-amber text-on-primary px-7 py-3.5 rounded-full font-bold hover:shadow-[0_0_28px_rgba(230,147,10,0.45)] hover:-translate-y-0.5 transition-all duration-300 group">
                            View Full Case Study
                            <span
                                class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-0.5">arrow_forward</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-24 max-w-md mx-auto">
                    <div
                        class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-white border border-border-gray/30 shadow-sm flex items-center justify-center text-primary/30">
                        <span class="material-symbols-outlined text-4xl">folder_open</span>
                    </div>
                    <h2 class="font-title-md text-primary mb-2">No projects published yet</h2>
                    <p class="font-body-md text-on-surface-variant font-light">
                        Security initiatives delivered by the CISO Office will be showcased here soon.
                    </p>
                </div>
            @endforelse

        </div>
    </section>

    {{-- ══════════ CTA ══════════ --}}
    @if ($projects->isNotEmpty())
        <section class="bg-gradient-to-r from-alert-amber to-[#f5a623] text-on-primary py-20 relative overflow-hidden">
            <div class="absolute inset-0 opacity-50"
                style="background-image: radial-gradient(circle, #ffffff 1.5px, transparent 1.5px); background-size: 22px 22px;">
            </div>
            <div
                class="max-w-container-max mx-auto px-margin-edge relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                <div>
                    <h2 class="font-headline-lg text-3xl font-bold text-white tracking-tight drop-shadow-sm">
                        Have a security concern to report?
                    </h2>
                    <p class="font-body-lg text-white/90 font-light mt-2">
                        The CISO Office is here to help — every report strengthens the enterprise.
                    </p>
                </div>
                <a href="{{ route('report.incident') }}"
                    class="font-label-md inline-flex items-center gap-2 bg-primary text-on-primary px-9 py-4 rounded-full font-bold shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 shrink-0">
                    <span class="material-symbols-outlined text-lg">shield</span>
                    Report an Incident
                </a>
            </div>
        </section>
    @endif

@endsection
