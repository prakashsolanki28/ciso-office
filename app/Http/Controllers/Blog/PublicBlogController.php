<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Tag;

class PublicBlogController extends Controller
{
    public function index(Request $request)
    {
        $blogs = Blog::with(['author', 'category', 'tags'])
            ->published()
            ->when($request->category, fn($q, $c) => $q->whereHas('category', fn($q) => $q->where('slug', $c)))
            ->when($request->tag, fn($q, $t) => $q->whereHas('tags', fn($q) => $q->where('slug', $t)))
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories  = Category::withCount(['blogs' => fn($q) => $q->published()])->get();
        $featuredTag = $request->tag ? Tag::where('slug', $request->tag)->first() : null;

        return view('blog.index', compact('blogs', 'categories', 'featuredTag'));
    }

    public function show(string $slug)
    {
        $blog = Blog::with(['author', 'category', 'tags'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Blog::with(['author', 'category'])
            ->published()
            ->where('id', '!=', $blog->id)
            ->where(function ($q) use ($blog) {
                $q->where('category_id', $blog->category_id)
                  ->orWhereHas('tags', fn($q) => $q->whereIn('tags.id', $blog->tags->pluck('id')));
            })
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('blog', 'related'));
    }
}
