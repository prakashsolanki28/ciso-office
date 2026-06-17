{{-- ══════════ STATISTICS ══════════ --}}
@if (!empty($project->statistics))
    <section class="pt-20 lg:pt-24">
        <div class="max-w-2xl mb-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-px w-8 bg-alert-amber"></div>
                <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">At a glance</span>
            </div>
            <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">The numbers</h2>
            <p class="font-body-lg text-on-surface-variant font-light mt-3">Key figures that define this project.</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-px bg-border-gray/30 rounded-2xl overflow-hidden ring-1 ring-black/5 shadow-sm">
            @foreach ($project->statistics as $stat)
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
    </section>
@endif
