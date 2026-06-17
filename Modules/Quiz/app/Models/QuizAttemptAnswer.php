<?php

namespace Modules\Quiz\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttemptAnswer extends Model
{
    protected $fillable = [
        'quiz_attempt_id', 'question_id', 'selected_option_ids',
        'text_answer', 'is_correct', 'marks_awarded', 'answered_at', 'locked',
    ];

    protected $casts = [
        'selected_option_ids' => 'array',
        'is_correct'          => 'boolean',
        'locked'              => 'boolean',
        'marks_awarded'       => 'decimal:2',
        'answered_at'         => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
