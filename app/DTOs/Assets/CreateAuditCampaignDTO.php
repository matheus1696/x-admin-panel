<?php

namespace App\DTOs\Assets;

class CreateAuditCampaignDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?int $unitId = null,
        public readonly ?int $sectorId = null,
        public readonly ?int $financialBlockId = null,
        public readonly ?string $startDate = null,
        public readonly ?string $dueDate = null,
        public readonly ?int $createdUserId = null,
    ) {}
}

