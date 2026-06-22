<?php

use Illuminate\Support\Facades\Route;
use Modules\PayrollFinanceProfileFinanceProfileIndex\Http\Controllers\PayrollFinanceProfileFinanceProfileIndexController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payrollfinanceprofilefinanceprofileindices', PayrollFinanceProfileFinanceProfileIndexController::class)->names('payrollfinanceprofilefinanceprofileindex');
});
