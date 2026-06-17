<?php

use Illuminate\Support\Facades\Route;
use Modules\Manjulika\Http\Controllers\ManjulikaController;
use Modules\Manjulika\Http\Controllers\ChatController;
use Modules\Manjulika\Http\Controllers\ChatSessionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('manjulikas', ManjulikaController::class)->names('manjulika');

    // Admin: browse saved chatbot sessions and transcripts.
    Route::get('manjulika/sessions', [ChatSessionController::class, 'index'])->name('manjulika.sessions.index');
    Route::get('manjulika/sessions/{session}', [ChatSessionController::class, 'show'])->name('manjulika.sessions.show');
    Route::delete('manjulika/sessions/{session}', [ChatSessionController::class, 'destroy'])->name('manjulika.sessions.destroy');
});

Route::prefix('cyber-chat')->name('cyber-chat.')->group(function () {
    Route::post('/send', [ChatController::class, 'send'])->name('send');
    Route::get('/history', [ChatController::class, 'history'])->name('history');
    Route::post('/clear', [ChatController::class, 'clear'])->name('clear');
});
