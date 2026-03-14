<?php

namespace App\Validation\TimeClock;

class GpsAccuracyValidator
{
    public function validateOrFail(?float $accuracy): void
    {
        $maxAllowedAccuracy = config('time_clock.max_allowed_accuracy_meters');

        if ($maxAllowedAccuracy === null || $accuracy === null) {
            return;
        }

        if ($accuracy > (float) $maxAllowedAccuracy) {
            throw new TimeClockValidationException(
                'low_accuracy',
                'A precisao do GPS esta abaixo do minimo esperado para este registro.',
            );
        }
    }
}
