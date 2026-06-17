<?php

namespace Modules\Quiz\Actions;

use Modules\Quiz\Models\QuizAttempt;
use Modules\Quiz\Models\QuizAttemptAnswer;
use Modules\Quiz\Models\QuizQuestion;

class QuizGrader
{
    /**
     * Auto-grade every question of an attempt, persist per-answer marks,
     * then store the overall score / percentage / pass result on the attempt.
     *
     * Objective questions (single / multiple / true_false) require the selected
     * option set to exactly match the correct option set (no partial credit).
     * Text questions are matched against `correct_text_answer` after normalization;
     * questions without a defined answer cannot be auto-graded and score 0.
     */
    public function grade(QuizAttempt $attempt): void
    {
        $quiz = $attempt->quiz()->with('questions.options')->first();
        $existing = $attempt->answers()->get()->keyBy('question_id');

        $score = 0.0;

        foreach ($quiz->questions as $question) {
            $marks = (float) ($question->marks ?? $quiz->marks_per_question);
            $answer = $existing->get($question->id);

            $isCorrect = $this->isAnswerCorrect($question, $answer);
            $awarded = $isCorrect ? $marks : 0.0;
            $score += $awarded;

            QuizAttemptAnswer::updateOrCreate(
                ['quiz_attempt_id' => $attempt->id, 'question_id' => $question->id],
                [
                    'is_correct'    => $isCorrect,
                    'marks_awarded' => $awarded,
                    // preserve answered_at if already set; leave nulls for unanswered
                    'answered_at'   => $answer?->answered_at,
                ]
            );
        }

        $total = (float) $quiz->total_marks;
        $percentage = $total > 0 ? round(($score / $total) * 100, 2) : 0.0;

        $attempt->score = $score;
        $attempt->total_marks = $total;
        $attempt->percentage = $percentage;
        $attempt->passed = $percentage >= (float) $quiz->pass_percentage;
        $attempt->save();
    }

    private function isAnswerCorrect(QuizQuestion $question, ?QuizAttemptAnswer $answer): bool
    {
        if ($answer === null) {
            return false;
        }

        if ($question->hasOptions()) {
            $correct = $question->options->where('is_correct', true)->pluck('id')
                ->map(fn ($id) => (int) $id)->sort()->values()->all();

            $selected = collect($answer->selected_option_ids ?? [])
                ->map(fn ($id) => (int) $id)->unique()->sort()->values()->all();

            return ! empty($correct) && $correct === $selected;
        }

        // text question
        $expected = $this->normalize($question->correct_text_answer);
        if ($expected === '') {
            return false; // no defined answer => cannot auto-grade
        }

        return $this->normalize($answer->text_answer) === $expected;
    }

    private function normalize(?string $value): string
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return mb_strtolower($value);
    }
}
