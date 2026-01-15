<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class OrganizationChart extends Model
{
    //
    protected $fillable = [
        'name',
        'parent_id',
        'order'
    ];

    public function children()
    {
        return $this->hasMany(OrganizationChart::class, 'parent_id')
            ->orderBy('order');
    }
}
