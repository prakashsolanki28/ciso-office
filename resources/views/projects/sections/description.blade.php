{{-- ══════════ OVERVIEW ══════════ --}}
@if ($project->description)
    <section class="pt-20 lg:pt-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-4">
                <div class="lg:sticky lg:top-28">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-px w-8 bg-alert-amber"></div>
                        <span class="font-label-sm text-alert-amber tracking-widest uppercase font-semibold">Overview</span>
                    </div>
                    <h2 class="font-headline-lg text-2xl font-bold text-primary tracking-tight">
                        About this project
                    </h2>
                </div>
            </div>
            <div class="lg:col-span-8">
                <div class="blog-content max-w-3xl">
                    {!! $project->description !!}
                </div>
            </div>
        </div>
    </section>
@endif
