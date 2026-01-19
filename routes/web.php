<?php

use App\Http\Controllers\Admin\Manage\Establishment\DepartmentController;
use App\Http\Controllers\Audit\LogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Administration\User\UserPage;
use App\Livewire\Configuration\Establishment\Establishment\EstablishmentList;
use App\Livewire\Configuration\Establishment\Establishment\EstablishmentShow;
use App\Livewire\Configuration\Establishment\EstablishmentType\EstablishmentTypePage;
use App\Livewire\Configuration\Financial\FinancialBlock\FinancialBlockPage;
use App\Livewire\Configuration\Occupation\OccupationPage;
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
        | Auditoria
        */
        Route::prefix('audit')
            ->middleware('can:audit.logs.view')
            ->name('audit.')
            ->group(function () {

                Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
            });

        /* Configurações */
        Route::get('/configuration/establishment/list', EstablishmentList::class)->middleware('can:admin.establishments.manage')->name('establishments.index');

        Route::get('/configuration/establishment/show/{code}', EstablishmentShow::class)->middleware('can:admin.establishments.manage')->name('establishments.show');

        Route::get('/configuration/establishment/type', EstablishmentTypePage::class)->middleware('can:admin.establishments.manage')->name('establishments.types.index');
        
        Route::get('/configuration/financial/block', FinancialBlockPage::class)->middleware('can:admin.financial-blocks.manage')->name('financial.blocks.index');

        Route::get('/configuration/occupation', OccupationPage::class)->middleware('can:admin.occupations.manage')->name('occupations.index');

        /* Gerenciamento de Região */
        Route::get('/configuration/region/countries', RegionCountryPage::class)->middleware('can:admin.regions.manage')->name('regions.countries.index');

        Route::get('/configuration/region/states', RegionStatePage::class)->middleware('can:admin.regions.manage')->name('regions.states.index');

        Route::get('/configuration/region/cities', RegionCityPage::class)->middleware('can:admin.regions.manage')->name('regions.cities.index');

        /* Gerenciamento de Usuários & Acessos */
        Route::get('/administration/user', UserPage::class)->middleware('can:users.view')->name('users.index');

        /* Organograma */   
        Route::get('/organization', OrganizationChartDashboardPage::class)->middleware('can:organization.view')->name('organization.index');
        
        Route::get('/organization/config', OrganizationChartConfigPage::class)->middleware('can:organization.manage')->name('organization.config.index');

        /* Fluxo de Trabalho */
        Route::get('/organization/workflow', WorkflowProcessesPage::class)->middleware('can:workflow.manage')->name('workflow.index');
    });
});

require __DIR__.'/auth.php';
