@props([
    'name' => null,
    'options' => [],
    'collection' => null,
    'labelField' => null,
    'valueField' => null,
    'default' => 'Selecione uma opção',
    'disabled' => false,
    'selected' => null,
])

@php
    if ($collection) {
        $options = $collection->map(fn($item) => [
            'value' => $item[$valueField],
            'label' => $item[$labelField],
        ])->toArray();
    }

    $baseBorder = "border-gray-300 bg-gray-50 text-gray-700 placeholder-gray-400 focus:border-green-700 focus:ring-green-700";
    $errorBorder = "border-red-500 bg-red-50 text-red-700 placeholder-red-400 focus:border-red-500 focus:ring-red-500";
@endphp

<div x-data="{
        open: false,
        search: '',
        value: '{{ old($name, $selected) }}',
        options: {{ json_encode($options) }},
        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(opt => opt.label.toLowerCase().includes(this.search.toLowerCase()))
        },
        selectOption(opt) {
            this.value = opt.value;
            this.open = false;
            $refs.hidden.value = opt.value;
        }
    }"
    class="relative w-full"
>
    <!-- Select Visual -->
    <div 
        @click="open = !open" 
        class="w-full rounded-md border px-3 py-2 text-[13px] shadow-sm transition-all duration-200 cursor-pointer flex justify-between items-center {{ $errors->has($name) && !$disabled ? $errorBorder : $baseBorder }}" :class="open ? 'ring-1 ring-green-700' : ''"
    >
            <span class="truncate" :class="value ? 'text-gray-800' : 'text-gray-400'">
                <span x-text="value ? (options.find(o => o.value == value)?.label ?? '') : '{{ $default }}'"></span>
            </span>
            <i class="fa-solid fa-chevron-down text-gray-400 ml-2 text-[10px]"></i>
    </div>

    <!-- Dropdown -->
    <div x-show="open" x-transition @click.outside="open = false" class="absolute mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 z-30 overflow-auto flex flex-col" style="display: none;">
        <!-- Campo Busca -->
        <div class="sticky top-0 bg-white border-b px-3 py-2">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                <input type="text" x-model="search" placeholder="Buscar..." class="w-full pl-8 pr-3 py-2 text-[13px] text-gray-700  bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:border-green-700 focus:ring-green-700" />
            </div>
        </div>

        <!-- Opções -->
        <template x-for="opt in filteredOptions" :key="opt.value">
            <div @click="selectOption(opt)" class="px-3 py-2 text-[13px] text-gray-700 cursor-pointer hover:bg-green-600 hover:text-white transition" :class="{ 'bg-green-700 text-white': value == opt.value }">
                <span x-text="opt.label"></span>
            </div>
        </template>

        {{ $slot }}

        <!-- Sem resultados -->
        <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-[13px] text-gray-500 italic select-none">
            Nenhum resultado encontrado
        </div>
    </div>

    <!-- Hidden input para envio no form -->
    <input type="hidden" name="{{ $name }}" x-ref="hidden" :value="value" />
</div>
