<?php

namespace Modules\Quiz\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id', 'question_text', 'question_text_hi', 'question_type',
        'marks', 'notes', 'correct_text_answer', 'order',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuizQuestionOption::class, 'question_id')->orderBy('order');
    }

    public function hasOptions(): bool
    {
        return in_array($this->question_type, ['single', 'multiple', 'true_false']);
    }
}
