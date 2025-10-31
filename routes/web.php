<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\Manage\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
    // return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/sinan', function () {
    return view('sms.surveillance.notification.sinan.sinan_create');
})->name('surveillance.notification.sinan');

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password.edit');
    Route::patch('/profile/password', [ProfileController::class, 'passwordUpdate'])->name('profile.password.update');
});

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

    Route::get('/log', [ActivityLogController::class, 'index'])->name('admin.logs.index')->middleware('can:view-logs');

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
