<?php

namespace App\DTOs\Assets;

class CreateReleaseOrderDTO
{
    /**
     * @param array<int> $assetIds
     */
    public function __construct(
        public readonly array $assetIds,
        public readonly int $unitId,
        public readonly ?int $sectorId = null,
        public readonly string $requesterName = '',
        public readonly ?string $receiverName = null,
        public readonly ?string $notes = null,
        public readonly ?int $actorUserId = null,
    ) {}
}

