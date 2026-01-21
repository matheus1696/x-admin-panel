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

    $baseBorder = "border-gray-300 bg-gray-50 text-gray-700 placeholder-gray-400 focus:border-green-700 focus:ring-green-700";
    $errorBorder = "border-red-500 bg-red-50 text-red-700 placeholder-red-400 focus:border-red-500 focus:ring-red-500";

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
        class="w-full rounded-md border px-3 py-2 text-[13px] shadow-sm transition-all duration-200 cursor-pointer flex justify-between items-center {{ $errors->has($name) && !$disabled ? $errorBorder : $baseBorder }}"
        :class="open ? 'ring-1 ring-green-700' : ''"
    >
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

    <!-- Dropdown -->
    <div
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="w-full absolute mt-1.5 bg-white border border-gray-300 rounded-lg shadow-lg z-20 max-h-60 overflow-auto"
    >
        <!-- Campo de busca -->
        <div class="sticky top-0 bg-white border-b p-2">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[13px]"></i>
                <input
                    type="text"
                    x-model="search"
                    placeholder="Buscar..."
                    class="w-full pl-8 pr-3 py-2 text-[13px] text-gray-700 border border-gray-200 rounded-md bg-gray-50 
                           focus:outline-none focus:border-green-700"
                    @click.stop
                >
            </div>
        </div>

        <!-- Opções -->
        <template x-for="option in filteredOptions" :key="option.value">
            <div
                @click="selectOption(option)"
                class="px-3 py-2 text-[13px] text-gray-700 cursor-pointer hover:bg-green-600 hover:text-white transition"
                :class="{'bg-green-600 text-white': selectedValue == option.value}"
            >
                <span x-text="option.label"></span>
            </div>
        </template>

        <!-- Nenhum resultado -->
        <div
            x-show="filteredOptions.length === 0"
            class="px-3 py-2 text-[13px] text-gray-500 italic text-center"
        >
            Nenhum resultado encontrado
        </div>
    </div>
</div>