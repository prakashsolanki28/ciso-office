<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'HRRL CISO Office - Premium Portal')</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-surface text-on-surface font-body-md antialiased selection:bg-secondary selection:text-white">

    <!-- Skip to content (keyboard users) -->
    <a href="#main"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[60] focus:rounded-full focus:bg-primary focus:px-5 focus:py-2.5 focus:font-label-md focus:font-bold focus:text-on-primary focus:shadow-lg">
        Skip to content
    </a>

    <!-- TopAppBar -->
    <header x-data="{ open: false }"
        class="bg-white/70 backdrop-blur-lg w-full top-0 sticky z-50 border-b border-border-gray/30 transition-all duration-300">
        <div class="flex justify-between items-center w-full px-margin-edge py-4 max-w-container-max mx-auto">
            <a href="{{ url('/') }}" aria-label="HRRL CISO Office home"
                class="flex items-center gap-3 sm:gap-4 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2">
                {{-- Main org: HPCL Rajasthan Refinery Limited --}}
                <img src="{{ asset('images/logo/hrrl.png') }}" alt="HPCL Rajasthan Refinery Limited"
                    class="h-9 sm:h-16 w-auto select-none">
                {{-- Divider (desktop only) --}}
                <span class="hidden sm:block h-9 w-px bg-border-gray/40" aria-hidden="true"></span>
                {{-- Sub org: CISO Office --}}
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-8 sm:h-9 sm:w-9">
                <span class="hidden sm:inline font-headline-lg text-xl tracking-tight font-bold text-primary">CISO
                    Office</span>
            </a>
            <nav aria-label="Primary" class="hidden md:flex gap-8">
                <a class="font-label-md rounded {{ request()->is('/') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-200' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                    @if (request()->is('/')) aria-current="page" @endif href="{{ url('/') }}">Home</a>
                <a class="font-label-md rounded {{ request()->routeIs('projects.public.*') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-200' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                    @if (request()->routeIs('projects.public.*')) aria-current="page" @endif
                    href="{{ route('projects.public.index') }}">Projects</a>
                <a class="font-label-md rounded {{ request()->routeIs('sops.public.*') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-200' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                    @if (request()->routeIs('sops.public.*')) aria-current="page" @endif
                    href="{{ route('sops.public.index') }}">SOPs</a>
                <a class="font-label-md rounded {{ request()->routeIs('awareness') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-200' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                    @if (request()->routeIs('awareness')) aria-current="page" @endif
                    href="{{ route('awareness') }}">Awareness</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('report.incident') }}"
                    class="font-label-md bg-alert-amber text-on-primary px-4 sm:px-6 py-2.5 rounded-full font-bold hover:shadow-[0_6px_20px_rgba(230,147,10,0.23)] hover:bg-opacity-90 transition-all transform hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber focus-visible:ring-offset-2">
                    Report Incident
                </a>
                <!-- Hamburger (mobile) -->
                <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-nav"
                    aria-label="Toggle navigation menu"
                    class="md:hidden inline-flex items-center justify-center w-11 h-11 rounded-full text-primary hover:bg-primary/5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                    <span class="material-symbols-outlined text-2xl" x-show="!open" aria-hidden="true">menu</span>
                    <span class="material-symbols-outlined text-2xl" x-show="open" x-cloak
                        aria-hidden="true">close</span>
                </button>
            </div>
        </div>

        <!-- Mobile nav panel -->
        <nav id="mobile-nav" aria-label="Mobile"
            class="md:hidden border-t border-border-gray/30 bg-white/95 backdrop-blur-lg" x-show="open" x-cloak
            x-transition @keydown.escape.window="open = false">
            <div class="px-margin-edge py-3 flex flex-col">
                <a @click="open = false"
                    class="py-3 font-label-md {{ request()->is('/') ? 'text-primary font-bold' : 'text-on-surface-variant' }}"
                    @if (request()->is('/')) aria-current="page" @endif href="{{ url('/') }}">Home</a>
                <a @click="open = false"
                    class="py-3 font-label-md border-t border-border-gray/20 {{ request()->routeIs('projects.public.*') ? 'text-primary font-bold' : 'text-on-surface-variant' }}"
                    @if (request()->routeIs('projects.public.*')) aria-current="page" @endif
                    href="{{ route('projects.public.index') }}">Projects</a>
                <a @click="open = false"
                    class="py-3 font-label-md border-t border-border-gray/20 {{ request()->routeIs('casestudies.public.*') ? 'text-primary font-bold' : 'text-on-surface-variant' }}"
                    @if (request()->routeIs('casestudies.public.*')) aria-current="page" @endif
                    href="{{ route('casestudies.public.index') }}">Case Studies</a>
                <a @click="open = false"
                    class="py-3 font-label-md border-t border-border-gray/20 {{ request()->routeIs('sops.public.*') ? 'text-primary font-bold' : 'text-on-surface-variant' }}"
                    @if (request()->routeIs('sops.public.*')) aria-current="page" @endif
                    href="{{ route('sops.public.index') }}">SOPs</a>
                <a @click="open = false"
                    class="py-3 font-label-md border-t border-border-gray/20 {{ request()->routeIs('awareness') ? 'text-primary font-bold' : 'text-on-surface-variant' }}"
                    @if (request()->routeIs('awareness')) aria-current="page" @endif
                    href="{{ route('awareness') }}">Awareness</a>
            </div>
        </nav>
    </header>

    <main id="main" tabindex="-1" class="focus:outline-none">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-surface-container-lowest border-t border-border-gray/20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-gutter w-full px-margin-edge py-16 max-w-container-max mx-auto">
            <div class="col-span-1 md:col-span-1">
                <img src="{{ asset('images/logo/hrrl.png') }}" alt="HPCL Rajasthan Refinery Limited"
                    class="h-10 w-auto mb-4 select-none">
                <div class="flex items-center gap-2 mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-7 w-7">
                    <span class="font-title-md font-bold text-primary tracking-tight">CISO Office</span>
                </div>
                <p class="font-label-sm text-on-surface-variant font-light">
                    &copy; {{ date('Y') }} HRRL CISO Office.<br />All security protocols strictly
                    enforced.
                </p>
            </div>
            <div class="col-span-1 md:col-span-3 flex flex-wrap gap-x-12 gap-y-4 justify-end items-center">
                <a class="font-label-sm text-on-surface-variant hover:text-primary transition-colors duration-200 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                    href="#">Privacy Policy</a>
                <a class="font-label-sm text-on-surface-variant hover:text-primary transition-colors duration-200 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                    href="#">Terms of Service</a>
                <a class="font-label-sm text-on-surface-variant hover:text-primary transition-colors duration-200 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                    href="mailto:ciso_office@hrrl.in">Contact Us</a>
                <a class="font-label-sm text-on-surface-variant hover:text-primary transition-colors duration-200 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                    href="mailto:ciso_office@hrrl.in">Contact IT Support</a>
                <a class="font-label-sm text-on-surface-variant hover:text-primary transition-colors duration-200 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                    href="#">Sitemap</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/marked@12/marked.min.js"></script>
    <style>
        /* Markdown prose inside assistant bubbles */
        .chat-md p {
            margin: 0 0 .45em;
        }

        .chat-md p:last-child {
            margin-bottom: 0;
        }

        .chat-md ul,
        .chat-md ol {
            padding-left: 1.25em;
            margin: .3em 0 .45em;
        }

        .chat-md ul {
            list-style: disc;
        }

        .chat-md ol {
            list-style: decimal;
        }

        .chat-md li {
            margin-bottom: .18em;
        }

        .chat-md strong {
            font-weight: 600;
        }

        .chat-md em {
            font-style: italic;
        }

        .chat-md a {
            text-decoration: underline;
            color: #1a56db;
            cursor: pointer;
        }

        .chat-md a[href^="tel"] {
            color: #0e9f6e;
            font-weight: 700;
        }

        .chat-md code {
            background: rgba(0, 0, 0, .07);
            border-radius: 3px;
            padding: 1px 4px;
            font-size: .85em;
            font-family: monospace;
        }

        .chat-md hr {
            border: none;
            border-top: 1px solid rgba(0, 0, 0, .1);
            margin: .45em 0;
        }

        /* Chat panel — default (compact) */
        #cyber-chat-panel {
            transition: width .25s ease, height .25s ease, border-radius .25s ease,
                top .25s ease, left .25s ease, bottom .25s ease, right .25s ease;
        }

        /* Full-screen override */
        #cyber-chat-panel.is-fullscreen {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100dvh !important;
            max-height: 100dvh !important;
            border-radius: 0 !important;
            z-index: 10000 !important;
        }

        #cyber-chat-panel.is-fullscreen #cyber-chat-messages {
            max-height: none !important;
        }

        #cyber-chat-panel.is-fullscreen #cyber-chat-messages-inner {
            max-width: 720px;
            margin: 0 auto;
            width: 100%;
        }

        #cyber-chat-panel.is-fullscreen #cyber-chat-input-row {
            max-width: 720px;
            margin: 0 auto;
            width: 100%;
        }
    </style>

    <!-- MANJULIKA CYBER SAFETY CHATBOT -->
    <div id="cyber-chat-widget" class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end gap-3">

        <!-- Chat Panel (compact by default) -->
        <div id="cyber-chat-panel"
            class="hidden flex-col bg-white rounded-2xl shadow-2xl border border-border-gray/20 overflow-hidden"
            style="width:380px; max-height:600px; filter:drop-shadow(0 20px 60px rgba(4,15,36,.20));">

            <!-- Header -->
            <div class="bg-[#040f24] px-4 py-3.5 flex items-center justify-between gap-3 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <!-- Avatar with online dot -->
                    <div class="relative flex-shrink-0">
                        <div
                            class="w-9 h-9 rounded-full bg-gradient-to-br from-alert-amber/30 to-alert-amber/10 flex items-center justify-center border border-alert-amber/30">
                            <span class="material-symbols-outlined text-alert-amber text-xl">support_agent</span>
                        </div>
                        <span
                            class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-green-400 border-2 border-[#040f24]"></span>
                    </div>
                    <div>
                        <div class="text-white font-semibold text-sm leading-tight tracking-wide">Cyber Dost</div>
                        <div class="text-white/45 text-[11px] leading-none mt-0.5">Cyber Safety Assistant · HRRL CISO
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-0.5">
                    <!-- Fullscreen toggle -->
                    <button id="cyber-chat-fullscreen" title="Full screen" aria-label="Toggle full screen"
                        class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors text-white/50 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber">
                        <span id="fs-icon" class="material-symbols-outlined text-[18px]"
                            aria-hidden="true">open_in_full</span>
                    </button>
                    <!-- Clear -->
                    <button id="cyber-chat-clear" title="Clear chat" aria-label="Clear chat"
                        class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors text-white/50 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">restart_alt</span>
                    </button>
                    <!-- Close -->
                    <button id="cyber-chat-close" aria-label="Close chat"
                        class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors text-white/50 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">close</span>
                    </button>
                </div>
            </div>

            <!-- Emergency bar -->
            <div class="bg-red-600/90 px-4 py-1.5 flex items-center gap-2 flex-shrink-0">
                <span class="material-symbols-outlined text-white text-sm">emergency</span>
                <span class="text-white text-[11px] font-medium">
                    Money at risk? Call <a href="tel:1930" class="font-bold underline">1930</a> immediately
                    &nbsp;·&nbsp;
                    <a href="https://cybercrime.gov.in" target="_blank" rel="noopener noreferrer"
                        class="underline">cybercrime.gov.in</a>
                </span>
            </div>

            <!-- Messages -->
            <div id="cyber-chat-messages" class="flex-1 overflow-y-auto bg-[#f8f9fb] min-h-0 py-4">
                <div id="cyber-chat-messages-inner" class="px-4 space-y-3">
                    <!-- Welcome -->
                    <div class="flex gap-2.5 items-end">
                        <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary text-sm">support_agent</span>
                        </div>
                        <div
                            class="chat-md bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-4 py-3 text-sm text-on-surface max-w-[82%] shadow-sm">
                            Hi! I'm <strong>Cyber Dost</strong> — the Cyber Safety Assistant from HRRL CISO Office. If
                            you've been hit by any cyber fraud or online scam, I'm here to help. What happened?
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input area -->
            <div class="flex-shrink-0 border-t border-gray-100 bg-white px-3 py-3">
                <div id="cyber-chat-input-row" class="flex gap-2 items-end">
                    <textarea id="cyber-chat-input" rows="1" placeholder="Type your message..."
                        class="flex-1 resize-none text-sm text-on-surface bg-[#f8f9fb] border border-gray-200 rounded-2xl px-4 py-2.5 focus:outline-none focus:border-primary/40 focus:bg-white transition-all placeholder:text-gray-400"
                        style="max-height:100px; overflow-y:auto;"></textarea>
                    <button id="cyber-chat-send"
                        class="w-10 h-10 rounded-full bg-[#040f24] flex items-center justify-center flex-shrink-0 hover:bg-primary transition-colors shadow-md disabled:opacity-40 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber focus-visible:ring-offset-1"
                        title="Send" aria-label="Send message">
                        <span class="material-symbols-outlined text-alert-amber text-lg"
                            aria-hidden="true">send</span>
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 text-center mt-2">Cyber Dost · HRRL CISO Office · Powered by AI</p>
            </div>
        </div>

        <!-- FAB -->
        <button id="cyber-chat-toggle"
            class="w-14 h-14 rounded-full bg-[#040f24] shadow-xl hover:shadow-2xl flex items-center justify-center transition-all duration-300 hover:-translate-y-0.5 relative group focus:outline-none focus-visible:ring-2 focus-visible:ring-alert-amber focus-visible:ring-offset-2"
            title="Chat with Cyber Dost" aria-label="Chat with Cyber Dost, Cyber Safety Assistant">
            <span id="cyber-chat-fab-icon" class="material-symbols-outlined text-alert-amber text-2xl"
                aria-hidden="true">support_agent</span>
            <span id="cyber-chat-unread"
                class="hidden absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 border-2 border-white text-[10px] font-bold text-white flex items-center justify-center animate-pulse">●</span>
            <!-- Tooltip -->
            <span
                class="absolute right-16 bg-[#040f24] text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg">
                Chat with Cyber Dost
            </span>
        </button>
    </div>

    <script>
        (function() {
            const SEND_URL = '{{ route('cyber-chat.send') }}';
            const HISTORY_URL = '{{ route('cyber-chat.history') }}';
            const CLEAR_URL = '{{ route('cyber-chat.clear') }}';
            const CSRF = '{{ csrf_token() }}';

            const widget = document.getElementById('cyber-chat-widget');
            const panel = document.getElementById('cyber-chat-panel');
            const msgOuter = document.getElementById('cyber-chat-messages');
            const msgInner = document.getElementById('cyber-chat-messages-inner');
            const toggle = document.getElementById('cyber-chat-toggle');
            const closeBtn = document.getElementById('cyber-chat-close');
            const clearBtn = document.getElementById('cyber-chat-clear');
            const fsBtn = document.getElementById('cyber-chat-fullscreen');
            const fsIcon = document.getElementById('fs-icon');
            const sendBtn = document.getElementById('cyber-chat-send');
            const input = document.getElementById('cyber-chat-input');
            const fabIcon = document.getElementById('cyber-chat-fab-icon');
            const unreadBadge = document.getElementById('cyber-chat-unread');

            let isOpen = false;
            let isSending = false;
            let isFullscreen = false;

            /* ── open / close ── */
            function openChat() {
                panel.classList.remove('hidden');
                panel.classList.add('flex');
                fabIcon.textContent = 'close';
                isOpen = true;
                unreadBadge.classList.add('hidden');
                scrollBottom();
                input.focus();
            }

            function closeChat() {
                panel.classList.add('hidden');
                panel.classList.remove('flex');
                fabIcon.textContent = 'support_agent';
                isOpen = false;
                if (isFullscreen) exitFullscreen();
            }
            toggle.addEventListener('click', () => isOpen ? closeChat() : openChat());
            closeBtn.addEventListener('click', closeChat);

            /* ── fullscreen ── */
            function enterFullscreen() {
                isFullscreen = true;
                panel.classList.add('is-fullscreen');
                panel.style.width = '';
                panel.style.maxHeight = '';
                fsIcon.textContent = 'close_fullscreen';
                widget.style.position = 'static';
                scrollBottom();
            }

            function exitFullscreen() {
                isFullscreen = false;
                panel.classList.remove('is-fullscreen');
                panel.style.width = '380px';
                panel.style.maxHeight = '600px';
                fsIcon.textContent = 'open_in_full';
                widget.style.position = '';
                scrollBottom();
            }
            fsBtn.addEventListener('click', () => isFullscreen ? exitFullscreen() : enterFullscreen());

            /* ── scroll ── */
            function scrollBottom() {
                setTimeout(() => {
                    msgOuter.scrollTop = msgOuter.scrollHeight;
                }, 60);
            }

            /* ── linkify ── */
            function linkifyNode(node) {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    if (['A', 'CODE', 'PRE'].includes(node.tagName)) return;
                    Array.from(node.childNodes).forEach(linkifyNode);
                    return;
                }
                if (node.nodeType !== Node.TEXT_NODE) return;
                const raw = node.textContent;
                const pattern =
                    /(https?:\/\/[^\s<>"']+|(?:[a-zA-Z0-9-]+\.)+(?:gov\.in|com|org|in|net)(?:\/[^\s<>"']*)?|\b1930\b|\b[6-9]\d{9}\b)/g;
                const parts = raw.split(pattern);
                if (parts.length === 1) return;
                const frag = document.createDocumentFragment();
                parts.forEach((part, i) => {
                    if (i % 2 === 0) {
                        if (part) frag.appendChild(document.createTextNode(part));
                        return;
                    }
                    const a = document.createElement('a');
                    if (/^\d/.test(part)) {
                        a.href = 'tel:' + part.replace(/\D/g, '');
                    } else {
                        a.href = part.startsWith('http') ? part : 'https://' + part;
                        a.target = '_blank';
                        a.rel = 'noopener noreferrer';
                    }
                    a.textContent = part;
                    frag.appendChild(a);
                });
                node.parentNode.replaceChild(frag, node);
            }

            function renderAssistant(text) {
                const div = document.createElement('div');
                div.innerHTML = marked.parse(text, {
                    breaks: true,
                    gfm: true
                });
                linkifyNode(div);
                return div.innerHTML;
            }

            /* ── append message ── */
            function appendMessage(role, text) {
                const isUser = role === 'user';
                const wrapper = document.createElement('div');
                wrapper.className = `flex gap-2.5 items-end ${isUser ? 'flex-row-reverse' : ''}`;

                const avatar = document.createElement('div');
                avatar.className =
                    `w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 ${isUser ? 'bg-alert-amber/20' : 'bg-primary/10'}`;
                avatar.innerHTML =
                    `<span class="material-symbols-outlined text-sm ${isUser ? 'text-alert-amber' : 'text-primary'}">${isUser ? 'person' : 'support_agent'}</span>`;

                const bubble = document.createElement('div');
                if (isUser) {
                    bubble.className =
                        'rounded-2xl rounded-br-sm px-4 py-2.5 text-sm max-w-[82%] shadow-sm bg-[#040f24] text-white whitespace-pre-wrap break-words';
                    bubble.textContent = text;
                } else {
                    bubble.className =
                        'chat-md rounded-2xl rounded-bl-sm px-4 py-2.5 text-sm max-w-[82%] shadow-sm bg-white border border-gray-200 text-on-surface break-words';
                    bubble.innerHTML = renderAssistant(text);
                }

                wrapper.appendChild(avatar);
                wrapper.appendChild(bubble);
                msgInner.appendChild(wrapper);
                scrollBottom();
            }

            /* ── typing indicator ── */
            function appendTyping() {
                const el = document.createElement('div');
                el.id = 'cyber-typing';
                el.className = 'flex gap-2.5 items-end';
                el.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-sm">support_agent</span>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm flex gap-1 items-center">
                    <span class="w-2 h-2 rounded-full bg-gray-300 animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 rounded-full bg-gray-300 animate-bounce" style="animation-delay:160ms"></span>
                    <span class="w-2 h-2 rounded-full bg-gray-300 animate-bounce" style="animation-delay:320ms"></span>
                </div>`;
                msgInner.appendChild(el);
                scrollBottom();
            }

            function removeTyping() {
                const el = document.getElementById('cyber-typing');
                if (el) el.remove();
            }

            /* ── send ── */
            async function sendMessage() {
                const text = input.value.trim();
                if (!text || isSending) return;
                isSending = true;
                sendBtn.disabled = true;
                appendMessage('user', text);
                input.value = '';
                input.style.height = 'auto';
                appendTyping();
                try {
                    const res = await fetch(SEND_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            message: text
                        }),
                    });
                    const data = await res.json();
                    removeTyping();
                    appendMessage('assistant', data.error || data.message);
                    if (!isOpen) unreadBadge.classList.remove('hidden');
                } catch {
                    removeTyping();
                    appendMessage('assistant', 'Network error. Please check your connection and try again.');
                } finally {
                    isSending = false;
                    sendBtn.disabled = false;
                    input.focus();
                }
            }

            sendBtn.addEventListener('click', sendMessage);
            input.addEventListener('keydown', e => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            input.addEventListener('input', () => {
                input.style.height = 'auto';
                input.style.height = Math.min(input.scrollHeight, 100) + 'px';
            });

            /* ── clear ── */
            clearBtn.addEventListener('click', async () => {
                if (!confirm('Clear chat history?')) return;
                await fetch(CLEAR_URL, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    }
                });
                msgInner.innerHTML = '';
                appendMessage('assistant', 'Chat cleared. How can I help you?');
            });

            /* ── load history on page load ── */
            (async () => {
                try {
                    const res = await fetch(HISTORY_URL, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.history && data.history.length > 0) {
                        msgInner.innerHTML = '';
                        data.history.forEach(msg => appendMessage(msg.role, msg.content));
                        unreadBadge.classList.remove('hidden');
                    }
                } catch {}
            })();
        })();
    </script>
</body>

</html>
