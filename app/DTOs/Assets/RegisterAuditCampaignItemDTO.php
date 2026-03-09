<?php

namespace App\DTOs\Assets;

class RegisterAuditCampaignItemDTO
{
    public function __construct(
        public readonly int $campaignItemId,
        public readonly string $status,
        public readonly ?string $photoPath = null,
        public readonly ?string $notes = null,
        public readonly ?string $observedUnit = null,
        public readonly ?string $observedSector = null,
        public readonly ?int $actorUserId = null,
    ) {}
}

