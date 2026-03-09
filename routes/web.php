<?php

use App\Http\Controllers\Audit\LogController;
use App\Http\Controllers\Assets\AuditCampaignPdfController;
use App\Http\Controllers\Assets\ReleaseOrderPdfController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Assets\InvoiceIndex;
use App\Livewire\Assets\AssetShow;
use App\Livewire\Assets\AssetsIndex;
use App\Livewire\Assets\AssetsStockIndex;
use App\Livewire\Assets\ReleaseOrderCreate;
use App\Livewire\Assets\ReleaseOrderIndex;
use App\Livewire\Assets\ReleaseOrderShow;
use App\Livewire\Assets\GlobalItemAssetsIndex;
use App\Livewire\Assets\AssetsByState;
use App\Livewire\Assets\AssetsByUnit;
use App\Livewire\Assets\AuditsByPeriod;
use App\Livewire\Assets\AuditMobile;
use App\Livewire\Assets\AuditCampaignCreate;
use App\Livewire\Assets\AuditCampaignIndex;
use App\Livewire\Assets\AuditCampaignShow;
use App\Livewire\Assets\PurchasesByPeriod;
use App\Livewire\Assets\TransfersByPeriod;
use App\Livewire\Administration\Task\TaskStatusPage;
use App\Livewire\Administration\Product\ProductMeasureUnitPage;
use App\Livewire\Administration\Product\ProductPage;
use App\Livewire\Administration\Product\ProductTypePage;
use App\Livewire\Administration\Supplier\SupplierPage;
use App\Livewire\Administration\User\UserPage;
use App\Livewire\Administration\User\UserPermissionPage;
use App\Livewire\Configuration\Establishment\Establishment\EstablishmentList;
use App\Livewire\Configuration\Establishment\Establishment\EstablishmentShow;
use App\Livewire\Configuration\Establishment\EstablishmentType\EstablishmentTypePage;
use App\Livewire\Configuration\Financial\FinancialBlock\FinancialBlockPage;
use App\Livewire\Configuration\Occupation\OccupationPage;
use App\Livewire\Configuration\Region\RegionCityPage;
use App\Livewire\Configuration\Region\RegionCountryPage;
use App\Livewire\Configuration\Region\RegionStatePage;
use App\Livewire\Organization\OrganizationChart\OrganizationChartConfigPage;
use App\Livewire\Organization\OrganizationChart\OrganizationChartDashboardFullPage;
use App\Livewire\Organization\OrganizationChart\OrganizationChartDashboardPage;
use App\Livewire\Organization\Workflow\WorkflowProcessesPage;
use App\Livewire\Public\Contact\ContactPage;
use App\Livewire\Task\TaskHubPage;
use App\Livewire\Task\TaskPage;
use App\Models\Assets\Asset;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas PÃºblicas
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));
Route::get('/contatos', ContactPage::class)->name('public.contacts.index');

/*
|--------------------------------------------------------------------------
| Rotas de AutenticaÃ§Ã£o (jÃ¡ incluÃ­das pelo Breeze/Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Autenticado
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /* Dashboard */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('notificacoes')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/marcar-todas', [NotificationController::class, 'readAll'])->name('read-all');
        Route::post('/{notificationId}/marcar', [NotificationController::class, 'read'])->name('read');
    });

    /*
    |--------------------------------------------------------------------------
    | Organograma
    |--------------------------------------------------------------------------
    */
    Route::get('/organograma', OrganizationChartDashboardPage::class)->name('chart.index');
    Route::get('/organograma/full', OrganizationChartDashboardFullPage::class)->name('chart.full.index');

    /*
    |--------------------------------------------------------------------------
    | Tarefas
    |--------------------------------------------------------------------------
    */
    Route::get('/tarefas', TaskHubPage::class)->name('tasks.index');
    Route::get('/tarefas/{uuid}', TaskPage::class)->name('tasks.show');

    /*
    |--------------------------------------------------------------------------
    | Ativos
    |--------------------------------------------------------------------------
    */
    Route::prefix('ativos')->name('assets.')->group(function () {
        Route::get('/estoque', AssetsStockIndex::class)->name('stock.index');
        Route::get('/lista', AssetsIndex::class)->name('index');
        Route::get('/lista/item-global', GlobalItemAssetsIndex::class)->name('items.global');
        Route::get('/item/{uuid}', AssetShow::class)->name('show');
        Route::get('/auditoria-mobile', AuditMobile::class)->name('audit-mobile');
        Route::prefix('/auditorias/campanhas')->name('audits.campaigns.')->middleware('can:audit,'.Asset::class)->group(function () {
            Route::get('/', AuditCampaignIndex::class)->name('index');
            Route::get('/nova', AuditCampaignCreate::class)->name('create');
            Route::get('/{uuid}', AuditCampaignShow::class)->name('show');
            Route::get('/{uuid}/pdf', AuditCampaignPdfController::class)->name('pdf');
        });
        Route::prefix('relatorios')->name('reports.')->middleware('can:viewReports,'.Asset::class)->group(function () {
            Route::get('/ativos-por-unidade', AssetsByUnit::class)->name('assets-by-unit');
            Route::get('/ativos-por-estado', AssetsByState::class)->name('assets-by-state');
            Route::get('/transferencias', TransfersByPeriod::class)->name('transfers-by-period');
            Route::get('/auditorias', AuditsByPeriod::class)->name('audits-by-period');
            Route::get('/compras', PurchasesByPeriod::class)->name('purchases-by-period');
        });

        Route::prefix('estoque/notas')->name('invoices.')->middleware('can:manageInvoices,'.Asset::class)->group(function () {
            Route::get('/', InvoiceIndex::class)->name('index');
        });

        Route::prefix('estoque/liberacoes')->name('release-orders.')->middleware('can:transfer,'.Asset::class)->group(function () {
            Route::get('/', ReleaseOrderIndex::class)->name('index');
            Route::get('/novo', ReleaseOrderCreate::class)->name('create');
            Route::get('/{uuid}/pdf', ReleaseOrderPdfController::class)->name('pdf');
            Route::get('/{uuid}', ReleaseOrderShow::class)->name('show');
        });
    });

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

    // Backward-compatible profile endpoints used by legacy tests and tooling.
    Route::get('/profile', [ProfileController::class, 'edit']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | AdministraÃ§Ã£o
    |--------------------------------------------------------------------------
    */
    Route::prefix('administracao')->name('administration.manage.')->group(function () {

        /* UsuÃ¡rios & Acessos */
        Route::get('/usuarios', UserPage::class)->middleware('can:administration.manage.users')->name('users');
        Route::get('/usuarios/{id}/permissoes', UserPermissionPage::class)->middleware('can:administration.manage.users.permissions')->name('users.permissions');
        Route::get('/fornecedores', SupplierPage::class)->middleware('can:administration.manage.suppliers')->name('suppliers');
        Route::get('/produtos', ProductPage::class)->middleware('can:administration.manage.products')->name('products');
        Route::get('/tipos-produto', ProductTypePage::class)->middleware('can:administration.manage.product-types')->name('product-types');
        Route::get('/unidades-medida', ProductMeasureUnitPage::class)->middleware('can:administration.manage.product-measure-units')->name('product-measure-units');

        /* Status / ExecuÃ§Ã£o de Tasks */
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/status', TaskStatusPage::class)->middleware('can:administration.manage.task')->name('status');
            Route::get('/categorias', TaskStatusPage::class)->middleware('can:administration.manage.task')->name('category');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ConfiguraÃ§Ãµes do Sistema
    |--------------------------------------------------------------------------
    */
    Route::prefix('configuracao')->name('configuration.manage.')->group(function () {

        /*
        | Estabelecimentos
        */
        Route::prefix('estabelecimentos')->middleware('can:configuration.manage.establishments')->name('establishments.')->group(function () {
            Route::get('/lista', EstablishmentList::class)->name('view');
            Route::get('/unidade/{code}', EstablishmentShow::class)->name('show');
            Route::get('/tipos', EstablishmentTypePage::class)->name('types');
        });

        /*
        | OcupaÃ§Ãµes (CBO)
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
        | RegiÃµes
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
    | OrganizaÃ§Ã£o
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
