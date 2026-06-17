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
