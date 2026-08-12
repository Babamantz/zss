<?php

use Illuminate\Support\Facades\Route;
use Modules\LEAVE\Http\Controllers\LEAVEController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('leaves', LEAVEController::class)->names('leave');
});
