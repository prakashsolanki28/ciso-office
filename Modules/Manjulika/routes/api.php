<?php

use Illuminate\Support\Facades\Route;
use Modules\Manjulika\Http\Controllers\ManjulikaController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('manjulikas', ManjulikaController::class)->names('manjulika');
});
