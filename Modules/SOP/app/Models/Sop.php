<?php

namespace Modules\SOP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sop extends Model
{
    use SoftDeletes;

    protected $table = 'sops';

    protected $fillable = [
        'title', 'description', 'icon', 'file_path', 'file_name', 'file_size', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getFileSizeHumanAttribute(): ?string
    {
        if (! $this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $this->file_size;
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, $i === 0 ? 0 : 1) . ' ' . $units[$i];
    }
}
