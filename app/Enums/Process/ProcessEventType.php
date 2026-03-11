<?php

namespace App\Enums\Process;

enum ProcessEventType: string
{
    case CREATED = 'PROCESS_CREATED';
    case STARTED = 'PROCESS_STARTED';
    case FORWARDED = 'PROCESS_FORWARDED';
    case RETURNED = 'PROCESS_RETURNED';
    case COMMENTED = 'PROCESS_COMMENTED';
    case OWNER_ASSIGNED = 'PROCESS_OWNER_ASSIGNED';
    case CLOSED = 'PROCESS_CLOSED';
    case CANCELLED = 'PROCESS_CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Criacao',
            self::STARTED => 'Inicio',
            self::FORWARDED => 'Encaminhamento',
            self::RETURNED => 'Retorno',
            self::COMMENTED => 'Comentario',
            self::OWNER_ASSIGNED => 'Atribuicao de responsavel',
            self::CLOSED => 'Encerramento',
            self::CANCELLED => 'Cancelamento',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::FORWARDED => 'bg-emerald-100 text-emerald-700',
            self::RETURNED => 'bg-amber-100 text-amber-700',
            self::OWNER_ASSIGNED => 'bg-indigo-100 text-indigo-700',
            self::COMMENTED => 'bg-sky-100 text-sky-700',
            self::CREATED, self::STARTED => 'bg-violet-100 text-violet-700',
            self::CLOSED => 'bg-slate-200 text-slate-700',
            self::CANCELLED => 'bg-rose-100 text-rose-700',
        };
    }
}
