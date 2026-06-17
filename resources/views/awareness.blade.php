@extends('layouts.public')

@section('title', 'Awareness — HRRL CISO Office')

@section('content')

    {{-- ══════════ HERO ══════════ --}}
    <section class="relative bg-primary text-white overflow-hidden border-b border-white/10">
        <div class="absolute inset-0 bg-cover bg-center opacity-[0.12]"
            style="background-image: url('/images/banner.png')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/95 to-primary/70"></div>

        <div class="max-w-container-max mx-auto px-margin-edge py-24 lg:py-28 relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-px w-10 bg-alert-amber"></div>
                <span class="font-label-md text-alert-amber tracking-widest uppercase text-xs font-semibold">Stay Informed</span>
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold tracking-tight leading-[1.05] mb-6 max-w-4xl">
                Cyber
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-alert-amber to-[#f5a623]">Awareness</span>
                Hub
            </h1>
            <p class="font-body-lg text-white/60 font-light max-w-2xl text-lg leading-relaxed">
                Articles, case studies, and newsletters from the HRRL CISO Office — everything you
                need to stay a step ahead of cyber threats.
            </p>
        </div>
    </section>

    @php $allEmpty = $blogs->isEmpty() && $caseStudies->isEmpty() && $newsletters->isEmpty(); @endphp

    @if ($allEmpty)
        <section class="py-section-gap bg-surface">
            <div class="max-w-container-max mx-auto px-margin-edge text-center py-16">
                <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-white border border-border-gray/30 shadow-sm flex items-center justify-center text-primary/30">
                    <span class="material-symbols-outlined text-4xl">menu_book</span>
                </div>
                <h2 class="font-title-md text-primary mb-2">Nothing published yet</h2>
                <p class="font-body-md text-on-surface-variant font-light">
                    Awareness content from the CISO Office will appear here soon.
                </p>
            </div>
        </section>
    @endif

    {{-- ══════════ BLOGS ══════════ --}}
    @if ($blogs->isNotEmpty())
        <section class="py-section-gap bg-surface">
            <div class="max-w-container-max mx-auto px-margin-edge">
                <div class="flex items-end justify-between gap-6 mb-10 flex-wrap">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-alert-amber"></div>
                            <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Articles</span>
                        </div>
                        <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">From the Blog</h2>
                    </div>
                    <a href="{{ route('blog.public.index') }}"
                        class="font-label-md inline-flex items-center gap-1.5 text-secondary hover:text-primary hover:gap-2.5 transition-all">
                        View all articles <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($blogs as $blog)
                        <a href="{{ route('blog.public.show', $blog->slug) }}"
                            class="group bg-white/70 backdrop-blur-lg border border-border-gray/30 rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex flex-col">
                            <div class="aspect-[16/10] overflow-hidden bg-primary/5">
                                @if ($blog->banner_url)
                                    <img src="{{ $blog->banner_url }}" alt="{{ $blog->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary/20 text-5xl">article</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6 flex flex-col flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    @if ($blog->category)
                                        <span class="inline-flex items-center bg-secondary/10 text-secondary font-label-sm px-2.5 py-1 rounded-full">{{ $blog->category->name }}</span>
                                    @endif
                                    @if ($blog->published_at)
                                        <span class="font-label-sm text-on-surface-variant/70">{{ $blog->published_at->format('M d, Y') }}</span>
                                    @endif
                                </div>
                                <h3 class="font-title-md text-primary tracking-tight mb-2 group-hover:text-secondary transition-colors line-clamp-2">{{ $blog->title }}</h3>
                                @if ($blog->excerpt)
                                    <p class="font-body-md text-on-surface-variant font-light line-clamp-3 mb-5">{{ $blog->excerpt }}</p>
                                @endif
                                <span class="mt-auto font-label-md text-secondary group-hover:text-primary transition-colors flex items-center gap-1 group-hover:gap-2">
                                    Read article <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ══════════ CASE STUDIES ══════════ --}}
    @if ($caseStudies->isNotEmpty())
        <section class="py-section-gap bg-white border-y border-border-gray/20">
            <div class="max-w-container-max mx-auto px-margin-edge">
                <div class="flex items-end justify-between gap-6 mb-10 flex-wrap">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-alert-amber"></div>
                            <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Proven Results</span>
                        </div>
                        <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">Case Studies</h2>
                    </div>
                    <a href="{{ route('casestudies.public.index') }}"
                        class="font-label-md inline-flex items-center gap-1.5 text-secondary hover:text-primary hover:gap-2.5 transition-all">
                        View all case studies <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($caseStudies as $caseStudy)
                        <a href="{{ route('casestudies.public.show', $caseStudy->slug) }}"
                            class="group bg-surface border border-border-gray/30 rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex flex-col">
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
                            <div class="p-6 flex flex-col flex-1">
                                @if ($caseStudy->client)
                                    <span class="inline-flex self-start items-center gap-1.5 bg-secondary/10 text-secondary font-label-sm px-2.5 py-1 rounded-full mb-3">
                                        <span class="material-symbols-outlined text-sm">business</span>{{ $caseStudy->client }}
                                    </span>
                                @endif
                                <h3 class="font-title-md text-primary tracking-tight mb-2 group-hover:text-secondary transition-colors line-clamp-2">{{ $caseStudy->title }}</h3>
                                @if ($caseStudy->short_description)
                                    <p class="font-body-md text-on-surface-variant font-light line-clamp-3 mb-5">{{ $caseStudy->short_description }}</p>
                                @endif
                                <span class="mt-auto font-label-md text-secondary group-hover:text-primary transition-colors flex items-center gap-1 group-hover:gap-2">
                                    Read case study <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ══════════ NEWSLETTERS ══════════ --}}
    @if ($newsletters->isNotEmpty())
        <section class="py-section-gap bg-surface">
            <div class="max-w-container-max mx-auto px-margin-edge">
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-px w-8 bg-alert-amber"></div>
                        <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Bulletins</span>
                    </div>
                    <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">Newsletters</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($newsletters as $newsletter)
                        <div class="group bg-white/70 backdrop-blur-lg border border-border-gray/30 rounded-2xl overflow-hidden flex flex-col">
                            <div class="aspect-[16/10] overflow-hidden bg-primary/5">
                                @if ($newsletter->image_url)
                                    <img src="{{ $newsletter->image_url }}" alt="{{ $newsletter->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary/20 text-5xl">mail</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6 flex flex-col flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1.5 bg-alert-amber/10 text-alert-amber font-label-sm px-2.5 py-1 rounded-full">
                                        <span class="material-symbols-outlined text-sm">mark_email_unread</span>Newsletter
                                    </span>
                                    @if ($newsletter->published_at)
                                        <span class="font-label-sm text-on-surface-variant/70">{{ $newsletter->published_at->format('M d, Y') }}</span>
                                    @endif
                                </div>
                                <h3 class="font-title-md text-primary tracking-tight mb-2 line-clamp-2">{{ $newsletter->title }}</h3>
                                @if ($newsletter->short_description)
                                    <p class="font-body-md text-on-surface-variant font-light line-clamp-3">{{ $newsletter->short_description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
