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
