<?php

use App\Enums\Assets\AssetState;
use App\Livewire\Assets\InvoiceIndex;
use App\Models\Administration\Product\Product;
use App\Models\Administration\Product\ProductMeasureUnit;
use App\Models\Administration\Supplier\Supplier;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function createAssetsInvoicesManager(): User
{
    $user = User::factory()->create();

    Permission::findOrCreate('assets.invoices.manage', 'web');
    $user->givePermissionTo('assets.invoices.manage');

    return $user;
}

function createAssetsInvoicesUnit(string $suffix): Establishment
{
    $countryId = DB::table('region_countries')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym_2' => 'E'.strtoupper(Str::random(1)),
        'acronym_3' => 'BY'.strtoupper(Str::random(1)).strtoupper(Str::random(1)),
        'title' => 'Pais nota '.$suffix,
        'filter' => 'pais nota '.$suffix,
        'country_ing' => 'Country invoice '.$suffix,
        'filter_country_ing' => 'country invoice '.$suffix,
        'code_iso' => 'EI-'.$suffix,
        'code_ddi' => '58'.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $stateId = DB::table('region_states')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym' => 'I'.$suffix,
        'title' => 'Estado nota '.$suffix,
        'filter' => 'estado nota '.$suffix,
        'code_uf' => 'IN'.$suffix,
        'code_ddd' => '5'.$suffix,
        'is_active' => true,
        'country_id' => $countryId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $cityId = DB::table('region_cities')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'code_ibge' => 'INV'.$suffix,
        'title' => 'Cidade nota '.$suffix,
        'filter' => 'cidade nota '.$suffix,
        'code_cep' => '73'.$suffix,
        'is_active' => true,
        'state_id' => $stateId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $typeId = DB::table('establishment_types')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Tipo nota '.$suffix,
        'filter' => 'tipo nota '.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $financialBlockId = DB::table('financial_blocks')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Bloco nota '.$suffix,
        'filter' => 'bloco nota '.$suffix,
        'acronym' => 'BN'.$suffix,
        'color' => '#334455',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return Establishment::create([
        'code' => 'INV-'.$suffix,
        'title' => 'Unidade nota '.$suffix,
        'surname' => 'UNI'.$suffix,
        'address' => 'Rua Nota',
        'number' => '10',
        'district' => 'Centro',
        'city_id' => $cityId,
        'state_id' => $stateId,
        'type_establishment_id' => $typeId,
        'financial_block_id' => $financialBlockId,
        'is_active' => true,
    ]);
}

function createAssetsInvoicesProduct(string $suffix): Product
{
    return Product::create([
        'code' => 'PRD-UI-'.$suffix,
        'title' => 'Produto UI '.$suffix,
        'description' => 'Produto de teste UI '.$suffix,
    ]);
}

function createAssetsInvoicesMeasureUnit(string $suffix, int $baseQuantity = 1): ProductMeasureUnit
{
    return ProductMeasureUnit::create([
        'acronym' => 'UN/'.$suffix,
        'title' => 'Unidade UI '.$suffix,
        'base_quantity' => $baseQuantity,
    ]);
}

test('invoice index page renders for authorized users', function () {
    $user = createAssetsInvoicesManager();

    $this->actingAs($user)
        ->get(route('assets.invoices.index'))
        ->assertOk();
});

test('invoice index modal creates and updates invoices through livewire', function () {
    $user = createAssetsInvoicesManager();
    $this->actingAs($user);

    $financialBlock = DB::table('financial_blocks')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Bloco Nota UI',
        'filter' => 'bloco nota ui',
        'acronym' => 'BNUI',
        'color' => '#123456',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $supplierOne = Supplier::query()->create([
        'title' => 'Fornecedor UI',
        'document' => '11.111.111/0001-11',
        'is_active' => true,
    ]);

    $supplierTwo = Supplier::query()->create([
        'title' => 'Fornecedor Atualizado',
        'document' => '22.222.222/0001-22',
        'is_active' => true,
    ]);

    Livewire::test(InvoiceIndex::class)
        ->call('openCreateInvoice')
        ->set('invoiceNumber', 'NF-UI-002')
        ->set('invoiceSeries', 'A')
        ->set('financialBlockId', $financialBlock)
        ->set('supplierId', $supplierOne->id)
        ->set('supplyOrder', '12345-6789')
        ->set('issueDate', now()->toDateString())
        ->set('receivedDate', now()->toDateString())
        ->set('notes', 'Criada no teste')
        ->call('saveInvoice')
        ->assertHasNoErrors();

    $invoice = AssetInvoice::query()->firstOrFail();

    expect($invoice->invoice_number)->toBe('NF-UI-002')
        ->and($invoice->created_user_id)->toBe($user->id);

    Livewire::test(InvoiceIndex::class)
        ->call('openEditInvoice', $invoice->uuid)
        ->set('financialBlockId', $financialBlock)
        ->set('supplierId', $supplierTwo->id)
        ->set('supplyOrder', '1234-5678')
        ->call('saveInvoice')
        ->assertHasNoErrors();

    $invoice->refresh();

    expect($invoice->supplier_name)->toBe('Fornecedor Atualizado')
        ->and((int) $invoice->financial_block_id)->toBe($financialBlock)
        ->and($invoice->supply_order)->toBe('1234-5678')
        ->and((float) $invoice->total_amount)->toBe(0.0);
});

test('invoice index modal manages items through livewire', function () {
    $user = createAssetsInvoicesManager();
    $this->actingAs($user);

    $invoice = AssetInvoice::create([
        'invoice_number' => 'NF-UI-MODAL-001',
        'supplier_name' => 'Fornecedor Modal',
        'issue_date' => now()->toDateString(),
        'total_amount' => 0,
    ]);

    $product = createAssetsInvoicesProduct('MODAL');
    $measureUnit = createAssetsInvoicesMeasureUnit('MODAL');

    Livewire::test(InvoiceIndex::class)
        ->call('openViewInvoice', $invoice->uuid)
        ->set('itemProductId', $product->id)
        ->set('itemProductMeasureUnitId', $measureUnit->id)
        ->set('itemCode', 'ITEM-MODAL-01')
        ->set('itemQuantity', 2)
        ->set('itemUnitPrice', '80.00')
        ->set('itemBrand', 'Marca Modal')
        ->set('itemModel', 'Modelo Modal')
        ->call('saveViewInvoiceItem')
        ->assertHasNoErrors();

    $invoice->refresh();
    $item = $invoice->items()->first();

    expect($invoice->items()->count())->toBe(1)
        ->and((float) $invoice->total_amount)->toBe(160.0)
        ->and($invoice->items()->first()?->item_code)->toBe('ITEM-MODAL-01');

    Livewire::test(InvoiceIndex::class)
        ->call('openViewInvoice', $invoice->uuid)
        ->call('editViewInvoiceItem', $item->id)
        ->set('itemQuantity', 3)
        ->set('itemUnitPrice', '90.00')
        ->call('saveViewInvoiceItem')
        ->assertHasNoErrors();

    $invoice->refresh();

    expect($invoice->items()->count())->toBe(1)
        ->and((float) $invoice->total_amount)->toBe(270.0);
});

test('invoice finalization sends pending item quantities to stock and locks item editing', function () {
    $user = createAssetsInvoicesManager();
    $this->actingAs($user);

    $unit = createAssetsInvoicesUnit('601');
    config()->set('assets.stock_default_unit_id', $unit->id);

    $invoice = AssetInvoice::create([
        'invoice_number' => 'NF-UI-005',
        'supplier_name' => 'Fornecedor Finalizacao',
        'issue_date' => now()->toDateString(),
        'total_amount' => 0,
    ]);

    $product = createAssetsInvoicesProduct('005');
    $measureUnit = createAssetsInvoicesMeasureUnit('005');

    $component = Livewire::test(InvoiceIndex::class)
        ->call('openViewInvoice', $invoice->uuid)
        ->set('itemProductId', $product->id)
        ->set('itemProductMeasureUnitId', $measureUnit->id)
        ->set('itemCode', '265963')
        ->set('itemQuantity', 3)
        ->set('itemUnitPrice', '100.00')
        ->set('itemBrand', 'Marca X')
        ->set('itemModel', 'Modelo Y')
        ->call('saveViewInvoiceItem')
        ->assertHasNoErrors()
        ->call('finalizeViewInvoice');

    $invoice->refresh();

    expect($invoice->is_finalized)->toBeTrue()
        ->and($invoice->finalized_at)->not->toBeNull()
        ->and((int) Asset::query()->whereHas('invoiceItem', fn ($q) => $q->where('asset_invoice_id', $invoice->id))->count())->toBe(3)
        ->and(Asset::query()->whereHas('invoiceItem', fn ($q) => $q->where('asset_invoice_id', $invoice->id))->first()?->state)->toBe(AssetState::IN_STOCK);

    $codes = Asset::query()
        ->whereHas('invoiceItem', fn ($q) => $q->where('asset_invoice_id', $invoice->id))
        ->orderBy('id')
        ->pluck('code')
        ->values()
        ->all();

    $patrimonyNumbers = Asset::query()
        ->whereHas('invoiceItem', fn ($q) => $q->where('asset_invoice_id', $invoice->id))
        ->orderBy('id')
        ->pluck('patrimony_number')
        ->values()
        ->all();

    expect($codes)->toBe(['265963', '265964', '265965'])
        ->and($patrimonyNumbers)->toBe(['265963', '265964', '265965']);

    $beforeItemsCount = $invoice->items()->count();

    $component
        ->call('openViewInvoice', $invoice->uuid)
        ->set('itemProductId', $product->id)
        ->set('itemProductMeasureUnitId', $measureUnit->id)
        ->set('itemQuantity', 1)
        ->set('itemUnitPrice', '50.00')
        ->call('saveViewInvoiceItem');

    $invoice->refresh();

    expect($invoice->items()->count())->toBe($beforeItemsCount);
});

