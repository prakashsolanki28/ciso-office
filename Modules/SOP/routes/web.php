<?php

use Illuminate\Support\Facades\Route;
use Modules\SOP\Http\Controllers\SOPController;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::resource('sops', SOPController::class)->except(['show', 'create', 'edit'])->names('sop');

    Route::get('sops/{sop}/download', [SOPController::class, 'download'])->name('sop.download');
});
