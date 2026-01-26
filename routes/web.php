<?php

use App\Http\Controllers\Audit\LogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Administration\Task\TaskStatusPage;
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
use App\Livewire\Organization\Workflow\WorkflowRunStatusPage;
use App\Livewire\Public\Contact\ContactPage;
use App\Livewire\Task\TaskPage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Público
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));
Route::get('/contatos', ContactPage::class)->name('public.contacts.index');

/*
|--------------------------------------------------------------------------
| Autenticado
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /* Dashboard */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Organograma
    |--------------------------------------------------------------------------
    */
    Route::get('/organograma', OrganizationChartDashboardPage::class)->name('chart.index');

    /*
    |--------------------------------------------------------------------------
    | Tarefas
    |--------------------------------------------------------------------------
    */
    Route::get('/tarefas', TaskPage::class)->name('tasks.index');

    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */
    Route::prefix('perfil')->name('profile.')->group(function () {
        Route::get('/editar', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');

        Route::get('/senha', [ProfileController::class, 'password'])->name('password.edit');
        Route::patch('/senha', [ProfileController::class, 'passwordUpdate'])->name('password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Administração
    |--------------------------------------------------------------------------
    */
    Route::prefix('administracao')->name('administration.manage.')->group(function () {

        /* Usuários & Acessos */
        Route::get('/usuarios', UserPage::class)->middleware('can:administration.manage.users')->name('users');

        /* Status / Execução de Tasks */
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/status', TaskStatusPage::class)->middleware('can:administration.manage.task')->name('status');
            Route::get('/categorias', TaskStatusPage::class)->middleware('can:administration.manage.task')->name('category');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Configurações do Sistema
    |--------------------------------------------------------------------------
    */
    Route::prefix('configuracao')->name('configuration.manage.')->group(function () {

        /*
        | Estabelecimentos
        */
        Route::prefix('estabelecimentos') ->middleware('can:configuration.manage.establishments') ->name('establishments.')
            ->group(function () {
                Route::get('/', EstablishmentList::class)->name('view');
                Route::get('/{code}', EstablishmentShow::class)->name('show');
                Route::get('/tipos', EstablishmentTypePage::class)->name('types');
            });

        /*
        | Ocupações (CBO)
        */
        Route::get('/ocupacoes', OccupationPage::class)
            ->middleware('can:configuration.manage.occupations')
            ->name('occupations');

        /*
        | Blocos Financeiros
        */
        Route::get('/financeiro/blocos', FinancialBlockPage::class)
            ->middleware('can:configuration.manage.financial-blocks')
            ->name('financial.blocks');

        /*
        | Regiões
        */
        Route::prefix('regioes')->middleware('can:configuration.manage.regions')->name('regions.')
            ->group(function () {
                Route::get('/paises', RegionCountryPage::class)->name('countries');
                Route::get('/estados', RegionStatePage::class)->name('states');
                Route::get('/cidades', RegionCityPage::class)->name('cities');
            });
    });

    /*
    |--------------------------------------------------------------------------
    | Organização
    |--------------------------------------------------------------------------
    */
    Route::prefix('organizacao')->name('organization.manage.')->group(function () {

        Route::get('/configuracao', OrganizationChartConfigPage::class)
            ->middleware('can:organization.manage.chart')
            ->name('chart');

        /*
        | Workflow
        */
        Route::get('/workflow', WorkflowProcessesPage::class)
            ->middleware('can:organization.manage.workflow')
            ->name('workflow');
    });

    /*
    |--------------------------------------------------------------------------
    | Auditoria
    |--------------------------------------------------------------------------
    */
    Route::prefix('auditoria')->middleware('can:audit.logs.view')->name('audit.')
        ->group(function () {
            Route::get('/logs', [LogController::class, 'index'])->name('logs.view');
        });
});

require __DIR__.'/auth.php';
