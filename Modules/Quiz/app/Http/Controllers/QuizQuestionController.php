<?php

namespace Modules\Quiz\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Quiz\Models\Quiz;
use Modules\Quiz\Models\QuizQuestion;

class QuizQuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('quiz::questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text'           => 'required|string',
            'question_text_hi'        => 'nullable|string',
            'question_type'           => 'required|in:single,multiple,true_false,text',
            'marks'                   => 'nullable|numeric|min:0|max:1000',
            'notes'                   => 'nullable|string|max:5000',
            'correct_text_answer'     => 'nullable|string|max:5000',
            'options'                 => 'nullable|array|max:20',
            'options.*.option_text'   => 'required_with:options|string|max:1000',
            'options.*.option_text_hi' => 'nullable|string|max:1000',
            'options.*.is_correct'    => 'nullable|in:0,1',
        ]);

        $question = $quiz->questions()->create([
            'question_text'       => $validated['question_text'],
            'question_text_hi'    => $validated['question_text_hi'] ?? null,
            'question_type'       => $validated['question_type'],
            'marks'               => isset($validated['marks']) && $validated['marks'] !== '' ? $validated['marks'] : null,
            'notes'               => $validated['notes'] ?? null,
            'correct_text_answer' => $validated['correct_text_answer'] ?? null,
            'order'               => $quiz->questions()->max('order') + 1,
        ]);

        if (in_array($validated['question_type'], ['single', 'multiple', 'true_false'])) {
            foreach ($request->input('options', []) as $i => $opt) {
                if (empty(trim($opt['option_text'] ?? ''))) {
                    continue;
                }
                $question->options()->create([
                    'option_text'    => $opt['option_text'],
                    'option_text_hi' => $opt['option_text_hi'] ?? null,
                    'is_correct'     => (bool) ($opt['is_correct'] ?? false),
                    'order'          => $i,
                ]);
            }
        }

        if ($request->input('action') === 'save_add') {
            return redirect()
                ->route('quiz.questions.create', $quiz)
                ->with('success', 'Question added! Add another one.');
        }

        return redirect()
            ->route('quiz.edit', $quiz)
            ->with('success', 'Question added successfully!');
    }

    public function edit(Quiz $quiz, QuizQuestion $question)
    {
        abort_if($question->quiz_id !== $quiz->id, 404);

        $question->load('options');

        return view('quiz::questions.edit', compact('quiz', 'question'));
    }

    public function update(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        abort_if($question->quiz_id !== $quiz->id, 404);

        $validated = $request->validate([
            'question_text'            => 'required|string',
            'question_text_hi'         => 'nullable|string',
            'question_type'            => 'required|in:single,multiple,true_false,text',
            'marks'                    => 'nullable|numeric|min:0|max:1000',
            'notes'                    => 'nullable|string|max:5000',
            'correct_text_answer'      => 'nullable|string|max:5000',
            'options'                  => 'nullable|array|max:20',
            'options.*.option_text'    => 'required_with:options|string|max:1000',
            'options.*.option_text_hi' => 'nullable|string|max:1000',
            'options.*.is_correct'     => 'nullable|in:0,1',
        ]);

        $question->update([
            'question_text'       => $validated['question_text'],
            'question_text_hi'    => $validated['question_text_hi'] ?? null,
            'question_type'       => $validated['question_type'],
            'marks'               => isset($validated['marks']) && $validated['marks'] !== '' ? $validated['marks'] : null,
            'notes'               => $validated['notes'] ?? null,
            'correct_text_answer' => $validated['correct_text_answer'] ?? null,
        ]);

        $question->options()->delete();

        if (in_array($validated['question_type'], ['single', 'multiple', 'true_false'])) {
            foreach ($request->input('options', []) as $i => $opt) {
                if (empty(trim($opt['option_text'] ?? ''))) {
                    continue;
                }
                $question->options()->create([
                    'option_text'    => $opt['option_text'],
                    'option_text_hi' => $opt['option_text_hi'] ?? null,
                    'is_correct'     => (bool) ($opt['is_correct'] ?? false),
                    'order'          => $i,
                ]);
            }
        }

        return redirect()
            ->route('quiz.edit', $quiz)
            ->with('success', 'Question updated successfully!');
    }

    public function destroy(Quiz $quiz, QuizQuestion $question)
    {
        abort_if($question->quiz_id !== $quiz->id, 404);

        $question->options()->delete();
        $question->delete();

        return back()->with('success', 'Question deleted.');
    }

    public function move(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        abort_if($question->quiz_id !== $quiz->id, 404);

        $request->validate(['direction' => 'required|in:up,down']);

        $direction = $request->input('direction');

        if ($direction === 'up') {
            $swap = $quiz->questions()->where('order', '<', $question->order)->orderByDesc('order')->first();
        } else {
            $swap = $quiz->questions()->where('order', '>', $question->order)->orderBy('order')->first();
        }

        if ($swap) {
            [$question->order, $swap->order] = [$swap->order, $question->order];
            $question->save();
            $swap->save();
        }

        return back();
    }
}
