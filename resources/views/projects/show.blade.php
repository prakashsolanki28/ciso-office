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

    <div class="bg-surface">
        <div class="max-w-container-max mx-auto px-margin-edge">

            {{-- Sections render in the order saved on the project (Project::orderedSections),
                 falling back to the default order. Each partial is self-guarding: empty
                 sections render nothing. --}}
            @foreach ($project->orderedSections() as $section)
                @includeIf('projects.sections.' . $section, ['project' => $project])
            @endforeach

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
