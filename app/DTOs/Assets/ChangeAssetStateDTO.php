<?php

namespace App\DTOs\Assets;

use App\Enums\Assets\AssetState;

class ChangeAssetStateDTO
{
    public function __construct(
        public readonly int $assetId,
        public readonly AssetState $toState,
        public readonly ?int $actorUserId = null,
        public readonly ?string $notes = null,
    ) {}
}
