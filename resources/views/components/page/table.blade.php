@props([ 'pagination' => null, 'color' => 'green' ])

<!-- 📊 Data Table Component -->
<div class="overflow-hidden w-full">
    <!-- Table Wrapper -->
    <div class="overflow-x-auto bg-white border border-gray-100 text-xs rounded-2xl shadow-sm">
        <table class="w-full table-fixed">
            <!-- Cabeçalho -->
            <thead class="font-semibold uppercase tracking-wider bg-{{ $color }}-200 text-{{ $color }}-800 text-left">
                {{ $thead ?? ''}}
            </thead>

            <!-- Corpo -->
            <tbody class="divide-y divide-gray-100 [&>tr:hover]:bg-{{ $color }}-50">
                {{ $tbody ?? ''}}
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    @if ($pagination)
        <div class="px-6 py-4">
            <div class="flex items-center justify-center">
                {{ $pagination->links('components.pagination') }}
            </div>
        </div>
    @endif
</div>
