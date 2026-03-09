<?php

use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Livewire\Assets\AssetShow;
use App\Livewire\Assets\AssetsIndex;
use App\Livewire\Assets\AssetsStockIndex;
use App\Livewire\Assets\AuditMobile;
use App\Livewire\Assets\ChangeStateForm;
use App\Livewire\Assets\ReleaseOrderCreate;
use App\Livewire\Assets\ReturnToPatrimonyForm;
use App\Livewire\Assets\TransferAssetForm;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetReleaseOrder;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function createAssetsOpsUser(array $permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
        $user->givePermissionTo($permission);
    }

    return $user;
}

function createAssetsOpsUnit(string $suffix): Establishment
{
    $acronym2 = null;
    $acronym3 = null;

    while ($acronym2 === null) {
        $candidate = 'D'.strtoupper(Str::random(1));

        if (! DB::table('region_countries')->where('acronym_2', $candidate)->exists()) {
            $acronym2 = $candidate;
        }
    }

    while ($acronym3 === null) {
        $candidate = 'BX'.strtoupper(Str::random(2));

        if (! DB::table('region_countries')->where('acronym_3', $candidate)->exists()) {
            $acronym3 = $candidate;
        }
    }

    $countryId = DB::table('region_countries')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym_2' => $acronym2,
        'acronym_3' => $acronym3,
        'title' => 'Pais ops '.$suffix,
        'filter' => 'pais ops '.$suffix,
        'country_ing' => 'Country ops '.$suffix,
        'filter_country_ing' => 'country ops '.$suffix,
        'code_iso' => 'DX-'.$suffix,
        'code_ddi' => '57'.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $stateId = DB::table('region_states')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym' => 'T'.$suffix,
        'title' => 'Estado ops '.$suffix,
        'filter' => 'estado ops '.$suffix,
        'code_uf' => 'TO'.$suffix,
        'code_ddd' => '6'.$suffix,
        'is_active' => true,
        'country_id' => $countryId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $cityId = DB::table('region_cities')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'code_ibge' => 'OPS'.$suffix,
        'title' => 'Cidade ops '.$suffix,
        'filter' => 'cidade ops '.$suffix,
        'code_cep' => '72'.$suffix,
        'is_active' => true,
        'state_id' => $stateId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $typeId = DB::table('establishment_types')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Tipo ops '.$suffix,
        'filter' => 'tipo ops '.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $financialBlockId = DB::table('financial_blocks')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Bloco ops '.$suffix,
        'filter' => 'bloco ops '.$suffix,
        'acronym' => 'BO'.$suffix,
        'color' => '#112233',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return Establishment::create([
        'code' => 'OPS-'.$suffix,
        'title' => 'Unidade ops '.$suffix,
        'surname' => 'UOP'.$suffix,
        'address' => 'Rua Operacional',
        'number' => '1',
        'district' => 'Centro',
        'city_id' => $cityId,
        'state_id' => $stateId,
        'type_establishment_id' => $typeId,
        'financial_block_id' => $financialBlockId,
        'is_active' => true,
    ]);
}

function createAssetsOpsSector(Establishment $unit, string $suffix): Department
{
    return Department::create([
        'title' => 'Setor ops '.$suffix,
        'establishment_id' => $unit->id,
    ]);
}

function createAssetsOpsAsset(
    AssetState $state,
    ?int $unitId = null,
    ?int $sectorId = null,
    string $description = 'Ativo operacional'
): Asset
{
    return Asset::create([
        'code' => 'AST-OPS-'.Str::upper(Str::random(8)),
        'description' => $description,
        'state' => $state,
        'unit_id' => $unitId,
        'sector_id' => $sectorId,
    ]);
}

test('assets pages render for authorized users', function () {
    $user = createAssetsOpsUser(['assets.view', 'assets.transfer', 'assets.state.change', 'assets.return']);
    $asset = createAssetsOpsAsset(AssetState::IN_STOCK);

    $this->actingAs($user)
        ->get(route('assets.stock.index'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('assets.index'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('assets.show', $asset->uuid))
        ->assertOk();
});

test('assets index filters by state through livewire', function () {
    $user = createAssetsOpsUser(['assets.view']);
    $this->actingAs($user);

    $inStock = createAssetsOpsAsset(AssetState::IN_STOCK, description: 'Item estoque');
    $inUse = createAssetsOpsAsset(AssetState::IN_USE, description: 'Item em uso');

    Livewire::test(AssetsIndex::class)
        ->set('filters.state', 'IN_USE')
        ->assertSee($inUse->description)
        ->assertDontSee($inStock->description);
});

test('assets index can be filtered by invoice item from query string', function () {
    $user = createAssetsOpsUser(['assets.view']);
    $this->actingAs($user);

    $invoice = \App\Models\Assets\AssetInvoice::create([
        'invoice_number' => 'NF-FLT-001',
        'supplier_name' => 'Fornecedor Filtro',
        'issue_date' => now()->toDateString(),
        'total_amount' => 100,
    ]);

    $itemA = \App\Models\Assets\AssetInvoiceItem::create([
        'asset_invoice_id' => $invoice->id,
        'description' => 'Mouse',
        'quantity' => 1,
        'unit_price' => 50,
        'total_price' => 50,
    ]);

    $itemB = \App\Models\Assets\AssetInvoiceItem::create([
        'asset_invoice_id' => $invoice->id,
        'description' => 'Teclado',
        'quantity' => 1,
        'unit_price' => 50,
        'total_price' => 50,
    ]);

    $assetA = Asset::create([
        'invoice_item_id' => $itemA->id,
        'code' => 'AST-FLT-A',
        'description' => 'Mouse',
        'state' => AssetState::IN_USE,
    ]);

    $assetB = Asset::create([
        'invoice_item_id' => $itemB->id,
        'code' => 'AST-FLT-B',
        'description' => 'Teclado',
        'state' => AssetState::IN_USE,
    ]);

    $this->get(route('assets.index', ['invoice_uuid' => $invoice->uuid, 'invoice_item_id' => $itemA->id]))
        ->assertOk()
        ->assertSee($itemA->description)
        ->assertDontSee($itemB->description);
});

test('global item page lists only assets from selected item', function () {
    $user = createAssetsOpsUser(['assets.view']);
    $this->actingAs($user);

    $desktopA = createAssetsOpsAsset(AssetState::IN_STOCK, description: 'Desktop');
    $desktopB = createAssetsOpsAsset(AssetState::IN_USE, description: 'Desktop');
    $mouse = createAssetsOpsAsset(AssetState::IN_STOCK, description: 'Mouse');

    $this->get(route('assets.items.global', ['item' => 'Desktop']))
        ->assertOk()
        ->assertSee($desktopB->code)
        ->assertDontSee($desktopA->code)
        ->assertDontSee($mouse->code);
});

test('stock page lists only in stock assets and release moves item out of stock', function () {
    $user = createAssetsOpsUser(['assets.view', 'assets.transfer']);
    $this->actingAs($user);

    $stockUnit = createAssetsOpsUnit('501');
    $targetUnit = createAssetsOpsUnit('502');
    $targetSector = createAssetsOpsSector($targetUnit, '502');

    config()->set('assets.stock_default_unit_id', $stockUnit->id);

    $stockAsset = createAssetsOpsAsset(AssetState::IN_STOCK, $stockUnit->id, null, 'Notebook Estoque');
    $inUseAsset = createAssetsOpsAsset(AssetState::IN_USE, $targetUnit->id, $targetSector->id, 'Notebook Em Uso');

    $this->get(route('assets.stock.index'))
        ->assertOk()
        ->assertSee('Notebook Estoque')
        ->assertDontSee('Notebook Em Uso');

    Livewire::test(AssetsStockIndex::class)
        ->call('openRelease', $stockAsset->id)
        ->set('unitId', $targetUnit->id)
        ->set('sectorId', $targetSector->id)
        ->call('release');

    $stockAsset->refresh();

    expect($stockAsset->state)->toBe(AssetState::IN_USE)
        ->and($stockAsset->unit_id)->toBe($targetUnit->id)
        ->and($stockAsset->sector_id)->toBe($targetSector->id)
        ->and($stockAsset->events()->latest('id')->first()?->type)->toBe(AssetEventType::RELEASED);
});

test('stock page creates invoice via modal form without leaving page', function () {
    $user = createAssetsOpsUser(['assets.view', 'assets.invoices.manage']);
    $this->actingAs($user);

    $this->get(route('assets.stock.index'))
        ->assertOk();

    $financialBlockId = DB::table('financial_blocks')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Bloco estoque',
        'filter' => 'bloco estoque',
        'acronym' => 'BE',
        'color' => '#123456',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $supplierId = DB::table('suppliers')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Fornecedor Estoque',
        'filter' => 'fornecedor estoque',
        'document' => '12345678000190',
        'email' => 'fornecedor@estoque.test',
        'phone' => '62999990000',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Livewire::test(AssetsStockIndex::class)
        ->call('openInvoiceForm')
        ->set('invoiceNumber', 'NF-EST-001')
        ->set('invoiceSeries', 'A1')
        ->set('financialBlockId', $financialBlockId)
        ->set('supplierId', $supplierId)
        ->set('issueDate', now()->toDateString())
        ->set('receivedDate', now()->toDateString())
        ->set('invoiceNotes', 'Cadastro via modal de estoque')
        ->call('saveInvoice');

    expect(\App\Models\Assets\AssetInvoice::query()
        ->where('invoice_number', 'NF-EST-001')
        ->exists())->toBeTrue();
});

test('release order creates batch release and renders cover sheet', function () {
    $user = createAssetsOpsUser(['assets.view', 'assets.transfer']);
    $this->actingAs($user);

    $stockUnit = createAssetsOpsUnit('611');
    $targetUnit = createAssetsOpsUnit('612');
    $targetSector = createAssetsOpsSector($targetUnit, '612');

    config()->set('assets.stock_default_unit_id', $stockUnit->id);

    $assetA = createAssetsOpsAsset(AssetState::IN_STOCK, $stockUnit->id, null, 'Monitor');
    $assetB = createAssetsOpsAsset(AssetState::IN_STOCK, $stockUnit->id, null, 'Teclado');

    Livewire::test(ReleaseOrderCreate::class)
        ->set('selectedAssetIds', [$assetA->id, $assetB->id])
        ->set('unitId', $targetUnit->id)
        ->set('sectorId', $targetSector->id)
        ->set('requesterName', 'Equipe de TI')
        ->set('receiverName', 'Responsavel Unidade')
        ->set('notes', 'Liberacao em lote para inicio de operacao')
        ->call('createReleaseOrder');

    $order = AssetReleaseOrder::query()->latest('id')->first();

    expect($order)->not->toBeNull()
        ->and($order->total_assets)->toBe(2);

    $assetA->refresh();
    $assetB->refresh();

    expect($assetA->state)->toBe(AssetState::IN_USE)
        ->and($assetA->unit_id)->toBe($targetUnit->id)
        ->and($assetB->state)->toBe(AssetState::IN_USE)
        ->and($assetB->unit_id)->toBe($targetUnit->id);

    $this->get(route('assets.release-orders.show', $order->uuid))
        ->assertOk()
        ->assertSee($order->code)
        ->assertSee('Assinatura de quem recebe');
});

test('asset show can load more timeline items', function () {
    $user = createAssetsOpsUser(['assets.view']);
    $this->actingAs($user);

    $asset = createAssetsOpsAsset(AssetState::IN_STOCK);

    foreach (range(1, 12) as $index) {
        $asset->events()->create([
            'type' => AssetEventType::AUDITED,
            'notes' => 'Evento '.$index,
        ]);
    }

    Livewire::test(AssetShow::class, ['uuid' => $asset->uuid])
        ->assertSet('eventsToShow', 10)
        ->call('loadMoreEvents')
        ->assertSet('eventsToShow', 20);
});

test('operation components execute transfer state change and return', function () {
    $user = createAssetsOpsUser(['assets.transfer', 'assets.state.change', 'assets.return', 'assets.view']);
    $this->actingAs($user);

    $unitA = createAssetsOpsUnit('401');
    $unitB = createAssetsOpsUnit('402');
    $sectorA = createAssetsOpsSector($unitA, '401');
    $sectorB = createAssetsOpsSector($unitB, '402');

    config()->set('assets.patrimony_unit_id', $unitB->id);

    $asset = createAssetsOpsAsset(AssetState::IN_USE, $unitA->id, $sectorA->id);

    Livewire::test(TransferAssetForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->set('unitId', $unitB->id)
        ->set('sectorId', $sectorB->id)
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    Livewire::test(ChangeStateForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->set('toState', AssetState::IN_STOCK->value)
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    Livewire::test(ChangeStateForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->set('toState', AssetState::MAINTENANCE->value)
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    Livewire::test(ChangeStateForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->set('toState', AssetState::IN_USE->value)
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    Livewire::test(ReturnToPatrimonyForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    $asset->refresh();

    expect($asset->state)->toBe(AssetState::IN_STOCK)
        ->and($asset->unit_id)->toBe($unitB->id)
        ->and($asset->sector_id)->toBeNull();
});

test('audit mobile finds asset uploads photo and records audit event', function () {
    Storage::fake('public');

    $user = createAssetsOpsUser(['assets.audit']);
    $this->actingAs($user);

    $asset = createAssetsOpsAsset(AssetState::IN_STOCK);
    $file = UploadedFile::fake()->image('audit.jpg');

    Livewire::test(AuditMobile::class)
        ->set('searchCode', $asset->code)
        ->call('searchAsset')
        ->assertSet('assetId', $asset->id)
        ->set('photo', $file)
        ->set('notes', 'Auditoria de campo')
        ->call('audit')
        ->assertHasNoErrors()
        ->assertSet('assetId', null);

    $asset->refresh();
    $event = $asset->events()->first();

    expect($event?->type)->toBe(AssetEventType::AUDITED)
        ->and($event?->payload['photo_path'] ?? null)->not->toBeNull();

    Storage::disk('public')->assertExists($event->payload['photo_path']);
});
