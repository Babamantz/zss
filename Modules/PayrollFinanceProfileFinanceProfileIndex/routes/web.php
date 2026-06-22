<?php

use Illuminate\Support\Facades\Route;
use Modules\PayrollFinanceProfileFinanceProfileIndex\Http\Controllers\PayrollFinanceProfileFinanceProfileIndexController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('payrollfinanceprofilefinanceprofileindices', PayrollFinanceProfileFinanceProfileIndexController::class)->names('payrollfinanceprofilefinanceprofileindex');
});
