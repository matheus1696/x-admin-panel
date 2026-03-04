<?php

namespace App\DTOs\Assets;

class AuditAssetDTO
{
    public function __construct(
        public readonly int $assetId,
        public readonly string $photoPath,
        public readonly ?int $actorUserId = null,
        public readonly ?string $notes = null,
    ) {}
}
