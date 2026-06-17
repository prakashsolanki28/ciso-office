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
    ];

    protected $casts = [
        'specifications'   => 'array',
        'statistics'       => 'array',
        'before_points'    => 'array',
        'after_points'     => 'array',
        'onboard_accounts' => 'array',
        'gallery'          => 'array',
        'charts'           => 'array',
    ];

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
