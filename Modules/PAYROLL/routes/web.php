<?php

use Illuminate\Support\Facades\Route;
use Modules\PAYROLL\Http\Controllers\PAYROLLController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('payrolls', PAYROLLController::class)->names('payroll');
});
