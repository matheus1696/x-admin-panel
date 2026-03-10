<?php

namespace App\Enums\Process;

enum ProcessEventType: string
{
    case CREATED = 'PROCESS_CREATED';
    case STARTED = 'PROCESS_STARTED';
    case CLOSED = 'PROCESS_CLOSED';
    case CANCELLED = 'PROCESS_CANCELLED';
}
