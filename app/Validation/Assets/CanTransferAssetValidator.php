<?php

namespace App\Validation\Assets;

use App\DTOs\Assets\TransferAssetDTO;
use App\Exceptions\Assets\AssetsValidationException;
use App\Models\Assets\Asset;

class CanTransferAssetValidator
{
    public function __construct(
        private readonly SectorBelongsToUnitValidator $sectorBelongsToUnitValidator,
    ) {}

    public function validateOrFail(Asset $asset, TransferAssetDTO $dto): void
    {
        if ((int) $asset->unit_id === $dto->unitId && (int) ($asset->sector_id ?? 0) === (int) ($dto->sectorId ?? 0)) {
            throw new AssetsValidationException('A transferencia precisa alterar a unidade ou o setor do ativo.');
        }

        $this->sectorBelongsToUnitValidator->validateOrFail($dto->unitId, $dto->sectorId);
    }
}
