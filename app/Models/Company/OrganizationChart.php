<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrganizationChart extends Model
{
    //
    protected $fillable = [
        'acronym',
        'title',
        'parent_id',
        'order',
        'hierarchy',
        'number_hierarchy',
        'status'
    ];

    public function children()
    {
        return $this->hasMany(OrganizationChart::class, 'hierarchy')
            ->where('status', true)
            ->orderBy('order');
    }

    public function toggleStatus(): self
    {
        $this->update(['status' => !$this->status]);
        return $this;
    }

    //Criação do Filter
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['filter'] = Str::ascii(strtolower($value));
    }
}
