<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogActivity
{
    public static function add(string $action)
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id'       => $user?->id,
            'user_name'     => $user?->name,
            'ip_address'    => Request::ip(),
            'method'        => Request::method(),
            'url'           => Request::fullUrl(),
            'action'        => $action,
        ]);
    }
}
