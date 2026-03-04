<?php

namespace App\Validation\Assets;

use App\DTOs\Assets\ReceiveStockDTO;
use App\Exceptions\Assets\AssetsValidationException;
use App\Models\Assets\AssetInvoiceItem;

class CanReceiveStockValidator
{
    public function validateOrFail(ReceiveStockDTO $dto, AssetInvoiceItem $item): void
    {
        if ($dto->quantity < 1) {
            throw new AssetsValidationException('A quantidade para entrada em estoque deve ser maior que zero.');
        }

        $receivedQuantity = $item->assets()->count();
        $remainingQuantity = max(0, (int) $item->quantity - $receivedQuantity);

        if ($dto->quantity > $remainingQuantity) {
            throw new AssetsValidationException('A quantidade recebida nao pode ser maior que o saldo disponivel do item da nota.');
        }
    }
}
