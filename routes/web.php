<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinanceReportController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Redirect root to dashboard
    Route::get('/', fn() => redirect('/dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::middleware('can:view users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
    Route::middleware('can:create users')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('can:edit users')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    Route::middleware('can:delete users')->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Role Management
    Route::middleware('can:view roles')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });
    Route::middleware('can:edit roles')->group(function () {
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    // Settings (superadmin only)
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Activity Log
    Route::middleware('can:view activity-log')->group(function () {
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });

    // Finance
    Route::resource('wallets', WalletController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('transactions', TransactionController::class)->except(['show']);
    Route::get('reports', [FinanceReportController::class, 'index'])->name('reports.index');
});

// PWA manifest (dynamic)
Route::get('/manifest.json', function () {
    return response()->json([
        'name' => setting('app_name', 'Aplikatif Admin'),
        'short_name' => setting('pwa_short_name', 'Aplikatif'),
        'description' => 'Admin panel ' . setting('app_name', 'Aplikatif'),
        'start_url' => '/dashboard',
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => setting('pwa_theme_color', '#4F46E5'),
        'orientation' => 'portrait-primary',
        'icons' => [
            ['src' => '/icons/icon-72.png', 'sizes' => '72x72', 'type' => 'image/png'],
            ['src' => '/icons/icon-96.png', 'sizes' => '96x96', 'type' => 'image/png'],
            ['src' => '/icons/icon-128.png', 'sizes' => '128x128', 'type' => 'image/png'],
            ['src' => '/icons/icon-144.png', 'sizes' => '144x144', 'type' => 'image/png'],
            ['src' => '/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
            ['src' => '/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
        ],
    ])->header('Content-Type', 'application/manifest+json');
});
