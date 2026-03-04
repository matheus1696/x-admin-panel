<?php

namespace App\Validation\Assets;

use App\DTOs\Assets\ChangeAssetStateDTO;
use App\Models\Assets\Asset;

class CanChangeStateValidator
{
    public function __construct(
        private readonly AllowedStateTransitionValidator $allowedStateTransitionValidator,
    ) {}

    public function validateOrFail(Asset $asset, ChangeAssetStateDTO $dto): void
    {
        $this->allowedStateTransitionValidator->validateOrFail($asset->state, $dto->toState);
    }
}
