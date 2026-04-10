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
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('can:dashboard.access');
    
    // Reports routes
    Route::middleware('can:reports.access')->group(function() {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
    });

    // Projects & Related routes
    Route::middleware('can:projects.access')->group(function() {
        Route::post('projects/{project}/assign', [\Modules\Projects\Controllers\ProjectController::class, 'assignTeam'])->name('projects.assignTeam');
        Route::post('projects/{project}/capex', [\Modules\Finance\Controllers\FinanceController::class, 'storeCapex'])->name('projects.storeCapex');
        Route::post('projects/{project}/opex', [\Modules\Finance\Controllers\FinanceController::class, 'storeOpex'])->name('projects.storeOpex');
        Route::post('projects/{project}/invoices', [\Modules\Finance\Controllers\FinanceController::class, 'storeInvoice'])->name('projects.storeInvoice');
        Route::post('projects/{project}/bg', [\Modules\Finance\Controllers\FinanceController::class, 'storeBG'])->name('projects.storeBG');
        Route::post('projects/{project}/documents', [\Modules\Documents\Controllers\DocumentController::class, 'store'])->name('documents.store');
        Route::get('documents/{document}/download', [\Modules\Documents\Controllers\DocumentController::class, 'download'])->name('documents.download');
        Route::resource('projects', \Modules\Projects\Controllers\ProjectController::class);
    });

    // Proposals
    Route::middleware('can:proposals.access')->group(function() {
        Route::resource('proposals', \Modules\Projects\Controllers\ProposalController::class);
    });

    // Departments
    Route::resource('departments', DepartmentController::class)->middleware('can:departments.access');

    // Users and Roles Access
    Route::middleware('can:administration.access')->group(function() {
        Route::resource('users', UserController::class)->middleware('can:users.access');
        Route::resource('roles', RoleController::class)->middleware('can:roles.access');
        Route::resource('permissions', \Modules\Roles\Controllers\PermissionController::class)->middleware('can:permissions.access');
    });
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
