<?php

namespace App\Validation\Assets;

use App\DTOs\Assets\AuditAssetDTO;
use App\Exceptions\Assets\AssetsValidationException;
use App\Models\Assets\Asset;

class CanAuditAssetValidator
{
    public function validateOrFail(Asset $asset, AuditAssetDTO $dto): void
    {
        if (! $asset->exists) {
            throw new AssetsValidationException('Ativo invalido para auditoria.');
        }

        if (trim($dto->photoPath) === '') {
            throw new AssetsValidationException('A auditoria exige uma referencia de foto.');
        }
    }
}
