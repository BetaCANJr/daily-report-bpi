<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Auth Routes (from Breeze)
require __DIR__.'/auth.php';

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    
    // Reports API - TAMBAHKAN INI
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/chart-data', [DailyReportController::class, 'chartData'])->name('chart-data');
    });
});
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Daily Reports - Resource Routes with additional custom routes
    Route::resource('reports', DailyReportController::class)->except(['show']);
    
    // Additional report routes
    Route::get('/reports/calendar', [DailyReportController::class, 'calendar'])->name('reports.calendar');
    Route::get('/reports/export', [DailyReportController::class, 'export'])->name('reports.export');
    Route::post('/reports/bulk-action', [DailyReportController::class, 'bulkAction'])->name('reports.bulk-action');
    Route::get('/reports/statistics', [DailyReportController::class, 'statistics'])->name('reports.statistics');
    
    // Dashboard cache management
    Route::post('/dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    
    // Reports Management - Using admin.reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
        Route::get('/analytics', [AdminController::class, 'reportAnalytics'])->name('analytics');
        Route::get('/export', [AdminReportController::class, 'export'])->name('export');
        Route::post('/bulk-action', [AdminReportController::class, 'bulkAction'])->name('bulk-action');
        
        // Individual report operations
        Route::get('/{report}', [AdminReportController::class, 'show'])->name('show');
        Route::put('/{report}', [AdminReportController::class, 'update'])->name('update');
        Route::delete('/{report}', [AdminReportController::class, 'destroy'])->name('destroy');
    });
    
    // Legacy reports route for compatibility - admin.reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
    
    // User Management - Using admin.users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        
        // Individual user operations
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        
        // Additional user management routes
        Route::put('/{user}/role', [UserController::class, 'updateRole'])->name('update-role');
        Route::put('/{user}/status', [UserController::class, 'updateStatus'])->name('update-status');
        Route::post('/{user}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('/{user}/force', [UserController::class, 'forceDelete'])->name('force-delete');
        Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/{user}/reports', [UserController::class, 'userReports'])->name('reports');
        Route::get('/{user}/activity', [UserController::class, 'userActivity'])->name('activity');
    });
    
    // Legacy user route for compatibility - admin.users
    Route::get('/users', [UserController::class, 'index'])->name('users');
    
    // System Management Routes
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/stats', [AdminController::class, 'systemStats'])->name('stats');
        Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear-cache');
        Route::get('/logs', [AdminController::class, 'systemLogs'])->name('logs');
        Route::get('/backup', [AdminController::class, 'backup'])->name('backup');
    });
    
    // Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AdminController::class, 'analytics'])->name('index');
        Route::get('/reports', [AdminController::class, 'reportAnalytics'])->name('reports');
        Route::get('/users', [AdminController::class, 'userAnalytics'])->name('users');
    });
});

// API Routes for AJAX requests
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    
    // Reports API
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/chart-data', [DailyReportController::class, 'chartData'])->name('chart-data');
    });
    
    // Admin API routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/reports/statistics', [AdminReportController::class, 'getStatistics'])->name('reports.statistics');
        Route::get('/users/activity', [UserController::class, 'getActivityData'])->name('users.activity');
    });
});

// Fallback route
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});