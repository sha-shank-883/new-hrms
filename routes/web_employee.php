<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Employee\EmployeeController as EmployeeProfileController;
use App\Http\Controllers\Manager\EmployeeController as ManagerEmployeeController;

/*
|--------------------------------------------------------------------------
| Employee Management Routes
|--------------------------------------------------------------------------
*/

// Admin Routes for Employee Management
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin|admin'])->group(function () {
    Route::resource('employees', AdminEmployeeController::class);
});

// Manager Routes for Employee Management
Route::prefix('manager')->name('manager.')->middleware(['auth', 'role:manager'])->group(function () {
    Route::resource('employees', ManagerEmployeeController::class)->only(['index', 'show']);
});

// Employee Routes for Profile Management
Route::prefix('employee')->name('employee.')->middleware(['auth', 'role:employee'])->group(function () {
    // Profile routes
    Route::get('profile', [EmployeeProfileController::class, 'profile'])->name('profile.index');
    Route::get('profile/edit', [EmployeeProfileController::class, 'editProfile'])->name('profile.edit');
    Route::put('profile', [EmployeeProfileController::class, 'updateProfile'])->name('profile.update');
    
    // Leave Requests routes (using resourceful naming convention with underscores)
    Route::prefix('leave-requests')->name('leave_requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'cancel'])->name('cancel');
    });
    
    // Attendance routes
    Route::prefix('attendances')->name('attendances.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\AttendanceController::class, 'index'])->name('index');
        
        // Check-in routes
        Route::get('/check-in', [\App\Http\Controllers\Employee\AttendanceController::class, 'showCheckInForm'])->name('check-in.form');
        Route::post('/check-in', [\App\Http\Controllers\Employee\AttendanceController::class, 'checkIn'])->name('check-in');
        
        // Check-out routes
        Route::get('/check-out', [\App\Http\Controllers\Employee\AttendanceController::class, 'showCheckOutForm'])->name('check-out.form');
        Route::post('/check-out', [\App\Http\Controllers\Employee\AttendanceController::class, 'checkOut'])->name('check-out');
    });
});