<?php

namespace Modules\Quiz\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Quiz\Models\Quiz;
use Modules\Quiz\Models\QuizAttempt;

class EmployeeQuizController extends Controller
{
    /**
     * List quizzes that are currently running & active, with this
     * employee's attempt status for each.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $quizzes = Quiz::active()
            ->withCount('questions')
            ->latest()
            ->paginate(12);

        $attempts = QuizAttempt::where('user_id', $userId)
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->groupBy('quiz_id');

        $stats = [];
        foreach ($quizzes as $quiz) {
            $stats[$quiz->id] = $this->attemptStats($quiz, $attempts->get($quiz->id, collect()));
        }

        return view('quiz::take.index', compact('quizzes', 'stats'));
    }

    /**
     * Quiz instructions / start screen.
     */
    public function intro(Quiz $quiz)
    {
        abort_unless($quiz->isActive(), 404);

        $myAttempts = $quiz->attempts()->where('user_id', Auth::id())->get();
        $stats = $this->attemptStats($quiz, $myAttempts);
        $questionCount = $quiz->questions()->count();

        return view('quiz::take.intro', compact('quiz', 'stats', 'questionCount'));
    }

    /**
     * Derive a uniform set of attempt stats for a quiz + this user's attempts.
     */
    private function attemptStats(Quiz $quiz, $attempts): array
    {
        $completed = $attempts->whereIn('status', ['submitted', 'expired']);

        return [
            'in_progress'     => $attempts->firstWhere('status', 'in_progress'),
            'completed_count' => $completed->count(),
            'best'            => $completed->max('percentage'),
            'best_passed'     => $completed->where('passed', true)->isNotEmpty(),
            'attempts_left'   => $quiz->isUnlimitedAttempts()
                ? null
                : max(0, (int) $quiz->attempts_allowed - $completed->count()),
        ];
    }
}
