<?php

namespace Modules\Quiz\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Quiz\Models\Quiz;
use Modules\Quiz\Models\QuizQuestion;
use Modules\Quiz\Models\QuizQuestionOption;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $quizzes = Quiz::withCount('questions')
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->language, fn($q, $l) => $q->where('language', $l))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('quiz::index', compact('quizzes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_time'  => 'nullable|date',
            'end_time'    => 'nullable|date|after_or_equal:start_time',
            'language'    => 'nullable|in:en,hi',
        ]);

        $quiz = Quiz::create([
            ...$validated,
            'user_id'  => Auth::id(),
            'status'   => 'draft',
            'language' => $validated['language'] ?? 'en',
        ]);

        return redirect()
            ->route('quiz.edit', $quiz)
            ->with('success', 'Quiz created! Configure it below.');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('questions.options');

        return view('quiz::edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'title_hi'               => 'nullable|string|max:255',
            'description'            => 'nullable|string|max:2000',
            'description_hi'         => 'nullable|string|max:2000',
            'start_time'             => 'nullable|date',
            'end_time'               => 'nullable|date',
            'marks_per_question'     => 'required|numeric|min:0|max:1000',
            'duration_type'          => 'nullable|in:per_question,full_paper',
            'duration_minutes'       => 'nullable|integer|min:1|max:600',
            'duration_per_question'  => 'nullable|integer|min:5|max:3600',
            'can_review_paper'       => 'boolean',
            'can_view_result'        => 'boolean',
            'attempts_allowed'       => 'nullable|integer|min:0',
            'pass_percentage'        => 'required|numeric|min:0|max:100',
            'status'                 => 'required|in:draft,published,archived',
            'language'               => 'required|in:en,hi',
            'questions'              => 'nullable|string',
            'banner'                 => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('banner')) {
            if ($quiz->banner) {
                Storage::disk('public')->delete($quiz->banner);
            }
            $quiz->banner = $request->file('banner')->store('quiz-banners', 'public');
        }

        $quiz->fill([
            'title'                 => $validated['title'],
            'title_hi'              => $validated['title_hi'] ?? null,
            'description'           => $validated['description'] ?? null,
            'description_hi'        => $validated['description_hi'] ?? null,
            'start_time'            => $validated['start_time'] ?? null,
            'end_time'              => $validated['end_time'] ?? null,
            'marks_per_question'    => $validated['marks_per_question'],
            'duration_type'         => $validated['duration_type'] ?? null,
            'duration_minutes'      => $validated['duration_minutes'] ?? null,
            'duration_per_question' => $validated['duration_per_question'] ?? null,
            'can_review_paper'      => $request->boolean('can_review_paper'),
            'can_view_result'       => $request->boolean('can_view_result'),
            'attempts_allowed'      => $validated['attempts_allowed'] ?? 0,
            'pass_percentage'       => $validated['pass_percentage'],
            'status'                => $validated['status'],
            'language'              => $validated['language'],
        ])->save();

        if ($request->filled('questions')) {
            $questions = json_decode($request->input('questions'), true);
            if (is_array($questions)) {
                $this->syncQuestions($quiz, $questions);
            }
        }

        return back()->with('success', 'Quiz saved successfully!');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->banner) {
            Storage::disk('public')->delete($quiz->banner);
        }

        $quiz->delete();

        return redirect()->route('quiz.index')->with('success', 'Quiz deleted.');
    }

    public function bannerUpload(Request $request, Quiz $quiz)
    {
        $request->validate(['banner' => 'required|image|max:5120']);

        if ($quiz->banner) {
            Storage::disk('public')->delete($quiz->banner);
        }

        $path = $request->file('banner')->store('quiz-banners', 'public');
        $quiz->update(['banner' => $path]);

        return response()->json(['url' => asset('storage/' . $path)]);
    }

    private function syncQuestions(Quiz $quiz, array $questions): void
    {
        $keptIds = [];

        foreach ($questions as $index => $qData) {
            if (empty(trim($qData['question_text'] ?? ''))) {
                continue;
            }

            $questionPayload = [
                'question_text'       => $qData['question_text'],
                'question_text_hi'    => $qData['question_text_hi'] ?? null,
                'question_type'       => $qData['question_type'] ?? 'single',
                'marks'               => isset($qData['marks']) && $qData['marks'] !== '' ? $qData['marks'] : null,
                'notes'               => $qData['notes'] ?? null,
                'correct_text_answer' => $qData['correct_text_answer'] ?? null,
                'order'               => $index,
            ];

            if (!empty($qData['id'])) {
                $question = QuizQuestion::find((int) $qData['id']);
                if ($question && $question->quiz_id === $quiz->id) {
                    $question->update($questionPayload);
                } else {
                    $question = $quiz->questions()->create($questionPayload);
                }
            } else {
                $question = $quiz->questions()->create($questionPayload);
            }

            $keptIds[] = $question->id;

            if (in_array($question->question_type, ['single', 'multiple', 'true_false'])) {
                $this->syncOptions($question, $qData['options'] ?? []);
            } else {
                $question->options()->delete();
            }
        }

        $quiz->questions()->whereNotIn('id', $keptIds)->each(function ($q) {
            $q->options()->delete();
            $q->delete();
        });
    }

    private function syncOptions(QuizQuestion $question, array $options): void
    {
        $keptIds = [];

        foreach ($options as $optIndex => $optData) {
            if (empty(trim($optData['option_text'] ?? ''))) {
                continue;
            }

            $optionPayload = [
                'option_text'    => $optData['option_text'],
                'option_text_hi' => $optData['option_text_hi'] ?? null,
                'is_correct'     => (bool) ($optData['is_correct'] ?? false),
                'order'          => $optIndex,
            ];

            if (!empty($optData['id'])) {
                $option = QuizQuestionOption::find((int) $optData['id']);
                if ($option && $option->question_id === $question->id) {
                    $option->update($optionPayload);
                    $keptIds[] = $option->id;
                    continue;
                }
            }

            $opt = $question->options()->create($optionPayload);
            $keptIds[] = $opt->id;
        }

        $question->options()->whereNotIn('id', $keptIds)->delete();
    }
}
