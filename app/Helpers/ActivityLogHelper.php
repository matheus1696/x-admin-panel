<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogHelper
{
    public static function add()
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id'       => $user?->id,
            'ip_address'    => Request::ip(),
            'method'        => Request::method(),
            'url'           => Request::fullUrl(),
        ]);
    }
}
