<?php

namespace App\Validation\TimeClock;

use RuntimeException;

class TimeClockValidationException extends RuntimeException
{
    public function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }
}
