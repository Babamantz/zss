<?php

use App\Livewire\Auth\Login;
use App\Livewire\Core\Admin\Role\RoleCreate;
use App\Livewire\Core\Admin\Role\RoleIndex;
use App\Livewire\Core\Admin\Users\PassswordReset;
use App\Livewire\Core\Admin\Users\UserCreate;
use App\Livewire\Core\Admin\Users\UserEdit;
use App\Livewire\Core\Index;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Core\Admin\Users\UserIndex;


Route::get('/', Login::class)->name('login');

// Protected routes (auth + tenant)
Route::middleware(['auth'])->group(function () {
    Route::get('/index', Index::class)->name('index');
    Route::get('/users', UserIndex::class)->name('users.index');
    Route::get('/users/create', UserCreate::class)->name('users.create');
    Route::get('/users/{id?}/{mode?}/edit', UserEdit::class)->name('users.edit');
    Route::get('/roles', RoleIndex::class)->name('roles.index');
    Route::get('/roles/create', RoleCreate::class)->name('roles.create');
    Route::get('/password/reset', PassswordReset::class)->name('password.reset');
    Route::get('/roles/{roleName}/permissions', RoleCreate::class)->name('roles.edit');

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
