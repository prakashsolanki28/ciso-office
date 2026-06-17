<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseStudy\Http\Controllers\CaseStudyController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('casestudies', CaseStudyController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy'])
        ->names('casestudy');

    Route::post('casestudies/{casestudy}/banner', [CaseStudyController::class, 'uploadBanner'])
        ->name('casestudy.banner.upload');
});
