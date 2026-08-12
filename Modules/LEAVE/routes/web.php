<?php

use Illuminate\Support\Facades\Route;
use Modules\LEAVE\Http\Controllers\LEAVEController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('leaves', LEAVEController::class)->names('leave');
});
