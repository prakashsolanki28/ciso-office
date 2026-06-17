@extends('layouts.public')

@section('title', 'Blog — ' . config('app.name'))

@section('content')

    {{-- Hero --}}
    <section class="relative bg-primary text-white py-16 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('/images/earth.png')"></div>
        <div class="max-w-6xl mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl font-bold mb-3">
                @if ($featuredTag)
                    Posts tagged "{{ $featuredTag->name }}"
                @else
                    Blog
                @endif
            </h1>
            <p class="text-white/70 text-lg">Insights, guides, and updates from our team.</p>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="flex gap-10">

            {{-- Posts grid --}}
            <main class="flex-1 min-w-0">
                {{-- Search --}}
                <form method="GET" class="mb-8">
                    <div class="flex gap-2">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="tag" value="{{ request('tag') }}">
                        <input
                            type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search articles…"
                            class="flex-1 rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:opacity-90 transition">
                            Search
                        </button>
                        @if (request()->hasAny(['search', 'category', 'tag']))
                            <a href="{{ route('blog.public.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-800 border border-gray-200 rounded-xl">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>

                @if ($blogs->isEmpty())
                    <div class="text-center py-16">
                        <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-400 font-medium">No articles found</p>
                    </div>
                @else
                    <div class="grid gap-8">
                        @foreach ($blogs as $i => $blog)
                            @if ($i === 0 && $blogs->currentPage() === 1 && !request()->hasAny(['search', 'category', 'tag']))
                                {{-- Featured first post --}}
                                <article class="group rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition bg-white">
                                    @if ($blog->banner_url)
                                        <a href="{{ route('blog.public.show', $blog->slug) }}">
                                            <img src="{{ $blog->banner_url }}" alt="{{ $blog->title }}" class="w-full h-72 object-cover group-hover:scale-[1.01] transition-transform duration-300">
                                        </a>
                                    @endif
                                    <div class="p-7">
                                        <div class="flex items-center gap-3 mb-3 flex-wrap">
                                            @if ($blog->category)
                                                <a href="{{ route('blog.public.index', ['category' => $blog->category->slug]) }}"
                                                   class="text-xs font-semibold px-2.5 py-1 rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition">
                                                    {{ $blog->category->name }}
                                                </a>
                                            @endif
                                            <span class="text-xs text-gray-400">{{ $blog->read_time }} min read</span>
                                        </div>
                                        <h2 class="text-2xl font-bold text-gray-900 group-hover:text-primary transition mb-2">
                                            <a href="{{ route('blog.public.show', $blog->slug) }}">{{ $blog->title }}</a>
                                        </h2>
                                        @if ($blog->excerpt)
                                            <p class="text-gray-500 leading-relaxed mb-4">{{ $blog->excerpt }}</p>
                                        @endif
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary font-semibold text-sm border border-primary">
                                                    {{-- {{ substr($blog->author->name, 0, 1) }} --}}
                                                    <img src="{{ asset('images/logo.png') }}" alt="{{ $blog->author->name }}"
                                                        class="w-full h-full object-cover rounded-full">
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">{{ $blog->author->name }}</p>
                                                    <p class="text-xs text-gray-400">{{ $blog->published_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <a href="{{ route('blog.public.show', $blog->slug) }}"
                                               class="text-sm font-medium text-primary hover:underline">
                                                Read more →
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @else
                                {{-- Regular card --}}
                                <article class="group flex gap-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden p-5">
                                    @if ($blog->banner_url)
                                        <a href="{{ route('blog.public.show', $blog->slug) }}" class="shrink-0">
                                            <img src="{{ $blog->banner_url }}" alt="{{ $blog->title }}" class="w-36 h-28 object-cover rounded-xl group-hover:scale-[1.02] transition-transform duration-300">
                                        </a>
                                    @endif
                                    <div class="flex-1 min-w-0 flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                                @if ($blog->category)
                                                    <a href="{{ route('blog.public.index', ['category' => $blog->category->slug]) }}"
                                                       class="text-xs font-semibold px-2 py-0.5 rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition">
                                                        {{ $blog->category->name }}
                                                    </a>
                                                @endif
                                                <span class="text-xs text-gray-400">{{ $blog->read_time }} min read</span>
                                            </div>
                                            <h2 class="text-base font-bold text-gray-900 group-hover:text-primary transition line-clamp-2 mb-1">
                                                <a href="{{ route('blog.public.show', $blog->slug) }}">{{ $blog->title }}</a>
                                            </h2>
                                            @if ($blog->excerpt)
                                                <p class="text-sm text-gray-500 line-clamp-2">{{ $blog->excerpt }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 mt-3 text-xs text-gray-400">
                                            <span>{{ $blog->author->name }}</span>
                                            <span>·</span>
                                            <span>{{ $blog?->published_at?->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    </div>

                    @if ($blogs->hasPages())
                        <div class="mt-10">{{ $blogs->links() }}</div>
                    @endif
                @endif
            </main>

            {{-- Sidebar --}}
            <aside class="w-64 shrink-0 hidden lg:block space-y-8">
                {{-- Categories --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Categories</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('blog.public.index') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-sm hover:bg-white hover:shadow-sm transition {{ !request('category') ? 'bg-white shadow-sm font-semibold text-primary' : 'text-gray-600' }}">
                                All Posts
                            </a>
                        </li>
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ route('blog.public.index', ['category' => $cat->slug]) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-sm hover:bg-white hover:shadow-sm transition {{ request('category') === $cat->slug ? 'bg-white shadow-sm font-semibold text-primary' : 'text-gray-600' }}">
                                    {{ $cat->name }}
                                    <span class="text-xs text-gray-400">{{ $cat->blogs_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>

@endsection
