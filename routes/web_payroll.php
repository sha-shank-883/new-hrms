<?php

use App\Http\Controllers\Admin\PayrollController as AdminPayrollController;
use App\Http\Controllers\Manager\PayrollController as ManagerPayrollController;
use App\Http\Controllers\Employee\PayrollController as EmployeePayrollController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payroll Routes
|--------------------------------------------------------------------------
|
| Here is where you can register payroll routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Admin Payroll Routes
Route::middleware(['auth', 'role:super_admin|admin'])->prefix('admin')->name('admin.')->group(function () {
    // Regular CRUD routes
    Route::resource('payrolls', AdminPayrollController::class);
    
    // Generate payroll for all employees
    Route::post('payrolls/generate', [AdminPayrollController::class, 'generatePayroll'])->name('payrolls.generate');
});

// Manager Payroll Routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    // View payrolls (index and show only)
    Route::get('payrolls', [ManagerPayrollController::class, 'index'])->name('payrolls.index');
    Route::get('payrolls/{payroll}', [ManagerPayrollController::class, 'show'])->name('payrolls.show');
});

// Employee Payroll Routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    // View own payslips
    Route::get('payslips', [EmployeePayrollController::class, 'index'])->name('payslips.index');
    Route::get('payslips/{payroll}', [EmployeePayrollController::class, 'show'])->name('payslips.show');
});