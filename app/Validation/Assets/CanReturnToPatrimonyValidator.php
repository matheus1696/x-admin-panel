<?php

namespace App\Validation\Assets;

use App\DTOs\Assets\ReturnToPatrimonyDTO;
use App\Enums\Assets\AssetState;
use App\Models\Assets\Asset;

class CanReturnToPatrimonyValidator
{
    public function __construct(
        private readonly AllowedStateTransitionValidator $allowedStateTransitionValidator,
    ) {}

    public function validateOrFail(Asset $asset, ReturnToPatrimonyDTO $dto): void
    {
        $this->allowedStateTransitionValidator->validateOrFail($asset->state, AssetState::IN_STOCK);
    }
}
