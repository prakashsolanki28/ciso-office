<?php

namespace Modules\Newsletter\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Http\Requests\StoreNewsletterRequest;
use Modules\Newsletter\Http\Requests\UpdateNewsletterRequest;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $newsletters = Newsletter::query()
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('newsletter::index', compact('newsletters'));
    }

    public function store(StoreNewsletterRequest $request)
    {
        $data = $request->validated();
        $data['published_at'] = $request->published_at ?: null;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('newsletters/images', 'public');
        }

        Newsletter::create($data);

        return redirect()->route('newsletter.index')
            ->with('success', 'Newsletter created successfully.');
    }

    public function update(UpdateNewsletterRequest $request, Newsletter $newsletter)
    {
        $data = $request->validated();
        $data['published_at'] = $request->published_at ?: null;

        if ($request->hasFile('image')) {
            if ($newsletter->image) {
                Storage::disk('public')->delete($newsletter->image);
            }
            $data['image'] = $request->file('image')->store('newsletters/images', 'public');
        } else {
            unset($data['image']);
        }

        $newsletter->update($data);

        return redirect()->route('newsletter.index')
            ->with('success', 'Newsletter updated successfully.');
    }

    public function destroy(Newsletter $newsletter)
    {
        if ($newsletter->image) {
            Storage::disk('public')->delete($newsletter->image);
        }

        $newsletter->delete();

        return redirect()->route('newsletter.index')
            ->with('success', 'Newsletter deleted successfully.');
    }
}
