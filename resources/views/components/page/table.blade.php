@props([ 'pagination' => null])

<!-- 📊 Data Table Component -->
<div class="w-[calc(100vw-35px)] md:w-full mt-4 overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
    <!-- Table Wrapper -->
    <table class="w-full text-[13px] divide-y divide-gray-100">
        <!-- Cabeçalho -->
        <thead class="font-semibold uppercase tracking-wider bg-green-100 text-green-800 text-left border-b border-green-200">
            {{ $thead ?? ''}}
        </thead>

        <!-- Corpo -->
        <tbody class="divide-y divide-gray-200/75 [&>tr:hover]:bg-green-50 [&>tr:nth-child(even)]:bg-green-50/75">
            {{ $tbody ?? ''}}
        </tbody>
    </table>
</div>

<!-- Paginação -->
@if ($pagination)
    <div class="flex flex-col items-center justify-between bg-gray-50 mt-3">
        <!-- Informação de paginação -->
        @if(method_exists($pagination, 'total'))
            <div class="text-xs text-gray-600/60">
                <span>Mostrando</span>
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