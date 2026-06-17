@extends('layouts.public')

@section('title', 'Report a Cyber Incident — HRRL CISO Office')

@section('content')

    <!-- PAGE HEADER -->
    <section class="relative bg-[#040f24] py-16 overflow-hidden border-b border-white/5">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-primary-container/30 via-[#040f24] to-[#040f24]"></div>
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: linear-gradient(rgba(255,255,255,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.5) 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="max-w-container-max mx-auto px-margin-edge relative z-10">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-white/40 text-xs font-label-md mb-6">
                <a href="{{ url('/') }}" class="hover:text-white/70 transition-colors">Home</a>
                <span class="material-symbols-outlined text-sm">chevron_right</span>
                <span class="text-white/60">Report Incident</span>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-px w-8 bg-alert-amber"></div>
                        <span class="text-alert-amber text-xs font-semibold tracking-widest uppercase font-label-md">HRRL CISO Office</span>
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-white tracking-tight">Report a Cyber Incident</h1>
                    <p class="text-white/50 mt-2 font-light max-w-xl">Complete this form as accurately as possible. All submissions are confidential and handled by the HRRL Security Operations Center.</p>
                </div>

                <!-- Contact callout -->
                <a href="mailto:ciso_office@hrrl.in"
                    class="flex-shrink-0 bg-alert-amber/10 border border-alert-amber/30 rounded-2xl px-5 py-4 flex items-center gap-4 hover:bg-alert-amber/15 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-alert-amber/20 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-alert-amber text-xl">mail</span>
                    </div>
                    <div>
                        <p class="text-white/50 text-xs font-label-md uppercase tracking-wider">Email CISO Office</p>
                        <p class="text-alert-amber font-bold text-lg tracking-wide">ciso_office@hrrl.in</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="py-16 bg-gray-50 min-h-screen">
        <div class="max-w-container-max mx-auto px-margin-edge">
            <div class="flex flex-col xl:flex-row gap-10">

                <!-- FORM -->
                <div class="flex-1">

                    {{-- Success banner --}}
                    @if(session('success'))
                    <div class="mb-6 flex items-start gap-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-6 py-5">
                        <span class="material-symbols-outlined text-green-500 text-xl flex-shrink-0 mt-0.5">check_circle</span>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                    @endif

                    {{-- Validation errors --}}
                    @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl px-6 py-5">
                        <p class="text-sm font-semibold text-red-700 mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                            <li class="text-sm text-red-600">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('report.incident.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- SECTION 1: Reporter Info -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="flex items-center gap-3 px-7 py-5 border-b border-gray-100 bg-gray-50/60">
                                <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary text-base">person</span>
                                </div>
                                <h2 class="font-semibold text-gray-800 text-sm tracking-tight">Reporter Information</h2>
                            </div>
                            <div class="p-7 grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="full_name" required value="{{ old('full_name') }}" placeholder="e.g. John Smith"
                                        pattern="[A-Za-z\s.'\-]+" title="Letters, spaces and . ' - only — no numbers."
                                        maxlength="255"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white">
                                    @error('full_name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Employee ID <span class="text-red-500">*</span></label>
                                    <input type="text" name="employee_id" required value="{{ old('employee_id') }}" placeholder="e.g. HRRL-00421"
                                        maxlength="100"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white">
                                    @error('employee_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Work Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="you@hrrl.in"
                                        pattern="[^@\s]+@(?:[Hh][Rr][Rr][Ll])\.(?:[Ii][Nn])" title="Use your @hrrl.in work email address."
                                        maxlength="255" inputmode="email"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white">
                                    @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@else<p class="mt-1.5 text-xs text-gray-400">Only @hrrl.in addresses are accepted.</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Mobile Number <span class="text-red-500">*</span></label>
                                    <input type="tel" name="mobile" required value="{{ old('mobile') }}" placeholder="e.g. 9876543210"
                                        pattern="(\+91[\s\-]?|0)?[6-9][0-9]{9}" title="Enter a valid 10-digit Indian mobile number (starting 6-9), optionally prefixed with +91."
                                        inputmode="tel" maxlength="14"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white">
                                    @error('mobile')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@else<p class="mt-1.5 text-xs text-gray-400">Indian mobile number only (e.g. +91 9876543210).</p>@enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Department / Division <span class="text-red-500">*</span></label>
                                    <select name="department" required
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white appearance-none">
                                        <option value="" disabled {{ old('department') ? '' : 'selected' }}>Select department</option>
                                        @foreach (['Engineering','Operations','Finance','HR','IT','Legal & Compliance','Procurement','Executive','Other'] as $dept)
                                        <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                        @endforeach
                                    </select>
                                    @error('department')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: Incident Details -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="flex items-center gap-3 px-7 py-5 border-b border-gray-100 bg-gray-50/60">
                                <div class="w-7 h-7 rounded-lg bg-alert-amber/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-alert-amber text-base">crisis_alert</span>
                                </div>
                                <h2 class="font-semibold text-gray-800 text-sm tracking-tight">Incident Details</h2>
                            </div>
                            <div class="p-7 grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Date of Incident <span class="text-red-500">*</span></label>
                                    <input type="date" name="incident_date" required value="{{ old('incident_date') }}" max="{{ now()->toDateString() }}"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white">
                                    @error('incident_date')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Approximate Time</label>
                                    <input type="time" name="incident_time" value="{{ old('incident_time') }}"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white">
                                    @error('incident_time')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Incident Type <span class="text-red-500">*</span></label>
                                    <select name="incident_type" required
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white appearance-none">
                                        <option value="" disabled {{ old('incident_type') ? '' : 'selected' }}>Select type</option>
                                        @foreach (['Phishing / Spear Phishing','Ransomware','Data Breach / Leak','Unauthorized Access','Malware / Virus','Social Engineering','Denial of Service (DoS/DDoS)','Insider Threat','Lost / Stolen Device','Suspicious Email','Other'] as $type)
                                        <option value="{{ $type }}" {{ old('incident_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('incident_type')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Systems / Assets Affected</label>
                                    <input type="text" name="assets_affected" value="{{ old('assets_affected') }}" placeholder="e.g. Workstation PC-4412, SAP Server"
                                        maxlength="500"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white">
                                    @error('assets_affected')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-3 uppercase tracking-wider">Severity Level <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        @foreach ([
                                            ['value' => 'critical', 'label' => 'Critical', 'desc' => 'Immediate response', 'color' => 'red', 'icon' => 'emergency'],
                                            ['value' => 'high', 'label' => 'High', 'desc' => 'Significant impact', 'color' => 'orange', 'icon' => 'warning'],
                                            ['value' => 'medium', 'label' => 'Medium', 'desc' => 'Moderate impact', 'color' => 'yellow', 'icon' => 'info'],
                                            ['value' => 'low', 'label' => 'Low', 'desc' => 'Minor impact', 'color' => 'green', 'icon' => 'check_circle'],
                                        ] as $sev)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="severity" value="{{ $sev['value'] }}" class="peer sr-only" {{ old('severity', 'high') === $sev['value'] ? 'checked' : '' }}>
                                            <div class="border-2 border-gray-200 rounded-xl p-3.5 text-center peer-checked:border-alert-amber peer-checked:bg-alert-amber/5 transition-all hover:border-gray-300">
                                                <span class="material-symbols-outlined text-2xl
                                                    @if($sev['color'] === 'red') text-red-500
                                                    @elseif($sev['color'] === 'orange') text-orange-500
                                                    @elseif($sev['color'] === 'yellow') text-yellow-500
                                                    @else text-green-500 @endif
                                                ">{{ $sev['icon'] }}</span>
                                                <p class="font-semibold text-gray-800 text-sm mt-1">{{ $sev['label'] }}</p>
                                                <p class="text-gray-400 text-xs mt-0.5">{{ $sev['desc'] }}</p>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: Description -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="flex items-center gap-3 px-7 py-5 border-b border-gray-100 bg-gray-50/60">
                                <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary text-base">description</span>
                                </div>
                                <h2 class="font-semibold text-gray-800 text-sm tracking-tight">Incident Description</h2>
                            </div>
                            <div class="p-7 space-y-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">What happened? <span class="text-red-500">*</span></label>
                                    <textarea name="description" required rows="5" minlength="20" placeholder="Describe the incident in as much detail as possible — what you saw, what you clicked, what changed, etc."
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white resize-none">{{ old('description') }}</textarea>
                                    @error('description')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@else<p class="mt-1.5 text-xs text-gray-400">Minimum 20 characters.</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Actions Taken So Far</label>
                                    <textarea name="actions_taken" rows="3" placeholder="e.g. Disconnected from network, changed password, did not open any attachments…"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-alert-amber/30 focus:border-alert-amber transition-all bg-white resize-none">{{ old('actions_taken') }}</textarea>
                                    @error('actions_taken')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 4: Evidence Upload -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="flex items-center gap-3 px-7 py-5 border-b border-gray-100 bg-gray-50/60">
                                <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary text-base">attach_file</span>
                                </div>
                                <h2 class="font-semibold text-gray-800 text-sm tracking-tight">Evidence & Attachments <span class="text-gray-400 font-normal">(Optional)</span></h2>
                            </div>
                            <div class="p-7">
                                <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-alert-amber/50 hover:bg-alert-amber/[0.02] transition-all group">
                                    <span class="material-symbols-outlined text-4xl text-gray-300 group-hover:text-alert-amber/60 transition-colors">cloud_upload</span>
                                    <p class="text-sm text-gray-400 mt-2">Drag & drop files, or <span class="text-alert-amber font-semibold">browse</span></p>
                                    <p class="text-xs text-gray-300 mt-1">Screenshots, logs, emails — max 10 MB per file</p>
                                    <input type="file" name="attachments[]" multiple accept=".png,.jpg,.jpeg,.pdf,.txt,.eml,.zip" class="hidden">
                                </label>
                            </div>
                        </div>

                        <!-- CONSENT + SUBMIT -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-7 space-y-6">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" name="consent" value="1" required {{ old('consent') ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-gray-300 text-alert-amber focus:ring-alert-amber/30 flex-shrink-0">
                                <span class="text-sm text-gray-500 font-light leading-relaxed">
                                    I confirm that the information provided is accurate to the best of my knowledge. I understand this report will be handled confidentially by the HRRL Security Operations Center under the <span class="text-primary font-medium">Incident Response Policy v3.2</span>.
                                </span>
                            </label>

                            <div class="flex flex-wrap items-center gap-4 pt-2 border-t border-gray-100">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-alert-amber text-white font-bold font-label-md px-10 py-4 rounded-full hover:shadow-[0_0_28px_rgba(230,147,10,0.45)] hover:-translate-y-0.5 transition-all duration-300">
                                    <span class="material-symbols-outlined text-base">send</span>
                                    Submit Incident Report
                                </button>
                                <a href="{{ url('/') }}"
                                    class="inline-flex items-center gap-2 text-gray-400 hover:text-gray-600 text-sm font-medium transition-colors">
                                    <span class="material-symbols-outlined text-base">arrow_back</span>
                                    Cancel &amp; Go Back
                                </a>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- SIDEBAR -->
                <aside class="xl:w-80 flex-shrink-0 space-y-6">

                    <!-- What happens next -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                            <h3 class="font-semibold text-gray-800 text-sm">What Happens Next?</h3>
                        </div>
                        <div class="p-6 space-y-5">
                            @foreach([
                                ['icon' => 'mark_email_read', 'title' => 'Acknowledgement', 'desc' => 'You\'ll receive a confirmation email within 5 minutes.'],
                                ['icon' => 'manage_search', 'title' => 'Triage & Review', 'desc' => 'The SOC team reviews and classifies your report within 1 hour.'],
                                ['icon' => 'groups', 'title' => 'Investigation', 'desc' => 'A dedicated analyst is assigned to investigate.'],
                                ['icon' => 'check_circle', 'title' => 'Resolution', 'desc' => 'You\'re notified when the incident is contained and closed.'],
                            ] as $i => $step)
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="w-8 h-8 rounded-full bg-alert-amber/10 border border-alert-amber/20 flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-alert-amber text-sm">{{ $step['icon'] }}</span>
                                    </div>
                                    @if(!$loop->last)
                                    <div class="w-px flex-1 bg-gray-100 min-h-[16px]"></div>
                                    @endif
                                </div>
                                <div class="pb-4">
                                    <p class="text-gray-800 font-semibold text-sm">{{ $step['title'] }}</p>
                                    <p class="text-gray-400 text-xs font-light mt-0.5 leading-relaxed">{{ $step['desc'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="bg-[#040f24] rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-white/5">
                            <h3 class="font-semibold text-white text-sm">Contact CISO Office</h3>
                        </div>
                        <div class="p-6">
                            <a href="mailto:ciso_office@hrrl.in" class="flex items-center gap-3 group">
                                <div class="w-8 h-8 rounded-full bg-alert-amber/15 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-alert-amber text-sm">mail</span>
                                </div>
                                <div>
                                    <p class="text-white/40 text-xs font-label-md uppercase tracking-wider">Email (24/7)</p>
                                    <p class="text-white font-bold text-sm group-hover:underline">ciso_office@hrrl.in</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Quick tips -->
                    <div class="bg-alert-amber/5 border border-alert-amber/20 rounded-2xl p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-alert-amber text-lg">lightbulb</span>
                            <h3 class="font-semibold text-gray-800 text-sm">Before You Submit</h3>
                        </div>
                        <ul class="space-y-2.5">
                            @foreach([
                                'Do NOT delete any suspicious emails or files.',
                                'Disconnect your device from the network if actively compromised.',
                                'Do not try to fix the problem yourself.',
                                'Note any error messages or unusual system behaviour.',
                            ] as $tip)
                            <li class="flex items-start gap-2.5 text-xs text-gray-500 font-light leading-relaxed">
                                <span class="material-symbols-outlined text-alert-amber text-sm flex-shrink-0 mt-0.5">check_small</span>
                                {{ $tip }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                </aside>

            </div>
        </div>
    </section>

@endsection
