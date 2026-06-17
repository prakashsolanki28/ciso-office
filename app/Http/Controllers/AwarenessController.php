<?php

namespace App\Http\Controllers;

use Modules\Blog\Models\Blog;
use Modules\CaseStudy\Models\CaseStudy;
use Modules\Newsletter\Models\Newsletter;

class AwarenessController extends Controller
{
    public function index()
    {
        $blogs = Blog::with(['author', 'category'])
            ->published()
            ->latest('published_at')
            ->latest()
            ->take(6)
            ->get();

        $caseStudies = CaseStudy::published()
            ->latest('published_at')
            ->latest()
            ->take(6)
            ->get();

        $newsletters = Newsletter::published()
            ->latest('published_at')
            ->latest()
            ->take(6)
            ->get();

        return view('awareness', compact('blogs', 'caseStudies', 'newsletters'));
    }
}
