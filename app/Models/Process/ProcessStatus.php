<?php

namespace App\Models\Process;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProcessStatus extends Model
{
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const CLOSED = 'CLOSED';
    public const CANCELLED = 'CANCELLED';

    protected $fillable = [
        'code',
        'label',
        'badge_class',
        'chart_color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return array<int, array{code:string,label:string,badge_class:string,chart_color:string,sort_order:int,is_active:bool}>
     */
    public static function defaults(): array
    {
        return [
            [
                'code' => self::IN_PROGRESS,
                'label' => 'Em andamento',
                'badge_class' => 'bg-blue-100 text-blue-700',
                'chart_color' => '#2563eb',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => self::CLOSED,
                'label' => 'Concluido',
                'badge_class' => 'bg-emerald-100 text-emerald-700',
                'chart_color' => '#059669',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => self::CANCELLED,
                'label' => 'Cancelado',
                'badge_class' => 'bg-red-100 text-red-700',
                'chart_color' => '#dc2626',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];
    }
}

