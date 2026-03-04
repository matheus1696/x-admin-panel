<?php

namespace App\Enums\Assets;

enum AssetState: string
{
    case IN_STOCK = 'IN_STOCK';
    case RELEASED = 'RELEASED';
    case IN_USE = 'IN_USE';
    case MAINTENANCE = 'MAINTENANCE';
    case DAMAGED = 'DAMAGED';
    case RETURNED_TO_PATRIMONY = 'RETURNED_TO_PATRIMONY';
}
