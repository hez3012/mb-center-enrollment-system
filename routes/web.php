<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::middleware(['auth', 'role:directress,admin,teacher,staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Profile Settings
        Route::get('/profile',            [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile',            [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');

        // User Management
        Route::middleware(['permission:create_user,edit_user,deactivate_user,view_user'])
            ->prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/',              [UserController::class, 'index'])->name('index');
                Route::get('/create',        [UserController::class, 'create'])->name('create');
                Route::post('/',             [UserController::class, 'store'])->name('store');
                Route::get('/{id}/edit',     [UserController::class, 'edit'])->name('edit');
                Route::put('/{id}',          [UserController::class, 'update'])->name('update');
                Route::patch('/{id}/toggle', [UserController::class, 'toggle'])->name('toggle');
            });
    });

Route::middleware(['auth', 'role:guardian'])
    ->prefix('portal')
    ->name('portal.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('portal.dashboard');
        })->name('dashboard');
    });

Route::fallback(function () {
    return redirect()->route('login');
});