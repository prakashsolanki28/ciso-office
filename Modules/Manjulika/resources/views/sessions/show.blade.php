<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('manjulika.sessions.index') }}"
                class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight line-clamp-1">
                {{ $session->display_name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Meta --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wider">Name</dt>
                        <dd class="mt-1 font-medium text-gray-800 dark:text-gray-200 {{ $session->name ? '' : 'italic text-gray-400 font-normal' }}">{{ $session->display_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wider">Messages</dt>
                        <dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $session->messages->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wider">Started</dt>
                        <dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $session->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wider">Last active</dt>
                        <dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ optional($session->last_active_at)->diffForHumans() ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs text-gray-400 uppercase tracking-wider">IP address</dt>
                        <dd class="mt-1 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $session->ip_address ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs text-gray-400 uppercase tracking-wider">User agent</dt>
                        <dd class="mt-1 text-xs text-gray-500 dark:text-gray-400 break-words">{{ $session->user_agent ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Transcript --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Transcript</h3>
                </div>

                @if ($session->messages->isEmpty())
                    <div class="py-12 text-center text-sm text-gray-400">No messages in this session.</div>
                @else
                    <div class="p-5 space-y-4 bg-[#f8f9fb] dark:bg-gray-900/30">
                        @foreach ($session->messages as $message)
                            @php $isUser = $message->role === 'user'; @endphp
                            <div class="flex gap-2.5 items-end {{ $isUser ? 'flex-row-reverse' : '' }}">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 {{ $isUser ? 'bg-alert-amber/20' : 'bg-primary/10' }}">
                                    <span class="material-symbols-outlined text-sm {{ $isUser ? 'text-alert-amber' : 'text-primary' }}">{{ $isUser ? 'person' : 'support_agent' }}</span>
                                </div>
                                <div class="max-w-[78%]">
                                    <div class="rounded-2xl px-4 py-2.5 text-sm shadow-sm whitespace-pre-wrap break-words {{ $isUser ? 'bg-[#040f24] text-white rounded-br-sm' : 'bg-white border border-gray-200 text-on-surface rounded-bl-sm' }}">{{ $message->content }}</div>
                                    <div class="mt-1 text-[10px] text-gray-400 {{ $isUser ? 'text-right' : '' }}">{{ $message->created_at->format('M d, H:i') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Delete --}}
            <div class="flex justify-end">
                <form method="POST" action="{{ route('manjulika.sessions.destroy', $session) }}"
                      x-data
                      @submit.prevent="if(confirm('Delete this session and its transcript?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-red-600 border border-red-200 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete session
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
