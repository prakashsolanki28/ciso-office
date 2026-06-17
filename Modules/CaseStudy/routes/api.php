<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseStudy\Http\Controllers\CaseStudyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('casestudies', CaseStudyController::class)->names('casestudy');
});
