<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\KostController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth Routes (untuk yang belum login)
Route::middleware('guest:owner')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});
// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->name('verification.verify');
Route::post('/email/resend', [AuthController::class, 'resendVerification'])
    ->name('verification.resend');
// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Owner Routes
Route::middleware('auth:owner')->prefix('owner')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
    
    // Kost Management
    Route::post('/kost', [KostController::class, 'store'])->name('kost.store');
    Route::put('/kost/{kost}', [KostController::class, 'update'])->name('kost.update');
    Route::delete('/kost/{kost}', [KostController::class, 'destroy'])->name('kost.destroy');
    Route::patch('/kost/{kost}/toggle', [KostController::class, 'toggleActive'])->name('kost.toggle');
    
    // Image Management
    Route::delete('/kost/{kost}/image', [KostController::class, 'deleteImage'])->name('kost.image.delete');
    Route::delete('/kost/{kost}/images/all', [KostController::class, 'deleteAllImages'])->name('kost.images.deleteAll');
});
// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'login']);
    });

    // Authenticated routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Kelola Owner
        Route::get('/owners', [App\Http\Controllers\Admin\AdminOwnerController::class, 'index'])->name('owners');
        Route::get('/owners/{owner}', [App\Http\Controllers\Admin\AdminOwnerController::class, 'show'])->name('owners.show');
        Route::delete('/owners/{owner}', [App\Http\Controllers\Admin\AdminOwnerManagementController::class, 'destroy'])->name('owners.destroy');
        
        // Kelola Kost & Approval
        Route::get('/kosts', [App\Http\Controllers\Admin\AdminKostController::class, 'index'])->name('kosts');
        Route::get('/kosts/{kost}', [App\Http\Controllers\Admin\AdminKostController::class, 'show'])->name('kosts.show');
        Route::patch('/kosts/{kost}/approve', [App\Http\Controllers\Admin\AdminKostController::class, 'approve'])->name('kosts.approve');
        Route::patch('/kosts/{kost}/reject', [App\Http\Controllers\Admin\AdminKostController::class, 'reject'])->name('kosts.reject');
        Route::delete('/kosts/{kost}', [App\Http\Controllers\Admin\AdminKostController::class, 'destroy'])->name('kosts.destroy');
        
        Route::post('/logout', [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('logout');
    });
});