@props([ 'pagination' => null])

<!-- 📊 Data Table Component -->
<div class="mt-4 overflow-x-auto">
    <!-- Table Wrapper -->
    <table class="w-full text-xs border-t border-gray-100 divide-y divide-gray-100">
        <!-- Cabeçalho -->
        <thead class="font-semibold uppercase tracking-wider bg-green-100 text-green-800 text-left border-b border-green-200">
            {{ $thead ?? ''}}
        </thead>

        <!-- Corpo -->
        <tbody class="divide-y divide-gray-100 [&>tr:hover]:bg-green-50 [&>tr:nth-child(even)]:bg-green-50/30">
            {{ $tbody ?? ''}}
        </tbody>
    </table>

    <!-- Paginação -->
    @if ($pagination)
        <div class="flex flex-col items-center justify-between bg-gray-50 pt-4">
            <!-- Informação de paginação -->
            @if(method_exists($pagination, 'total'))
                <div class="text-xs text-gray-600/60">
                    <span class="hidden sm:inline">Mostrando</span>
                    <span class="font-medium">{{ $pagination->firstItem() ?? 0 }}-{{ $pagination->lastItem() ?? 0 }}</span>
                    de <span class="font-medium">{{ $pagination->total() }}</span>
                </div>
            @endif
            
            <!-- Links de paginação -->
            <div class="flex items-center">
                {{ $pagination->links('components.pagination') }}
            </div>
        </div>
    @endif
</div>