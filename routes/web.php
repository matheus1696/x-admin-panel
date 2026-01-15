<?php

use App\Http\Controllers\Admin\Configuration\OccupationController;
use App\Http\Controllers\Admin\Configuration\RegionController;
use App\Http\Controllers\Admin\Manage\Establishment\DepartmentController;
use App\Http\Controllers\Admin\Manage\Establishment\EstablishmentController;
use App\Http\Controllers\Admin\Manage\Establishment\EstablishmentTypeController;
use App\Http\Controllers\Admin\Manage\Establishment\FinancialBlockController;
use App\Http\Controllers\Admin\Manage\UserController;
use App\Http\Controllers\Audit\LogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Company\OrganizationChartConfigPage;
use App\Livewire\Company\OrganizationChartDashboardPage;
use App\Livewire\Company\OrganizationChartPage;
use App\Livewire\Workflow\WorkflowPage;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('can:dashboard-view')->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');

        Route::get('/password', [ProfileController::class, 'password'])->name('password.edit');
        Route::patch('/password', [ProfileController::class, 'passwordUpdate'])->name('password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Establishments
    |--------------------------------------------------------------------------
    */
    Route::prefix('establishments')->middleware('can:establishment-view')->name('establishments.')->group(function () {
        Route::get('/', [EstablishmentController::class, 'index'])->name('index');
        Route::get('/{establishment}/show', [EstablishmentController::class, 'show'])->name('show');
        Route::get('/create', [EstablishmentController::class, 'create'])->name('create');
        Route::post('/', [EstablishmentController::class, 'store'])->name('store');
        Route::get('/{establishment}/edit', [EstablishmentController::class, 'edit'])->name('edit');
        Route::put('/{establishment}', [EstablishmentController::class, 'update'])->name('update');
    });

    Route::prefix('establishment/type')->name('establishments.types.')->group(function () {
        Route::get('/', [EstablishmentTypeController::class, 'index'])->middleware('can:establishment-type-view')->name('index');
    });

    /*
    |--------------------------------------------------------------------------
    | Financial Blocks
    |--------------------------------------------------------------------------
    */
    Route::prefix('financial\blocks')->name('financial.blocks.')->group(function () {
        Route::get('/', [FinancialBlockController::class, 'index'])->middleware('can:financial-block-view')->name('index');
        Route::get('/create', [FinancialBlockController::class, 'create'])->middleware('can:financial-block-create')->name('create');
        Route::post('/', [FinancialBlockController::class, 'store'])->middleware('can:financial-block-create')->name('store');
        Route::get('/{financialBlock}/edit', [FinancialBlockController::class, 'edit'])->middleware('can:financial-block-edit')->name('edit');
        Route::put('/{financialBlock}', [FinancialBlockController::class, 'update'])->middleware('can:financial-block-edit')->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('can:user-view')->name('index');
        Route::get('/create', [UserController::class, 'create'])->middleware('can:user-create')->name('create');
        Route::post('/', [UserController::class, 'store'])->middleware('can:user-create')->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('can:user-edit')->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('can:user-edit')->name('update');
        Route::get('/{user}/permissions', [UserController::class, 'permissionEdit'])->middleware('can:user-permission')->name('permissions.edit');
        Route::put('/{user}/permissions', [UserController::class, 'permissionUpdate'])->middleware('can:user-permission')->name('permissions.update');
        Route::patch('/{user}/password', [UserController::class, 'password'])->middleware('can:user-password')->name('password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Configurations
    |--------------------------------------------------------------------------
    */
    Route::prefix('config')->name('config.')->group(function () {

        // Occupations
        Route::prefix('occupations')->name('occupations.')->group(function () {
            Route::get('/', [OccupationController::class, 'index'])
                ->middleware('can:occupation-view')
                ->name('index');

            Route::get('/{occupation}/edit', [OccupationController::class, 'edit'])
                ->middleware('can:occupation-view')
                ->name('edit');

            Route::put('/{occupation}', [OccupationController::class, 'update'])
                ->middleware('can:occupation-view')
                ->name('update');

            Route::patch('/{occupation}/status', [OccupationController::class, 'status'])
                ->middleware('can:occupation-view')
                ->name('status');
        });

        // Regions
        Route::prefix('regions')->name('regions.')->middleware('can:region-view')->group(function () {

            Route::get('/cities', [RegionController::class, 'cityIndex'])
                ->name('cities.index');

            Route::patch('/cities/{city}/status', [RegionController::class, 'cityStatus'])
                ->name('cities.status');

            Route::get('/states', [RegionController::class, 'stateIndex'])
                ->name('states.index');

            Route::patch('/states/{state}/status', [RegionController::class, 'stateStatus'])
                ->name('states.status');

            Route::get('/countries', [RegionController::class, 'countryIndex'])
                ->name('countries.index');

            Route::patch('/countries/{country}/status', [RegionController::class, 'countryStatus'])
                ->name('countries.status');
        });

        Route::prefix('departments')->middleware('auth')->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('departments.index');
            Route::get('/create', [DepartmentController::class, 'create'])->name('departments.create');
            Route::post('/create', [DepartmentController::class, 'store'])->name('departments.store');
            Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
            Route::put('/{department}/edit', [DepartmentController::class, 'update'])->name('departments.update');
            Route::put('/{department}/status', [DepartmentController::class, 'status'])->name('departments.status');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Audit Logs
    |--------------------------------------------------------------------------
    */
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/logs', [LogController::class, 'index'])
            ->middleware('can:log-view')
            ->name('logs.index');
    });

    Route::get('/admin/workflow', WorkflowPage::class)->name('admin.workflow.index');
    Route::get('/admin/organization', OrganizationChartDashboardPage::class)->name('admin.organization.index');
    Route::get('/admin/config/organization', OrganizationChartConfigPage::class)->name('admin.organization.config.index');
});

require __DIR__.'/auth.php';
