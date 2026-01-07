@props([ 'condition' => false, ])

<x-page.table-td>
    <div class="flex justify-center">
    @if ($condition)
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
            Ativo
        </span>
    @else
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
            Inativo
        </span>
    @endif
</div>

</x-page.table-td>