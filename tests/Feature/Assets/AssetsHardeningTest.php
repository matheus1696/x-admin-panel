<?php

use App\DTOs\Assets\ReturnToPatrimonyDTO;
use App\DTOs\Assets\TransferAssetDTO;
use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Services\Assets\AssetOperationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function createAssetsHardeningUnit(string $suffix): Establishment
{
    do {
        $acronym2 = 'H'.strtoupper(Str::random(1));
    } while (DB::table('region_countries')->where('acronym_2', $acronym2)->exists());

    do {
        $acronym3 = 'HZ'.strtoupper(Str::random(2));
    } while (DB::table('region_countries')->where('acronym_3', $acronym3)->exists());

    $countryId = DB::table('region_countries')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym_2' => $acronym2,
        'acronym_3' => $acronym3,
        'title' => 'Pais hard '.$suffix,
        'filter' => 'pais hard '.$suffix,
        'country_ing' => 'Country hard '.$suffix,
        'filter_country_ing' => 'country hard '.$suffix,
        'code_iso' => 'HI-'.$suffix,
        'code_ddi' => '59'.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $stateId = DB::table('region_states')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'acronym' => 'H'.$suffix,
        'title' => 'Estado hard '.$suffix,
        'filter' => 'estado hard '.$suffix,
        'code_uf' => 'HD'.$suffix,
        'code_ddd' => '4'.$suffix,
        'is_active' => true,
        'country_id' => $countryId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $cityId = DB::table('region_cities')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'code_ibge' => 'HAR'.$suffix,
        'title' => 'Cidade hard '.$suffix,
        'filter' => 'cidade hard '.$suffix,
        'code_cep' => '74'.$suffix,
        'is_active' => true,
        'state_id' => $stateId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $typeId = DB::table('establishment_types')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Tipo hard '.$suffix,
        'filter' => 'tipo hard '.$suffix,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $financialBlockId = DB::table('financial_blocks')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'title' => 'Bloco hard '.$suffix,
        'filter' => 'bloco hard '.$suffix,
        'acronym' => 'BH'.$suffix,
        'color' => '#445566',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return Establishment::create([
        'code' => 'HAR-'.$suffix,
        'title' => 'Unidade hard '.$suffix,
        'surname' => 'UHA'.$suffix,
        'address' => 'Rua Hard',
        'number' => '20',
        'district' => 'Centro',
        'city_id' => $cityId,
        'state_id' => $stateId,
        'type_establishment_id' => $typeId,
        'financial_block_id' => $financialBlockId,
        'is_active' => true,
    ]);
}

function createAssetsHardeningSector(Establishment $unit, string $suffix): Department
{
    return Department::create([
        'title' => 'Setor hard '.$suffix,
        'establishment_id' => $unit->id,
    ]);
}

test('asset operations are idempotent for repeated requests after success', function () {
    $user = User::factory()->create();
    $unit = createAssetsHardeningUnit('701');
    $otherUnit = createAssetsHardeningUnit('702');
    $sector = createAssetsHardeningSector($unit, '701');
    $otherSector = createAssetsHardeningSector($otherUnit, '702');

    config()->set('assets.patrimony_unit_id', $otherUnit->id);

    $service = app(AssetOperationService::class);
    $asset = Asset::create([
        'code' => 'AST-HARD-001',
        'description' => 'Ativo hardening',
        'state' => AssetState::IN_USE,
        'unit_id' => $unit->id,
        'sector_id' => $sector->id,
    ]);

    $service->transferAsset(new TransferAssetDTO(
        assetId: $asset->id,
        unitId: $otherUnit->id,
        sectorId: $otherSector->id,
        actorUserId: $user->id,
    ));

    $service->transferAsset(new TransferAssetDTO(
        assetId: $asset->id,
        unitId: $otherUnit->id,
        sectorId: $otherSector->id,
        actorUserId: $user->id,
    ));

    $service->returnToPatrimony(new ReturnToPatrimonyDTO(
        assetId: $asset->id,
        actorUserId: $user->id,
    ));

    $service->returnToPatrimony(new ReturnToPatrimonyDTO(
        assetId: $asset->id,
        actorUserId: $user->id,
    ));

    $asset->refresh();

    expect($asset->events()->count())->toBe(2)
        ->and($asset->events()->pluck('type')->all())->toBe([
            AssetEventType::RETURNED_TO_PATRIMONY,
            AssetEventType::TRANSFERRED,
        ]);
});
