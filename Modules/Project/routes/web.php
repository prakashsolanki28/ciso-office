<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Http\Controllers\ProjectController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('projects', ProjectController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy'])
        ->names('project');

    Route::post('projects/{project}/banner', [ProjectController::class, 'uploadBanner'])
        ->name('project.banner.upload');

    Route::post('projects/{project}/account-logo', [ProjectController::class, 'uploadAccountLogo'])
        ->name('project.account-logo.upload');

    Route::post('projects/{project}/gallery-image', [ProjectController::class, 'uploadGalleryImage'])
        ->name('project.gallery-image.upload');
});
