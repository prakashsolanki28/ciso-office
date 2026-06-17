<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Incident Reports
            </h2>
            <span class="text-sm text-gray-500">{{ $incidents->total() }} total reports</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash message --}}
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <form method="GET" action="{{ route('admin.incidents.index') }}" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, type…"
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 dark:bg-gray-700 dark:text-white w-52">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Severity</label>
                        <select name="severity" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 dark:bg-gray-700 dark:text-white">
                            <option value="">All</option>
                            <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Status</label>
                        <select name="status" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 dark:bg-gray-700 dark:text-white">
                            <option value="">All</option>
                            <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review</option>
                            <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'severity', 'status']))
                    <a href="{{ route('admin.incidents.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2 transition-colors">Clear</a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if($incidents->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm font-medium">No incident reports found.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">#</th>
                                <th class="px-5 py-3.5">Reporter</th>
                                <th class="px-5 py-3.5">Type</th>
                                <th class="px-5 py-3.5">Date</th>
                                <th class="px-5 py-3.5">Severity</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Submitted</th>
                                <th class="px-5 py-3.5">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($incidents as $incident)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-4 text-gray-400 font-mono text-xs">#{{ $incident->id }}</td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $incident->full_name }}</p>
                                    <p class="text-gray-400 text-xs mt-0.5">{{ $incident->email }}</p>
                                    @if($incident->department)
                                    <p class="text-gray-400 text-xs">{{ $incident->department }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-700 dark:text-gray-300 max-w-[160px]">
                                    <span title="{{ $incident->incident_type }}" class="line-clamp-2">{{ $incident->incident_type }}</span>
                                </td>
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $incident->incident_date->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize {{ $incident->getSeverityColorClass() }}">
                                        {{ $incident->severity }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <form method="POST" action="{{ route('admin.incidents.status', $incident) }}">
                                        @csrf @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                            class="text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-300 dark:bg-gray-700 dark:text-white {{ $incident->getStatusColorClass() }}">
                                            <option value="new" {{ $incident->status === 'new' ? 'selected' : '' }}>New</option>
                                            <option value="in_review" {{ $incident->status === 'in_review' ? 'selected' : '' }}>In Review</option>
                                            <option value="investigating" {{ $incident->status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                                            <option value="resolved" {{ $incident->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="closed" {{ $incident->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-5 py-4 text-gray-400 text-xs whitespace-nowrap">
                                    {{ $incident->created_at->diffForHumans() }}
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.incidents.show', $incident) }}"
                                        class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs font-semibold transition-colors">
                                        View
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if($incidents->hasPages())
            <div class="flex justify-center">
                {{ $incidents->links() }}
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
