<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Models\Newsletter;

function admin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

it('lists newsletters on the admin index', function () {
    $n = Newsletter::create(['title' => 'Quarterly Security Digest', 'status' => 'published']);

    $this->actingAs(admin())
        ->get('/newsletters')
        ->assertOk()
        ->assertSee('Quarterly Security Digest');
});

it('creates a newsletter with an uploaded image', function () {
    Storage::fake('public');

    $this->actingAs(admin())
        ->post('/newsletters', [
            'title'             => 'Phishing Awareness Drive',
            'short_description' => 'Monthly awareness mailer.',
            'status'            => 'published',
            'published_at'      => '2026-06-10 09:00',
            'image'             => UploadedFile::fake()->image('cover.png'),
        ])
        ->assertRedirect(route('newsletter.index'))
        ->assertSessionHas('success');

    $n = Newsletter::firstWhere('title', 'Phishing Awareness Drive');
    expect($n)->not->toBeNull()
        ->and($n->status)->toBe('published')
        ->and($n->published_at)->not->toBeNull()
        ->and($n->image)->not->toBeNull();

    Storage::disk('public')->assertExists($n->image);
});

it('requires a title', function () {
    $this->actingAs(admin())
        ->post('/newsletters', ['status' => 'draft'])
        ->assertSessionHasErrors('title');

    expect(Newsletter::count())->toBe(0);
});

it('updates a newsletter and replaces the image', function () {
    Storage::fake('public');

    $n = Newsletter::create([
        'title'  => 'Old Title',
        'status' => 'draft',
        'image'  => 'newsletters/images/old.png',
    ]);
    Storage::disk('public')->put('newsletters/images/old.png', 'x');

    $this->actingAs(admin())
        ->put("/newsletters/{$n->id}", [
            'title'  => 'New Title',
            'status' => 'archived',
            'image'  => UploadedFile::fake()->image('new.png'),
        ])
        ->assertRedirect(route('newsletter.index'))
        ->assertSessionHas('success');

    $n->refresh();
    expect($n->title)->toBe('New Title')
        ->and($n->status)->toBe('archived')
        ->and($n->image)->not->toBe('newsletters/images/old.png');

    Storage::disk('public')->assertMissing('newsletters/images/old.png');
    Storage::disk('public')->assertExists($n->image);
});

it('keeps the existing image when none is uploaded on update', function () {
    $n = Newsletter::create([
        'title'  => 'Keep Image',
        'status' => 'draft',
        'image'  => 'newsletters/images/keep.png',
    ]);

    $this->actingAs(admin())
        ->put("/newsletters/{$n->id}", ['title' => 'Keep Image Edited', 'status' => 'draft'])
        ->assertRedirect(route('newsletter.index'));

    $n->refresh();
    expect($n->title)->toBe('Keep Image Edited')
        ->and($n->image)->toBe('newsletters/images/keep.png');
});

it('deletes a newsletter and its image', function () {
    Storage::fake('public');

    $n = Newsletter::create(['title' => 'To Delete', 'status' => 'draft', 'image' => 'newsletters/images/del.png']);
    Storage::disk('public')->put('newsletters/images/del.png', 'x');

    $this->actingAs(admin())
        ->delete("/newsletters/{$n->id}")
        ->assertRedirect(route('newsletter.index'))
        ->assertSessionHas('success');

    expect(Newsletter::find($n->id))->toBeNull();
    Storage::disk('public')->assertMissing('newsletters/images/del.png');
});

it('blocks guests from the admin index', function () {
    $this->get('/newsletters')->assertRedirect('/login');
});
