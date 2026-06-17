@extends('layouts.public')

@section('title', $caseStudy->title . ' — Case Studies · HRRL CISO Office')

@push('styles')
    <meta name="description" content="{{ $caseStudy->short_description }}">
    <meta property="og:title" content="{{ $caseStudy->title }}">
    <meta property="og:description" content="{{ $caseStudy->short_description }}">
    @if ($caseStudy->image_url)
        <meta property="og:image" content="{{ $caseStudy->image_url }}">
    @endif
@endpush

@section('content')

    {{-- ══════════ HERO ══════════ --}}
    <section class="relative bg-[#040f24] text-white overflow-hidden">
        @if ($caseStudy->image_url)
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $caseStudy->image_url }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#040f24] via-[#040f24]/85 to-[#040f24]/60"></div>
        @else
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-primary-container/60 via-[#040f24] to-[#040f24]"></div>
        @endif

        <div class="max-w-container-max mx-auto px-margin-edge pt-10 pb-20 lg:pb-24 relative z-10">
            {{-- breadcrumb --}}
            <nav class="flex items-center gap-2 font-label-sm text-white/50 mb-16">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span class="text-white/25">/</span>
                <a href="{{ route('casestudies.public.index') }}" class="hover:text-white transition-colors">Case Studies</a>
                <span class="text-white/25">/</span>
                <span class="text-white/80 line-clamp-1">{{ $caseStudy->title }}</span>
            </nav>

            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    @if ($caseStudy->client)
                        <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur font-label-sm text-white px-3 py-1 rounded-full">
                            <span class="material-symbols-outlined text-sm text-alert-amber">business</span>
                            {{ $caseStudy->client }}
                        </span>
                    @endif
                    @if ($caseStudy->published_at)
                        <span class="font-label-sm text-white/50">{{ $caseStudy->published_at->format('F j, Y') }}</span>
                    @endif
                </div>
                <h1 class="text-4xl lg:text-6xl font-bold tracking-tight leading-[1.05] mb-6">
                    {{ $caseStudy->title }}
                </h1>
                @if ($caseStudy->short_description)
                    <p class="font-body-lg text-white/65 font-light text-lg leading-relaxed">
                        {{ $caseStudy->short_description }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    <div class="bg-surface">
        <div class="max-w-container-max mx-auto px-margin-edge">

            {{-- ══════════ BODY ══════════ --}}
            @if ($caseStudy->content)
                <section class="pt-16 lg:pt-20">
                    <div class="blog-content max-w-3xl">
                        {!! $caseStudy->content !!}
                    </div>
                </section>
            @endif

            {{-- ══════════ RESULTS ══════════ --}}
            @if ($caseStudy->results)
                <section class="pt-16 max-w-3xl">
                    <div class="bg-white rounded-2xl border border-emerald-200 ring-1 ring-emerald-100 overflow-hidden">
                        <div class="px-7 py-5 bg-emerald-50 border-b border-emerald-100 flex items-center gap-3">
                            <span class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <span class="material-symbols-outlined text-xl">trending_up</span>
                            </span>
                            <div>
                                <h2 class="font-title-md text-emerald-700 leading-none">Results &amp; Outcomes</h2>
                                <p class="font-label-sm text-emerald-600/70 mt-1">The measurable impact</p>
                            </div>
                        </div>
                        <div class="p-7 font-body-md text-on-surface whitespace-pre-line leading-relaxed">
                            {{ $caseStudy->results }}
                        </div>
                    </div>
                </section>
            @endif

            {{-- ══════════ BACK NAV ══════════ --}}
            <div class="mt-16 lg:mt-20 pt-10 pb-section-gap border-t border-border-gray/30 flex flex-wrap items-center justify-between gap-6">
                <a href="{{ route('casestudies.public.index') }}"
                    class="font-label-md inline-flex items-center gap-2 text-primary hover:gap-3 transition-all duration-300 group">
                    <span class="material-symbols-outlined text-lg transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                    Back to all Case Studies
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
