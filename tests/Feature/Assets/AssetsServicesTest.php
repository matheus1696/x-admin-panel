<?php

use App\DTOs\Assets\AuditAssetDTO;
use App\DTOs\Assets\ChangeAssetStateDTO;
use App\DTOs\Assets\CreateInvoiceDTO;
use App\DTOs\Assets\ReceiveStockDTO;
use App\DTOs\Assets\ReturnToPatrimonyDTO;
use App\DTOs\Assets\TransferAssetDTO;
use App\DTOs\Assets\UpsertInvoiceItemDTO;
use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Exceptions\Assets\AssetsValidationException;
use App\Models\Administration\Product\Product;
use App\Models\Administration\Product\ProductMeasureUnit;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetEvent;
use App\Models\Assets\AssetInvoiceItem;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Services\Assets\AssetOperationService;
use App\Services\Assets\AuditService;
use App\Services\Assets\InvoiceService;
use App\Services\Assets\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function createAssetsServicesUnit(string $suffix): Establishment
{
    $acronym2 = null;
    $acronym3 = null;

    while ($acronym2 === null) {
        $candidate = 'C'.strtoupper(Str::random(1));

        if (! DB::table('region_countries')->where('acronym_2', $candidate)->exists()) {
            $acronym2 = $candidate;
        }
    }

    while ($acronym3 === null) {
        $candidate = 'BR'.strtoupper(Str::random(2));

        if (! DB::table('region_countries')->where('acronym_3', $candidate)->exists()) {
            $acronym3 = $candidate;
        }
    }

    $countryId = DB::table('region_countries')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym_2' => $acronym2,
        'acronym_3' => $acronym3,
        'title' => 'Pais '.$suffix,
        'filter' => 'pais '.$suffix,
        'country_ing' => 'Country '.$suffix,
        'filter_country_ing' => 'country '.$suffix,
        'code_iso' => 'CISO-'.$suffix,
        'code_ddi' => '56'.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $stateId = DB::table('region_states')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym' => 'S'.$suffix,
        'title' => 'Estado '.$suffix,
        'filter' => 'estado '.$suffix,
        'code_uf' => 'UF'.$suffix,
        'code_ddd' => '7'.$suffix,
        'is_active' => true,
        'country_id' => $countryId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $cityId = DB::table('region_cities')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'code_ibge' => 'CIDADE'.$suffix,
        'title' => 'Cidade '.$suffix,
        'filter' => 'cidade '.$suffix,
        'code_cep' => '71'.$suffix,
        'is_active' => true,
        'state_id' => $stateId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $typeId = DB::table('establishment_types')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Tipo '.$suffix,
        'filter' => 'tipo '.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $financialBlockId = DB::table('financial_blocks')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Bloco '.$suffix,
        'filter' => 'bloco '.$suffix,
        'acronym' => 'BL'.$suffix,
        'color' => '#654321',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return Establishment::create([
        'code' => 'UNIT-'.$suffix,
        'title' => 'Unidade '.$suffix,
        'surname' => 'UND'.$suffix,
        'address' => 'Avenida Teste',
        'number' => '200',
        'district' => 'Centro',
        'city_id' => $cityId,
        'state_id' => $stateId,
        'type_establishment_id' => $typeId,
        'financial_block_id' => $financialBlockId,
        'is_active' => true,
    ]);
}

function createAssetsServicesSector(Establishment $unit, string $suffix): Department
{
    return Department::create([
        'title' => 'Setor '.$suffix,
        'establishment_id' => $unit->id,
    ]);
}

function createAssetsServiceAsset(
    AssetState $state,
    ?int $unitId = null,
    ?int $sectorId = null,
    ?int $invoiceItemId = null
): Asset {
    return Asset::create([
        'invoice_item_id' => $invoiceItemId,
        'code' => 'AST-SRV-'.Str::upper(Str::random(8)),
        'description' => 'Ativo de teste',
        'state' => $state,
        'unit_id' => $unitId,
        'sector_id' => $sectorId,
    ]);
}

function createAssetsServiceProduct(string $suffix): Product
{
    return Product::create([
        'code' => 'PRD-'.$suffix,
        'title' => 'Produto '.$suffix,
        'description' => 'Produto de teste '.$suffix,
    ]);
}

function createAssetsServiceMeasureUnit(string $suffix, int $baseQuantity = 1): ProductMeasureUnit
{
    return ProductMeasureUnit::create([
        'acronym' => 'UN/'.$suffix,
        'title' => 'Unidade '.$suffix,
        'base_quantity' => $baseQuantity,
    ]);
}

test('invoice service creates, updates and deletes invoice items', function () {
    $user = User::factory()->create();
    $service = app(InvoiceService::class);

    $invoice = $service->createInvoice(new CreateInvoiceDTO(
        invoiceNumber: 'NF-400',
        invoiceSeries: '1',
        supplierName: 'Fornecedor Principal',
        supplierDocument: '12.345.678/0001-99',
        issueDate: '2026-03-04',
        receivedDate: '2026-03-05',
        totalAmount: '2500.00',
        notes: 'Criada via teste',
        createdUserId: $user->id,
    ));

    $product = createAssetsServiceProduct('400');
    $measureUnit = createAssetsServiceMeasureUnit('400');

    $item = $service->addOrUpdateItem(new UpsertInvoiceItemDTO(
        assetInvoiceId: $invoice->id,
        itemId: null,
        productId: $product->id,
        productMeasureUnitId: $measureUnit->id,
        itemCode: 'ITEM-400',
        description: 'Notebook',
        quantity: 2,
        unitPrice: 1250,
        brand: 'Lenovo',
        model: 'T14',
        metadata: ['color' => 'black'],
    ));

    $invoice->refresh();
    expect((float) $invoice->total_amount)->toBe(2500.0);

    $updatedItem = $service->addOrUpdateItem(new UpsertInvoiceItemDTO(
        assetInvoiceId: $invoice->id,
        itemId: $item->id,
        productId: $product->id,
        productMeasureUnitId: $measureUnit->id,
        itemCode: 'ITEM-400',
        description: 'Notebook atualizado',
        quantity: 3,
        unitPrice: 1200,
        brand: 'Lenovo',
        model: 'T14 Gen 2',
        metadata: ['color' => 'black'],
    ));

    $invoice->refresh();
    expect((float) $invoice->total_amount)->toBe(3600.0);

    $service->deleteItem($updatedItem->id);

    $invoice->refresh();

    expect($invoice->refresh()->created_user_id)->toBe($user->id)
        ->and($invoice->items()->count())->toBe(0)
        ->and((float) $invoice->total_amount)->toBe(0.0);
});

test('stock service receives one asset per quantity and prevents exceeding remaining balance', function () {
    $user = User::factory()->create();
    $unit = createAssetsServicesUnit('101');

    config()->set('assets.stock_default_unit_id', $unit->id);

    $invoiceService = app(InvoiceService::class);
    $stockService = app(StockService::class);

    $invoice = $invoiceService->createInvoice(new CreateInvoiceDTO(
        invoiceNumber: 'NF-401',
        invoiceSeries: null,
        supplierName: 'Fornecedor Estoque',
        supplierDocument: null,
        issueDate: '2026-03-04',
        totalAmount: 1800,
    ));

    $product = createAssetsServiceProduct('401');
    $measureUnit = createAssetsServiceMeasureUnit('401');

    $item = $invoiceService->addOrUpdateItem(new UpsertInvoiceItemDTO(
        assetInvoiceId: $invoice->id,
        itemId: null,
        productId: $product->id,
        productMeasureUnitId: $measureUnit->id,
        itemCode: null,
        description: 'Monitor',
        quantity: 2,
        unitPrice: 900,
    ));

    $assets = $stockService->receiveStock(new ReceiveStockDTO(
        invoiceItemId: $item->id,
        quantity: 2,
        actorUserId: $user->id,
        acquiredDate: '2026-03-04',
    ));

    expect($assets)->toHaveCount(2)
        ->and($assets[0]->state)->toBe(AssetState::IN_STOCK)
        ->and($assets[0]->unit_id)->toBe($unit->id)
        ->and(AssetEvent::where('type', AssetEventType::STOCK_RECEIVED->value)->count())->toBe(2);

    expect(fn () => $stockService->receiveStock(new ReceiveStockDTO(
        invoiceItemId: $item->id,
        quantity: 1,
        actorUserId: $user->id,
    )))->toThrow(AssetsValidationException::class);
});

test('stock service mirrors patrimony code from invoice item into code and patrimony number', function () {
    $user = User::factory()->create();
    $unit = createAssetsServicesUnit('102');

    config()->set('assets.stock_default_unit_id', $unit->id);

    $invoiceService = app(InvoiceService::class);
    $stockService = app(StockService::class);

    $invoice = $invoiceService->createInvoice(new CreateInvoiceDTO(
        invoiceNumber: 'NF-402',
        invoiceSeries: null,
        supplierName: 'Fornecedor Patrimonio',
        supplierDocument: null,
        issueDate: '2026-03-04',
        totalAmount: 2400,
    ));

    $product = createAssetsServiceProduct('402');
    $measureUnit = createAssetsServiceMeasureUnit('402');

    $item = $invoiceService->addOrUpdateItem(new UpsertInvoiceItemDTO(
        assetInvoiceId: $invoice->id,
        itemId: null,
        productId: $product->id,
        productMeasureUnitId: $measureUnit->id,
        itemCode: '265963',
        description: 'Desktop',
        quantity: 2,
        unitPrice: 1200,
    ));

    $assets = $stockService->receiveStock(new ReceiveStockDTO(
        invoiceItemId: $item->id,
        quantity: 2,
        actorUserId: $user->id,
        acquiredDate: '2026-03-04',
    ));

    expect($assets)->toHaveCount(2)
        ->and($assets[0]->code)->toBe('265963')
        ->and($assets[1]->code)->toBe('265964')
        ->and($assets[0]->patrimony_number)->toBe('265963')
        ->and($assets[1]->patrimony_number)->toBe('265964');
});

test('asset operation service transfers, changes state and returns to patrimony', function () {
    $user = User::factory()->create();
    $unit = createAssetsServicesUnit('201');
    $otherUnit = createAssetsServicesUnit('202');
    $sector = createAssetsServicesSector($unit, '201');
    $otherSector = createAssetsServicesSector($otherUnit, '202');

    config()->set('assets.patrimony_unit_id', $otherUnit->id);

    $service = app(AssetOperationService::class);
    $asset = createAssetsServiceAsset(AssetState::IN_USE, $unit->id, $sector->id);

    $service->transferAsset(new TransferAssetDTO(
        assetId: $asset->id,
        unitId: $otherUnit->id,
        sectorId: $otherSector->id,
        actorUserId: $user->id,
        notes: 'Transferido',
    ));

    $service->changeAssetState(new ChangeAssetStateDTO(
        assetId: $asset->id,
        toState: AssetState::IN_STOCK,
        actorUserId: $user->id,
        notes: 'Retornado para estoque',
    ));

    $service->changeAssetState(new ChangeAssetStateDTO(
        assetId: $asset->id,
        toState: AssetState::MAINTENANCE,
        actorUserId: $user->id,
        notes: 'Manutencao',
    ));

    $service->changeAssetState(new ChangeAssetStateDTO(
        assetId: $asset->id,
        toState: AssetState::IN_USE,
        actorUserId: $user->id,
        notes: 'Em uso novamente',
    ));

    $service->returnToPatrimony(new ReturnToPatrimonyDTO(
        assetId: $asset->id,
        actorUserId: $user->id,
        notes: 'Retorno',
    ));

    $asset->refresh();

    expect($asset->state)->toBe(AssetState::IN_STOCK)
        ->and($asset->unit_id)->toBe($otherUnit->id)
        ->and($asset->sector_id)->toBeNull()
        ->and($asset->events()->count())->toBe(5)
        ->and($asset->events()->latest()->first()?->type)->toBe(AssetEventType::RETURNED_TO_PATRIMONY);
});

test('audit service records audited event payload and rejects empty photo', function () {
    $user = User::factory()->create();
    $service = app(AuditService::class);
    $asset = createAssetsServiceAsset(AssetState::IN_STOCK);

    $service->auditAsset(new AuditAssetDTO(
        assetId: $asset->id,
        photoPath: 'assets/audits/audit-1.jpg',
        actorUserId: $user->id,
        notes: 'Auditoria OK',
    ));

    $event = $asset->events()->first();

    expect($event?->type)->toBe(AssetEventType::AUDITED)
        ->and($event?->payload)->toBe(['photo_path' => 'assets/audits/audit-1.jpg']);

    expect(fn () => $service->auditAsset(new AuditAssetDTO(
        assetId: $asset->id,
        photoPath: '   ',
        actorUserId: $user->id,
    )))->toThrow(AssetsValidationException::class);
});
