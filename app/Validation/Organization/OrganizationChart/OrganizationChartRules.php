<?php

namespace App\Validation\Organization\OrganizationChart;

use Illuminate\Validation\Rule;

/**
 * Esta classe centraliza as regras de validação
 * para permitir reutilização entre Livewire e FormRequests.
 *
 * Motivo:
 * - Livewire não utiliza FormRequest nativamente
 * - Evita duplicação de regras entre Livewire e Controllers
 * - Mantém as validações desacopladas da camada HTTP
 *
 * Use esta classe:
 * - Diretamente no Livewire via $this->validate()
 * - Dentro de FormRequests quando o fluxo for HTTP
 */

class OrganizationChartRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|string',
            'acronym' => [
                'required',
                'string',
                'max:10',
                Rule::unique('organization_charts', 'acronym'),
            ],
            'hierarchy' => 'required|integer',
        ];
    }

    public static function update(?int $chartId): array
    {
        return [
            'title' => 'required|string',
            'acronym' => [
                'required',
                'string',
                'max:10',
                Rule::unique('organization_charts', 'acronym')
                    ->ignore($chartId),
            ],
            'hierarchy' => 'required|integer',
        ];
    }
}
