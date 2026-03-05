<?php

namespace App\DTOs\Assets;

class ReleaseAssetDTO
{
    public function __construct(
        public readonly int $assetId,
        public readonly int $unitId,
        public readonly string $patrimonyNumber,
        public readonly ?int $sectorId = null,
        public readonly ?int $actorUserId = null,
        public readonly ?string $notes = null,
    ) {}
}
