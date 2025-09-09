<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;

/*
|--------------------------------------------------------------------------
| Management Routes
|--------------------------------------------------------------------------
*/

// Admin Routes
Route::group(['middleware' => ['auth', 'role:super_admin|admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    // Department Management
    Route::resource('departments', DepartmentController::class);
    
    // Task Management
    Route::resource('tasks', AdminTaskController::class);
    Route::post('tasks/{task}/assign', [AdminTaskController::class, 'assign'])->name('tasks.assign');
    Route::post('tasks/{task}/complete', [AdminTaskController::class, 'markComplete'])->name('tasks.complete');
});

// Manager Routes
Route::group(['middleware' => ['auth', 'role:manager'], 'prefix' => 'manager', 'as' => 'manager.'], function () {
    // Department Views
    Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
    
    // Task Management
    Route::resource('tasks', AdminTaskController::class)->except(['destroy']);
    Route::post('tasks/{task}/complete', [AdminTaskController::class, 'markComplete'])->name('tasks.complete');
});

// Employee Routes
Route::group(['middleware' => ['auth', 'role:employee'], 'prefix' => 'employee', 'as' => 'employee.'], function () {
    // Task Views
    Route::get('tasks', [AdminTaskController::class, 'myTasks'])->name('tasks.mine');
    Route::get('tasks/{task}', [AdminTaskController::class, 'show'])->name('tasks.show');
    Route::post('tasks/{task}/complete', [AdminTaskController::class, 'markComplete'])->name('tasks.complete');
});
