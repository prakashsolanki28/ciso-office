<?php

namespace Modules\SOP\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\SOP\Models\Sop;
use Modules\SOP\Http\Requests\StoreSopRequest;
use Modules\SOP\Http\Requests\UpdateSopRequest;

class SOPController extends Controller
{
    public function index(Request $request)
    {
        $sops = Sop::query()
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('sop::index', compact('sops'));
    }

    public function store(StoreSopRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('sops/documents', 'public');

        Sop::create([
            'title'       => $request->title,
            'description' => $request->description,
            'icon'        => $request->icon,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $file->getSize(),
            'is_public'   => $request->boolean('is_public'),
        ]);

        return redirect()->route('sop.index')
            ->with('success', 'SOP created successfully.');
    }

    public function update(UpdateSopRequest $request, Sop $sop)
    {
        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'icon'        => $request->icon,
            'is_public'   => $request->boolean('is_public'),
        ];

        if ($request->hasFile('file')) {
            if ($sop->file_path) {
                Storage::disk('public')->delete($sop->file_path);
            }

            $file = $request->file('file');
            $data['file_path'] = $file->store('sops/documents', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        $sop->update($data);

        return redirect()->route('sop.index')
            ->with('success', 'SOP updated successfully.');
    }

    public function destroy(Sop $sop)
    {
        if ($sop->file_path) {
            Storage::disk('public')->delete($sop->file_path);
        }

        $sop->delete();

        return redirect()->route('sop.index')
            ->with('success', 'SOP deleted successfully.');
    }

    public function download(Sop $sop)
    {
        abort_unless($sop->file_path && Storage::disk('public')->exists($sop->file_path), 404);

        return Storage::disk('public')->download($sop->file_path, $sop->file_name);
    }
}
