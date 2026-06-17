<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Tag;
use Modules\Blog\Http\Requests\StoreBlogRequest;
use Modules\Blog\Http\Requests\UpdateBlogRequest;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogs = Blog::with(['author', 'category', 'tags'])
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->category, fn($q, $c) => $q->where('category_id', $c))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('blog::index', compact('blogs', 'categories'));
    }

    public function store(StoreBlogRequest $request)
    {
        $blog = Blog::create([
            'user_id'      => Auth::id(),
            'category_id'  => $request->category_id ?: null,
            'title'        => $request->title,
            'slug'         => $request->slug ?: Blog::uniqueSlug($request->title),
            'status'       => $request->status ?? 'draft',
            'published_at' => $request->status === 'scheduled' ? $request->published_at : null,
        ]);

        return redirect()->route('blog.edit', $blog)
            ->with('success', 'Blog created! Fill in the content and publish when ready.');
    }

    public function edit(Blog $blog)
    {
        $blog->load(['category', 'tags']);
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();

        return view('blog::edit', compact('blog', 'categories', 'tags'));
    }

    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $data = $request->validated();

        if ($request->hasFile('banner')) {
            if ($blog->banner) {
                Storage::disk('public')->delete($blog->banner);
            }
            $data['banner'] = $request->file('banner')->store('blogs/banners', 'public');
        } else {
            unset($data['banner']);
        }

        if ($data['status'] === 'published' && ! $blog->published_at) {
            $data['published_at'] = now();
        } elseif ($data['status'] === 'draft') {
            $data['published_at'] = null;
        }

        $blog->update($data);

        if ($request->has('tags')) {
            $tagIds = collect($request->tags)->map(function ($tagName) {
                return Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                )->id;
            });
            $blog->tags()->sync($tagIds);
        } else {
            $blog->tags()->detach();
        }

        return back()->with('success', 'Blog saved successfully.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->banner) {
            Storage::disk('public')->delete($blog->banner);
        }
        $blog->delete();

        return redirect()->route('blog.index')
            ->with('success', 'Blog deleted successfully.');
    }

    public function uploadBanner(Request $request, Blog $blog)
    {
        $request->validate(['banner' => 'required|image|max:5120']);

        if ($blog->banner) {
            Storage::disk('public')->delete($blog->banner);
        }

        $path = $request->file('banner')->store('blogs/banners', 'public');
        $blog->update(['banner' => $path]);

        return response()->json(['url' => asset('storage/' . $path), 'path' => $path]);
    }
}
