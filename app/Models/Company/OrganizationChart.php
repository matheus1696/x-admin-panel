<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class OrganizationChart extends Model
{
    //
    protected $fillable = [
        'acronym',
        'name',
        'parent_id',
        'order',
        'hierarchy',
        'number_hierarchy',
        'status'
    ];

    public function children()
    {
        return $this->hasMany(OrganizationChart::class, 'parent_id')
            ->orderBy('order');
    }
}
