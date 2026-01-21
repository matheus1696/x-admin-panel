<?php

namespace App\Models\Administration\User;

use App\Models\Traits\HasStatus;
use App\Models\Traits\HasTitleFilter;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use HasStatus, HasTitleFilter, HasUuid, HasUuidRouteKey;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'filter',
        'status',
    ];
}
