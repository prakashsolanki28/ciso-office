<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use Illuminate\Http\Request;

class IncidentReportController extends Controller
{
    public function index(Request $request)
    {
        $query = IncidentReport::latest();

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('incident_type', 'like', "%{$search}%");
            });
        }

        $incidents = $query->paginate(20)->withQueryString();

        return view('admin.incidents.index', compact('incidents'));
    }

    public function show(IncidentReport $incident)
    {
        return view('admin.incidents.show', compact('incident'));
    }

    public function updateStatus(Request $request, IncidentReport $incident)
    {
        $validated = $request->validate([
            'status'     => ['required', 'in:new,in_review,investigating,resolved,closed'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $incident->update([
            'status'     => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        return back()->with('success', 'Status updated successfully.');
    }
}
