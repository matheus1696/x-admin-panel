<?php

namespace App\DTOs\Assets;

class UpsertInvoiceItemDTO
{
    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        public readonly int $assetInvoiceId,
        public readonly ?int $itemId,
        public readonly ?string $itemCode,
        public readonly string $description,
        public readonly int $quantity,
        public readonly float|int|string $unitPrice,
        public readonly float|int|string $totalPrice,
        public readonly ?string $brand = null,
        public readonly ?string $model = null,
        public readonly ?array $metadata = null,
    ) {}
}
