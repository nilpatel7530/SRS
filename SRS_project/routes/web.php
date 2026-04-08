<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

use Modules\Admin\Controllers\DashboardController;
use Modules\Reports\Controllers\ReportController;



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Reports routes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');

    // Projects routes
    Route::get('projects', [\Modules\Projects\Controllers\ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{project}', [\Modules\Projects\Controllers\ProjectController::class, 'show'])->name('projects.show');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
