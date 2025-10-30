@props([
    'color' => 'green',
    'name' => null,
    'options' => [],
    'collection' => null,
    'labelField' => 'name',
    'valueField' => 'id',
    'default' => 'Selecione uma opção',
    'disabled' => false,
    'selected' => null,
])

@php
    // Se for uma coleção, converte para o formato [value, label]
    if ($collection) {
        $options = $collection->map(fn($item) => [
            'value' => $item[$valueField],
            'label' => $item[$labelField],
        ])->toArray();
    }

    $baseBorder = "border-gray-300 bg-gray-50 text-gray-700 placeholder-gray-400 focus:border-{$color}-700 focus:ring-{$color}-700";
    $errorBorder = "border-red-500 bg-red-50 text-red-700 placeholder-red-400 focus:border-red-500 focus:ring-red-500";
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selectedValue: @entangle($attributes->wire('model')),
        options: {{ json_encode($options) }},
        get filteredOptions() {
            return this.options.filter(opt => opt.label.toLowerCase().includes(this.search.toLowerCase()))
        },
        selectOption(option) {
            this.selectedValue = option.value
            this.open = false
        }
    }"
    class="relative w-full"
>
    <!-- Campo principal -->
    <div
        @click="open = !open"
        class="w-full rounded-md border px-3 py-2 text-xs shadow-sm transition-all duration-200 cursor-pointer flex justify-between items-center
            {{ $errors->has($name) && !$disabled ? $errorBorder : $baseBorder }}"
    >
        <span
            class="truncate"
            :class="selectedValue ? 'text-gray-700' : 'text-gray-400'"
            x-text="selectedValue 
                ? options.find(o => o.value == selectedValue)?.label 
                : '{{ $default }}'">
        </span>
        <i class="fa-solid fa-chevron-down text-gray-400 ml-2 text-[10px]"></i>
    </div>

    <!-- Dropdown -->
    <div
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="absolute left-0 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg z-20 max-h-60 overflow-auto"
    >
        <!-- Campo de busca -->
        <div class="sticky top-0 bg-white border-b px-1.5 border-{{ $color }}-300">
            <input
                type="text"
                x-model="search"
                placeholder="Buscar..."
                class="w-full px-3 py-2 my-2 text-xs text-gray-700 border border-gray-200 rounded-md bg-gray-50 placeholder-gray-400 
                       focus:outline-none focus:ring-0 focus:border-{{ $color }}-700"
            >
        </div>

        <!-- Opções -->
        <template x-for="option in filteredOptions" :key="option.value">
            <div
                @click="selectOption(option)"
                class="px-3 py-2 my-1 text-xs text-gray-700 cursor-pointer hover:bg-{{ $color }}-600 hover:text-white transition"
                :class="{'bg-{{ $color }}-600 text-white': selectedValue === option.value}"
            >
                <span x-text="option.label"></span>
            </div>
        </template>

        <!-- Nenhum resultado -->
        <div
            x-show="filteredOptions.length === 0"
            class="px-3 py-2 text-xs text-gray-500 italic select-none"
        >
            Nenhum resultado encontrado
        </div>
    </div>
</div>
