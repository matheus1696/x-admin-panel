<?php

namespace App\Models\Process;

use App\Models\Administration\User\User;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessEvent extends Model
{
    use HasUuid;

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'process_id',
        'event_number',
        'event_type',
        'description',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'event_number' => 'integer',
        'created_at' => 'datetime',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
