<?php

namespace App\Http\Controllers\CaseStudy;

use App\Http\Controllers\Controller;
use Modules\CaseStudy\Models\CaseStudy;

class PublicCaseStudyController extends Controller
{
    public function index()
    {
        $caseStudies = CaseStudy::published()
            ->latest('published_at')
            ->latest()
            ->get();

        return view('case-studies.index', compact('caseStudies'));
    }

    public function show(string $slug)
    {
        $caseStudy = CaseStudy::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('case-studies.show', compact('caseStudy'));
    }
}
