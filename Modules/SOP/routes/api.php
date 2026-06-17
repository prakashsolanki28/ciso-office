<?php

use Illuminate\Support\Facades\Route;
use Modules\SOP\Http\Controllers\SOPController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sops', SOPController::class)->names('sop');
});
