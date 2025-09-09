<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/test-dashboard', [DashboardController::class, 'index']);
