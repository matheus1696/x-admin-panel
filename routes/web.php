<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Users Management
    Route::prefix('users')->middleware('can:manage-users')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('users.index')->middleware('can:view-users');
        Route::get('/create', [ProfileController::class, 'edit'])->name('users.create')->middleware('can:create-users');
        Route::post('/', [ProfileController::class, 'edit'])->name('users.store')->middleware('can:create-users');
        Route::get('/{user}/edit', [ProfileController::class, 'edit'])->name('users.edit')->middleware('can:edit-users');
        Route::put('/{user}', [ProfileController::class, 'edit'])->name('users.update')->middleware('can:edit-users');
        Route::delete('/{user}', [ProfileController::class, 'edit'])->name('users.destroy')->middleware('can:delete-users');
    });

    // Reports
    Route::prefix('reports')->middleware('can:view-reports')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('reports.index');
        Route::get('/export', [ProfileController::class, 'edit'])->name('reports.export');
    });

    // Settings
    Route::prefix('settings')->middleware('can:manage-settings')->group(function () {
        Route::get('/general', [ProfileController::class, 'edit'])->name('settings.general');
        Route::put('/general', [ProfileController::class, 'edit'])->name('settings.update');
    });

    // Roles and Permissions
    Route::prefix('roles')->middleware('can:manage-roles')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('roles.index');
        Route::get('/create', [ProfileController::class, 'edit'])->name('roles.create');
        Route::post('/', [ProfileController::class, 'edit'])->name('roles.store');
        Route::get('/{role}/edit', [ProfileController::class, 'edit'])->name('roles.edit');
        Route::put('/{role}', [ProfileController::class, 'edit'])->name('roles.update');
        Route::delete('/{role}', [ProfileController::class, 'edit'])->name('roles.destroy');
    });

    // Admin Only Routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/system-logs', [ProfileController::class, 'edit'])->name('admin.system-logs');
        Route::get('/backup', [ProfileController::class, 'edit'])->name('admin.backup');
        Route::post('/backup/create', [ProfileController::class, 'edit'])->name('admin.backup.create');
    });

require __DIR__.'/auth.php';
