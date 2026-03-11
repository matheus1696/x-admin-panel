<?php

namespace App\Enums\Process;

enum ProcessStatus: string
{
    case OPEN = 'OPEN';
    case IN_PROGRESS = 'IN_PROGRESS';
    case ON_HOLD = 'ON_HOLD';
    case CLOSED = 'CLOSED';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Aberto',
            self::IN_PROGRESS => 'Em andamento',
            self::ON_HOLD => 'Em espera',
            self::CLOSED => 'Concluido',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::OPEN => 'bg-slate-100 text-slate-700',
            self::IN_PROGRESS => 'bg-blue-100 text-blue-700',
            self::ON_HOLD => 'bg-amber-100 text-amber-700',
            self::CLOSED => 'bg-emerald-100 text-emerald-700',
            self::CANCELLED => 'bg-red-100 text-red-700',
        };
    }

    public function chartColor(): string
    {
        return match ($this) {
            self::OPEN => '#64748b',
            self::IN_PROGRESS => '#2563eb',
            self::ON_HOLD => '#d97706',
            self::CLOSED => '#059669',
            self::CANCELLED => '#dc2626',
        };
    }
}
