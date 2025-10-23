<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'ip_address',
        'method',
        'url',
        'action',
    ];
    
    //UUID
    protected static function booted()
    {
        static::creating(function ($log) {
            if (!$log->uuid) {
                $log->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
