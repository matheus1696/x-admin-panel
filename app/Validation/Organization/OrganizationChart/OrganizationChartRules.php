<?php

namespace App\Validation\Organization\OrganizationChart;

use Illuminate\Support\Facades\DB;
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
                'uppercase',
                'max:10',
                Rule::unique('organization_charts', 'acronym'),
            ],
            'hierarchy' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $hierarchyId = (int) $value;

                    if ($hierarchyId === 0) {
                        return;
                    }

                    if (! DB::table('organization_charts')->where('id', $hierarchyId)->exists()) {
                        $fail('O setor pai informado nao existe.');
                    }
                },
            ],
            'responsible_user_id' => 'nullable|integer|exists:users,id',
        ];
    }

    public static function update(?int $chartId): array
    {
        return [
            'title' => 'required|string',
            'acronym' => [
                'required',
                'string',
                'uppercase',
                'max:10',
                Rule::unique('organization_charts', 'acronym')
                    ->ignore($chartId),
            ],
            'hierarchy' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, \Closure $fail) use ($chartId): void {
                    $hierarchyId = (int) $value;

                    if ($hierarchyId === 0) {
                        return;
                    }

                    if ($chartId !== null && $hierarchyId === $chartId) {
                        $fail('Um setor nao pode ser pai dele mesmo.');

                        return;
                    }

                    if (! DB::table('organization_charts')->where('id', $hierarchyId)->exists()) {
                        $fail('O setor pai informado nao existe.');
                    }
                },
            ],
            'responsible_user_id' => [
                'nullable',
                'integer',
                Rule::exists('organization_chart_user', 'user_id')
                    ->where('organization_chart_id', $chartId),
            ],
        ];
    }
}
