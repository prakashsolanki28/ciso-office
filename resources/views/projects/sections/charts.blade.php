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
