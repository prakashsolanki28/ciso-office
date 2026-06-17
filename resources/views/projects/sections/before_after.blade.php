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
