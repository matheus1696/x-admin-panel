<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogHelper
{
    public static function action($description)
    {
        ActivityLog::create([
            'user_id'       => Auth::user()->id ?? null,
            'ip_address'    => Request::ip(),
            'method'        => Request::method(),
            'url'           => Request::fullUrl(),
            'description'   => $description,
        ]);
    }
}
