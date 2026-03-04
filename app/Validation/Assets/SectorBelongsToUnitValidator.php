<?php

namespace App\Validation\Assets;

use App\Exceptions\Assets\AssetsValidationException;
use App\Models\Configuration\Establishment\Establishment\Department;

class SectorBelongsToUnitValidator
{
    public function validateOrFail(?int $unitId, ?int $sectorId): void
    {
        if ($sectorId === null) {
            return;
        }

        $sector = Department::query()->findOrFail($sectorId);

        if ($unitId === null || (int) $sector->establishment_id !== $unitId) {
            throw new AssetsValidationException('O setor informado nao pertence a unidade selecionada.');
        }
    }
}
