<?php

use Illuminate\Support\Facades\Route;
use Modules\HRM\Livewire\HRM\EmployeeDashboard;
use Modules\HRM\Livewire\HRM\Employees\EmployeeEdit;
use Modules\HRM\Livewire\HRM\Employees\EmployeeIndex;
use Modules\HRM\Livewire\HRM\Employees\EmployeeCreate;
use Modules\HRM\Livewire\Reports\EmployeesReportIndex;

Route::name('hrm.')->prefix('module/hrm')->middleware(['auth'])->group(function () {
    // Route::resource('hrms', HRMController::class)->names('hrm'); 
    Route::get('/dashboard', EmployeeDashboard::class)->name('employees.dashboard');
    Route::get('/employees/index', EmployeeIndex::class)->name('employees.index');
    Route::get('/employees/create', EmployeeCreate::class)->name('employees.create');
    Route::get('/employees/{mode}/{employeeId}/edit', EmployeeEdit::class)->name('employee.edit');
    Route::get('/employees/{mode}/{employeeId}/create', EmployeeCreate::class)->name('employee.view');
    Route::get('/employees/reports', EmployeesReportIndex::class)->name('employee.report');
});
