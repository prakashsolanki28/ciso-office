@extends('layouts.public')

@section('title', $project->name . ' — Projects · HRRL CISO Office')

@push('styles')
    <meta name="description" content="{{ $project->short_description }}">
    <meta property="og:title" content="{{ $project->name }}">
    <meta property="og:description" content="{{ $project->short_description }}">
    @if ($project->banner_url)
        <meta property="og:image" content="{{ $project->banner_url }}">
    @endif
@endpush

@section('content')

    @php
        $stats = $project->statistics ?? [];
        $heroStats = array_slice($stats, 0, 4);
        // Literal classes (so Tailwind detects them) for the stats strip column count.
        $statCols = match (count($heroStats)) {
            1 => 'lg:grid-cols-1',
            2 => 'lg:grid-cols-2',
            3 => 'lg:grid-cols-3',
            default => 'lg:grid-cols-4',
        };
    @endphp

    {{-- ══════════ CINEMATIC HERO ══════════ --}}
    <section class="relative bg-[#040f24] text-white overflow-hidden">
        {{-- banner background --}}
        @if ($project->banner_url)
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $project->banner_url }}')">
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#040f24] via-[#040f24]/85 to-[#040f24]/60"></div>
        @else
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-primary-container/60 via-[#040f24] to-[#040f24]">
            </div>
        @endif
        {{-- dot texture --}}
        <div class="absolute inset-0 opacity-[0.04]"
            style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 26px 26px;">
        </div>

        <div class="max-w-container-max mx-auto px-margin-edge pt-10 pb-20 lg:pb-28 relative z-10">
            {{-- breadcrumb --}}
            <nav class="flex items-center gap-2 font-label-sm text-white/50 mb-16">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span class="text-white/25">/</span>
                <a href="{{ route('projects.public.index') }}" class="hover:text-white transition-colors">Projects</a>
                <span class="text-white/25">/</span>
                <span class="text-white/80 line-clamp-1">{{ $project->name }}</span>
            </nav>

            <div class="max-w-3xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-px w-10 bg-alert-amber"></div>
                    <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Case Study</span>
                </div>
                <h1 class="text-4xl lg:text-6xl font-bold tracking-tight leading-[1.05] mb-6">
                    {{ $project->name }}
                </h1>
                @if ($project->short_description)
                    <p class="font-body-lg text-white/65 font-light text-lg leading-relaxed">
                        {{ $project->short_description }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- ══════════ STATS STRIP (overlapping) ══════════ --}}
    @if (!empty($heroStats))
        <div class="max-w-container-max mx-auto px-margin-edge relative z-20 -mt-12">
            <div
                class="bg-border-gray/30 rounded-2xl shadow-xl ring-1 ring-black/5 grid grid-cols-2 {{ $statCols }} gap-px overflow-hidden">
                @foreach ($heroStats as $stat)
                    <div class="bg-white px-6 py-7 text-center">
                        @if (!empty($stat['icon']))
                            <div
                                class="w-10 h-10 mx-auto mb-3 rounded-full bg-primary/5 flex items-center justify-center text-primary">
                                <i data-lucide="{{ $stat['icon'] }}" style="width:20px;height:20px;"></i>
                            </div>
                        @endif
                        <div
                            class="font-headline-lg text-3xl lg:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-primary to-secondary tracking-tight">
                            {{ $stat['value'] ?? '' }}
                        </div>
                        <div class="font-label-sm text-on-surface-variant mt-1.5">{{ $stat['key'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-surface">
        <div class="max-w-container-max mx-auto px-margin-edge">

            {{-- ══════════ CHARTS / METRICS ══════════ --}}
            @if (!empty($project->charts))
                @php
                    $chartCols = count($project->charts) === 1 ? 'lg:grid-cols-1' : 'lg:grid-cols-2';
                @endphp
                <section class="pt-20 lg:pt-24">
                    <div class="max-w-2xl mb-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-alert-amber"></div>
                            <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Metrics</span>
                        </div>
                        <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">By the numbers</h2>
                        <p class="font-body-lg text-on-surface-variant font-light mt-3">Key measures behind this project,
                            visualised.</p>
                    </div>
                    <div class="grid grid-cols-1 {{ $chartCols }} gap-6">
                        @foreach ($project->charts as $i => $chart)
                            <div
                                class="bg-white border border-border-gray/30 rounded-2xl p-6 lg:p-7 shadow-sm hover:shadow-lg transition-shadow duration-300">
                                <h3 class="font-title-md text-primary tracking-tight mb-5">{{ $chart['title'] ?? '' }}</h3>
                                <div id="chart-{{ $i }}" class="apex-chart min-h-[320px]"></div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ══════════ OVERVIEW ══════════ --}}
            @if ($project->description)
                <section class="pt-20 lg:pt-24">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <div class="lg:col-span-4">
                            <div class="lg:sticky lg:top-28">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="h-px w-8 bg-alert-amber"></div>
                                    <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Overview</span>
                                </div>
                                <h2 class="font-headline-lg text-2xl font-bold text-primary tracking-tight">
                                    About this project
                                </h2>
                            </div>
                        </div>
                        <div class="lg:col-span-8">
                            <div class="blog-content max-w-3xl">
                                {!! $project->description !!}
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- ══════════ SPECIFICATIONS ══════════ --}}
            @if (!empty($project->specifications))
                <section class="pt-20 lg:pt-24">
                    <div class="max-w-2xl mb-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-alert-amber"></div>
                            <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Capabilities</span>
                        </div>
                        <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">Features &amp;
                            Specifications</h2>
                        <p class="font-body-lg text-on-surface-variant font-light mt-3">Key capabilities engineered into this
                            project.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($project->specifications as $spec)
                            <div
                                class="bg-white/70 backdrop-blur-lg border border-border-gray/30 p-7 rounded-2xl hover:-translate-y-1 hover:shadow-xl transition-all duration-300 group relative overflow-hidden">
                                <div
                                    class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-secondary to-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <div
                                    class="w-12 h-12 rounded-xl bg-primary/5 flex items-center justify-center text-primary mb-5 group-hover:scale-110 transition-transform duration-300">
                                    <i data-lucide="{{ $spec['icon'] ?: 'circle-dashed' }}" style="width:24px;height:24px;"></i>
                                </div>
                                <h3 class="font-title-md text-primary tracking-tight mb-2">{{ $spec['title'] ?? '' }}</h3>
                                @if (!empty($spec['description']))
                                    <p class="font-body-md text-on-surface-variant font-light leading-relaxed">
                                        {{ $spec['description'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ══════════ IMPACT (before / after) ══════════ --}}
            @if (!empty($project->before_points) || !empty($project->after_points))
                <section class="pt-20 lg:pt-24">
                    <div class="max-w-2xl mb-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-alert-amber"></div>
                            <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Outcomes</span>
                        </div>
                        <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">The Impact</h2>
                        <p class="font-body-lg text-on-surface-variant font-light mt-3">Where we started, and what changed
                            after implementation.</p>
                    </div>

                    <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                        {{-- Before --}}
                        <div class="bg-white rounded-2xl border border-border-gray/30 overflow-hidden shadow-sm">
                            <div class="px-7 py-5 bg-amber-50 border-b border-amber-100 flex items-center gap-3">
                                <span
                                    class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                    <span class="material-symbols-outlined text-xl">history</span>
                                </span>
                                <div>
                                    <h3 class="font-title-md text-amber-700 leading-none">Before</h3>
                                    <p class="font-label-sm text-amber-600/70 mt-1">State before implementation</p>
                                </div>
                            </div>
                            <ul class="p-7 space-y-4">
                                @forelse ($project->before_points ?? [] as $point)
                                    <li class="flex items-start gap-3 font-body-md text-on-surface-variant">
                                        <span class="mt-2 w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                        <span>{{ $point['text'] ?? '' }}</span>
                                    </li>
                                @empty
                                    <li class="font-body-md text-on-surface-variant/50 font-light">No data recorded.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- After --}}
                        <div class="bg-white rounded-2xl border border-emerald-200 overflow-hidden shadow-sm ring-1 ring-emerald-100">
                            <div class="px-7 py-5 bg-emerald-50 border-b border-emerald-100 flex items-center gap-3">
                                <span
                                    class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                    <span class="material-symbols-outlined text-xl">verified</span>
                                </span>
                                <div>
                                    <h3 class="font-title-md text-emerald-700 leading-none">After</h3>
                                    <p class="font-label-sm text-emerald-600/70 mt-1">Outcome after implementation</p>
                                </div>
                            </div>
                            <ul class="p-7 space-y-4">
                                @forelse ($project->after_points ?? [] as $point)
                                    <li class="flex items-start gap-3 font-body-md text-on-surface">
                                        <span class="material-symbols-outlined text-emerald-500 text-lg shrink-0">check_circle</span>
                                        <span>{{ $point['text'] ?? '' }}</span>
                                    </li>
                                @empty
                                    <li class="font-body-md text-on-surface-variant/50 font-light">No data recorded.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </section>
            @endif

            {{-- ══════════ ONBOARDED ACCOUNTS ══════════ --}}
            @if (!empty($project->onboard_accounts))
                <section class="pt-20 lg:pt-24">
                    <div class="max-w-2xl mb-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-alert-amber"></div>
                            <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Coverage</span>
                        </div>
                        <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">Onboarded Accounts</h2>
                        <p class="font-body-lg text-on-surface-variant font-light mt-3">Teams and systems brought under this
                            project's protection.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($project->onboard_accounts as $account)
                            <div
                                class="bg-white border border-border-gray/30 rounded-2xl p-6 flex items-start gap-4 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-surface border border-border-gray/30 flex items-center justify-center overflow-hidden shrink-0">
                                    @if (!empty($account['logo']))
                                        <img src="{{ asset('storage/' . $account['logo']) }}"
                                            alt="{{ $account['name'] ?? '' }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="font-headline-lg text-xl text-primary/40 font-bold">
                                            {{ strtoupper(substr($account['name'] ?? '?', 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0 pt-0.5">
                                    <h3 class="font-title-md text-primary tracking-tight text-base leading-snug">
                                        {{ $account['name'] ?? '' }}</h3>
                                    @if (!empty($account['description']))
                                        <p class="font-body-md text-on-surface-variant font-light text-sm mt-1 leading-relaxed">
                                            {{ $account['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ══════════ GALLERY ══════════ --}}
            @if (!empty($project->gallery))
                <section class="pt-20 lg:pt-24">
                    <div class="max-w-2xl mb-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-alert-amber"></div>
                            <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Gallery</span>
                        </div>
                        <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">A look inside</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($project->gallery as $item)
                            @if (!empty($item['image']))
                                <figure
                                    class="group relative rounded-2xl overflow-hidden ring-1 ring-black/5 shadow-sm hover:shadow-xl transition-all duration-300 aspect-[4/3] bg-primary/5">
                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                        alt="{{ $item['caption'] ?? '' }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                    @if (!empty($item['caption']))
                                        <figcaption
                                            class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/70 via-black/30 to-transparent translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                                            <span class="font-label-md text-white text-sm">{{ $item['caption'] }}</span>
                                        </figcaption>
                                    @endif
                                </figure>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ══════════ BACK NAV ══════════ --}}
            <div class="mt-20 lg:mt-24 pb-section-gap pt-10 border-t border-border-gray/30 flex flex-wrap items-center justify-between gap-6">
                <a href="{{ route('projects.public.index') }}"
                    class="font-label-md inline-flex items-center gap-2 text-primary hover:gap-3 transition-all duration-300 group">
                    <span class="material-symbols-outlined text-lg transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                    Back to all Projects
                </a>
                <a href="{{ route('report.incident') }}"
                    class="font-label-md inline-flex items-center gap-2 bg-alert-amber text-on-primary px-7 py-3.5 rounded-full font-bold hover:shadow-[0_0_28px_rgba(230,147,10,0.45)] hover:-translate-y-0.5 transition-all duration-300">
                    <span class="material-symbols-outlined text-lg">shield</span>
                    Report an Incident
                </a>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Render the Lucide icons used in Specifications / Statistics.
        window.addEventListener('DOMContentLoaded', () => {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    </script>

    @if (!empty($project->charts))
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            window.__projectCharts = @json($project->charts);

            window.addEventListener('DOMContentLoaded', () => {
                if (typeof ApexCharts === 'undefined') return;

                // Brand palette (from tailwind.config.js).
                const PALETTE = ['#236390', '#E6930A', '#0a2342', '#5b8fb0', '#94ccff', '#105783', '#b2c7ef'];
                const AXIS = '#44474e';
                const GRID = '#e5e7eb';

                (window.__projectCharts || []).forEach((chart, i) => {
                    const el = document.querySelector('#chart-' + i);
                    if (!el || !Array.isArray(chart.points) || chart.points.length === 0) return;

                    const labels = chart.points.map(p => p.label);
                    const values = chart.points.map(p => Number(p.value) || 0);
                    const type = ['bar', 'line', 'area', 'radar', 'pie'].includes(chart.type) ? chart.type : 'bar';

                    // Per-point admin colors, falling back to the brand palette by position.
                    const isHex = c => typeof c === 'string' && /^#[0-9a-fA-F]{6}$/.test(c);
                    const colors = chart.points.map((p, idx) => isHex(p.color) ? p.color : PALETTE[idx % PALETTE.length]);

                    const base = {
                        chart: {
                            type,
                            height: 320,
                            fontFamily: 'inherit',
                            toolbar: { show: false },
                            animations: { enabled: true, easing: 'easeinout', speed: 700 },
                            parentHeightOffset: 0,
                        },
                        series: [{ name: chart.title || 'Value', data: values }],
                        grid: { borderColor: GRID, strokeDashArray: 4 },
                        tooltip: { theme: 'light' },
                        dataLabels: { enabled: false },
                        legend: { show: false },
                    };

                    let options;
                    if (type === 'bar') {
                        options = {
                            ...base,
                            colors: colors,
                            plotOptions: {
                                bar: { distributed: true, borderRadius: 6, columnWidth: '55%' },
                            },
                            dataLabels: { enabled: true, style: { colors: ['#fff'], fontWeight: 600 } },
                            xaxis: { categories: labels, labels: { style: { colors: AXIS } }, axisBorder: { color: GRID }, axisTicks: { color: GRID } },
                            yaxis: { labels: { style: { colors: AXIS } } },
                        };
                    } else if (type === 'line' || type === 'area') {
                        options = {
                            ...base,
                            colors: [colors[0]],
                            stroke: { curve: 'smooth', width: 3 },
                            fill: type === 'area'
                                ? { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } }
                                : { type: 'solid' },
                            markers: { size: 5, colors: ['#E6930A'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 7 } },
                            xaxis: { categories: labels, labels: { style: { colors: AXIS } }, axisBorder: { color: GRID }, axisTicks: { color: GRID } },
                            yaxis: { labels: { style: { colors: AXIS } } },
                        };
                    } else if (type === 'pie') {
                        options = {
                            ...base,
                            series: values,           // pie expects a flat number[]
                            labels: labels,           // top-level labels, not xaxis.categories
                            colors: colors,
                            stroke: { width: 2, colors: ['#fff'] },
                            dataLabels: { enabled: true, style: { fontWeight: 600 } },
                            legend: { show: true, position: 'bottom', labels: { colors: AXIS } },
                        };
                    } else { // radar
                        options = {
                            ...base,
                            colors: [colors[0]],
                            stroke: { width: 2 },
                            fill: { opacity: 0.2 },
                            markers: { size: 4, colors: [colors[0]], strokeColors: '#fff', strokeWidth: 2 },
                            xaxis: { categories: labels, labels: { style: { colors: Array(labels.length).fill(AXIS) } } },
                        };
                    }

                    new ApexCharts(el, options).render();
                });
            });
        </script>
    @endif
@endpush
