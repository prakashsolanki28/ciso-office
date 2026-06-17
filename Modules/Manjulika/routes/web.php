<?php

use Illuminate\Support\Facades\Route;
use Modules\Manjulika\Http\Controllers\ManjulikaController;
use Modules\Manjulika\Http\Controllers\ChatController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('manjulikas', ManjulikaController::class)->names('manjulika');
});

Route::prefix('cyber-chat')->name('cyber-chat.')->group(function () {
    Route::post('/send', [ChatController::class, 'send'])->name('send');
    Route::get('/history', [ChatController::class, 'history'])->name('history');
    Route::post('/clear', [ChatController::class, 'clear'])->name('clear');
});
