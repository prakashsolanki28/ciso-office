<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Blog\Models\Tag;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::when($request->q, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($tags);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);

        $tag = Tag::firstOrCreate(
            ['slug' => Str::slug($request->name)],
            ['name' => $request->name]
        );

        return response()->json($tag, 201);
    }
}
