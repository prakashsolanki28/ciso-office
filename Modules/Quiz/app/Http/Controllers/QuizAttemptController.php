<?php

namespace Modules\Quiz\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Quiz\Actions\QuizGrader;
use Modules\Quiz\Models\Quiz;
use Modules\Quiz\Models\QuizAttempt;
use Modules\Quiz\Models\QuizAttemptAnswer;
use Modules\Quiz\Models\QuizQuestion;

class QuizAttemptController extends Controller
{
    /**
     * Create a new attempt (or resume an in-progress one), enforcing the
     * quiz window and the per-quiz attempt limit. Concurrency-safe.
     */
    public function start(Quiz $quiz)
    {
        abort_unless($quiz->isActive(), 403, 'This quiz is not currently available.');

        if ($quiz->questions()->count() === 0) {
            return redirect()->route('quiz.take.intro', $quiz)
                ->with('error', 'This quiz has no questions yet.');
        }

        $userId = Auth::id();

        $attempt = DB::transaction(function () use ($quiz, $userId) {
            $attempts = $quiz->attempts()
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->get();

            if ($resume = $attempts->firstWhere('status', 'in_progress')) {
                return $resume;
            }

            $completed = $attempts->whereIn('status', ['submitted', 'expired'])->count();
            if (! $quiz->isUnlimitedAttempts() && $completed >= (int) $quiz->attempts_allowed) {
                return null;
            }

            $data = [
                'quiz_id'        => $quiz->id,
                'user_id'        => $userId,
                'status'         => 'in_progress',
                'attempt_number' => (int) ($attempts->max('attempt_number') ?? 0) + 1,
                'started_at'     => now(),
            ];

            if ($quiz->duration_type === 'full_paper') {
                $data['deadline_at'] = now()->addMinutes((int) $quiz->duration_minutes);
            } elseif ($quiz->duration_type === 'per_question') {
                $first = $quiz->questions()->orderBy('order')->orderBy('id')->first();
                $data['current_question_id'] = $first?->id;
                $data['current_deadline_at'] = now()->addSeconds((int) $quiz->duration_per_question);
            }

            try {
                return QuizAttempt::create($data);
            } catch (QueryException $e) {
                // Concurrent start collided on the unique key — resume the winner.
                return $quiz->attempts()->where('user_id', $userId)->inProgress()->first();
            }
        });

        if (! $attempt) {
            return redirect()->route('quiz.take.intro', $quiz)
                ->with('error', 'You have no attempts remaining for this quiz.');
        }

        return redirect()->route('quiz.take.show', $attempt);
    }

    /**
     * Render the take screen for an in-progress attempt.
     */
    public function show(QuizAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        $quiz = $attempt->quiz;

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('quiz.take.result', $attempt);
        }

        if ($this->enforceDeadlines($attempt)) {
            return redirect()->route('quiz.take.result', $attempt)
                ->with('info', 'Time expired — your attempt was submitted automatically.');
        }

        $quiz->load('questions.options');
        $answers = $attempt->answers()->get()->keyBy('question_id');
        $secondsLeft = $this->secondsLeft($attempt);

        if ($quiz->duration_type === 'per_question') {
            $currentQuestion = $quiz->questions->firstWhere('id', $attempt->current_question_id);
            $position = $quiz->questions->search(fn ($q) => $q->id === $attempt->current_question_id);

            return view('quiz::take.show', compact('attempt', 'quiz', 'answers', 'secondsLeft', 'currentQuestion', 'position'));
        }

        return view('quiz::take.show', compact('attempt', 'quiz', 'answers', 'secondsLeft'));
    }

    /**
     * Per-question mode: save the current answer and advance to the next
     * question (or finish if it was the last one).
     */
    public function answer(Request $request, QuizAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        $quiz = $attempt->quiz;
        abort_unless($quiz->duration_type === 'per_question', 400);

        if ($this->enforceDeadlines($attempt) || $attempt->status !== 'in_progress') {
            return redirect()->route('quiz.take.result', $attempt);
        }

        // Only accept a submission for the active (current, unlocked) question.
        if ((int) $request->input('question_id') === (int) $attempt->current_question_id) {
            $question = $quiz->questions()->find($attempt->current_question_id);
            if ($question) {
                $this->storeAnswer($attempt, $question, [
                    'options' => (array) $request->input('options', []),
                    'text'    => $request->input('text'),
                ]);
            }
        }

        $this->advancePointer($attempt);
        $attempt->refresh();

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('quiz.take.result', $attempt);
        }

        return redirect()->route('quiz.take.show', $attempt);
    }

    /**
     * Full-paper / no-timer mode: persist all posted answers and grade.
     */
    public function submit(Request $request, QuizAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        DB::transaction(function () use ($request, $attempt) {
            $locked = QuizAttempt::whereKey($attempt->id)->lockForUpdate()->first();
            if ($locked->status !== 'in_progress') {
                return; // already finalized (double-submit / concurrent)
            }

            $quiz = $locked->quiz()->with('questions.options')->first();

            $expired = $quiz->duration_type === 'full_paper'
                && $locked->deadline_at && now()->greaterThan($locked->deadline_at);
            $windowClosed = ! $quiz->isActive() && $quiz->end_time && now()->greaterThan($quiz->end_time);

            if (! $expired && ! $windowClosed) {
                foreach ((array) $request->input('answers', []) as $questionId => $payload) {
                    $question = $quiz->questions->firstWhere('id', (int) $questionId);
                    if ($question) {
                        $this->storeAnswer($locked, $question, [
                            'options' => (array) ($payload['options'] ?? []),
                            'text'    => $payload['text'] ?? null,
                        ]);
                    }
                }
            }

            $this->finalize($locked, ($expired || $windowClosed) ? 'expired' : 'submitted');
        });

        return redirect()->route('quiz.take.result', $attempt);
    }

    public function result(QuizAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'in_progress') {
            return redirect()->route('quiz.take.show', $attempt);
        }

        $quiz = $attempt->quiz;

        return view('quiz::take.result', compact('attempt', 'quiz'));
    }

    public function review(QuizAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        $quiz = $attempt->quiz;

        abort_unless($quiz->can_review_paper, 403);

        if ($attempt->status === 'in_progress') {
            return redirect()->route('quiz.take.show', $attempt);
        }

        $quiz->load('questions.options');
        $answers = $attempt->answers()->get()->keyBy('question_id');

        return view('quiz::take.review', compact('attempt', 'quiz', 'answers'));
    }

    // ---------------------------------------------------------------------
    // Internal helpers
    // ---------------------------------------------------------------------

    private function authorizeAttempt(QuizAttempt $attempt): void
    {
        abort_if($attempt->user_id !== Auth::id(), 403);
    }

    /**
     * Handle expiry of the quiz window and timers. Returns true when the
     * attempt was auto-finalized as a result (caller should go to result).
     */
    private function enforceDeadlines(QuizAttempt $attempt): bool
    {
        $quiz = $attempt->quiz;

        // Quiz scheduling window closed mid-attempt.
        if (! $quiz->isActive() && $quiz->end_time && now()->greaterThan($quiz->end_time)) {
            $this->finalize($attempt, 'expired');

            return true;
        }

        if ($quiz->duration_type === 'full_paper') {
            if ($attempt->deadline_at && now()->greaterThan($attempt->deadline_at)) {
                $this->finalize($attempt, 'expired');

                return true;
            }
        } elseif ($quiz->duration_type === 'per_question') {
            while (
                $attempt->status === 'in_progress'
                && $attempt->current_question_id
                && $attempt->current_deadline_at
                && now()->greaterThan($attempt->current_deadline_at)
            ) {
                $this->advancePointer($attempt);
                $attempt->refresh();
            }

            return $attempt->status !== 'in_progress';
        }

        return false;
    }

    /**
     * Per-question mode: lock the current question and move the pointer to
     * the next one, or finalize the attempt if there is no next question.
     */
    private function advancePointer(QuizAttempt $attempt): void
    {
        $quiz = $attempt->quiz;
        $questionIds = $quiz->questions()->orderBy('order')->orderBy('id')->pluck('id')->all();

        if ($attempt->current_question_id) {
            QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)
                ->where('question_id', $attempt->current_question_id)
                ->update(['locked' => true]);
        }

        $idx = array_search((int) $attempt->current_question_id, array_map('intval', $questionIds), true);
        $nextId = ($idx !== false && isset($questionIds[$idx + 1])) ? $questionIds[$idx + 1] : null;

        if ($nextId === null) {
            $this->finalize($attempt, 'submitted');

            return;
        }

        $attempt->update([
            'current_question_id' => $nextId,
            'current_deadline_at' => now()->addSeconds((int) $quiz->duration_per_question),
        ]);
    }

    /**
     * Grade the attempt and mark it finished. Idempotent.
     */
    private function finalize(QuizAttempt $attempt, string $status): void
    {
        if ($attempt->status !== 'in_progress') {
            return;
        }

        (new QuizGrader)->grade($attempt);

        $attempt->update([
            'status'              => $status,
            'submitted_at'        => now(),
            'current_question_id' => null,
            'current_deadline_at' => null,
        ]);
    }

    /**
     * Persist (insert or update) a single answer row. Ignores locked rows.
     */
    private function storeAnswer(QuizAttempt $attempt, QuizQuestion $question, array $payload): void
    {
        $existing = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->first();

        if ($existing && $existing->locked) {
            return;
        }

        $data = ['answered_at' => now()];

        if ($question->hasOptions()) {
            $validIds = $question->options->pluck('id')->map(fn ($id) => (int) $id)->all();
            $selected = collect($payload['options'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => in_array($id, $validIds, true))
                ->unique()->values()->all();

            // single / true_false accept at most one option
            if ($question->question_type !== 'multiple') {
                $selected = array_slice($selected, 0, 1);
            }

            $data['selected_option_ids'] = $selected;
            $data['text_answer'] = null;
        } else {
            $text = $payload['text'] ?? null;
            $data['text_answer'] = is_string($text) ? $text : null;
            $data['selected_option_ids'] = null;
        }

        QuizAttemptAnswer::updateOrCreate(
            ['quiz_attempt_id' => $attempt->id, 'question_id' => $question->id],
            $data
        );
    }

    /**
     * Server-authoritative seconds remaining on the active timer (or null).
     */
    private function secondsLeft(QuizAttempt $attempt): ?int
    {
        $quiz = $attempt->quiz;

        $deadline = match ($quiz->duration_type) {
            'full_paper'   => $attempt->deadline_at,
            'per_question' => $attempt->current_deadline_at,
            default        => null,
        };

        if (! $deadline) {
            return null;
        }

        return max(0, $deadline->getTimestamp() - now()->getTimestamp());
    }
}
