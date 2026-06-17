@extends('layouts.public')

@section('title', 'HRRL CISO Office - Premium Portal')

@section('content')
    <!-- HERO SECTION -->
    <section class="relative py-28 sm:py-32 lg:py-48 overflow-hidden border-b border-border-gray/20">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('/images/banner.png')"
            role="img" aria-label="HRRL refinery cyber security operations">
        </div>
        <!-- Scrim for text legibility over the banner -->
        <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/65 to-primary/20"></div>
        <div class="max-w-container-max mx-auto px-margin-edge relative z-10">
            <div class="max-w-3xl">
                <h1
                    class="font-display-lg text-4xl sm:text-5xl lg:text-6xl font-bold text-white tracking-tight drop-shadow-md mb-6">
                    Security is a culture,<br /> not a software
                </h1>
                <p class="font-body-lg mb-stack-lg max-w-2xl font-light text-lg sm:text-xl text-white/90">
                    Advanced cyber threat protection, real-time intelligence, and comprehensive security awareness
                    for industrial-scale operations.
                </p>
                <div class="flex flex-wrap gap-4 mt-8">
                    <a href="{{ route('report.incident') }}"
                        class="bg-alert-amber text-on-primary font-label-md px-8 py-3.5 rounded-full hover:shadow-[0_0_30px_rgba(230,147,10,0.6)] hover:-translate-y-0.5 transition-all duration-300 font-bold focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary">
                        Report a Cyber Incident
                    </a>
                    <a href="{{ route('awareness') }}"
                        class="bg-white/10 backdrop-blur-sm border border-white/40 text-white font-label-md px-8 py-3.5 rounded-full hover:bg-white/20 hover:-translate-y-0.5 shadow-sm hover:shadow-md transition-all duration-300 font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary">
                        Read Awareness Articles
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CYBER SAFETY ASSISTANT ABOUT -->
    <section class="py-section-gap bg-gray-50 relative overflow-hidden border-b border-gray-200">
        <!-- Subtle dot pattern -->
        <div class="absolute inset-0 opacity-[0.035]"
            style="background-image: radial-gradient(circle, #1e293b 1px, transparent 1px); background-size: 28px 28px;">
        </div>
        <!-- Soft amber glow top-right -->
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-alert-amber/10 blur-3xl pointer-events-none"></div>

        <div class="max-w-container-max mx-auto px-margin-edge relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 xl:gap-20">

                <!-- LEFT: About Content -->
                <div class="lg:w-1/2 flex flex-col gap-7">

                    <!-- Label -->
                    <div class="flex items-center gap-3">
                        <div class="h-px w-10 bg-alert-amber"></div>
                        <span class="font-label-md text-alert-amber tracking-widest uppercase text-xs font-semibold">HRRL
                            CISO Office</span>
                    </div>

                    <!-- Heading -->
                    <div>
                        <h2 class="text-4xl xl:text-5xl font-bold text-gray-900 tracking-tight leading-tight">
                            Cyber Dost<br />
                            <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-alert-amber to-[#f5a623]">Assistant</span>
                        </h2>
                        <p class="mt-4 font-body-lg text-on-surface-variant font-light text-lg leading-relaxed max-w-lg">
                            Your always-on AI-powered security companion — delivering real-time threat guidance, policy
                            awareness, and incident support across the HRRL enterprise.
                        </p>
                    </div>

                    <!-- Feature list -->
                    <ul class="space-y-4">
                        <li class="flex items-start gap-4">
                            <div
                                class="mt-0.5 flex-shrink-0 w-9 h-9 rounded-xl bg-alert-amber/10 border border-alert-amber/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-alert-amber text-lg"
                                    aria-hidden="true">smart_toy</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">AI-Powered Threat Guidance</p>
                                <p class="text-on-surface-variant text-sm font-light mt-0.5">Instant, contextual answers to
                                    your
                                    security questions — 24/7, no tickets required.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div
                                class="mt-0.5 flex-shrink-0 w-9 h-9 rounded-xl bg-alert-amber/10 border border-alert-amber/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-alert-amber text-lg"
                                    aria-hidden="true">shield_lock</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Policy & Compliance Intelligence</p>
                                <p class="text-on-surface-variant text-sm font-light mt-0.5">Understand HRRL security
                                    policies,
                                    compliance requirements, and best practices instantly.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div
                                class="mt-0.5 flex-shrink-0 w-9 h-9 rounded-xl bg-alert-amber/10 border border-alert-amber/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-alert-amber text-lg"
                                    aria-hidden="true">crisis_alert</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Incident Response Support</p>
                                <p class="text-on-surface-variant text-sm font-light mt-0.5">Step-by-step guidance during a
                                    security
                                    event, routed directly to the CISO team.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div
                                class="mt-0.5 flex-shrink-0 w-9 h-9 rounded-xl bg-alert-amber/10 border border-alert-amber/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-alert-amber text-lg"
                                    aria-hidden="true">bar_chart_4_bars</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Security Awareness & Training</p>
                                <p class="text-on-surface-variant text-sm font-light mt-0.5">Personalised micro-learning
                                    tailored to
                                    your role, department, and risk profile.</p>
                            </div>
                        </li>
                    </ul>
                    <!-- CTA -->
                    <div class="flex flex-wrap gap-4">
                        <button onclick="document.getElementById('cyber-chat-toggle').click()"
                            class="inline-flex items-center gap-2 bg-alert-amber text-white font-label-md px-8 py-3.5 rounded-full hover:shadow-[0_0_24px_rgba(230,147,10,0.45)] hover:-translate-y-0.5 transition-all duration-300 font-bold focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber focus-visible:ring-offset-2">
                            <span class="material-symbols-outlined text-base" aria-hidden="true">chat</span>
                            Chat with Assistant
                        </button>
                    </div>
                </div>

                <!-- RIGHT: 3D Bot Model -->
                <div class="lg:w-1/2 flex items-center justify-center">
                    <model-viewer src="{{ asset('images/bot.glb') }}" alt="HRRL Cyber Safety AI Assistant Bot" auto-rotate
                        camera-controls disable-zoom shadow-intensity="1" exposure="1.2" rotation-per-second="20deg"
                        interaction-prompt="none"
                        style="width: 100%; height: 500px; background: transparent;"></model-viewer>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.5.0/model-viewer.min.js"></script>
    @endpush

     <!-- MAJOR PROJECTS -->
    @if (isset($projects) && $projects->isNotEmpty())
        <section class="py-section-gap bg-gray-50 relative overflow-hidden border-y border-gray-200">
            <!-- Soft blue glow -->
            <div class="absolute top-1/4 -left-32 w-96 h-96 rounded-full bg-secondary/5 blur-3xl pointer-events-none">
            </div>

            <div class="max-w-container-max mx-auto px-margin-edge relative z-10">
                <!-- Header -->
                <div class="mb-12 text-center md:text-left">
                    <div class="flex items-center gap-3 mb-4 justify-center md:justify-start">
                        <div class="h-px w-10 bg-alert-amber"></div>
                        <span class="font-label-md text-alert-amber tracking-widest uppercase text-xs font-semibold">Our
                            Work</span>
                    </div>
                    <h2 class="font-headline-lg text-3xl sm:text-4xl font-bold text-primary tracking-tight">Major Projects
                    </h2>
                    <p class="font-body-lg text-on-surface-variant mt-1 font-light">Security initiatives delivered by the
                        HRRL CISO Office to protect the enterprise.</p>
                </div>

                <!-- Project grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    @foreach ($projects as $project)
                        @php
                            $stat = ($project->statistics ?? [])[0] ?? null;
                        @endphp
                        <a href="{{ route('projects.public.show', $project->slug) }}"
                            class="group flex flex-col bg-white/70 backdrop-blur-lg border border-border-gray/30 rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber focus-visible:ring-offset-2"
                            aria-label="View project: {{ $project->name }}">
                            <!-- Banner -->
                            <div class="relative overflow-hidden">
                                @if ($project->banner_url)
                                    <img src="{{ $project->banner_url }}" alt="{{ $project->name }}"
                                        class="w-full h-52 object-contain transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div
                                        class="w-full aspect-[4/3] bg-gradient-to-br from-primary/15 via-secondary/10 to-alert-amber/10 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary/25 text-7xl"
                                            aria-hidden="true">shield</span>
                                    </div>
                                @endif
                                <!-- gradient sheen -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-primary/30 via-transparent to-transparent">
                                </div>
                                @if ($stat && !empty($stat['value']))
                                    <!-- floating stat -->
                                    <div
                                        class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-md rounded-xl shadow-lg px-4 py-2">
                                        <div class="font-title-md font-bold text-primary leading-none">
                                            {{ $stat['value'] }}</div>
                                        <div class="font-label-sm text-on-surface-variant mt-1">{{ $stat['key'] ?? '' }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Body -->
                            <div class="flex flex-col flex-1 p-7">
                                <h3
                                    class="font-title-md text-primary tracking-tight mb-2 group-hover:text-secondary transition-colors">
                                    {{ $project->name }}
                                </h3>
                                @if ($project->short_description)
                                    <p class="font-body-md text-on-surface-variant font-light line-clamp-2 mb-6">
                                        {{ $project->short_description }}
                                    </p>
                                @endif
                                <span
                                    class="mt-auto font-label-md text-secondary group-hover:text-primary transition-colors flex items-center gap-1 group-hover:gap-2 w-max">
                                    View Project <span class="material-symbols-outlined text-sm"
                                        aria-hidden="true">arrow_forward</span>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- View all -->
                <div class="flex justify-center md:justify-start">
                    <a href="{{ route('projects.public.index') }}"
                        class="inline-flex items-center gap-2 bg-alert-amber text-on-primary font-label-md px-8 py-3.5 rounded-full hover:shadow-[0_0_24px_rgba(230,147,10,0.45)] hover:-translate-y-0.5 transition-all duration-300 font-bold focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber focus-visible:ring-offset-2">
                        View All Projects
                        <span class="material-symbols-outlined text-base" aria-hidden="true">arrow_forward</span>
                    </a>
                </div>
            </div>
        </section>
    @endif
    <!-- AWARENESS HUB -->
    <section class="py-section-gap bg-surface">
        <div class="max-w-container-max mx-auto px-margin-edge">
            <div class="mb-12 text-center md:text-left">
                <h2 class="font-headline-lg text-3xl sm:text-4xl font-bold text-primary tracking-tight">Cyber Awareness</h2>
                <p class="font-body-lg text-on-surface-variant mt-1 font-light">Essential knowledge to protect
                    yourself and the organization.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @forelse ($blogs as $blog)
                    <a href="{{ route('blog.public.show', $blog->slug) }}"
                        class="group flex flex-col bg-white/70 backdrop-blur-lg border border-border-gray/30 rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber focus-visible:ring-offset-2"
                        aria-label="Read article: {{ $blog->title }}">
                        <!-- Banner -->
                        <div class="relative overflow-hidden">
                            @if ($blog->banner_url)
                                <img src="{{ $blog->banner_url }}" alt="{{ $blog->title }}"
                                    class="w-full aspect-[16/9] object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div
                                    class="w-full aspect-[16/9] bg-gradient-to-br from-primary/15 via-secondary/10 to-alert-amber/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary/25 text-7xl"
                                        aria-hidden="true">article</span>
                                </div>
                            @endif
                            @if ($blog->category)
                                <span
                                    class="absolute top-4 left-4 bg-white/90 backdrop-blur-md text-primary font-label-sm font-semibold px-3 py-1 rounded-full shadow-sm">
                                    {{ $blog->category->name }}
                                </span>
                            @endif
                        </div>
                        <!-- Body -->
                        <div class="flex flex-col flex-1 p-7">
                            <div class="flex items-center gap-2 text-on-surface-variant font-label-sm mb-3">
                                <span class="material-symbols-outlined text-sm" aria-hidden="true">schedule</span>
                                <span>{{ $blog->read_time }} min read</span>
                                @if ($blog->published_at)
                                    <span aria-hidden="true">·</span>
                                    <span>{{ $blog->published_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                            <h3
                                class="font-title-md text-primary tracking-tight mb-2 line-clamp-2 group-hover:text-secondary transition-colors">
                                {{ $blog->title }}
                            </h3>
                            @if ($blog->excerpt)
                                <p class="font-body-md text-on-surface-variant font-light line-clamp-2 mb-6">
                                    {{ $blog->excerpt }}
                                </p>
                            @endif
                            <span
                                class="mt-auto font-label-md text-secondary group-hover:text-primary transition-colors flex items-center gap-1 group-hover:gap-2 w-max">
                                Read Article <span class="material-symbols-outlined text-sm"
                                    aria-hidden="true">arrow_forward</span>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
                        <span class="material-symbols-outlined text-on-surface-variant/40 text-5xl mb-3"
                            aria-hidden="true">article</span>
                        <p class="font-body-md text-on-surface-variant">No awareness articles published yet. Check back
                            soon.</p>
                    </div>
                @endforelse
            </div>

            <!-- View all articles -->
            <div class="flex justify-center md:justify-start mb-16">
                <a href="{{ route('blog.public.index') }}"
                    class="inline-flex items-center gap-2 bg-alert-amber text-on-primary font-label-md px-8 py-3.5 rounded-full hover:shadow-[0_0_24px_rgba(230,147,10,0.45)] hover:-translate-y-0.5 transition-all duration-300 font-bold focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber focus-visible:ring-offset-2">
                    View All Articles
                    <span class="material-symbols-outlined text-base" aria-hidden="true">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
    <!-- INCIDENT RESPONSE GUIDE -->
    <section class="py-section-gap bg-white border-y border-border-gray/20">
        <div class="max-w-container-max mx-auto px-margin-edge">
            <div class="mb-16 max-w-3xl">
                <h2 class="font-headline-lg text-3xl sm:text-4xl font-bold text-primary tracking-tight">What to Do If
                    You're a Victim of Cybercrime</h2>
                <p class="font-body-lg text-on-surface-variant mt-4 font-light">Immediate, clinical actions to take
                    to mitigate damage and contain threats.</p>
            </div>
            <div class="flex flex-col lg:flex-row gap-16">
                <div class="lg:w-1/2">
                    <div class="border-l-4 border-alert-amber pl-8 py-2">
                        <ol class="space-y-6 font-body-md text-on-surface relative">
                            <li
                                class="relative before:content-['1'] before:absolute before:-left-12 before:w-6 before:h-6 before:bg-surface-container before:rounded-full before:flex before:items-center before:justify-center before:text-sm before:font-bold before:text-primary">
                                <span class="font-bold text-primary block text-lg mb-1">Stay calm.</span> Do not
                                panic or try to hide the incident.
                            </li>
                            <li
                                class="relative before:content-['2'] before:absolute before:-left-12 before:w-6 before:h-6 before:bg-surface-container before:rounded-full before:flex before:items-center before:justify-center before:text-sm before:font-bold before:text-primary">
                                <span class="font-bold text-primary block text-lg mb-1">Disconnect network.</span>
                                Immediately unplug your ethernet cable or disconnect from Wi-Fi.
                            </li>
                            <li
                                class="relative before:content-['3'] before:absolute before:-left-12 before:w-6 before:h-6 before:bg-surface-container before:rounded-full before:flex before:items-center before:justify-center before:text-sm before:font-bold before:text-primary">
                                <span class="font-bold text-primary block text-lg mb-1">Do NOT delete files.</span>
                                Leave your computer exactly as it is to preserve evidence.
                            </li>
                            <li
                                class="relative before:content-['4'] before:absolute before:-left-12 before:w-6 before:h-6 before:bg-surface-container before:rounded-full before:flex before:items-center before:justify-center before:text-sm before:font-bold before:text-primary">
                                <span class="font-bold text-primary block text-lg mb-1">Document what you
                                    saw.</span> Write down any error messages or strange behavior.
                            </li>
                            <li
                                class="relative before:content-['5'] before:absolute before:-left-12 before:w-6 before:h-6 before:bg-surface-container before:rounded-full before:flex before:items-center before:justify-center before:text-sm before:font-bold before:text-primary">
                                <span class="font-bold text-primary block text-lg mb-1">Cooperate fully.</span>
                                Work closely with the Incident Response team.
                            </li>
                            <li
                                class="relative before:content-['6'] before:absolute before:-left-12 before:w-6 before:h-6 before:bg-surface-container before:rounded-full before:flex before:items-center before:justify-center before:text-sm before:font-bold before:text-primary">
                                <span class="font-bold text-primary block text-lg mb-1">Do not discuss.</span> Keep
                                the incident confidential unless instructed otherwise.
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="lg:w-1/2 grid grid-cols-1 gap-6">
                    <div
                        class="bg-surface/50 p-6 rounded-xl border border-border-gray/30 hover:border-alert-amber/50 transition-colors group">
                        <h3 class="font-title-md text-primary mb-2 flex items-center justify-between">
                            Phishing Incident
                            <span aria-hidden="true"
                                class="material-symbols-outlined text-alert-amber opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
                        </h3>
                        <p class="font-body-md text-on-surface-variant mb-4">Employee clicked a malicious link
                            leading to compromised credentials.</p>
                        <button
                            class="text-secondary font-label-md hover:underline font-semibold rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                            aria-label="Read the Phishing Incident case study">Read Case
                            Study</button>
                    </div>
                    <div
                        class="bg-surface/50 p-6 rounded-xl border border-border-gray/30 hover:border-alert-amber/50 transition-colors group">
                        <h3 class="font-title-md text-primary mb-2 flex items-center justify-between">
                            Ransomware Attack
                            <span aria-hidden="true"
                                class="material-symbols-outlined text-alert-amber opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
                        </h3>
                        <p class="font-body-md text-on-surface-variant mb-4">Malware encrypted local files after a
                            suspicious download.</p>
                        <button
                            class="text-secondary font-label-md hover:underline font-semibold rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                            aria-label="Read the Ransomware Attack case study">Read Case
                            Study</button>
                    </div>
                    <div
                        class="bg-surface/50 p-6 rounded-xl border border-border-gray/30 hover:border-alert-amber/50 transition-colors group">
                        <h3 class="font-title-md text-primary mb-2 flex items-center justify-between">
                            Third-Party Breach
                            <span aria-hidden="true"
                                class="material-symbols-outlined text-alert-amber opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
                        </h3>
                        <p class="font-body-md text-on-surface-variant mb-4">Vendor compromise led to unauthorized
                            access attempts.</p>
                        <button
                            class="text-secondary font-label-md hover:underline font-semibold rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                            aria-label="Read the Third-Party Breach case study">Read Case
                            Study</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
   
    <!-- FINAL CTA -->
    <section class="bg-gradient-to-r from-alert-amber to-[#f5a623] text-on-primary py-24 relative overflow-hidden">
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9zdmc+')] opacity-50">
        </div>
        <div
            class="max-w-container-max mx-auto px-margin-edge text-center flex flex-col items-center justify-center gap-8 relative z-10">
            <h2 class="font-display-lg text-4xl sm:text-5xl font-bold text-white tracking-tight drop-shadow-md">Spotted
                something suspicious?</h2>
            <p class="font-body-lg max-w-2xl text-white/90 text-lg sm:text-xl font-light">Security is everyone's
                responsibility. If you see something, say something.</p>
            <div class="flex flex-wrap justify-center gap-6 mt-6">
                <a href="{{ route('report.incident') }}"
                    class="bg-primary text-on-primary font-label-md px-10 py-4 rounded-full shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 font-bold text-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-alert-amber">
                    Report Online
                </a>
                <a href="mailto:ciso_office@hrrl.in"
                    class="bg-transparent border-2 border-white text-white font-label-md px-10 py-4 rounded-full hover:bg-white hover:text-alert-amber shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 font-bold text-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-alert-amber">
                    Email CISO Office
                </a>
            </div>
        </div>
    </section>

@endsection
