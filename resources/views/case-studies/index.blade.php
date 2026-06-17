@extends('layouts.public')

@section('title', 'Case Studies — HRRL CISO Office')

@section('content')

    {{-- ══════════ HERO ══════════ --}}
    <section class="relative bg-primary text-white overflow-hidden border-b border-white/10">
        <div class="absolute inset-0 bg-cover bg-center opacity-[0.12]"
            style="background-image: url('/images/banner.png')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/95 to-primary/70"></div>

        <div class="max-w-container-max mx-auto px-margin-edge py-24 lg:py-28 relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-px w-10 bg-alert-amber"></div>
                <span class="font-label-md text-alert-amber tracking-widest uppercase text-xs font-semibold">Proven Results</span>
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold tracking-tight leading-[1.05] mb-6 max-w-4xl">
                Case
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-alert-amber to-[#f5a623]">Studies</span>
            </h1>
            <p class="font-body-lg text-white/60 font-light max-w-2xl text-lg leading-relaxed">
                Real-world security engagements delivered by the HRRL CISO Office — the challenge,
                the approach, and the measurable outcomes.
            </p>
        </div>
    </section>

    {{-- ══════════ GRID ══════════ --}}
    <section class="py-section-gap bg-surface">
        <div class="max-w-container-max mx-auto px-margin-edge">
            @if ($caseStudies->isEmpty())
                <div class="text-center py-24 max-w-md mx-auto">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-white border border-border-gray/30 shadow-sm flex items-center justify-center text-primary/30">
                        <span class="material-symbols-outlined text-4xl">description</span>
                    </div>
                    <h2 class="font-title-md text-primary mb-2">No case studies published yet</h2>
                    <p class="font-body-md text-on-surface-variant font-light">
                        Published case studies from the CISO Office will appear here soon.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($caseStudies as $caseStudy)
                        <a href="{{ route('casestudies.public.show', $caseStudy->slug) }}"
                            class="group bg-white/70 backdrop-blur-lg border border-border-gray/30 rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex flex-col">
                            {{-- Image --}}
                            <div class="aspect-[16/10] overflow-hidden bg-primary/5">
                                @if ($caseStudy->image_url)
                                    <img src="{{ $caseStudy->image_url }}" alt="{{ $caseStudy->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary/20 text-5xl">shield</span>
                                    </div>
                                @endif
                            </div>
                            {{-- Body --}}
                            <div class="p-6 flex flex-col flex-1">
                                @if ($caseStudy->client)
                                    <span class="inline-flex self-start items-center gap-1.5 bg-secondary/10 text-secondary font-label-sm px-2.5 py-1 rounded-full mb-3">
                                        <span class="material-symbols-outlined text-sm">business</span>
                                        {{ $caseStudy->client }}
                                    </span>
                                @endif
                                <h2 class="font-title-md text-primary tracking-tight mb-2 group-hover:text-secondary transition-colors line-clamp-2">
                                    {{ $caseStudy->title }}
                                </h2>
                                @if ($caseStudy->short_description)
                                    <p class="font-body-md text-on-surface-variant font-light line-clamp-3 mb-5">
                                        {{ $caseStudy->short_description }}
                                    </p>
                                @endif
                                <span class="mt-auto font-label-md text-secondary group-hover:text-primary transition-colors flex items-center gap-1 group-hover:gap-2">
                                    Read case study <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

@endsection
