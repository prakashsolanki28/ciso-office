<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\BlogController;
use Modules\Blog\Http\Controllers\CategoryController;
use Modules\Blog\Http\Controllers\TagController;

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('blogs', BlogController::class)
        ->except(['create', 'show'])
        ->names('blog');

    Route::post('blogs/{blog}/banner', [BlogController::class, 'uploadBanner'])
        ->name('blog.banner.upload');

    // AJAX helpers for the editor UI
    Route::prefix('blog-categories')->name('blog.categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
    });

    Route::prefix('blog-tags')->name('blog.tags.')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('index');
        Route::post('/', [TagController::class, 'store'])->name('store');
    });
});
