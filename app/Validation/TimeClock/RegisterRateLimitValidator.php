<?php

namespace App\Validation\TimeClock;

use App\Models\TimeClock\TimeClockEntry;

class RegisterRateLimitValidator
{
    public function validateOrFail(int $userId): void
    {
        $lastEntry = TimeClockEntry::query()
            ->where('user_id', $userId)
            ->latest('occurred_at')
            ->first();

        if (! $lastEntry) {
            return;
        }

        if ($lastEntry->occurred_at && $lastEntry->occurred_at->diffInSeconds(now()) < 5) {
            throw new TimeClockValidationException('rate_limited', 'Aguarde alguns segundos antes de registrar novamente.');
        }
    }
}
