<x-app-layout>

    <x-page.header icon="fa-solid fa-sitemap" title="Organizational Chart" subtitle="Manage departments and hierarchy">
        <x-slot name="button">
            @can('auth')
            <x-slot name="button">                
                <x-button.btn-link href="{{ route('config.departments.create') }}" value="Novo Departamento" icon="fa-solid fa-plus" />
            </x-slot>
            @endcan
        </x-slot>
    </x-page.header>

    <div class="py-6 w-full">
        <x-page.table>
            <x-slot name="thead">
                <tr>
                    <x-page.table-th value="Department" />
                    <x-page.table-th value="Acronym" class="w-32 text-center" />
                    <x-page.table-th value="Status" class="w-32 text-center" />
                    <x-page.table-th value="Actions" class="w-40 text-center" />
                </tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($departments as $department)
                    <tr>
                        <!-- Nome com indentação -->
                        <x-page.table-td>
                            <div class="flex items-center gap-2" style="margin-left: {{ $department->level }}px">
                                <i class="fa-solid fa-diagram-project text-green-600 text-xs"></i>
                                <span class="font-medium">{{ $department->title }}</span>
                            </div>
                        </x-page.table-td>

                        <x-page.table-td class="text-center">
                            {{ $department->acronym ?? '-' }}
                        </x-page.table-td>

                        <x-page.table-td class="text-center">
                            @if($department->status)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Inactive</span>
                            @endif
                        </x-page.table-td>

                        <x-page.table-td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('config.departments.edit', $department) }}"
                                    class="text-green-700 hover:text-green-900">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                @can('status-departments')
                                    <form method="POST" action="{{ route('config.departments.status', $department) }}">
                                        @csrf
                                        @method('PUT')
                                        <button class="text-gray-500 hover:text-gray-800">
                                            <i class="fa-solid fa-power-off"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </x-page.table-td>
                    </tr>
                @endforeach
            </x-slot>
        </x-page.table>
    </div>
    
</x-app-layout>
