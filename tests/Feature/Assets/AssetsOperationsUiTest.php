<?php

use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Livewire\Assets\AssetShow;
use App\Livewire\Assets\AssetsIndex;
use App\Livewire\Assets\AuditMobile;
use App\Livewire\Assets\ChangeStateForm;
use App\Livewire\Assets\ReleaseAssetForm;
use App\Livewire\Assets\ReturnToPatrimonyForm;
use App\Livewire\Assets\TransferAssetForm;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
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
    $countryId = DB::table('region_countries')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym_2' => 'D'.strtoupper(Str::random(1)),
        'acronym_3' => 'BX'.strtoupper(Str::random(1)).strtoupper(Str::random(1)),
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

function createAssetsOpsAsset(AssetState $state, ?int $unitId = null, ?int $sectorId = null): Asset
{
    return Asset::create([
        'code' => 'AST-OPS-'.Str::upper(Str::random(8)),
        'description' => 'Ativo operacional',
        'state' => $state,
        'unit_id' => $unitId,
        'sector_id' => $sectorId,
    ]);
}

test('assets pages render for authorized users', function () {
    $user = createAssetsOpsUser(['assets.view', 'assets.release', 'assets.transfer', 'assets.state.change', 'assets.return']);
    $asset = createAssetsOpsAsset(AssetState::IN_STOCK);

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

    $inStock = createAssetsOpsAsset(AssetState::IN_STOCK);
    $released = createAssetsOpsAsset(AssetState::RELEASED);

    Livewire::test(AssetsIndex::class)
        ->set('filters.state', 'RELEASED')
        ->assertSee($released->code)
        ->assertDontSee($inStock->code);
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
        'state' => AssetState::IN_STOCK,
    ]);

    $assetB = Asset::create([
        'invoice_item_id' => $itemB->id,
        'code' => 'AST-FLT-B',
        'description' => 'Teclado',
        'state' => AssetState::IN_STOCK,
    ]);

    $this->get(route('assets.index', ['invoice_uuid' => $invoice->uuid, 'invoice_item_id' => $itemA->id]))
        ->assertOk()
        ->assertSee($assetA->code)
        ->assertDontSee($assetB->code);
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

test('operation components execute release transfer state change and return', function () {
    $user = createAssetsOpsUser(['assets.release', 'assets.transfer', 'assets.state.change', 'assets.return', 'assets.view']);
    $this->actingAs($user);

    $unitA = createAssetsOpsUnit('401');
    $unitB = createAssetsOpsUnit('402');
    $sectorA = createAssetsOpsSector($unitA, '401');
    $sectorB = createAssetsOpsSector($unitB, '402');

    config()->set('assets.patrimony_unit_id', $unitB->id);

    $asset = createAssetsOpsAsset(AssetState::IN_STOCK);

    Livewire::test(ReleaseAssetForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->set('unitId', $unitA->id)
        ->set('sectorId', $sectorA->id)
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    $asset->refresh();
    expect($asset->state)->toBe(AssetState::RELEASED);

    Livewire::test(TransferAssetForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->set('unitId', $unitB->id)
        ->set('sectorId', $sectorB->id)
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    Livewire::test(ChangeStateForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->set('toState', AssetState::IN_USE->value)
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    Livewire::test(ChangeStateForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->set('toState', AssetState::MAINTENANCE->value)
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    Livewire::test(ChangeStateForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->set('toState', AssetState::RELEASED->value)
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    Livewire::test(ReturnToPatrimonyForm::class, ['assetUuid' => $asset->uuid])
        ->call('open')
        ->call('save')
        ->assertRedirect(route('assets.show', $asset->uuid));

    $asset->refresh();

    expect($asset->state)->toBe(AssetState::RETURNED_TO_PATRIMONY)
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
