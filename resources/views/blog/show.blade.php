@extends('layouts.public')

@section('title', ($blog->meta_title ?: $blog->title) . ' — ' . config('app.name'))

@push('styles')
    <meta name="description" content="{{ $blog->meta_description ?: $blog->excerpt }}">
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $blog->meta_title ?: $blog->title }}">
    <meta property="og:description" content="{{ $blog->meta_description ?: $blog->excerpt }}">
    @if ($blog->banner_url)
        <meta property="og:image" content="{{ $blog->banner_url }}">
    @endif
@endpush

@section('content')

    <article class="max-w-5xl mx-auto px-6 py-12">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
            <a href="{{ route('blog.public.index') }}" class="hover:text-primary">Blog</a>
            @if ($blog->category)
                <span>/</span>
                <a href="{{ route('blog.public.index', ['category' => $blog->category->slug]) }}"
                    class="hover:text-primary">
                    {{ $blog->category->name }}
                </a>
            @endif
            <span>/</span>
            <span class="text-gray-600 line-clamp-1">{{ $blog->title }}</span>
        </nav>

        {{-- Category & Meta --}}
        <div class="flex items-center gap-3 flex-wrap mb-5">
            @if ($blog->category)
                <a href="{{ route('blog.public.index', ['category' => $blog->category->slug]) }}"
                    class="text-xs font-bold px-3 py-1 rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition uppercase tracking-wider">
                    {{ $blog->category->name }}
                </a>
            @endif
            <span class="text-xs text-gray-400">{{ $blog->read_time }} min read</span>
            <span class="text-xs text-gray-400">{{ $blog->published_at->format('F j, Y') }}</span>
        </div>

        {{-- Title --}}
        <h1 class="text-4xl font-bold text-gray-900 leading-tight mb-6">
            {{ $blog->title }}
        </h1>

        {{-- Author --}}
        <div class="flex items-center gap-3 mb-8 pb-8 border-b border-gray-100">
            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">
                {{-- {{ substr($blog->author->name, 0, 1) }} --}}
                <img src="{{ asset('images/logo.png') }}" alt="{{ $blog->author->name }}"
                    class="w-full h-full object-cover rounded-full">
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">{{ $blog->author->name }}</p>
                <p class="text-xs text-gray-400">Published {{ $blog->published_at->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- Banner --}}
        @if ($blog->banner_url)
            <div class="mb-10 rounded-2xl overflow-hidden shadow-sm">
                <img src="{{ $blog->banner_url }}" alt="{{ $blog->title }}" class="w-full object-cover max-h-[480px]">
            </div>
        @endif

        {{-- Excerpt --}}
        @if ($blog->excerpt)
            <p class="text-lg text-gray-500 leading-relaxed mb-8 border-l-4 border-primary pl-4 italic">
                {{ $blog->excerpt }}
            </p>
        @endif

        {{-- Content --}}
        <div class="blog-content">
            {!! $blog->content !!}
        </div>

        {{-- Tags --}}
        @if ($blog->tags->isNotEmpty())
            <div class="mt-10 pt-8 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($blog->tags as $tag)
                        <a href="{{ route('blog.public.index', ['tag' => $tag->slug]) }}"
                            class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-600 hover:bg-primary hover:text-white transition">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Author Bio --}}
        <div class="mt-10 p-6 bg-gray-50 rounded-2xl flex items-start gap-4">
            <div
                class="w-14 h-14 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-xl shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="{{ $blog->author->name }}"
                    class="w-full h-full object-cover rounded-full">
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">{{ $blog->author->name }}</p>
                <p class="text-sm text-gray-500">Author at {{ config('app.name') }}</p>
            </div>
        </div>

    </article>

    {{-- Related Posts --}}
    @if ($related->isNotEmpty())
        <section class="bg-white border-t border-gray-100 py-14">
            <div class="max-w-6xl mx-auto px-6">
                <h2 class="text-xl font-bold text-gray-900 mb-8">Related Articles</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($related as $post)
                        <article
                            class="group bg-surface rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition">
                            @if ($post->banner_url)
                                <a href="{{ route('blog.public.show', $post->slug) }}">
                                    <img src="{{ $post->banner_url }}" alt="{{ $post->title }}"
                                        class="w-full h-40 object-cover group-hover:scale-[1.02] transition-transform duration-300">
                                </a>
                            @endif
                            <div class="p-5">
                                @if ($post->category)
                                    <span class="text-xs font-semibold text-primary">{{ $post->category->name }}</span>
                                @endif
                                <h3
                                    class="text-sm font-bold text-gray-900 group-hover:text-primary transition mt-1 mb-2 line-clamp-2">
                                    <a href="{{ route('blog.public.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                <p class="text-xs text-gray-400">{{ $post->published_at->format('M d, Y') }} ·
                                    {{ $post->read_time }} min read</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
