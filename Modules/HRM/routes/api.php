<?php

use Illuminate\Support\Facades\Route;
use Modules\HRM\Http\Controllers\HRMController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('hrms', HRMController::class)->names('hrm');
});
