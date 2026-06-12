<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController     as AdminProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Portal\DashboardController  as PortalDashboardController;
use App\Http\Controllers\Portal\ProfileController    as PortalProfileController;
use App\Http\Controllers\Portal\EnrollmentController as PortalEnrollmentController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Portal\ActivityController;

/*
|--------------------------------------------------------------------------
| Root — redirect based on auth state to prevent redirect loops
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role?->role_name;
        if ($role === 'guardian') {
            return redirect()->route('portal.dashboard');
        }
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (no guest middleware — handled in controller)
|--------------------------------------------------------------------------
*/
Route::get('/login',     [AuthController::class,     'showLogin'])->name('login');
Route::post('/login',    [AuthController::class,     'login'])->name('login.post');
Route::post('/logout',   [AuthController::class,     'logout'])->name('logout');
Route::get('/register',  [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile Settings
    Route::get('/profile', [AdminProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])
        ->name('profile.update');
    
    Route::get('/audit-log', [AuditLogController::class, 'index'])
        ->middleware('permission:view_audit_log')
        ->name('audit-log.index');

    // ----------------------------------------------------------------
    // User Management
    // ----------------------------------------------------------------
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:view_user')
        ->name('users.index');

    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:create_user')
        ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:create_user')
        ->name('users.store');

    Route::get('/users/{id}', [UserController::class, 'show'])
        ->middleware('permission:view_user')
        ->name('users.show');

    Route::get('/users/{id}/edit', [UserController::class, 'edit'])
        ->middleware('permission:edit_user')
        ->name('users.edit');

    Route::put('/users/{id}', [UserController::class, 'update'])
        ->middleware('permission:edit_user')
        ->name('users.update');

    Route::patch('/users/{id}/toggle', [UserController::class, 'toggle'])
        ->middleware('permission:deactivate_user')
        ->name('users.toggle');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])
        ->middleware('permission:edit_user')
        ->name('users.destroy');

    // ----------------------------------------------------------------
    // Guardian Management
    // ----------------------------------------------------------------
    Route::get('/guardians', [GuardianController::class, 'index'])
        ->middleware('permission:view_guardian')
        ->name('guardians.index');

    Route::get('/guardians/{id}', [GuardianController::class, 'show'])
        ->middleware('permission:view_guardian')
        ->name('guardians.show');

    Route::get('/guardians/{id}/edit', [GuardianController::class, 'edit'])
        ->middleware('permission:edit_guardian')
        ->name('guardians.edit');

    Route::put('/guardians/{id}', [GuardianController::class, 'update'])
        ->middleware('permission:edit_guardian')
        ->name('guardians.update');

    // ----------------------------------------------------------------
    // Student Management
    // ----------------------------------------------------------------
    Route::get('/students', [StudentController::class, 'index'])
        ->middleware('permission:view_student')
        ->name('students.index');

    Route::get('/students/create', [StudentController::class, 'create'])
        ->middleware('permission:create_student')
        ->name('students.create');

    Route::post('/students', [StudentController::class, 'store'])
        ->middleware('permission:create_student')
        ->name('students.store');

    Route::get('/students/{id}', [StudentController::class, 'show'])
        ->middleware('permission:view_student')
        ->name('students.show');

    Route::get('/students/{id}/edit', [StudentController::class, 'edit'])
        ->middleware('permission:edit_student')
        ->name('students.edit');

    Route::put('/students/{id}', [StudentController::class, 'update'])
        ->middleware('permission:edit_student')
        ->name('students.update');

    Route::delete('/students/{id}', [StudentController::class, 'destroy'])
        ->middleware('permission:delete_student')
        ->name('students.destroy');

    // ----------------------------------------------------------------
    // Enrollment Management
    // ----------------------------------------------------------------
    Route::get('/enrollments', [EnrollmentController::class, 'index'])
        ->middleware('permission:view_enrollment')
        ->name('enrollments.index');

    Route::get('/enrollments/create', [EnrollmentController::class, 'create'])
        ->middleware('permission:create_walkin_enrollment')
        ->name('enrollments.create');

    Route::post('/enrollments', [EnrollmentController::class, 'store'])
        ->middleware('permission:create_walkin_enrollment')
        ->name('enrollments.store');

    Route::get('/enrollments/{id}', [EnrollmentController::class, 'show'])
        ->middleware('permission:view_enrollment')
        ->name('enrollments.show');

    Route::get('/enrollments/{id}/edit', [EnrollmentController::class, 'edit'])
        ->middleware('permission:edit_enrollment')
        ->name('enrollments.edit');

    Route::put('/enrollments/{id}', [EnrollmentController::class, 'update'])
        ->middleware('permission:edit_enrollment')
        ->name('enrollments.update');

    Route::patch('/enrollments/{id}/approve', [EnrollmentController::class, 'approve'])
        ->middleware('permission:approve_enrollment')
        ->name('enrollments.approve');

    Route::patch('/enrollments/{id}/reject', [EnrollmentController::class, 'reject'])
        ->middleware('permission:approve_enrollment')
        ->name('enrollments.reject');

    Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy'])
        ->middleware('permission:delete_enrollment')
        ->name('enrollments.destroy');

    // ----------------------------------------------------------------
    // Payment Management
    // ----------------------------------------------------------------
    Route::get('/enrollments/{id}/payment', [PaymentController::class, 'create'])
        ->middleware('permission:record_payment')
        ->name('enrollments.payment.create');

    Route::post('/enrollments/{id}/payment', [PaymentController::class, 'store'])
        ->middleware('permission:record_payment')
        ->name('enrollments.payment.store');
});

/*
|--------------------------------------------------------------------------
| Guardian Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('portal')->name('portal.')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [PortalDashboardController::class, 'index'])
        ->name('dashboard');

    // Profile Settings
    Route::get('/profile', [PortalProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile', [PortalProfileController::class, 'update'])
        ->name('profile.update');

    // Enrollments
    Route::get('/enrollments', [PortalEnrollmentController::class, 'index'])
        ->name('enrollments.index');

    Route::get('/enrollments/create', [PortalEnrollmentController::class, 'create'])
        ->name('enrollments.create');

    Route::post('/enrollments', [PortalEnrollmentController::class, 'store'])
        ->name('enrollments.store');

    Route::get('/enrollments/{id}', [PortalEnrollmentController::class, 'show'])
        ->name('enrollments.show');

    Route::get('/my-activity', [ActivityController::class, 'index'])
        ->name('activity.index');
});