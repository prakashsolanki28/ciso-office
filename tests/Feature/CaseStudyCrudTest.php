<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\CaseStudy\Models\CaseStudy;

function csAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

it('lists case studies on the admin index', function () {
    CaseStudy::create(['title' => 'Refinery SOC Rollout', 'status' => 'published']);

    $this->actingAs(csAdmin())
        ->get('/casestudies')
        ->assertOk()
        ->assertSee('Refinery SOC Rollout');
});

it('creates a case study from the modal and redirects to edit', function () {
    $this->actingAs(csAdmin())
        ->post('/casestudies', [
            'title'  => 'Zero Trust Migration',
            'status' => 'draft',
        ])
        ->assertSessionHas('success');

    $cs = CaseStudy::firstWhere('title', 'Zero Trust Migration');
    expect($cs)->not->toBeNull()
        ->and($cs->slug)->toBe('zero-trust-migration');

    $this->actingAs(csAdmin())
        ->post('/casestudies', ['title' => 'Another', 'status' => 'draft'])
        ->assertRedirect(route('casestudy.edit', CaseStudy::firstWhere('title', 'Another')));
});

it('renders the admin edit page with the TipTap editor', function () {
    $cs = CaseStudy::create(['title' => 'Editable', 'status' => 'draft', 'content' => '<p>Existing body</p>']);

    $this->actingAs(csAdmin())
        ->get(route('casestudy.edit', $cs))
        ->assertOk()
        ->assertSee('Case Study Body')
        ->assertSee('Editable');
});

it('requires a title to create', function () {
    $this->actingAs(csAdmin())
        ->post('/casestudies', ['status' => 'draft'])
        ->assertSessionHasErrors('title');

    expect(CaseStudy::count())->toBe(0);
});

it('updates a case study with body, client, results and a new cover image', function () {
    Storage::fake('public');

    $cs = CaseStudy::create([
        'title'  => 'Old',
        'status' => 'draft',
        'image'  => 'case-studies/images/old.png',
    ]);
    Storage::disk('public')->put('case-studies/images/old.png', 'x');

    $this->actingAs(csAdmin())
        ->patch("/casestudies/{$cs->id}", [
            'title'             => 'New Title',
            'slug'              => $cs->slug,
            'short_description' => 'A summary.',
            'content'           => '<p>The full write-up.</p>',
            'client'            => 'HRRL Refinery',
            'results'           => '40% fewer incidents.',
            'status'            => 'published',
            'published_at'      => '2026-06-10 09:00',
            'image'             => UploadedFile::fake()->image('cover.png'),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $cs->refresh();
    expect($cs->title)->toBe('New Title')
        ->and($cs->client)->toBe('HRRL Refinery')
        ->and($cs->results)->toBe('40% fewer incidents.')
        ->and($cs->status)->toBe('published')
        ->and($cs->content)->toContain('full write-up')
        ->and($cs->image)->not->toBe('case-studies/images/old.png');

    Storage::disk('public')->assertMissing('case-studies/images/old.png');
    Storage::disk('public')->assertExists($cs->image);
});

it('keeps the existing image when none is uploaded on update', function () {
    $cs = CaseStudy::create(['title' => 'Keep', 'status' => 'draft', 'image' => 'case-studies/images/keep.png']);

    $this->actingAs(csAdmin())
        ->patch("/casestudies/{$cs->id}", [
            'title'  => 'Keep Edited',
            'slug'   => $cs->slug,
            'status' => 'draft',
        ])
        ->assertRedirect();

    $cs->refresh();
    expect($cs->title)->toBe('Keep Edited')
        ->and($cs->image)->toBe('case-studies/images/keep.png');
});

it('deletes a case study and its image', function () {
    Storage::fake('public');

    $cs = CaseStudy::create(['title' => 'Del', 'status' => 'draft', 'image' => 'case-studies/images/del.png']);
    Storage::disk('public')->put('case-studies/images/del.png', 'x');

    $this->actingAs(csAdmin())
        ->delete("/casestudies/{$cs->id}")
        ->assertRedirect(route('casestudy.index'))
        ->assertSessionHas('success');

    expect(CaseStudy::find($cs->id))->toBeNull();
    Storage::disk('public')->assertMissing('case-studies/images/del.png');
});

it('uploads a cover image via the banner endpoint', function () {
    Storage::fake('public');

    $cs = CaseStudy::create(['title' => 'Banner', 'status' => 'draft']);

    $this->actingAs(csAdmin())
        ->post("/casestudies/{$cs->id}/banner", ['image' => UploadedFile::fake()->image('b.png')])
        ->assertOk()
        ->assertJsonStructure(['url', 'path']);

    expect($cs->refresh()->image)->not->toBeNull();
    Storage::disk('public')->assertExists($cs->image);
});

it('shows published case studies on the public pages', function () {
    $cs = CaseStudy::create([
        'title'        => 'Public Win',
        'status'       => 'published',
        'published_at' => now()->subDay(),
        'content'      => '<p>Body here.</p>',
    ]);

    $this->get('/case-studies')->assertOk()->assertSee('Public Win');
    $this->get("/case-studies/{$cs->slug}")->assertOk()->assertSee('Body here.', false);
});

it('hides drafts from the public pages', function () {
    $cs = CaseStudy::create(['title' => 'Hidden Draft', 'status' => 'draft']);

    $this->get('/case-studies')->assertOk()->assertDontSee('Hidden Draft');
    $this->get("/case-studies/{$cs->slug}")->assertNotFound();
});

it('404s an unknown public slug', function () {
    $this->get('/case-studies/does-not-exist')->assertNotFound();
});

it('blocks guests from the admin index', function () {
    $this->get('/casestudies')->assertRedirect('/login');
});
