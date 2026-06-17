<?php

namespace Modules\Quiz\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'description', 'banner',
        'start_time', 'end_time',
        'marks_per_question', 'duration_type', 'duration_minutes', 'duration_per_question',
        'can_review_paper', 'can_view_result', 'attempts_allowed', 'pass_percentage', 'status',
        'language', 'title_hi', 'description_hi',
    ];

    protected $casts = [
        'start_time'         => 'datetime',
        'end_time'           => 'datetime',
        'marks_per_question' => 'decimal:2',
        'pass_percentage'    => 'decimal:2',
        'can_review_paper'   => 'boolean',
        'can_view_result'    => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->withDefault(['name' => 'Deleted User']);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * "Running & active": published and within its scheduled window (if any).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('start_time')->orWhere('start_time', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_time')->orWhere('end_time', '>=', now()));
    }

    public function isActive(): bool
    {
        return $this->status === 'published'
            && ($this->start_time === null || now()->greaterThanOrEqualTo($this->start_time))
            && ($this->end_time === null || now()->lessThanOrEqualTo($this->end_time));
    }

    public function isUnlimitedAttempts(): bool
    {
        return (int) $this->attempts_allowed === 0;
    }

    public function getHasTimerAttribute(): bool
    {
        return $this->duration_type !== null;
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage/' . $this->banner) : null;
    }

    public function getTotalMarksAttribute(): float
    {
        return $this->questions->sum(fn($q) => $q->marks ?? $this->marks_per_question);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'published' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            'archived'  => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
            default     => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        };
    }

    public function getLanguageBadgeAttribute(): array
    {
        return match ($this->language) {
            'hi'    => ['text' => 'हिंदी', 'class' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'],
            default => ['text' => 'EN', 'class' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
        };
    }

    public function getHasHindiContentAttribute(): bool
    {
        return !empty($this->title_hi);
    }
}
