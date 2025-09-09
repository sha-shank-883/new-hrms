<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// Test route - remove after testing
Route::get('/test-dashboard', function() {
    return (new DashboardController())->index();
});

// Include leave and attendance routes
require __DIR__.'/web_leave_attendance.php';

// Include employee management routes
require __DIR__.'/web_employee.php';

// Include payroll management routes
require __DIR__.'/web_payroll.php';

// Temporary route to check admin role
Route::get('/check-admin-role', function () {
    $user = \App\Models\User::where('email', 'admin@hrms.local')->first();
    
    if (!$user) {
        return response()->json(['error' => 'Admin user not found'], 404);
    }
    
    return response()->json([
        'user' => $user->name,
        'email' => $user->email,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name')
    ]);
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
    });
});

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin|admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])
        ->name('settings');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])
        ->name('settings.update');
    // Other admin routes will be included here
});

// HR routes
Route::prefix('hr')->name('hr.')->middleware(['auth', 'role:hr_manager'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\HR\DashboardController::class, 'index'])->name('dashboard');
    // Other HR routes will be included here
});

// Department Manager routes
Route::prefix('department')->name('department.')->middleware(['auth', 'role:department_manager'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Department\DashboardController::class, 'index'])->name('dashboard');
    // Other department manager routes will be included here
});

// Employee routes
Route::prefix('employee')->name('employee.')->middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');
    // Other employee routes will be included here
});

// Admin routes group
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', function () {
        // Check if view exists
        if (!\View::exists('admin.dashboard')) {
            return 'Admin dashboard view not found';
        }
        return view('admin.dashboard');
        $totalRoles = \Spatie\Permission\Models\Role::count();
        $totalPermissions = \Spatie\Permission\Models\Permission::count();
        
        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalEmployees' => $totalEmployees,
            'totalDepartments' => $totalDepartments,
            'totalRoles' => $totalRoles,
            'totalPermissions' => $totalPermissions,
        ]);
    })->name('dashboard');

    // Permission Management Routes
    Route::resource('permissions', App\Http\Controllers\PermissionController::class)->names([
        'index' => 'permissions.index',
        'create' => 'permissions.create',
        'store' => 'permissions.store',
        'edit' => 'permissions.edit',
        'update' => 'permissions.update',
        'destroy' => 'permissions.destroy',
    ]);

    // Task Management - Moved to web_management.php
    // Task routes are now managed in web_management.php with proper admin/manager/employee access control

    // User Management Routes
    Route::resource('users', App\Http\Controllers\UserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);

    // Department Management Routes
    Route::resource('departments', App\Http\Controllers\Admin\DepartmentController::class)->names([
        'index' => 'departments.index',
        'create' => 'departments.create',
        'store' => 'departments.store',
        'show' => 'departments.show',
        'edit' => 'departments.edit',
        'update' => 'departments.update',
        'destroy' => 'departments.destroy',
    ]);

    // Role Management Routes
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class)->names([
        'index' => 'roles.index',
        'create' => 'roles.create',
        'store' => 'roles.store',
        'edit' => 'roles.edit',
        'update' => 'roles.update',
        'destroy' => 'roles.destroy',
    ]);
});

// HR Manager routes
Route::middleware(['auth', 'role:hr_manager'])->prefix('hr')->group(function () {
    Route::get('/dashboard', function () {
        return view('hr.dashboard');
    })->name('hr.dashboard');
});

// Department routes
Route::middleware(['auth', 'role:admin|super_admin|department_manager'])
    ->prefix('department')
    ->name('department.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Department\DashboardController::class, 'index'])
            ->name('dashboard');
    });

// Employee routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->group(function () {
    Route::get('/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');
});

// In your routes file (e.g., routes/web.php)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// End of routes
