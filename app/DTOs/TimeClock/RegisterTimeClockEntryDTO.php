<?php

namespace App\DTOs\TimeClock;

use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;

class RegisterTimeClockEntryDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly CarbonImmutable $occurredAt,
        public readonly ?UploadedFile $photo,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?float $accuracy,
        public readonly array $deviceMeta,
        public readonly string $status,
        public readonly ?int $locationId = null,
    ) {
    }
}
