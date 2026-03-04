<?php

namespace App\DTOs\Assets;

class CreateInvoiceDTO
{
    public function __construct(
        public readonly string $invoiceNumber,
        public readonly ?string $invoiceSeries,
        public readonly string $supplierName,
        public readonly ?string $supplierDocument,
        public readonly string $issueDate,
        public readonly ?string $receivedDate = null,
        public readonly float|int|string $totalAmount = 0,
        public readonly ?string $notes = null,
        public readonly ?int $createdUserId = null,
    ) {}
}
