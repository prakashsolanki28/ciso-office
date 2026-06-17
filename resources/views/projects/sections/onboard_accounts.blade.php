{{-- ══════════ ONBOARDED ACCOUNTS ══════════ --}}
@if (!empty($project->onboard_accounts))
    <section class="pt-20 lg:pt-24">
        <div class="max-w-2xl mb-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-px w-8 bg-alert-amber"></div>
                <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Coverage</span>
            </div>
            <h2 class="font-headline-lg text-3xl font-bold text-primary tracking-tight">Onboarded Accounts</h2>
            <p class="font-body-lg text-on-surface-variant font-light mt-3">Teams and systems brought under this
                project's protection.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($project->onboard_accounts as $account)
                <div
                    class="bg-white border border-border-gray/30 rounded-2xl p-6 flex items-start gap-4 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-14 h-14 rounded-2xl bg-surface border border-border-gray/30 flex items-center justify-center overflow-hidden shrink-0">
                        @if (!empty($account['logo']))
                            <img src="{{ asset('storage/' . $account['logo']) }}"
                                alt="{{ $account['name'] ?? '' }}" class="w-full h-full object-cover">
                        @else
                            <span class="font-headline-lg text-xl text-primary/40 font-bold">
                                {{ strtoupper(substr($account['name'] ?? '?', 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div class="min-w-0 pt-0.5">
                        <h3 class="font-title-md text-primary tracking-tight text-base leading-snug">
                            {{ $account['name'] ?? '' }}</h3>
                        @if (!empty($account['description']))
                            <p class="font-body-md text-on-surface-variant font-light text-sm mt-1 leading-relaxed">
                                {{ $account['description'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
