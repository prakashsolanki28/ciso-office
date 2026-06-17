<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use Illuminate\Http\Request;

class IncidentReportController extends Controller
{
    public function create()
    {
        return view('report-incident');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Name: letters, spaces and . ' - only — no digits allowed.
            'full_name'       => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'-]+$/u'],
            'employee_id'     => ['nullable', 'string', 'max:100'],
            // Work email must be a valid address on the @hrrl.in domain (case-insensitive).
            'email'           => ['required', 'email:rfc', 'max:255', 'regex:/@hrrl\.in$/i'],
            // Indian mobile: 10 digits starting 6-9, optional +91 / 91 / 0 prefix.
            'mobile'          => ['required', 'string', 'regex:/^(?:\+91[\s-]?|0)?[6-9]\d{9}$/'],
            'department'      => ['nullable', 'string', 'max:255'],
            'incident_date'   => ['required', 'date', 'before_or_equal:today'],
            'incident_time'   => ['nullable', 'date_format:H:i'],
            'incident_type'   => ['required', 'string', 'max:255'],
            'assets_affected' => ['nullable', 'string', 'max:500'],
            'severity'        => ['required', 'in:critical,high,medium,low'],
            'description'     => ['required', 'string', 'min:20'],
            'actions_taken'   => ['nullable', 'string'],
            'attachments.*'   => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx,txt,csv'],
            'consent'         => ['accepted'],
        ], [
            'full_name.regex' => 'The full name may only contain letters, spaces and the characters . \' - (no numbers).',
            'email.regex'     => 'The work email must be a valid @hrrl.in address.',
            'mobile.required' => 'Please provide a contact mobile number.',
            'mobile.regex'    => 'Enter a valid 10-digit Indian mobile number (starting 6-9), optionally prefixed with +91.',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store('incident-attachments', 'local');
            }
        }

        IncidentReport::create([
            'full_name'       => $validated['full_name'],
            'employee_id'     => $validated['employee_id'] ?? null,
            'email'           => $validated['email'],
            'mobile'          => $validated['mobile'],
            'department'      => $validated['department'] ?? null,
            'incident_date'   => $validated['incident_date'],
            'incident_time'   => $validated['incident_time'] ?? null,
            'incident_type'   => $validated['incident_type'],
            'assets_affected' => $validated['assets_affected'] ?? null,
            'severity'        => $validated['severity'],
            'description'     => $validated['description'],
            'actions_taken'   => $validated['actions_taken'] ?? null,
            'attachments'     => $attachmentPaths ?: null,
        ]);

        return redirect()->route('report.incident')
            ->with('success', 'Your incident report has been submitted. The HRRL CISO team will contact you shortly.');
    }
}
