<?php

namespace App\Enums\Assets;

enum AssetState: string
{
    case IN_STOCK = 'IN_STOCK';
    case IN_USE = 'IN_USE';
    case MAINTENANCE = 'MAINTENANCE';
    case DAMAGED = 'DAMAGED';
}
