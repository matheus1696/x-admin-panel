@props([
    'pagination' => null,
    'striped' => true,
    'hover' => true,
    'bordered' => true,
    'compact' => true,
    'stickyHeader' => true,
    'emptyMessage' => 'Nenhum registro encontrado',
])

@php
    $normalizeBoolean = static fn (mixed $value): bool => filter_var($value, FILTER_VALIDATE_BOOLEAN) || $value === true || $value === 1 || $value === '1';

    $striped = $normalizeBoolean($striped);
    $hover = $normalizeBoolean($hover);
    $bordered = $normalizeBoolean($bordered);
    $compact = $normalizeBoolean($compact);
    $stickyHeader = $normalizeBoolean($stickyHeader);

    $tableDensityClass = $compact
        ? '[&_th]:px-4 [&_th]:py-3 [&_td]:px-4 [&_td]:py-2.5'
        : '[&_th]:px-6 [&_th]:py-4 [&_td]:px-6 [&_td]:py-3.5';

    $theadClass = $stickyHeader ? 'sticky top-0 z-10' : '';
@endphp

<div class="relative">
    <!-- Container da tabela com rolagem horizontal -->
    <div class="relative overflow-x-auto rounded-xl border {{ $bordered ? 'border-gray-300' : 'border-gray-200' }} shadow-lg transition-shadow duration-300 hover:shadow-xl">
        <table class="relative w-full table-auto text-[13px] {{ $tableDensityClass }}">
            <!-- Cabeçalho -->
            <thead class="{{ $theadClass }} bg-gradient-to-r from-emerald-700 via-emerald-800 to-emerald-800 text-left text-[12px] uppercase tracking-wider text-white shadow-lg">
                <tr>
                    {{ $thead ?? '' }}
                </tr>
            </thead>

            <!-- Corpo da tabela -->
            <tbody
                class="divide-y divide-gray-100 bg-white
                    @if ($striped) [&>tr:nth-child(even)]:bg-gray-100/75 @endif
                    @if ($hover) [&>tr:hover]:bg-emerald-50/75 [&>tr:hover]:transition-colors [&>tr:hover]:duration-200 @endif
                "
            >
                @if (isset($tbody) && $tbody->isNotEmpty())
                    {{ $tbody }}
                @else
                    <!-- Estado vazio -->
                    <tr>
                        <td colspan="100%" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                    <i class="fas fa-inbox text-2xl text-gray-400"></i>
                                </div>

                                <p class="text-sm font-medium text-gray-500">{{ $emptyMessage }}</p>

                                @if (isset($emptyAction))
                                    <div class="mt-2">
                                        {{ $emptyAction }}
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>

            <!-- Rodapé da tabela -->
            @if (isset($tfoot))
                <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                    {{ $tfoot }}
                </tfoot>
            @endif
        </table>
    </div>

    <!-- Paginação -->
    @if ($pagination && $pagination->total() > 0)
        <div class="mt-4 flex flex-col items-center justify-between gap-2 px-2 sm:flex-row">
            <div class="flex items-center gap-2 bg-gray-50 px-4 text-xs text-gray-500 lg:pb-4">
                <i class="fas fa-database text-[10px] text-emerald-500"></i>
                <span>
                    Mostrando <span class="font-semibold text-gray-700">{{ $pagination->firstItem() ?? 0 }}</span>
                    até <span class="font-semibold text-gray-700">{{ $pagination->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-700">{{ $pagination->total() }}</span> registros
                </span>
            </div>

            <div class="flex items-center gap-2">
                {{ $pagination->links('components.pagination') }}
            </div>
        </div>
    @endif
</div>
