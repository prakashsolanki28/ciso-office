<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Modules\Project\Models\Project;

class PublicProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();

        return view('projects.index', compact('projects'));
    }

    public function show(string $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        return view('projects.show', compact('project'));
    }
}
