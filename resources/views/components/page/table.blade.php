@props([ 'pagination' => null ])

<!-- 📊 Data Table Component -->
<div class="overflow-hidden w-full">
    <!-- Table Wrapper -->
    <div class="overflow-x-auto bg-white {{ config('xadminpanel.class_table') }}">
        <table class="w-full table-fixed">
            <!-- Cabeçalho -->
            <thead class="font-semibold uppercase tracking-wider {{ config('xadminpanel.class_thead') }}">
                {{ $thead ?? ''}}
            </thead>

            <!-- Corpo -->
            <tbody class="divide-y {{ config('xadminpanel.class_tbody') }}">
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
