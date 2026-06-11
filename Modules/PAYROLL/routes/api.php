<?php

use Illuminate\Support\Facades\Route;
use Modules\PAYROLL\Http\Controllers\PAYROLLController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payrolls', PAYROLLController::class)->names('payroll');
});
