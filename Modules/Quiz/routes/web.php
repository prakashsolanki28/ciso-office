<?php

use Illuminate\Support\Facades\Route;
use Modules\Quiz\Http\Controllers\EmployeeQuizController;
use Modules\Quiz\Http\Controllers\QuizAttemptController;
use Modules\Quiz\Http\Controllers\QuizController;
use Modules\Quiz\Http\Controllers\QuizQuestionController;

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('quizzes', QuizController::class)->names('quiz');
    Route::post('quizzes/{quiz}/banner', [QuizController::class, 'bannerUpload'])->name('quiz.banner.upload');

    Route::prefix('quizzes/{quiz}/questions')->name('quiz.questions.')->group(function () {
        Route::get('create', [QuizQuestionController::class, 'create'])->name('create');
        Route::post('/', [QuizQuestionController::class, 'store'])->name('store');
        Route::get('{question}/edit', [QuizQuestionController::class, 'edit'])->name('edit');
        Route::patch('{question}', [QuizQuestionController::class, 'update'])->name('update');
        Route::delete('{question}', [QuizQuestionController::class, 'destroy'])->name('destroy');
        Route::post('{question}/move', [QuizQuestionController::class, 'move'])->name('move');
    });
});

// Employee quiz-taking
Route::middleware(['auth', 'verified', 'quiz.employee'])
    ->prefix('my-quizzes')->name('quiz.take.')->group(function () {
        Route::get('available', [EmployeeQuizController::class, 'index'])->name('index');
        Route::get('{quiz}/intro', [EmployeeQuizController::class, 'intro'])->name('intro');
        Route::post('{quiz}/start', [QuizAttemptController::class, 'start'])->name('start');

        Route::get('attempts/{attempt}', [QuizAttemptController::class, 'show'])->name('show');
        Route::post('attempts/{attempt}/answer', [QuizAttemptController::class, 'answer'])->name('answer');
        Route::post('attempts/{attempt}/submit', [QuizAttemptController::class, 'submit'])->name('submit');
        Route::get('attempts/{attempt}/result', [QuizAttemptController::class, 'result'])->name('result');
        Route::get('attempts/{attempt}/review', [QuizAttemptController::class, 'review'])->name('review');
    });
