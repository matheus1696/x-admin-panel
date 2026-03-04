<?php

namespace App\DTOs\Assets;

class ReceiveStockDTO
{
    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        public readonly int $invoiceItemId,
        public readonly int $quantity,
        public readonly ?string $description = null,
        public readonly ?string $brand = null,
        public readonly ?string $model = null,
        public readonly ?string $acquiredDate = null,
        public readonly ?int $actorUserId = null,
        public readonly ?array $metadata = null,
    ) {}
}
