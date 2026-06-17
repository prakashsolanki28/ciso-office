<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.incidents.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Incident #{{ $incident->id }} — {{ $incident->incident_type }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash --}}
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Main detail --}}
                <div class="lg:col-span-2 space-y-5">

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Reporter Information</h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 gap-4 text-sm">
                            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Full Name</p><p class="text-gray-800 dark:text-gray-200 font-medium">{{ $incident->full_name }}</p></div>
                            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Employee ID</p><p class="text-gray-800 dark:text-gray-200 font-medium">{{ $incident->employee_id ?: '—' }}</p></div>
                            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Email</p><p class="text-gray-800 dark:text-gray-200 font-medium">{{ $incident->email }}</p></div>
                            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Department</p><p class="text-gray-800 dark:text-gray-200 font-medium">{{ $incident->department ?: '—' }}</p></div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Incident Details</h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 gap-4 text-sm">
                            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Type</p><p class="text-gray-800 dark:text-gray-200 font-medium">{{ $incident->incident_type }}</p></div>
                            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Date & Time</p><p class="text-gray-800 dark:text-gray-200 font-medium">{{ $incident->incident_date->format('d M Y') }}{{ $incident->incident_time ? ' at ' . $incident->incident_time : '' }}</p></div>
                            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Assets Affected</p><p class="text-gray-800 dark:text-gray-200 font-medium">{{ $incident->assets_affected ?: '—' }}</p></div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Severity</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize {{ $incident->getSeverityColorClass() }}">{{ $incident->severity }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Description</h3>
                        </div>
                        <div class="p-6 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $incident->description }}</div>
                    </div>

                    @if($incident->actions_taken)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Actions Taken</h3>
                        </div>
                        <div class="p-6 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $incident->actions_taken }}</div>
                    </div>
                    @endif

                    @if($incident->admin_note)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Admin Note</h3>
                        </div>
                        <div class="p-6 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $incident->admin_note }}</div>
                    </div>
                    @endif

                    @if($incident->attachments && count($incident->attachments) > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Attachments ({{ count($incident->attachments) }})</h3>
                        </div>
                        <ul class="p-6 space-y-2">
                            @foreach($incident->attachments as $path)
                            <li class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                {{ basename($path) }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-5">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Update Status</h3>
                        </div>
                        <div class="p-5">
                            <form method="POST" action="{{ route('admin.incidents.status', $incident) }}" class="space-y-4">
                                @csrf @method('PATCH')
                                <div>
                                    <label for="status" class="block text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1.5">Status</label>
                                    <select name="status" id="status"
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 dark:bg-gray-700 dark:text-white">
                                        <option value="new" {{ $incident->status === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="in_review" {{ $incident->status === 'in_review' ? 'selected' : '' }}>In Review</option>
                                        <option value="investigating" {{ $incident->status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                                        <option value="resolved" {{ $incident->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="closed" {{ $incident->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="admin_note" class="block text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1.5">Admin Note <span class="normal-case text-gray-300">(optional)</span></label>
                                    <textarea name="admin_note" id="admin_note" rows="4"
                                        placeholder="Add a note about this decision…"
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 dark:bg-gray-700 dark:text-white resize-y">{{ old('admin_note', $incident->admin_note) }}</textarea>
                                    @error('admin_note')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg px-4 py-2 transition-colors">
                                    Save
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Meta</h3>
                        </div>
                        <div class="p-5 space-y-3 text-sm">
                            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-0.5">Submitted</p><p class="text-gray-700 dark:text-gray-300">{{ $incident->created_at->format('d M Y H:i') }}</p></div>
                            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-0.5">Last Updated</p><p class="text-gray-700 dark:text-gray-300">{{ $incident->updated_at->diffForHumans() }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
