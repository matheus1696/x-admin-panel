<?php

namespace App\Validation\Assets;

use App\Enums\Assets\AssetState;
use App\Exceptions\Assets\AssetsValidationException;

class AllowedStateTransitionValidator
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $allowedTransitions = [
        'IN_STOCK' => ['IN_USE', 'MAINTENANCE', 'DAMAGED'],
        'IN_USE' => ['IN_STOCK', 'MAINTENANCE', 'DAMAGED'],
        'MAINTENANCE' => ['IN_STOCK', 'IN_USE', 'DAMAGED'],
        'DAMAGED' => [],
    ];

    public function validateOrFail(AssetState $fromState, AssetState $toState): void
    {
        if ($fromState === $toState) {
            throw new AssetsValidationException('O estado de destino deve ser diferente do estado atual.');
        }

        $allowed = $this->allowedTransitions[$fromState->value] ?? [];

        if (! in_array($toState->value, $allowed, true)) {
            throw new AssetsValidationException('Transicao de estado nao permitida.');
        }
    }
}
