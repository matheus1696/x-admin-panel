<?php

namespace App\DTOs\Assets;

class ReturnToPatrimonyDTO
{
    public function __construct(
        public readonly int $assetId,
        public readonly ?int $actorUserId = null,
        public readonly ?string $notes = null,
    ) {}
}
