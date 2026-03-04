<?php

namespace App\Enums\Assets;

enum AssetEventType: string
{
    case STOCK_RECEIVED = 'STOCK_RECEIVED';
    case RELEASED = 'RELEASED';
    case IN_USE = 'IN_USE';
    case MAINTENANCE = 'MAINTENANCE';
    case DAMAGED = 'DAMAGED';
    case RETURNED_TO_PATRIMONY = 'RETURNED_TO_PATRIMONY';
    case TRANSFERRED = 'TRANSFERRED';
    case AUDITED = 'AUDITED';
    case STATE_CHANGED = 'STATE_CHANGED';
}
