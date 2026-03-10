<?php

namespace App\Enums\Process;

enum ProcessStatus: string
{
    case OPEN = 'OPEN';
    case IN_PROGRESS = 'IN_PROGRESS';
    case ON_HOLD = 'ON_HOLD';
    case CLOSED = 'CLOSED';
    case CANCELLED = 'CANCELLED';
}

