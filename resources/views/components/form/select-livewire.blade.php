@props([
    'name' => null,
    'options' => [],
    'collection' => null,
    'labelAcronym' => null,
    'labelField' => 'name',
    'valueField' => 'id',
    'default' => 'Selecione uma opção',
    'disabled' => false,
    'selected' => null,
    'wireModel' => null,
    'variant' => 'default', // default | inline
])

@php
    if ($collection) {
        $options = $collection->map(function ($item) use ($labelAcronym, $labelField, $valueField) {
            return [
                'value' => data_get($item, $valueField),
                'label' => $labelAcronym
                    ? data_get($item, $labelAcronym) . ' - ' . data_get($item, $labelField)
                    : data_get($item, $labelField),
            ];
        })->toArray();
    }

    $defaultTailwind = "w-full rounded-md border px-3 py-2 text-xs shadow-sm transition-all duration-200 cursor-pointer flex items-center gap-2";

    $variants = [
        'default' => [
            'base' => "ring-1 ring-green-700 border-gray-300 bg-gray-50 text-gray-700 placeholder-gray-400 focus:border-green-700 focus:ring-green-700",
            'error' => "ring-1 ring-red-700 border-red-500 bg-red-50 text-red-700 placeholder-red-400 focus:border-red-500 focus:ring-red-500",
        ],

        'inline' => [
            'base' => "border-transparent bg-transparent text-gray-700 px-0 py-1 shadow-none focus:border-green-600 focus:bg-white",
            'error' => "border-red-500 bg-transparent text-red-700 px-0 py-1 shadow-none",
        ],
    ];

    $variantConfig = $variants[$variant] ?? $variants['default'];
    
    $baseBorder  = $defaultTailwind . ' ' . $variantConfig['base'];
    $errorBorder = $defaultTailwind . ' ' . $variantConfig['error'];

    $wireModelName = $wireModel ?? $name;
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selectedValue: @entangle($wireModelName).live,
        options: {{ json_encode($options) }},
        
        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(opt => 
                opt.label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        
        selectOption(option) {
            this.selectedValue = option.value;
            this.open = false;
        }
    }"
    class="relative w-full"
>
    <!-- Campo principal -->
    <div
        @click="open = !open"
        class=" {{ $errors->has($name) && !$disabled ? $errorBorder : $baseBorder }}"
    >
        {{ $avatar ?? null }}
        <div class="flex-1 flex justify-between">
            <span
                class="truncate"
                :class="selectedValue ? 'text-gray-700' : 'text-gray-400'"
                x-text="selectedValue 
                    ? (options.find(o => o.value == selectedValue)?.label || '{{ $default }}')
                    : '{{ $default }}'">
            </span>
            <i 
                class="fa-solid fa-chevron-down text-gray-400 ml-2 text-[10px] transition-transform duration-200"
                :class="open ? 'rotate-180' : ''"
            ></i>
        </div>
    </div>

    <!-- Dropdown -->
    <div
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="w-full absolute mt-1.5 bg-white border border-gray-300 rounded-lg shadow-lg z-50 max-h-60 overflow-auto"
    >
        <!-- Campo de busca -->
        <div class="sticky top-0 bg-white border-b p-2">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" x-model="search" placeholder="Buscar..." class="w-full pl-8 pr-3 py-2 text-xs text-gray-700 border border-gray-200 rounded-md focus:outline-none ring-transparent focus:ring-green-700 focus:border-green-700" @click.stop
                >
            </div>
        </div>

        <!-- Opções -->
        <template x-for="option in filteredOptions" :key="option.value">
            <div
                @click="selectOption(option)"
                class="flex items-center justify-center gap-2 px-3 py-2 text-xs text-gray-700 cursor-pointer hover:bg-green-600 hover:text-white transition"
                :class="{'bg-green-700 text-white': selectedValue == option.value}"
            >
                {{ $avatar ?? null }}
                <div class="flex-1">
                    <span x-text="option.label" class=" line-clamp-1" :title="option.label"></span>
                </div>
            </div>
        </template>

        <!-- Nenhum resultado -->
        <div
            x-show="filteredOptions.length === 0"
            class="px-3 py-2 text-xs text-gray-500 italic text-center"
        >
            Nenhum resultado encontrado
        </div>
    </div>
</div>