<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\Administration\User\User;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'ip_address',
        'method',
        'url',
        'action',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    protected static function booted(): void
    {
        static::creating(function (self $log): void {
            if (!$log->uuid) {
                $log->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
