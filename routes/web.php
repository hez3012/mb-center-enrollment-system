<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Portal\ProfileController     as PortalProfileController;
use App\Http\Controllers\Portal\DashboardController   as PortalDashboardController;
use App\Http\Controllers\Portal\EnrollmentController  as PortalEnrollmentController;

Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // ── Admin routes ──────────────────────────────────────────
    Route::prefix('admin')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        // Profile Settings
        Route::get('/profile',    [ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::put('/profile',    [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::delete('/profile', [ProfileController::class, 'deactivate'])->name('admin.profile.deactivate');

        // ── User Management ────────────────────────────────────
        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:view_user')->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])
            ->middleware('permission:create_user')->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:create_user')->name('admin.users.store');
        Route::get('/users/{id}', [UserController::class, 'show'])
            ->middleware('permission:view_user')->name('admin.users.show');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])
            ->middleware('permission:edit_user')->name('admin.users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])
            ->middleware('permission:edit_user')->name('admin.users.update');
        Route::patch('/users/{id}/toggle', [UserController::class, 'toggle'])
            ->middleware('permission:edit_user')->name('admin.users.toggle');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])
            ->middleware('permission:delete_user')->name('admin.users.destroy');

        // ── Guardian Management ────────────────────────────────
        Route::get('/guardians', [GuardianController::class, 'index'])
            ->middleware('permission:view_guardian')->name('admin.guardians.index');
        Route::get('/guardians/{id}', [GuardianController::class, 'show'])
            ->middleware('permission:view_guardian')->name('admin.guardians.show');
        Route::get('/guardians/{id}/edit', [GuardianController::class, 'edit'])
            ->middleware('permission:edit_guardian')->name('admin.guardians.edit');
        Route::put('/guardians/{id}', [GuardianController::class, 'update'])
            ->middleware('permission:edit_guardian')->name('admin.guardians.update');

        // ── Student Management ─────────────────────────────────
        Route::get('/students', [StudentController::class, 'index'])
            ->middleware('permission:view_student')->name('admin.students.index');
        Route::get('/students/create', [StudentController::class, 'create'])
            ->middleware('permission:create_student')->name('admin.students.create');
        Route::post('/students', [StudentController::class, 'store'])
            ->middleware('permission:create_student')->name('admin.students.store');
        Route::get('/students/{id}', [StudentController::class, 'show'])
            ->middleware('permission:view_student')->name('admin.students.show');
        Route::get('/students/{id}/edit', [StudentController::class, 'edit'])
            ->middleware('permission:edit_student')->name('admin.students.edit');
        Route::put('/students/{id}', [StudentController::class, 'update'])
            ->middleware('permission:edit_student')->name('admin.students.update');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])
            ->middleware('permission:delete_student')->name('admin.students.destroy');

        // ── Enrollment Management ──────────────────────────────
        // Enrollment Management
        Route::get('/enrollments', [EnrollmentController::class, 'index'])
            ->middleware('permission:view_enrollment')
            ->name('admin.enrollments.index');

        Route::get('/enrollments/create', [EnrollmentController::class, 'create'])
            ->middleware('permission:create_walkin_enrollment')
            ->name('admin.enrollments.create');

        Route::post('/enrollments', [EnrollmentController::class, 'store'])
            ->middleware('permission:create_walkin_enrollment')
            ->name('admin.enrollments.store');

        Route::get('/enrollments/{id}', [EnrollmentController::class, 'show'])
            ->middleware('permission:view_enrollment')
            ->name('admin.enrollments.show');

        Route::get('/enrollments/{id}/edit', [EnrollmentController::class, 'edit'])
            ->middleware('permission:edit_enrollment')
            ->name('admin.enrollments.edit');

        Route::put('/enrollments/{id}', [EnrollmentController::class, 'update'])
            ->middleware('permission:edit_enrollment')
            ->name('admin.enrollments.update');

        Route::patch('/enrollments/{id}/approve', [EnrollmentController::class, 'approve'])
            ->middleware('permission:approve_enrollment')
            ->name('admin.enrollments.approve');

        Route::patch('/enrollments/{id}/reject', [EnrollmentController::class, 'reject'])
            ->middleware('permission:approve_enrollment')
            ->name('admin.enrollments.reject');

        Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy'])
            ->middleware('permission:delete_enrollment')
            ->name('admin.enrollments.destroy');
    });

    // ── Guardian Portal routes ─────────────────────────────────
    Route::prefix('portal')->group(function () {
        Route::get('/dashboard', [PortalDashboardController::class, 'index'])->name('portal.dashboard');
        Route::get('/profile',   [PortalProfileController::class, 'edit'])->name('portal.profile.edit');
        Route::put('/profile',   [PortalProfileController::class, 'update'])->name('portal.profile.update');

        Route::get('/enrollments', [PortalEnrollmentController::class, 'index'])->name('portal.enrollments.index');
        Route::get('/enrollments/create', [PortalEnrollmentController::class, 'create'])->name('portal.enrollments.create');
        Route::post('/enrollments', [PortalEnrollmentController::class, 'store'])->name('portal.enrollments.store');
        Route::get('/enrollments/{id}', [PortalEnrollmentController::class, 'show'])->name('portal.enrollments.show');
    });
});
