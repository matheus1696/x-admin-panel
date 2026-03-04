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
        'IN_STOCK' => ['RELEASED'],
        'RELEASED' => ['IN_USE', 'MAINTENANCE', 'RETURNED_TO_PATRIMONY'],
        'IN_USE' => ['MAINTENANCE'],
        'MAINTENANCE' => ['RELEASED'],
        'DAMAGED' => [],
        'RETURNED_TO_PATRIMONY' => [],
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
