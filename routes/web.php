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
use App\Livewire\Workflow\WorkflowPage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard (SEM permissão)
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

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
    | Administração
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        /*
        | Estabelecimentos
        */
        Route::prefix('establishments')
            ->middleware('can:admin.establishments.manage')
            ->name('establishments.')
            ->group(function () {

                Route::get('/', [EstablishmentController::class, 'index'])->name('index');
                Route::get('/create', [EstablishmentController::class, 'create'])->name('create');
                Route::post('/', [EstablishmentController::class, 'store'])->name('store');
                Route::get('/{establishment}/show', [EstablishmentController::class, 'show'])->name('show');
                Route::get('/{establishment}/edit', [EstablishmentController::class, 'edit'])->name('edit');
                Route::put('/{establishment}', [EstablishmentController::class, 'update'])->name('update');

                // Tipos
                Route::get('/types', [EstablishmentTypeController::class, 'index'])
                    ->name('types.index');
            });

        /*
        | Blocos Financeiros
        */
        Route::prefix('financial-blocks')
            ->middleware('can:admin.financial-blocks.manage')
            ->name('financial.blocks.')
            ->group(function () {

                Route::get('/', [FinancialBlockController::class, 'index'])->name('index');
                Route::get('/create', [FinancialBlockController::class, 'create'])->name('create');
                Route::post('/', [FinancialBlockController::class, 'store'])->name('store');
                Route::get('/{financialBlock}/edit', [FinancialBlockController::class, 'edit'])->name('edit');
                Route::put('/{financialBlock}', [FinancialBlockController::class, 'update'])->name('update');
            });

        /*
        | Ocupações
        */
        Route::prefix('occupations')
            ->middleware('can:admin.occupations.manage')
            ->name('occupations.')
            ->group(function () {

                Route::get('/', [OccupationController::class, 'index'])->name('index');
                Route::get('/{occupation}/edit', [OccupationController::class, 'edit'])->name('edit');
                Route::put('/{occupation}', [OccupationController::class, 'update'])->name('update');
                Route::patch('/{occupation}/status', [OccupationController::class, 'status'])->name('status');
            });

        /*
        | Regiões
        */
        Route::prefix('regions')
            ->middleware('can:admin.regions.manage')
            ->name('regions.')
            ->group(function () {

                Route::get('/countries', [RegionController::class, 'countryIndex'])->name('countries.index');
                Route::patch('/countries/{country}/status', [RegionController::class, 'countryStatus'])->name('countries.status');

                Route::get('/states', [RegionController::class, 'stateIndex'])->name('states.index');
                Route::patch('/states/{state}/status', [RegionController::class, 'stateStatus'])->name('states.status');

                Route::get('/cities', [RegionController::class, 'cityIndex'])->name('cities.index');
                Route::patch('/cities/{city}/status', [RegionController::class, 'cityStatus'])->name('cities.status');
            });

        /*
        | Departamentos (Organograma)
        */
        Route::prefix('departments')
            ->middleware('can:organization.manage')
            ->name('departments.')
            ->group(function () {

                Route::get('/', [DepartmentController::class, 'index'])->name('index');
                Route::get('/create', [DepartmentController::class, 'create'])->name('create');
                Route::post('/', [DepartmentController::class, 'store'])->name('store');
                Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
                Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
                Route::put('/{department}/status', [DepartmentController::class, 'status'])->name('status');
            });

        /*
        | Usuários & Acessos
        */
        Route::prefix('users')->name('users.')->group(function () {

            Route::get('/', [UserController::class, 'index'])
                ->middleware('can:users.view')
                ->name('index');

            Route::get('/create', [UserController::class, 'create'])
                ->middleware('can:users.create')
                ->name('create');

            Route::post('/', [UserController::class, 'store'])
                ->middleware('can:users.create')
                ->name('store');

            Route::get('/{user}/edit', [UserController::class, 'edit'])
                ->middleware('can:users.update')
                ->name('edit');

            Route::put('/{user}', [UserController::class, 'update'])
                ->middleware('can:users.update')
                ->name('update');

            Route::get('/{user}/permissions', [UserController::class, 'permissionEdit'])
                ->middleware('can:users.permissions')
                ->name('permissions.edit');

            Route::put('/{user}/permissions', [UserController::class, 'permissionUpdate'])
                ->middleware('can:users.permissions')
                ->name('permissions.update');

            Route::patch('/{user}/password', [UserController::class, 'password'])
                ->middleware('can:users.password')
                ->name('password.update');
        });

        /*
        | Auditoria
        */
        Route::prefix('audit')
            ->middleware('can:audit.logs.view')
            ->name('audit.')
            ->group(function () {

                Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
            });

        /*
        | Workflow & Organograma (Livewire)
        */
        Route::get('/workflow', WorkflowPage::class)
            ->middleware('can:workflow.manage')
            ->name('workflow.index');

        Route::get('/organization', OrganizationChartDashboardPage::class)
            ->middleware('can:organization.view')
            ->name('organization.index');

        Route::get('/organization/config', OrganizationChartConfigPage::class)
            ->middleware('can:organization.manage')
            ->name('organization.config.index');
    });
});

require __DIR__.'/auth.php';
