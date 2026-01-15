<?php

use Illuminate\Support\Facades\Route;
use Modules\HRM\Http\Controllers\HRMController;
use Modules\HRM\Livewire\HRM\EmployeeDashboard;
use Modules\HRM\Livewire\HRM\Employees\EmployeeIndex;
use Modules\HRM\Livewire\HRM\Employees\EmployeeCreate;
use Modules\HRM\Livewire\HRM\Employees\EmployeeCreateEdit;

Route::name('hrm.')->prefix('module/hrm')->middleware(['auth'])->group(function () {
    // Route::resource('hrms', HRMController::class)->names('hrm'); 
    Route::get('/dashboard', EmployeeDashboard::class)->name('employees.dashboard');
    Route::get('/employees/index', EmployeeIndex::class)->name('employees.index');
    Route::get('/employees/create', EmployeeCreate::class)->name('employees.create');
});
