<?php

use App\Enums\Assets\AssetState;
use App\Livewire\Assets\InvoiceForm;
use App\Livewire\Assets\InvoiceIndex;
use App\Livewire\Assets\InvoiceShow;
use App\Livewire\Assets\ReceiveStockForm;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
use App\Models\Assets\AssetInvoiceItem;
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

test('invoice pages render for authorized users', function () {
    $user = createAssetsInvoicesManager();
    $invoice = AssetInvoice::create([
        'invoice_number' => 'NF-UI-001',
        'supplier_name' => 'Fornecedor UI',
        'issue_date' => now()->toDateString(),
        'total_amount' => 1000,
    ]);

    $this->actingAs($user)
        ->get(route('assets.invoices.index'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('assets.invoices.create'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('assets.invoices.show', $invoice->uuid))
        ->assertOk();
});

test('invoice show exposes direct link to assets filtered by invoice item', function () {
    $user = createAssetsInvoicesManager();
    Permission::findOrCreate('assets.view', 'web');
    $user->givePermissionTo('assets.view');

    $invoice = AssetInvoice::create([
        'invoice_number' => 'NF-UI-LINK',
        'supplier_name' => 'Fornecedor Link',
        'issue_date' => now()->toDateString(),
        'total_amount' => 100,
    ]);

    $item = AssetInvoiceItem::create([
        'asset_invoice_id' => $invoice->id,
        'description' => 'Headset',
        'quantity' => 1,
        'unit_price' => 100,
        'total_price' => 100,
    ]);

    $this->actingAs($user)
        ->get(route('assets.invoices.show', $invoice->uuid))
        ->assertOk()
        ->assertSee(str_replace('&', '&amp;', route('assets.index', ['invoice_uuid' => $invoice->uuid, 'invoice_item_id' => $item->id])), false);
});

test('invoice form creates and updates invoices through livewire', function () {
    $user = createAssetsInvoicesManager();
    $this->actingAs($user);

    Livewire::test(InvoiceForm::class)
        ->set('invoiceNumber', 'NF-UI-002')
        ->set('invoiceSeries', 'A')
        ->set('supplierName', 'Fornecedor UI')
        ->set('supplierDocument', '11.111.111/0001-11')
        ->set('issueDate', '2026-03-04')
        ->set('receivedDate', '2026-03-05')
        ->set('totalAmount', '2500.50')
        ->set('notes', 'Criada no teste')
        ->call('save')
        ->assertHasNoErrors();

    $invoice = AssetInvoice::query()->firstOrFail();

    expect($invoice->invoice_number)->toBe('NF-UI-002')
        ->and($invoice->created_user_id)->toBe($user->id);

    Livewire::test(InvoiceForm::class, ['uuid' => $invoice->uuid])
        ->set('supplierName', 'Fornecedor Atualizado')
        ->set('totalAmount', '3000.00')
        ->call('save')
        ->assertHasNoErrors();

    $invoice->refresh();

    expect($invoice->supplier_name)->toBe('Fornecedor Atualizado')
        ->and((float) $invoice->total_amount)->toBe(3000.0);
});

test('invoice show manages invoice items through livewire', function () {
    $user = createAssetsInvoicesManager();
    $this->actingAs($user);

    $invoice = AssetInvoice::create([
        'invoice_number' => 'NF-UI-003',
        'supplier_name' => 'Fornecedor Itens',
        'issue_date' => now()->toDateString(),
        'total_amount' => 500,
    ]);

    $component = Livewire::test(InvoiceShow::class, ['uuid' => $invoice->uuid])
        ->call('createItem')
        ->set('itemCode', 'ITEM-UI-01')
        ->set('description', 'Mouse')
        ->set('quantity', 2)
        ->set('unitPrice', '50.00')
        ->set('totalPrice', '100.00')
        ->set('brand', 'Logi')
        ->set('model', 'M100')
        ->call('saveItem')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $item = AssetInvoiceItem::query()->firstOrFail();

    expect($item->description)->toBe('Mouse')
        ->and($item->asset_invoice_id)->toBe($invoice->id);

    $component
        ->call('editItem', $item->id)
        ->set('description', 'Mouse sem fio')
        ->set('totalPrice', '120.00')
        ->call('saveItem')
        ->assertHasNoErrors();

    $item->refresh();

    expect($item->description)->toBe('Mouse sem fio');

    $component->call('deleteItem', $item->id);

    expect(AssetInvoiceItem::count())->toBe(0);
});

test('receive stock form creates assets from invoice item inside invoice show flow', function () {
    $user = createAssetsInvoicesManager();
    Permission::findOrCreate('assets.stock.receive', 'web');
    $user->givePermissionTo('assets.stock.receive');

    $this->actingAs($user);

    $unit = createAssetsInvoicesUnit('501');
    config()->set('assets.stock_default_unit_id', $unit->id);

    $invoice = AssetInvoice::create([
        'invoice_number' => 'NF-UI-004',
        'supplier_name' => 'Fornecedor Estoque UI',
        'issue_date' => now()->toDateString(),
        'total_amount' => 1500,
    ]);

    $item = AssetInvoiceItem::create([
        'asset_invoice_id' => $invoice->id,
        'description' => 'Teclado',
        'quantity' => 2,
        'unit_price' => 750,
        'total_price' => 1500,
    ]);

    Livewire::test(ReceiveStockForm::class, ['assetInvoiceItemId' => $item->id])
        ->call('open')
        ->set('quantity', 2)
        ->set('acquiredDate', '2026-03-04')
        ->set('description', 'Teclado mecanico')
        ->set('brand', 'Keyco')
        ->set('model', 'K100')
        ->call('save')
        ->assertRedirect(route('assets.invoices.show', $invoice->uuid));

    expect(Asset::count())->toBe(2)
        ->and(Asset::query()->first()?->state)->toBe(AssetState::IN_STOCK)
        ->and(Asset::query()->first()?->unit_id)->toBe($unit->id);
});
