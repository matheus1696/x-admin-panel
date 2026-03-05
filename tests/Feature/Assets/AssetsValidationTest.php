<?php

use App\DTOs\Assets\ReceiveStockDTO;
use App\Enums\Assets\AssetState;
use App\Exceptions\Assets\AssetsValidationException;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
use App\Models\Assets\AssetInvoiceItem;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Validation\Assets\AllowedStateTransitionValidator;
use App\Validation\Assets\CanReceiveStockValidator;
use App\Validation\Assets\SectorBelongsToUnitValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function createAssetsValidationUnit(string $suffix): Establishment
{
    $countryId = DB::table('region_countries')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym_2' => 'B'.strtoupper(Str::random(1)),
        'acronym_3' => 'BRA'.strtoupper(Str::random(3)),
        'title' => 'Brasil '.$suffix,
        'filter' => 'brasil '.$suffix,
        'country_ing' => 'Brazil '.$suffix,
        'filter_country_ing' => 'brazil '.$suffix,
        'code_iso' => 'ISO-'.$suffix,
        'code_ddi' => '55'.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $stateId = DB::table('region_states')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym' => 'S'.$suffix,
        'title' => 'Estado '.$suffix,
        'filter' => 'estado '.$suffix,
        'code_uf' => $suffix,
        'code_ddd' => '8'.$suffix,
        'is_active' => true,
        'country_id' => $countryId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $cityId = DB::table('region_cities')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'code_ibge' => 'IBGE'.$suffix,
        'title' => 'Cidade '.$suffix,
        'filter' => 'cidade '.$suffix,
        'code_cep' => '70'.$suffix,
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
        'acronym' => 'FB'.$suffix,
        'color' => '#123456',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return Establishment::create([
        'code' => 'EST-'.$suffix,
        'title' => 'Unidade '.$suffix,
        'surname' => 'UNI'.$suffix,
        'address' => 'Rua Teste',
        'number' => '100',
        'district' => 'Centro',
        'city_id' => $cityId,
        'state_id' => $stateId,
        'type_establishment_id' => $typeId,
        'financial_block_id' => $financialBlockId,
        'is_active' => true,
    ]);
}

test('allowed state transition validator accepts valid transitions and blocks invalid ones', function () {
    $validator = app(AllowedStateTransitionValidator::class);

    $validator->validateOrFail(AssetState::IN_STOCK, AssetState::IN_USE);
    $validator->validateOrFail(AssetState::IN_USE, AssetState::MAINTENANCE);

    expect(fn () => $validator->validateOrFail(AssetState::IN_STOCK, AssetState::IN_STOCK))
        ->toThrow(AssetsValidationException::class);

    expect(fn () => $validator->validateOrFail(AssetState::DAMAGED, AssetState::IN_USE))
        ->toThrow(AssetsValidationException::class);
});

test('sector belongs to unit validator allows nullable sector and blocks mismatch', function () {
    $validator = app(SectorBelongsToUnitValidator::class);
    $unit = createAssetsValidationUnit('001');
    $otherUnit = createAssetsValidationUnit('002');

    $sector = Department::create([
        'title' => 'Setor 001',
        'establishment_id' => $unit->id,
    ]);

    $validator->validateOrFail($unit->id, null);
    $validator->validateOrFail($unit->id, $sector->id);

    expect(fn () => $validator->validateOrFail($otherUnit->id, $sector->id))
        ->toThrow(AssetsValidationException::class);
});

test('can receive stock validator respects remaining item balance', function () {
    $invoice = AssetInvoice::create([
        'invoice_number' => 'NF-300',
        'supplier_name' => 'Fornecedor Teste',
        'issue_date' => now()->toDateString(),
        'total_amount' => 3000,
    ]);

    $item = AssetInvoiceItem::create([
        'asset_invoice_id' => $invoice->id,
        'description' => 'Monitor',
        'quantity' => 3,
        'unit_price' => 1000,
        'total_price' => 3000,
    ]);

    Asset::create([
        'invoice_item_id' => $item->id,
        'code' => 'AST-VALID-001',
        'description' => 'Monitor',
        'state' => AssetState::IN_STOCK,
    ]);

    $validator = app(CanReceiveStockValidator::class);

    $validator->validateOrFail(
        new ReceiveStockDTO(
            invoiceItemId: $item->id,
            quantity: 2,
        ),
        $item
    );

    expect(fn () => $validator->validateOrFail(
        new ReceiveStockDTO(
            invoiceItemId: $item->id,
            quantity: 3,
        ),
        $item
    ))->toThrow(AssetsValidationException::class);
});
