<?php

namespace App\Validation\TimeClock;

use Illuminate\Http\UploadedFile;

class PhotoRequiredValidator
{
    public function validateOrFail(?UploadedFile $photo): void
    {
        if (! config('time_clock.photo_required')) {
            return;
        }

        if (! $photo) {
            throw new TimeClockValidationException('missing_photo', 'Nao foi possivel validar os dados do registro.');
        }
    }
}
