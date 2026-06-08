<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Admin routes (directress, admin, teacher, staff)
Route::middleware(['auth', 'role:directress,admin,teacher,staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });

// Guardian portal routes
Route::middleware(['auth', 'role:guardian'])
    ->prefix('portal')
    ->name('portal.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('portal.dashboard');
        })->name('dashboard');
    });

// Catch all undefined routes
Route::fallback(function () {
    return redirect()->route('login');
});