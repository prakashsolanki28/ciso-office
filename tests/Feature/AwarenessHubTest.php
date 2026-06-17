<?php

use App\Models\User;
use Modules\Blog\Models\Blog;
use Modules\CaseStudy\Models\CaseStudy;
use Modules\Newsletter\Models\Newsletter;

it('is publicly accessible and shows published content from all three sources', function () {
    $user = User::factory()->create();

    Blog::create([
        'user_id' => $user->id, 'title' => 'Phishing 101',
        'status' => 'published', 'published_at' => now()->subDay(),
    ]);
    CaseStudy::create([
        'title' => 'SOC Rollout Win', 'status' => 'published', 'published_at' => now()->subDay(),
    ]);
    Newsletter::create([
        'title' => 'June Security Bulletin', 'status' => 'published', 'published_at' => now()->subDay(),
    ]);

    $this->get('/awareness')
        ->assertOk()
        ->assertSee('Phishing 101')
        ->assertSee('SOC Rollout Win')
        ->assertSee('June Security Bulletin')
        ->assertSee('View all articles')
        ->assertSee('View all case studies');
});

it('hides drafts/unpublished content from the awareness hub', function () {
    $user = User::factory()->create();

    Blog::create(['user_id' => $user->id, 'title' => 'Draft Article', 'status' => 'draft']);
    CaseStudy::create(['title' => 'Draft Study', 'status' => 'draft']);
    Newsletter::create(['title' => 'Archived Bulletin', 'status' => 'archived']);
    Newsletter::create(['title' => 'Future Bulletin', 'status' => 'published', 'published_at' => now()->addWeek()]);

    $res = $this->get('/awareness')->assertOk();
    $res->assertDontSee('Draft Article');
    $res->assertDontSee('Draft Study');
    $res->assertDontSee('Archived Bulletin');
    $res->assertDontSee('Future Bulletin');
});
