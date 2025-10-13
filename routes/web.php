<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\PropertyManagementController;
use App\Http\Controllers\Admin\JobManagementController;
use App\Http\Controllers\Admin\ApplicationManagementController;
use App\Http\Controllers\Admin\FavoriteManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Root route - redirect to admin dashboard or login
Route::get('/', function () {
    if (Auth::check() && Auth::user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    // Admin Authentication Routes
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Admin Routes
    Route::middleware(['admin'])->group(function () {
        
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('admin.profile');
        Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings');
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

        // Users Management
        Route::prefix('users')->name('admin.users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::put('/{user}/toggle-admin', [UserManagementController::class, 'toggleAdmin'])->name('toggle-admin');
        });

        // Properties Management
        Route::prefix('properties')->name('admin.properties.')->group(function () {
            Route::get('/', [PropertyManagementController::class, 'index'])->name('index');
            Route::get('/{property}', [PropertyManagementController::class, 'show'])->name('show');
            Route::get('/{property}/edit', [PropertyManagementController::class, 'edit'])->name('edit');
            Route::put('/{property}', [PropertyManagementController::class, 'update'])->name('update');
            Route::delete('/{property}', [PropertyManagementController::class, 'destroy'])->name('destroy');
        });

        // Jobs Management
        Route::prefix('jobs')->name('admin.jobs.')->group(function () {
            Route::get('/', [JobManagementController::class, 'index'])->name('index');
            Route::get('/{job}', [JobManagementController::class, 'show'])->name('show');
            Route::get('/{job}/edit', [JobManagementController::class, 'edit'])->name('edit');
            Route::put('/{job}', [JobManagementController::class, 'update'])->name('update');
            Route::delete('/{job}', [JobManagementController::class, 'destroy'])->name('destroy');
        });

        // Applications Management
        Route::prefix('applications')->name('admin.applications.')->group(function () {
            Route::get('/', [ApplicationManagementController::class, 'index'])->name('index');
            Route::get('/{application}', [ApplicationManagementController::class, 'show'])->name('show');
            Route::put('/{application}/status', [ApplicationManagementController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{application}', [ApplicationManagementController::class, 'destroy'])->name('destroy');
        });

        // Favorites Management
        Route::prefix('favorites')->name('admin.favorites.')->group(function () {
            Route::get('/', [FavoriteManagementController::class, 'index'])->name('index');
            Route::delete('/{favorite}', [FavoriteManagementController::class, 'destroy'])->name('destroy');
        });
    });
});
