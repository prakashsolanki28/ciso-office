<?php

use App\Models\User;
use Modules\Quiz\Models\Quiz;
use Modules\Quiz\Models\QuizAttempt;

/**
 * Create a published quiz with single-choice questions (option 0 = correct).
 */
function makeQuiz(array $attrs = [], int $questions = 3): Quiz
{
    $admin = User::factory()->create(['role' => 'admin']);

    $quiz = Quiz::create(array_merge([
        'user_id'            => $admin->id,
        'title'              => 'Sample Quiz',
        'status'             => 'published',
        'marks_per_question' => 1,
        'pass_percentage'    => 50,
        'attempts_allowed'   => 0,
        'can_view_result'    => true,
        'can_review_paper'   => true,
        'language'           => 'en',
    ], $attrs));

    for ($i = 1; $i <= $questions; $i++) {
        $q = $quiz->questions()->create([
            'question_text' => "Question {$i}",
            'question_type' => 'single',
            'order'         => $i,
        ]);
        $q->options()->create(['option_text' => 'Correct', 'is_correct' => true, 'order' => 0]);
        $q->options()->create(['option_text' => 'Wrong', 'is_correct' => false, 'order' => 1]);
    }

    return $quiz->fresh('questions.options');
}

function employee(array $attrs = []): User
{
    return User::factory()->create(array_merge(['role' => 'employee'], $attrs));
}

/** Build a correct full-paper answers payload for a single-choice quiz. */
function correctAnswers(Quiz $quiz): array
{
    $answers = [];
    foreach ($quiz->questions as $q) {
        $answers[$q->id] = ['options' => [$q->options->firstWhere('is_correct', true)->id]];
    }

    return $answers;
}

// ---------------------------------------------------------------------------
// Access control & listing
// ---------------------------------------------------------------------------

it('lists only running & active quizzes to employees', function () {
    $published = makeQuiz(['title' => 'Live Quiz', 'status' => 'published']);
    $draft = makeQuiz(['title' => 'Draft Quiz', 'status' => 'draft']);
    $future = makeQuiz(['title' => 'Future Quiz', 'status' => 'published', 'start_time' => now()->addDay()]);
    $past = makeQuiz(['title' => 'Past Quiz', 'status' => 'published', 'end_time' => now()->subDay()]);

    $this->actingAs(employee())
        ->get(route('quiz.take.index'))
        ->assertOk()
        ->assertSee('Live Quiz')
        ->assertDontSee('Draft Quiz')
        ->assertDontSee('Future Quiz')
        ->assertDontSee('Past Quiz');
});

it('forbids admins from the employee take area', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('quiz.take.index'))->assertForbidden();
});

it('blocks deactivated employees from logging in', function () {
    $user = employee(['email' => 'inactive@test.com', 'is_active' => false]);

    $this->post('/login', ['email' => 'inactive@test.com', 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

// ---------------------------------------------------------------------------
// Attempt lifecycle
// ---------------------------------------------------------------------------

it('starts a full-paper attempt with a server deadline', function () {
    $quiz = makeQuiz(['duration_type' => 'full_paper', 'duration_minutes' => 30]);

    $this->actingAs(employee())->post(route('quiz.take.start', $quiz))->assertRedirect();

    $attempt = QuizAttempt::sole();
    expect($attempt->status)->toBe('in_progress');
    expect($attempt->deadline_at)->not->toBeNull();
});

it('resumes the existing in-progress attempt instead of creating a new one', function () {
    $quiz = makeQuiz();
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));

    expect(QuizAttempt::where('quiz_id', $quiz->id)->count())->toBe(1);
});

it('enforces the per-quiz attempt limit', function () {
    $quiz = makeQuiz(['attempts_allowed' => 1]);
    $emp = employee();

    // First attempt: start and submit.
    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();
    $this->actingAs($emp)->post(route('quiz.take.submit', $attempt), ['answers' => correctAnswers($quiz)]);

    // Second start should be blocked.
    $this->actingAs($emp)->post(route('quiz.take.start', $quiz))
        ->assertRedirect(route('quiz.take.intro', $quiz));

    expect(QuizAttempt::where('quiz_id', $quiz->id)->count())->toBe(1);
});

it('allows unlimited attempts when attempts_allowed is 0', function () {
    $quiz = makeQuiz(['attempts_allowed' => 0]);
    $emp = employee();

    foreach (range(1, 3) as $n) {
        $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('status', 'in_progress')->sole();
        $this->actingAs($emp)->post(route('quiz.take.submit', $attempt), ['answers' => correctAnswers($quiz)]);
    }

    expect(QuizAttempt::where('quiz_id', $quiz->id)->count())->toBe(3);
});

it('renders the full-paper take screen with all questions', function () {
    $quiz = makeQuiz(['duration_type' => 'full_paper', 'duration_minutes' => 20], questions: 3);
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();

    $this->actingAs($emp)->get(route('quiz.take.show', $attempt))
        ->assertOk()
        ->assertSee('Question 1')
        ->assertSee('Question 3')
        ->assertSee('Submit Quiz');
});

it('renders the per-question take screen with only the current question', function () {
    $quiz = makeQuiz(['duration_type' => 'per_question', 'duration_per_question' => 60], questions: 3);
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();

    $this->actingAs($emp)->get(route('quiz.take.show', $attempt))
        ->assertOk()
        ->assertSee('Question 1')
        ->assertDontSee('Question 2');
});

// ---------------------------------------------------------------------------
// Grading
// ---------------------------------------------------------------------------

it('auto-grades a fully correct submission as 100% and passed', function () {
    $quiz = makeQuiz(['pass_percentage' => 60], questions: 4);
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();
    $this->actingAs($emp)->post(route('quiz.take.submit', $attempt), ['answers' => correctAnswers($quiz)]);

    $attempt->refresh();
    expect((float) $attempt->percentage)->toBe(100.0);
    expect($attempt->passed)->toBeTrue();
    expect($attempt->status)->toBe('submitted');
});

it('marks unanswered questions wrong and fails below the pass mark', function () {
    $quiz = makeQuiz(['pass_percentage' => 60], questions: 4);
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();

    // Answer only the first question correctly.
    $first = $quiz->questions->first();
    $answers = [$first->id => ['options' => [$first->options->firstWhere('is_correct', true)->id]]];
    $this->actingAs($emp)->post(route('quiz.take.submit', $attempt), ['answers' => $answers]);

    $attempt->refresh();
    expect((float) $attempt->percentage)->toBe(25.0);
    expect($attempt->passed)->toBeFalse();
    // a zero row exists for every question
    expect($attempt->answers()->count())->toBe(4);
});

it('grades multiple-choice with exact-set matching only', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $quiz = Quiz::create([
        'user_id' => $admin->id, 'title' => 'Multi', 'status' => 'published',
        'marks_per_question' => 1, 'pass_percentage' => 50, 'attempts_allowed' => 0, 'language' => 'en',
    ]);
    $q = $quiz->questions()->create(['question_text' => 'Pick both', 'question_type' => 'multiple', 'order' => 1]);
    $a = $q->options()->create(['option_text' => 'A', 'is_correct' => true, 'order' => 0]);
    $b = $q->options()->create(['option_text' => 'B', 'is_correct' => true, 'order' => 1]);
    $c = $q->options()->create(['option_text' => 'C', 'is_correct' => false, 'order' => 2]);
    $emp = employee();

    // Only one of two correct selected -> wrong.
    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();
    $this->actingAs($emp)->post(route('quiz.take.submit', $attempt), ['answers' => [$q->id => ['options' => [$a->id]]]]);
    expect((float) $attempt->fresh()->percentage)->toBe(0.0);
});

// ---------------------------------------------------------------------------
// Per-question mode
// ---------------------------------------------------------------------------

it('advances the pointer in per-question mode and finishes after the last question', function () {
    $quiz = makeQuiz(['duration_type' => 'per_question', 'duration_per_question' => 60], questions: 2);
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();
    $q1 = $quiz->questions[0];
    $q2 = $quiz->questions[1];
    expect($attempt->current_question_id)->toBe($q1->id);

    // Answer Q1 -> pointer moves to Q2.
    $this->actingAs($emp)->post(route('quiz.take.answer', $attempt), [
        'question_id' => $q1->id,
        'options'     => [$q1->options->firstWhere('is_correct', true)->id],
    ]);
    expect($attempt->fresh()->current_question_id)->toBe($q2->id);

    // Answer Q2 (last) -> attempt finalizes.
    $this->actingAs($emp)->post(route('quiz.take.answer', $attempt), [
        'question_id' => $q2->id,
        'options'     => [$q2->options->firstWhere('is_correct', true)->id],
    ])->assertRedirect(route('quiz.take.result', $attempt));

    $attempt->refresh();
    expect($attempt->status)->toBe('submitted');
    expect((float) $attempt->percentage)->toBe(100.0);
});

// ---------------------------------------------------------------------------
// Timer / window expiry
// ---------------------------------------------------------------------------

it('auto-submits a full-paper attempt once the deadline passes', function () {
    $quiz = makeQuiz(['duration_type' => 'full_paper', 'duration_minutes' => 10]);
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();

    $this->travel(11)->minutes();

    $this->actingAs($emp)->get(route('quiz.take.show', $attempt))
        ->assertRedirect(route('quiz.take.result', $attempt));

    expect($attempt->fresh()->status)->toBe('expired');
});

it('auto-submits when the quiz scheduling window closes mid-attempt', function () {
    $quiz = makeQuiz(['status' => 'published', 'end_time' => now()->addMinutes(5)]);
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();

    $this->travel(10)->minutes(); // window now closed

    $this->actingAs($emp)->get(route('quiz.take.show', $attempt))
        ->assertRedirect(route('quiz.take.result', $attempt));

    expect($attempt->fresh()->status)->toBe('expired');
});

// ---------------------------------------------------------------------------
// Result / review gating & ownership
// ---------------------------------------------------------------------------

it('hides the score when can_view_result is false', function () {
    $quiz = makeQuiz(['can_view_result' => false]);
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();
    $this->actingAs($emp)->post(route('quiz.take.submit', $attempt), ['answers' => correctAnswers($quiz)]);

    $this->actingAs($emp)->get(route('quiz.take.result', $attempt))
        ->assertOk()
        ->assertDontSee('Score:')
        ->assertSee('Results are not available');
});

it('blocks review when can_review_paper is false', function () {
    $quiz = makeQuiz(['can_review_paper' => false]);
    $emp = employee();

    $this->actingAs($emp)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();
    $this->actingAs($emp)->post(route('quiz.take.submit', $attempt), ['answers' => correctAnswers($quiz)]);

    $this->actingAs($emp)->get(route('quiz.take.review', $attempt))->assertForbidden();
});

it('prevents an employee from viewing another employee\'s attempt', function () {
    $quiz = makeQuiz();
    $owner = employee();
    $other = employee();

    $this->actingAs($owner)->post(route('quiz.take.start', $quiz));
    $attempt = QuizAttempt::sole();

    $this->actingAs($other)->get(route('quiz.take.show', $attempt))->assertForbidden();
});
