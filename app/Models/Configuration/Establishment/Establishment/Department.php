<?php

namespace App\Models\Configuration\Establishment\Establishment;

use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{    
    use HasTitleFilter, HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'title',
        'filter',
        'contact',
        'extension',
        'type_contact',
        'establishment_id'
    ];

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'establishment_id');
    }
}
