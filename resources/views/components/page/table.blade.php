@props([ 'pagination' => null])

<!-- 📊 Data Table Component -->
<div class="relative overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
    <table class="w-full text-[13px] table-auto [&_th]:px-3 [&_th]:py-2 [&_th]:text-left [&_td]:px-3 [&_td]:py-2">

        <thead class="sticky top-0 z-10 bg-green-100 text-green-800 uppercase tracking-wider border-b">
            {{ $thead ?? '' }}
        </thead>

        <tbody class="divide-y divide-gray-200 [&>tr:hover]:bg-green-100/50 [&>tr:nth-child(even)]:bg-gray-50"> 
            {{ $tbody ?? '' }}
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