<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\Manage\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return redirect()->route('login'); });

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password.edit');
    Route::patch('/profile/password', [ProfileController::class, 'passwordUpdate'])->name('profile.password.update');

    // Users Management
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index')->middleware('can:view-users');
        Route::get('/create', [UserController::class, 'create'])->name('users.create')->middleware('can:create-users');
        Route::post('/', [UserController::class, 'store'])->name('users.store')->middleware('can:create-users');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('can:edit-users');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update')->middleware('can:edit-users');
        Route::put('/{user}/permission', [UserController::class, 'permission'])->name('users.permission')->middleware('can:permission-users');
        Route::patch('/{user}/password', [UserController::class, 'password'])->name('users.password')->middleware('can:password-users');
    });

    // Activity Logs
    Route::get('/log', [ActivityLogController::class, 'index'])->name('admin.logs.index')->middleware('can:view-logs');
});

require __DIR__.'/auth.php';
