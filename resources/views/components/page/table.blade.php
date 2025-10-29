@props([
    'pagination' => null,
    'striped' => true,
    'hover' => true,
    'borderColor' => 'blue-100',
    'headerColor' => 'blue-50',
])

<!-- 📊 Data Table Component -->
<div class="overflow-hidden w-full">
    <!-- Table Wrapper -->
    <div class="overflow-x-auto bg-white rounded-2xl shadow-sm border border-{{ $borderColor }} text-xs transition-all duration-200">
        <table class="w-full divide-y divide-gray-200 table-fixed">
            <!-- Cabeçalho -->
            <thead class="bg-{{ $headerColor }} text-blue-800 text-left font-semibold uppercase tracking-wider">
                {{ $thead ?? ''}}
            </thead>

            <!-- Corpo -->
            <tbody class="divide-y divide-gray-100 {{ $striped ? 'odd:bg-gray-50/30 even:bg-white' : '' }} {{ $hover ? 'hover:[&>tr]:bg-blue-50/30 transition-all duration-150' : '' }}">
                {{ $tbody ?? ''}}
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    @if ($pagination)
        <div class="px-6 py-4">
            <div class="flex items-center justify-center">
                {{ $pagination->links() }}
            </div>
        </div>
    @endif
</div>
