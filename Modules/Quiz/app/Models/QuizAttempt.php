<?php

namespace Modules\Quiz\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id', 'user_id', 'status', 'attempt_number',
        'started_at', 'deadline_at', 'submitted_at',
        'current_question_id', 'current_deadline_at',
        'score', 'total_marks', 'percentage', 'passed',
    ];

    protected $casts = [
        'started_at'          => 'datetime',
        'deadline_at'         => 'datetime',
        'submitted_at'        => 'datetime',
        'current_deadline_at' => 'datetime',
        'score'               => 'decimal:2',
        'total_marks'         => 'decimal:2',
        'percentage'          => 'decimal:2',
        'passed'              => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    public function currentQuestion()
    {
        return $this->belongsTo(QuizQuestion::class, 'current_question_id');
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Whether the full-paper deadline (if any) has passed.
     */
    public function isExpired(): bool
    {
        return $this->deadline_at !== null && now()->greaterThan($this->deadline_at);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }
}
