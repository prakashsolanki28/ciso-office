<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'short_description', 'banner', 'description',
        'specifications', 'statistics', 'before_points', 'after_points', 'onboard_accounts', 'gallery', 'charts',
        'section_order',
    ];

    protected $casts = [
        'specifications'   => 'array',
        'statistics'       => 'array',
        'before_points'    => 'array',
        'after_points'     => 'array',
        'onboard_accounts' => 'array',
        'gallery'          => 'array',
        'charts'           => 'array',
        'section_order'    => 'array',
    ];

    /**
     * Orderable public-page sections, keyed by their partial name (the file
     * under resources/views/projects/sections/). The array order here is the
     * default order used when a project has no saved section_order. This is the
     * single source of truth — the editor, controller and public page all read
     * from it.
     */
    public const SECTIONS = [
        'statistics'       => 'Statistics',
        'charts'           => 'Charts',
        'description'      => 'Description',
        'specifications'   => 'Specifications / Features',
        'before_after'     => 'Before & After',
        'onboard_accounts' => 'Onboard Accounts',
        'gallery'          => 'Gallery',
    ];

    /**
     * The section keys in the order they should render on the public page:
     * the saved order first (filtered to known keys), then any known keys not
     * present in the saved order appended — so legacy nulls and any sections
     * added after this project was last saved are handled automatically.
     *
     * @return list<string>
     */
    public function orderedSections(): array
    {
        $known = array_keys(self::SECTIONS);
        $saved = is_array($this->section_order)
            ? array_values(array_intersect($this->section_order, $known))
            : [];

        return array_values(array_unique([...$saved, ...$known]));
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = static::uniqueSlug($project->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = static::withTrashed()->where('slug', 'like', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage/' . $this->banner) : null;
    }
}
