<?php

use App\Http\Controllers\Attendance\AttendanceController;
use App\Livewire\Auth\Login;
use App\Livewire\Chain\ApprovalQueue;
use App\Livewire\Chain\ChainTransactionCreate;
use App\Livewire\Chain\TransactionTimeline;
use App\Livewire\Core\Admin\Reports\UserReportIndex;
use App\Livewire\Core\Admin\Role\RoleCreate;
use App\Livewire\Core\Admin\Role\RoleIndex;
use App\Livewire\Core\Admin\Users\PassswordReset;
use App\Livewire\Core\Admin\Users\UserCreate;
use App\Livewire\Core\Admin\Users\UserEdit;
use App\Livewire\Core\Admin\Users\UserIndex;
use App\Livewire\Core\Attendance\AttendanceCreateEdit;
use App\Livewire\Core\Attendance\AttendanceIndex;
use App\Livewire\Core\Document\DocumentCreateEdit;
use App\Livewire\Core\Document\DocumentIndex;
use App\Livewire\Core\Index;
use App\Livewire\Core\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\PAYROLL\Http\Controllers\Payroll\Report\Pdf\PayrollListPdfController;
use Modules\PAYROLL\Http\Controllers\Payroll\Report\Pdf\PayslipPdfController;
use Modules\PAYROLL\Livewire\Dashboard;
use Modules\PAYROLL\Livewire\Payroll\Employee\EmployeeComponentIndex;
use Modules\PAYROLL\Livewire\Payroll\FinanceProfile\FinanceProfileIndex;
use Modules\PAYROLL\Livewire\Payroll\PayPeriod\PayPeriodIndex;
use Modules\PAYROLL\Livewire\Payroll\PayrollEntries\PayrollEntryIndex;
use Modules\PAYROLL\Livewire\Payroll\PayrollEntries\PayrollEntryShow;
use Modules\PAYROLL\Livewire\Payroll\Reports\Deduction\DeductionReportIndex;
use Modules\PAYROLL\Livewire\Payroll\RunPayroll;
use Modules\PAYROLL\Livewire\Payroll\Setups\SalaryComponentIndex;

Route::get('/', Login::class)->name('login');

// Protected routes (auth + tenant)
Route::middleware(['auth'])->group(function () {
    Route::get('/index', Index::class)->name('index');
    Route::get('/profile', UserProfile::class)->name('profile');
    Route::get('/attendance/preview/{id}', [AttendanceController::class, 'preview'])
        ->name('attendance.preview');

    Route::prefix('chain')->name('chain.')->group(function () {
        Route::get(
            'transaction/create',
            ChainTransactionCreate::class
        )->name('transaction.create');
        Route::get(
            'queue',
            ApprovalQueue::class
        )->name('queue');

        Route::get(
            'transaction/{transactionId}/timeline',
            TransactionTimeline::class
        )->name('transaction.timeline');
    });
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/index', DocumentIndex::class)->name('index');
        Route::get('/create', DocumentCreateEdit::class)->name('create');
        Route::get('/edit/{documentId}/{mode?}', DocumentCreateEdit::class)->name('edit');
    });

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/index', AttendanceIndex::class)->name('index');
        Route::get('/create', AttendanceCreateEdit::class)->name('create');
        Route::get('/edit/{attendanceId}/{mode?}', AttendanceCreateEdit::class)->name('edit');
    });

    Route::prefix('module/payroll')->name('payroll.')->group(function () {

        Route::get('dashboard',        Dashboard::class)
            ->name('dashboard');
        Route::get('components',        SalaryComponentIndex::class)
            ->name('components');

        Route::get('pay-periods',       PayPeriodIndex::class)
            ->name('pay-periods');

        Route::get('employee-components', EmployeeComponentIndex::class)
            ->name('employee-components');
        Route::get('finance-profiles', FinanceProfileIndex::class)
            ->name('finance-profiles');

        Route::get('run/{period?}',     RunPayroll::class)
            ->name('run');

        Route::get('entries',          PayrollEntryIndex::class)
            ->name('entries');

        Route::get('entries/{entry}', PayrollEntryShow::class)
            ->name('entry.show');
        // routes/web.php

        Route::get(
            'payroll/period/{period}/list-pdf',
            [PayrollListPdfController::class, 'view']
        )
            ->name('period.list-pdf');
        // routes/web.php — inside your payroll route group

        Route::get(
            'entries/{entry}/payslip-pdf',
            [PayslipPdfController::class, 'view']
        )
            ->name('entry.payslip-pdf');
        Route::get(
            'reports/deductions',DeductionReportIndex::class
        )
            ->name('reports.deductions');
    });

    Route::get('/users', UserIndex::class)->name('users.index');
    Route::get('/users/create', UserCreate::class)->name('users.create');
    Route::get('/users/{id?}/{mode?}/edit', UserEdit::class)->name('users.edit');
    Route::get('/roles', RoleIndex::class)->name('roles.index');
    Route::get('/roles/create', RoleCreate::class)->name('roles.create');
    Route::get('/password/reset', PassswordReset::class)->name('password.reset');
    Route::get('/roles/{roleName}/permissions', RoleCreate::class)->name('roles.edit');
    Route::get('/report/users', UserReportIndex::class)->name('user.report.index');

    // Add logout route
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Auth::logout(); // Logs out the currently authenticated user

        $request->session()->invalidate(); // Invalidates the current session
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Logged Out');
    })->name('logout');
});
