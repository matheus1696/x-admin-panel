<?php

namespace App\Validation\Assets;

use App\DTOs\Assets\ReleaseAssetDTO;
use App\Enums\Assets\AssetState;
use App\Models\Assets\Asset;

class CanReleaseAssetValidator
{
    public function __construct(
        private readonly AllowedStateTransitionValidator $allowedStateTransitionValidator,
        private readonly SectorBelongsToUnitValidator $sectorBelongsToUnitValidator,
    ) {}

    public function validateOrFail(Asset $asset, ReleaseAssetDTO $dto): void
    {
        $this->allowedStateTransitionValidator->validateOrFail($asset->state, AssetState::IN_USE);
        $this->sectorBelongsToUnitValidator->validateOrFail($dto->unitId, $dto->sectorId);
    }
}
