<?php

namespace App\Enums\Process;

enum ProcessEventType: string
{
    case CREATED = 'PROCESS_CREATED';
    case STARTED = 'PROCESS_STARTED';
    case FORWARDED = 'PROCESS_FORWARDED';
    case RETURNED = 'PROCESS_RETURNED';
    case COMMENTED = 'PROCESS_COMMENTED';
    case OWNER_ASSIGNED = 'PROCESS_OWNER_ASSIGNED';
    case CLOSED = 'PROCESS_CLOSED';
    case CANCELLED = 'PROCESS_CANCELLED';
}
