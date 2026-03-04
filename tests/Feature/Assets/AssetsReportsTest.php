<?php

use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
use App\Services\Assets\AssetsReportService;
use Spatie\Permission\Models\Permission;

function createAssetsReportUser(): User
{
    $user = User::factory()->create();
    Permission::findOrCreate('assets.reports.view', 'web');
    $user->givePermissionTo('assets.reports.view');

    return $user;
}

test('assets report service returns consolidated datasets', function () {
    $invoice = AssetInvoice::create([
        'invoice_number' => 'NF-REP-001',
        'supplier_name' => 'Fornecedor Relatorio',
        'issue_date' => now()->toDateString(),
        'total_amount' => 900,
    ]);

    $asset = Asset::create([
        'code' => 'AST-REP-001',
        'description' => 'Ativo relatorio',
        'state' => AssetState::IN_STOCK,
    ]);

    $asset->events()->create([
        'type' => AssetEventType::TRANSFERRED,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $asset->events()->create([
        'type' => AssetEventType::AUDITED,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $service = app(AssetsReportService::class);

    expect($service->assetsByUnit())->toHaveCount(1)
        ->and($service->assetsByState()->first()['state'])->toBe(AssetState::IN_STOCK->value)
        ->and($service->transfersByPeriod(['startDate' => now()->toDateString()]))->toHaveCount(1)
        ->and($service->auditsByPeriod(['startDate' => now()->toDateString()]))->toHaveCount(1)
        ->and($service->purchasesByPeriod(['startDate' => now()->toDateString()])->first()?->invoice_number)->toBe($invoice->invoice_number);
});

test('assets report pages render for authorized users', function () {
    $user = createAssetsReportUser();
    $this->actingAs($user);

    $this->get(route('assets.reports.assets-by-unit'))->assertOk();
    $this->get(route('assets.reports.assets-by-state'))->assertOk();
    $this->get(route('assets.reports.transfers-by-period'))->assertOk();
    $this->get(route('assets.reports.audits-by-period'))->assertOk();
    $this->get(route('assets.reports.purchases-by-period'))->assertOk();
});
