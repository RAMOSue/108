<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Routes that require authentication and setDB middleware
Route::middleware(['auth', 'setDB'])->group(function () {
    // Dashboard route - AdminController
    
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/user', [AdminController::class, 'users'])->name('user');

     // Admin routes 
     Route::get('/user', [AdminController::class, 'users'])->name('user');
        Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
        Route::post('/admin/users/{id}', [AdminController::class, 'updateRole'])->name('admin.updateRole');
        Route::post('/admin/users', [AdminController::class, 'addUser'])->name('admin.addUser');
        Route::delete('/admin/tribes/{id}', [AdminController::class, 'deleteTribe'])->name('admin.deleteTribe');
        Route::post('/admin/refresh-stats', [AdminController::class, 'refreshStatistics'])->name('admin.refreshActivityLogs');
        Route::get('/admin/activity-logs', [AdminController::class, 'getActivityLogs'])->name('admin.activityLogs');
    });

     // staff routes
     Route::middleware('staff')->group(function () {
        Route::get('/staff', [StaffController::class, 'dashboard'])->name('staff');  // staff profile info
        Route::get('/staff/dashboard/stats', [StaffController::class, 'getDashboardStats'])->name('staff.getDashboardStats');
        Route::post('/staff/tribes', [StaffController::class, 'storeTribe'])->name('staff.storeTribe');
        Route::put('/staff/tribes/{tribes}', [StaffController::class, 'updateTribe'])->name('staff.updateTribe');
        Route::get('/staff/tribes/{tribes}/edit', [StaffController::class, 'editTribe'])->name('staff.editTribe');
        Route::delete('/staff/tribes/{tribes}', [StaffController::class, 'deleteTribe'])->name('staff.deleteTribe');
    });


    //User routes
    Route::get('/user', [UserController::class, 'users'])->name('user');
    
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

// Authentication routes
require __DIR__ . '/auth.php';