<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
// Get a studio by code with nested steps -> sections -> choices (filtered by gender if provided)
    Route::get('/studios/{studio:code}', [\App\Http\Controllers\Api\StudioPublicController::class, 'show']);


// Optional endpoints if you want to load per-step lazily
    Route::get('/studios/{studio:code}/steps', [\App\Http\Controllers\Api\StudioPublicController::class, 'steps']);
    Route::get('/steps/{step}/sections', [\App\Http\Controllers\Api\StudioPublicController::class, 'sections']);

    // Generate (NEW)
    Route::post('/studios/{studio:code}/generate', [\App\Http\Controllers\Api\StudioPublicController::class, 'generate']);
});