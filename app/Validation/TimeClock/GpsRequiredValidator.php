<?php

namespace App\Validation\TimeClock;

class GpsRequiredValidator
{
    public function validateOrFail(?float $lat, ?float $lng, ?float $accuracy): void
    {
        if (! config('time_clock.gps_required')) {
            return;
        }

        if ($lat === null || $lng === null || $accuracy === null) {
            throw new TimeClockValidationException('missing_gps', 'Nao foi possivel validar os dados do registro.');
        }
    }
}
