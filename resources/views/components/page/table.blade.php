@props([ 
    'pagination' => null,
    'striped' => true,
    'hover' => true,
    'bordered' => true,
    'compact' => true,
    'stickyHeader' => true,
    'emptyMessage' => 'Nenhum registro encontrado',
])

<!-- 📊 Data Table Component Premium -->
<div class="relative">
    
    <!-- Tabela Container com Scroll Suave -->
    <div class="relative overflow-x-auto rounded-xl border {{ $bordered ? 'border-gray-300' : 'border-gray-200' }} shadow-lg hover:shadow-xl transition-shadow duration-300">
        
        <table class="w-full text-[13px] table-auto relative">
            
            <!-- Cabeçalho Sticky com Design Premium -->
            <thead class="sticky top-0 z-10 bg-gradient-to-r from-emerald-700 via-emerald-800 to-emerald-800 text-[12px] text-white uppercase tracking-wider shadow-lg">
                <tr>
                    {{ $thead ?? '' }}
                </tr>
            </thead>
            
            <!-- Corpo da Tabela - COM STRIPED E HOVER -->
            <tbody class="divide-y divide-gray-100 bg-white 
                @if($striped) [&>tr:nth-child(even)]:bg-gray-100/75 @endif
                @if($hover) [&>tr:hover]:bg-emerald-50/75 [&>tr:hover]:transition-colors [&>tr:hover]:duration-200 @endif
            ">
                @if(isset($tbody) && $tbody->isNotEmpty())
                    {{ $tbody }}
                @else
                    <!-- Mensagem quando vazio -->
                    <tr>
                        <td colspan="100%" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-inbox text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm text-gray-500 font-medium">{{ $emptyMessage }}</p>
                                @if(isset($emptyAction))
                                    <div class="mt-2">
                                        {{ $emptyAction }}
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
            
            <!-- Rodapé da Tabela (para totais, etc) -->
            @if(isset($tfoot))
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    {{ $tfoot }}
                </tfoot>
            @endif
        </table>
    </div>
    
    <!-- Paginação Premium -->
    @if ($pagination && $pagination->total() > 0)
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 mt-4 px-2">
            
            <!-- Informação de registros -->
            <div class="flex items-center gap-2 text-xs text-gray-500 bg-gray-50 px-4 lg:pb-4">
                <i class="fas fa-database text-emerald-500 text-[10px]"></i>
                <span>
                    Mostrando <span class="font-semibold text-gray-700">{{ $pagination->firstItem() ?? 0 }}</span>
                    até <span class="font-semibold text-gray-700">{{ $pagination->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-700">{{ $pagination->total() }}</span> registros
                </span>
            </div>
            
            <!-- Links de paginação customizados -->
            <div class="flex items-center gap-2">
                {{ $pagination->links('components.pagination') }}
            </div>
        </div>
    @endif
</div>