<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                Cyber Dost — Chat Sessions
            </h2>
            <span class="text-sm text-gray-400">{{ $sessions->total() }} total</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search by name</label>
                        <input
                            type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search visitor name…"
                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                    </div>
                    <button type="submit" class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">
                        Filter
                    </button>
                    @if (request('search'))
                        <a href="{{ route('manjulika.sessions.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($sessions->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">No chat sessions yet</p>
                        <p class="text-sm text-gray-400 mt-1">Sessions appear here once visitors chat with Cyber Dost.</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Messages</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Started</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Last active</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">IP</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($sessions as $session)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('manjulika.sessions.show', $session) }}"
                                           class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-primary {{ $session->name ? '' : 'italic text-gray-400' }}">
                                            {{ $session->display_name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center justify-center min-w-7 px-2 py-0.5 text-xs font-medium bg-primary/10 text-primary rounded-full">
                                            {{ $session->messages_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-gray-500">
                                        {{ $session->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-4 py-4 text-xs text-gray-500">
                                        {{ optional($session->last_active_at)->diffForHumans() ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-xs text-gray-400 font-mono">{{ $session->ip_address ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('manjulika.sessions.show', $session) }}"
                                               class="p-1.5 text-gray-400 hover:text-primary transition" title="View transcript">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('manjulika.sessions.destroy', $session) }}"
                                                  x-data
                                                  @submit.prevent="if(confirm('Delete this session and its transcript?')) $el.submit()">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($sessions->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $sessions->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
