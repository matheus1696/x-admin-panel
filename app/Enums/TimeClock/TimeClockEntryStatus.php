<?php

namespace App\Enums\TimeClock;

enum TimeClockEntryStatus: string
{
    case OK = 'OK';
    case MISSING_GPS = 'MISSING_GPS';
    case MISSING_PHOTO = 'MISSING_PHOTO';
    case LOW_ACCURACY = 'LOW_ACCURACY';
}
