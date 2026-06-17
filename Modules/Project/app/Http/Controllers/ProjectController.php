<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Project\Models\Project;
use Modules\Project\Http\Requests\StoreProjectRequest;
use Modules\Project\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('project::index', compact('projects'));
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            'name'              => $request->name,
            'slug'              => $request->slug ?: Project::uniqueSlug($request->name),
            'short_description' => $request->short_description,
        ]);

        return redirect()->route('project.edit', $project)
            ->with('success', 'Project created! Fill in the details and save when ready.');
    }

    public function edit(Project $project)
    {
        return view('project::edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $specs = $this->decodeJsonField($request->specifications, fn($s) => [
            'icon'        => trim($s['icon'] ?? ''),
            'title'       => trim($s['title'] ?? ''),
            'description' => trim($s['description'] ?? ''),
        ], 'title');

        $stats = $this->decodeJsonField($request->statistics, fn($s) => [
            'icon'  => trim($s['icon'] ?? ''),
            'key'   => trim($s['key'] ?? ''),
            'value' => trim($s['value'] ?? ''),
        ], 'key');

        $before = $this->decodeJsonField($request->before_points, fn($s) => [
            'text' => trim($s['text'] ?? ''),
        ], 'text');

        $after = $this->decodeJsonField($request->after_points, fn($s) => [
            'text' => trim($s['text'] ?? ''),
        ], 'text');

        $accounts = null;
        if ($request->filled('onboard_accounts')) {
            $decoded = json_decode($request->onboard_accounts, true);
            if (is_array($decoded)) {
                $accounts = array_values(array_filter(
                    array_map(fn($a) => [
                        'name'        => trim($a['name'] ?? ''),
                        'description' => trim($a['description'] ?? ''),
                        'logo'        => trim($a['logo'] ?? ''),
                    ], $decoded),
                    fn($a) => $a['name'] !== ''
                ));
            }
        }

        // Charts are nested (chart → data points), so decode them dedicatedly
        // rather than via decodeJsonField (which is single-level).
        $charts = null;
        if ($request->filled('charts')) {
            $decoded = json_decode($request->charts, true);
            if (is_array($decoded)) {
                $allowedTypes = ['bar', 'line', 'area', 'radar', 'pie'];
                $charts = array_values(array_filter(
                    array_map(function ($c) use ($allowedTypes) {
                        $type = $c['type'] ?? 'bar';

                        // Keep only points with a label and a numeric value (check the
                        // raw value before casting, so non-numeric points are dropped).
                        $points = [];
                        foreach (is_array($c['points'] ?? null) ? $c['points'] : [] as $p) {
                            $label = trim($p['label'] ?? '');
                            $raw   = $p['value'] ?? null;
                            if ($label === '' || ! is_numeric($raw)) {
                                continue;
                            }
                            // Keep the color only if it's a valid 6-digit hex; otherwise
                            // leave it null so the public page falls back to the palette.
                            $rawColor = $p['color'] ?? null;
                            $color    = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $rawColor) ? $rawColor : null;
                            $points[] = ['label' => $label, 'value' => (float) $raw, 'color' => $color];
                        }

                        return [
                            'title'  => trim($c['title'] ?? ''),
                            'type'   => in_array($type, $allowedTypes, true) ? $type : 'bar',
                            'points' => $points,
                        ];
                    }, $decoded),
                    fn($c) => $c['title'] !== '' && count($c['points']) > 0
                ));
            }
        }

        // Section order — keep only known section keys, dedupe, then append any
        // known keys missing from the submitted order so every section always
        // has a place. Falls back to null (= model default order) when absent.
        $sectionOrder = null;
        if ($request->filled('section_order')) {
            $decoded = json_decode($request->section_order, true);
            if (is_array($decoded)) {
                $known = array_keys(Project::SECTIONS);
                $clean = array_values(array_unique(array_filter(
                    $decoded,
                    fn($k) => in_array($k, $known, true)
                )));
                $sectionOrder = array_values(array_unique([...$clean, ...$known]));
            }
        }

        $data = [
            'name'              => $request->name,
            'slug'              => $request->slug ?: $project->slug,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'specifications'    => $specs,
            'statistics'        => $stats,
            'before_points'     => $before,
            'after_points'      => $after,
            'onboard_accounts'  => $accounts,
            'gallery'           => $this->decodeJsonField($request->gallery, fn($g) => [
                'image'   => trim($g['image'] ?? ''),
                'caption' => trim($g['caption'] ?? ''),
            ], 'image'),
            'charts'            => $charts,
            'section_order'     => $sectionOrder,
        ];

        if ($request->hasFile('banner')) {
            if ($project->banner) {
                Storage::disk('public')->delete($project->banner);
            }
            $data['banner'] = $request->file('banner')->store('projects/banners', 'public');
        }

        $project->update($data);

        return back()->with('success', 'Project saved successfully.');
    }

    public function uploadBanner(Request $request, Project $project)
    {
        $request->validate(['banner' => 'required|image|max:5120']);

        if ($project->banner) {
            Storage::disk('public')->delete($project->banner);
        }

        $path = $request->file('banner')->store('projects/banners', 'public');
        $project->update(['banner' => $path]);

        return response()->json(['url' => asset('storage/' . $path), 'path' => $path]);
    }

    public function uploadGalleryImage(Request $request, Project $project)
    {
        $request->validate(['image' => 'required|image|max:5120']);

        $path = $request->file('image')->store('projects/gallery', 'public');

        return response()->json([
            'url'  => asset('storage/' . $path),
            'path' => $path,
        ]);
    }

    public function uploadAccountLogo(Request $request, Project $project)
    {
        $request->validate(['logo' => 'required|image|max:2048']);

        $path = $request->file('logo')->store('projects/accounts', 'public');

        return response()->json([
            'url'  => asset('storage/' . $path),
            'path' => $path,
        ]);
    }

    private function decodeJsonField(?string $raw, callable $mapper, string $requiredKey): ?array
    {
        if (! $raw) return null;
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) return null;
        $rows = array_values(array_filter(
            array_map($mapper, $decoded),
            fn($row) => ($row[$requiredKey] ?? '') !== ''
        ));
        return $rows ?: null;
    }

    public function destroy(Project $project)
    {
        if ($project->banner) {
            Storage::disk('public')->delete($project->banner);
        }

        $project->delete();

        return redirect()->route('project.index')
            ->with('success', 'Project deleted successfully.');
    }
}
