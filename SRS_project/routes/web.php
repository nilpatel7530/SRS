<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

use Modules\Admin\Controllers\DashboardController;
use Modules\Reports\Controllers\ReportController;
use Modules\Users\Controllers\UserController;
use Modules\Roles\Controllers\RoleController;
use Modules\Projects\Controllers\DepartmentController;
use Modules\Admin\Controllers\BrandingController;



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('can:dashboard.access');
    
    // Reports routes
    Route::middleware('can:reports.access')->group(function() {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/yearwise', [ReportController::class, 'yearwise'])->name('reports.yearwise');
        Route::get('reports/reconciliation', [ReportController::class, 'reconciliation'])->name('reports.reconciliation');
        Route::get('reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
    });

    // Projects & Related routes
    Route::middleware('can:projects.view')->group(function() {
        Route::middleware('can:projects.create')->group(function() {
            Route::get('projects/create', [\Modules\Projects\Controllers\ProjectController::class, 'create'])->name('projects.create');
            Route::post('projects', [\Modules\Projects\Controllers\ProjectController::class, 'store'])->name('projects.store');
        });

        Route::get('projects', [\Modules\Projects\Controllers\ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}', [\Modules\Projects\Controllers\ProjectController::class, 'show'])->name('projects.show');
        
        Route::middleware('can:projects.edit')->group(function() {
            Route::get('projects/{project}/edit', [\Modules\Projects\Controllers\ProjectController::class, 'edit'])->name('projects.edit');
            Route::put('projects/{project}', [\Modules\Projects\Controllers\ProjectController::class, 'update'])->name('projects.update');
            Route::post('projects/{project}/assign', [\Modules\Projects\Controllers\ProjectController::class, 'assignTeam'])->name('projects.assignTeam');
            Route::post('projects/{project}/capex', [\Modules\Finance\Controllers\FinanceController::class, 'storeCapex'])->name('projects.storeCapex');
            Route::post('projects/{project}/opex', [\Modules\Finance\Controllers\FinanceController::class, 'storeOpex'])->name('projects.storeOpex');
            Route::post('projects/{project}/invoices', [\Modules\Finance\Controllers\FinanceController::class, 'storeInvoice'])->name('projects.storeInvoice');
            Route::post('projects/{project}/invoices/{invoice}/payment', [\Modules\Finance\Controllers\FinanceController::class, 'updatePayment'])->name('projects.updateInvoicePayment');
            Route::post('projects/{project}/bg', [\Modules\Finance\Controllers\FinanceController::class, 'storeBG'])->name('projects.storeBG');
            Route::post('projects/{project}/target', [\Modules\Finance\Controllers\FinanceController::class, 'storeTarget'])->name('projects.storeTarget');
        });

        Route::middleware('can:projects.delete')->group(function() {
            Route::delete('projects/{project}', [\Modules\Projects\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');
        });
        
        Route::post('projects/{project}/documents', [\Modules\Documents\Controllers\DocumentController::class, 'store'])->name('documents.store');
        Route::get('documents/{document}/download', [\Modules\Documents\Controllers\DocumentController::class, 'download'])->name('documents.download');
    });

    // Proposals
    Route::middleware('can:proposals.access')->group(function() {
        Route::resource('proposals', \Modules\Projects\Controllers\ProposalController::class);
    });
    
    // Finance Routes
    Route::middleware('can:finance.access')->group(function() {
        Route::get('finance/invoices', [\Modules\Finance\Controllers\FinanceController::class, 'index'])->name('finance.invoices.index');
    });

    // Departments
    Route::resource('departments', DepartmentController::class)->middleware('can:departments.access');

    // Users and Roles Access
    Route::middleware('can:administration.access')->group(function() {
        Route::resource('users', UserController::class)->middleware('can:users.access');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus')->middleware('can:users.access');
        Route::resource('roles', RoleController::class)->middleware('can:roles.access');
        Route::resource('permissions', \Modules\Roles\Controllers\PermissionController::class)->middleware('can:permissions.access');
        
        // Branding Settings
        Route::get('branding', [BrandingController::class, 'index'])->name('admin.branding.index');
        Route::post('branding', [BrandingController::class, 'update'])->name('admin.branding.update');
        Route::post('branding/fix-storage', [BrandingController::class, 'fixStorageLink'])->name('admin.branding.fixStorage');
    });
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
