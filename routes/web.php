<?php

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
use App\Livewire\Organization\Workflow\WorkflowRunStatus;
use App\Livewire\Organization\Workflow\WorkflowRunStatusPage;
use App\Livewire\Public\Contact\ContactPage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));
Route::get('/contatos', ContactPage::class)->name('contacts.index');

/* Autenticado */
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('perfil')->name('profile.')->group(function () {
        Route::get('/editar', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');

        Route::get('/altera/senha', [ProfileController::class, 'password'])->name('password.edit');
        Route::patch('/password', [ProfileController::class, 'passwordUpdate'])->name('password.update');
    });

    /* Administração do Sistema */
    Route::prefix('administracao/sistema')->name('admin.')->group(function () {
        
        /* Gerenciamento de Usuários & Acessos */
        Route::get('usuario', UserPage::class)->middleware('can:users.view')->name('users.index');
    });

    /* Configurações */
    Route::prefix('configuracao/sistema')->name('config.')->group(function () {

        /* Configurações do Estabelecimento */
        Route::prefix('estabelecimento')->name('establishments.')->middleware('can:config.establishments.manage')->group(function () {
            Route::get('lista', EstablishmentList::class)->name('index');
            Route::get('detalhe/{code}', EstablishmentShow::class)->name('show');
            Route::get('tipo', EstablishmentTypePage::class)->name('types.index');
        });

        /* Gerenciamento de Ocupações */
        Route::get('ocupacoes', OccupationPage::class)->middleware('can:config.occupations.manage')->name('occupations.index');

        /* Gerenciamento do Bloco Financeiro */
        Route::get('blocos/financeiro', FinancialBlockPage::class)->middleware('can:config.financial-blocks.manage')->name('financial.blocks.index');

        /* Gerenciamento de Região */
        Route::prefix('regioes')->name('regions.')->middleware('can:config.regions.manage')->group(function () {
            Route::get('paises', RegionCountryPage::class)->name('countries.index');
            Route::get('estados', RegionStatePage::class)->name('states.index');
            Route::get('cidades', RegionCityPage::class)->name('cities.index');
        });
    });

    /* Organização */
    Route::prefix('organizacao')->name('organization.')->group(function () {

        /* Organograma */   
        Route::get('dashboard', OrganizationChartDashboardPage::class)->middleware('can:organization.chart.dashboard.view')->name('chart.dashboard.index');
        Route::get('configuracao', OrganizationChartConfigPage::class)->middleware('can:organization.chart.config.manage')->name('chart.config.index');

        /* Fluxo de Trabalho */
        Route::get('fluxo/trabalho', WorkflowProcessesPage::class)->middleware('can:organization.workflow.manage')->name('workflow.config.index');
        Route::get('fluxo/trabalho/status', WorkflowRunStatusPage::class)->middleware('can:organization.workflow.manage')->name('workflow.config.status');
    });

    /* Auditoria do Sistema */
    Route::prefix('auditoria')->name('audit.')->middleware('can:audit.logs.view')->group(function () {
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    });
});

require __DIR__.'/auth.php';