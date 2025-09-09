<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use App\Http\Controllers\Employee\LeaveRequestController as EmployeeLeaveRequestController;
use App\Http\Controllers\Manager\LeaveRequestController as ManagerLeaveRequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Manager\AttendanceController as ManagerAttendanceController;

/*
|--------------------------------------------------------------------------
| Leave and Attendance Routes
|--------------------------------------------------------------------------
*/

// Admin Routes
Route::group(['middleware' => ['auth', 'role:super_admin|admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    // Leave Types
    Route::resource('leave-types', LeaveTypeController::class);
    
    // Leave Requests
    Route::resource('leave-requests', AdminLeaveRequestController::class);
    Route::post('leave-requests/{id}/approve', [AdminLeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{id}/reject', [AdminLeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    
    // Attendances
    Route::resource('attendances', AdminAttendanceController::class);
    Route::get('attendance-report', [AdminAttendanceController::class, 'report'])->name('attendances.report');
    Route::get('attendance-report/export', [AdminAttendanceController::class, 'export'])->name('attendances.report.export');
});

// Manager Routes
Route::group(['middleware' => ['auth', 'role:manager'], 'prefix' => 'manager', 'as' => 'manager.'], function () {
    // Leave Requests
    Route::resource('leave-requests', ManagerLeaveRequestController::class)->only(['index', 'show']);
    Route::post('leave-requests/{id}/approve', [ManagerLeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{id}/reject', [ManagerLeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    
    // Attendances
    Route::resource('attendances', ManagerAttendanceController::class)->only(['index', 'show']);
    Route::get('attendance-report', [ManagerAttendanceController::class, 'report'])->name('attendances.report');
});

// Employee Routes - Moved to web_employee.php to avoid route conflicts