<?php

use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\IncidentReportController as AdminIncidentController;
use App\Http\Controllers\AwarenessController;
use App\Http\Controllers\Blog\PublicBlogController;
use App\Http\Controllers\CaseStudy\PublicCaseStudyController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Project\PublicProjectController;
use App\Http\Controllers\SOP\PublicSopController;
use Illuminate\Support\Facades\Route;
use Modules\Blog\Models\Blog;
use Modules\Project\Models\Project;

Route::get('/', function () {
    $projects = Project::latest()->take(3)->get();

    $blogs = Blog::with(['author', 'category'])
        ->published()
        ->latest('published_at')
        ->take(6)
        ->get();

    return view('welcome', compact('projects', 'blogs'));
});

// Public incident report
Route::get('/report-incident', [IncidentReportController::class, 'create'])->name('report.incident');
Route::post('/report-incident', [IncidentReportController::class, 'store'])->name('report.incident.store');

// Public blog routes
Route::prefix('blog')->name('blog.public.')->group(function () {
    Route::get('/', [PublicBlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [PublicBlogController::class, 'show'])->name('show');
});

// Public project routes
Route::prefix('our-projects')->name('projects.public.')->group(function () {
    Route::get('/', [PublicProjectController::class, 'index'])->name('index');
    Route::get('/{slug}', [PublicProjectController::class, 'show'])->name('show');
});

// Public case study routes
Route::prefix('case-studies')->name('casestudies.public.')->group(function () {
    Route::get('/', [PublicCaseStudyController::class, 'index'])->name('index');
    Route::get('/{slug}', [PublicCaseStudyController::class, 'show'])->name('show');
});

// Public SOP routes
Route::prefix('sops')->name('sops.public.')->group(function () {
    Route::get('/', [PublicSopController::class, 'index'])->name('index');
});

// Public awareness hub (aggregates published blogs, case studies & newsletters)
Route::get('/awareness', [AwarenessController::class, 'index'])->name('awareness');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/incidents', [AdminIncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/{incident}', [AdminIncidentController::class, 'show'])->name('incidents.show');
    Route::patch('/incidents/{incident}/status', [AdminIncidentController::class, 'updateStatus'])->name('incidents.status');

    // Employee account management
    Route::resource('employees', AdminEmployeeController::class)->except(['show'])->names('employees');
    Route::patch('/employees/{employee}/deactivate', [AdminEmployeeController::class, 'deactivate'])->name('employees.deactivate');
    Route::patch('/employees/{employee}/reset-password', [AdminEmployeeController::class, 'resetPassword'])->name('employees.reset-password');
});

require __DIR__.'/auth.php';
