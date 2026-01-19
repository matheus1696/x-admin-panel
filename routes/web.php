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
use App\Livewire\Administration\User\UserPage;
use App\Livewire\Configuration\Establishment\EstablishmentType\EstablishmentTypePage;
use App\Livewire\Configuration\Financial\FinancialBlock\FinancialBlockPage;
use App\Livewire\Configuration\Region\RegionCityPage;
use App\Livewire\Configuration\Region\RegionCountryPage;
use App\Livewire\Configuration\Region\RegionStatePage;
use App\Livewire\Organization\OrganizationChart\OrganizationChartConfigPage;
use App\Livewire\Organization\OrganizationChart\OrganizationChartDashboardPage;
use App\Livewire\Organization\Workflow\WorkflowProcessesPage;
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
        | Auditoria
        */
        Route::prefix('audit')
            ->middleware('can:audit.logs.view')
            ->name('audit.')
            ->group(function () {

                Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
            });

        /* Configurações */
        Route::get('/configuration/establishment/type', EstablishmentTypePage::class)->middleware('can:admin.establishments.manage')->name('establishments.types.index');
        
        Route::get('/configuration/financial/block', FinancialBlockPage::class)->middleware('can:admin.financial-blocks.manage')->name('financial.blocks.index');

        Route::get('/configuration/region/countries', RegionCountryPage::class)->middleware('can:admin.regions.manage')->name('regions.countries.index');

        Route::get('/configuration/region/states', RegionStatePage::class)->middleware('can:admin.regions.manage')->name('regions.states.index');

        Route::get('/configuration/region/cities', RegionCityPage::class)->middleware('can:admin.regions.manage')->name('regions.cities.index');

        /* Gerenciamento de Usuários & Acessos */
        Route::get('/administration/user', UserPage::class)->middleware('can:users.view')->name('users.index');

        /* Organograma */   
        Route::get('/organization', OrganizationChartDashboardPage::class)->middleware('can:organization.view')->name('organization.index');
        Route::get('/organization/config', OrganizationChartConfigPage::class)->middleware('can:organization.manage')->name('organization.config.index');

        /* Fluxo de Trabalho */
        Route::get('/workflow', WorkflowProcessesPage::class)->middleware('can:workflow.manage')->name('workflow.index');
    });
});

require __DIR__.'/auth.php';
