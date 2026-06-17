<?php

namespace Modules\CaseStudy\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\CaseStudy\Models\CaseStudy;
use Modules\CaseStudy\Http\Requests\StoreCaseStudyRequest;
use Modules\CaseStudy\Http\Requests\UpdateCaseStudyRequest;

class CaseStudyController extends Controller
{
    public function index(Request $request)
    {
        $caseStudies = CaseStudy::query()
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('casestudy::index', compact('caseStudies'));
    }

    public function store(StoreCaseStudyRequest $request)
    {
        $caseStudy = CaseStudy::create([
            'title'        => $request->title,
            'slug'         => $request->slug ?: CaseStudy::uniqueSlug($request->title),
            'status'       => $request->status ?? 'draft',
            'published_at' => $request->published_at ?: null,
        ]);

        return redirect()->route('casestudy.edit', $caseStudy)
            ->with('success', 'Case study created! Fill in the details and save when ready.');
    }

    public function edit(CaseStudy $casestudy)
    {
        return view('casestudy::edit', ['caseStudy' => $casestudy]);
    }

    public function update(UpdateCaseStudyRequest $request, CaseStudy $casestudy)
    {
        $data = $request->validated();
        $data['published_at'] = $request->published_at ?: null;

        if ($request->hasFile('image')) {
            if ($casestudy->image) {
                Storage::disk('public')->delete($casestudy->image);
            }
            $data['image'] = $request->file('image')->store('case-studies/images', 'public');
        } else {
            unset($data['image']);
        }

        $casestudy->update($data);

        return back()->with('success', 'Case study saved successfully.');
    }

    public function destroy(CaseStudy $casestudy)
    {
        if ($casestudy->image) {
            Storage::disk('public')->delete($casestudy->image);
        }

        $casestudy->delete();

        return redirect()->route('casestudy.index')
            ->with('success', 'Case study deleted successfully.');
    }

    public function uploadBanner(Request $request, CaseStudy $casestudy)
    {
        $request->validate(['image' => 'required|image|max:5120']);

        if ($casestudy->image) {
            Storage::disk('public')->delete($casestudy->image);
        }

        $path = $request->file('image')->store('case-studies/images', 'public');
        $casestudy->update(['image' => $path]);

        return response()->json(['url' => asset('storage/' . $path), 'path' => $path]);
    }
}
