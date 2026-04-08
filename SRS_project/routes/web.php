<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

use Modules\Admin\Controllers\DashboardController;
use Modules\Reports\Controllers\ReportController;
use Modules\Users\Controllers\UserController;
use Modules\Roles\Controllers\RoleController;
use Modules\Projects\Controllers\DepartmentController;



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Reports routes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');

    // Projects routes
    Route::resource('projects', \Modules\Projects\Controllers\ProjectController::class);

    // Departments
    Route::resource('departments', DepartmentController::class);

    // Users and Roles
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
