@props([
    'name' => null,
    'options' => [],
    'collection' => null,
    'labelAcronym' => null,
    'labelField' => 'title',
    'valueField' => 'id',
    'default' => 'Selecione uma opção',
    'disabled' => false,
    'selected' => null,
    'variant' => 'default',
])

@php
    if ($collection) {
        $options = $collection->map(function ($item) use ($labelAcronym, $labelField, $valueField) {
            return [
                'value' => data_get($item, $valueField),
                'label' => $labelAcronym
                    ? data_get($item, $labelAcronym).' - '.data_get($item, $labelField)
                    : data_get($item, $labelField),
            ];
        })->toArray();
    }

    $defaultTailwind = "w-full rounded-md border px-3 py-2 text-xs shadow-sm transition-all duration-200 flex items-center gap-2";

    $variants = [
        'default' => [
            'base' => "ring-1 ring-green-700 border-gray-300 bg-gray-50 text-gray-700",
            'error' => "ring-1 ring-red-700 border-red-500 bg-red-50 text-red-700",
        ],
        'inline' => [
            'base' => "border-transparent bg-transparent text-gray-700 px-0 py-1 shadow-none",
            'error' => "border-red-500 bg-transparent text-red-700 px-0 py-1 shadow-none",
        ],
    ];

    $variantConfig = $variants[$variant] ?? $variants['default'];

    $baseBorder  = $defaultTailwind.' '.$variantConfig['base'];
    $errorBorder = $defaultTailwind.' '.$variantConfig['error'];
@endphp

<div
    x-data="{
        open: false,
        search: '',
        highlighted: 0,
        options: {{ json_encode($options) }},
        selectedValue: 
        @if (filled($attributes->wire('model'))) 
            @entangle($attributes->wire('model')->value).live,
        @else 
            {{ json_encode($selected) }}
        @endif,
        

        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(o =>
                o.label.toLowerCase().includes(this.search.toLowerCase())
            );
        },

        select(option) {
            this.selectedValue = option.value;
            this.close();
        },

        openDropdown() {
            if ({{ $disabled ? 'true' : 'false' }}) return;
            this.open = true;
            this.$nextTick(() => this.$refs.search?.focus());
        },

        close() {
            this.open = false;
            this.search = '';
            this.highlighted = 0;
        },

        moveNext() {
            if (this.highlighted < this.filteredOptions.length - 1) {
                this.highlighted++;
            }
        },

        movePrev() {
            if (this.highlighted > 0) {
                this.highlighted--;
            }
        },

        selectHighlighted() {
            if (this.filteredOptions[this.highlighted]) {
                this.select(this.filteredOptions[this.highlighted]);
            }
        }
    }"
    x-init="
        @if (!filled($attributes->wire('model'))) 
            if (selectedValue !== null && selectedValue !== '') {
                $nextTick(() => {
                    $refs.hidden.dispatchEvent(
                        new Event('input', { bubbles: true })
                    );
                });
            }
        @endif

        $watch('open', v => !v && (search = '', highlighted = 0))
    "
    class="relative w-full"
    @keydown.escape.window="close"
    @keydown.enter.prevent="open && selectHighlighted()"
    @keydown.arrow-down.prevent="open ? moveNext() : openDropdown()"
    @keydown.arrow-up.prevent="open && movePrev()"
>

    <!-- Campo -->
    <div
        @click="open ? close() : openDropdown()"
        class="{{ $errors->has($name) && !$disabled ? $errorBorder : $baseBorder }}
               {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
    >
        <div class="flex-1 truncate"
             :class="selectedValue ? 'text-gray-700' : 'text-gray-400'"
             x-text="
                selectedValue
                ? (options.find(o => o.value == selectedValue)?.label || '{{ $default }}')
                : '{{ $default }}'
             ">
        </div>

        <i class="fa-solid fa-chevron-down text-[10px] transition-transform"
           :class="open ? 'rotate-180' : ''"></i>
    </div>

    <!-- 🔑 Input Hidden (fonte da verdade) -->
    <input
        x-ref="hidden"
        type="hidden"
        name="{{ $name }}"
        :value="selectedValue"
        {{ $attributes->whereStartsWith('wire:') }}
    >

    <!-- Dropdown -->
    <div
        x-show="open"
        x-transition
        @click.outside="close"
        class="absolute z-50 mt-0.5 w-full bg-white border border-gray-300 shadow-lg max-h-60 overflow-auto"
    >
        <!-- Busca -->
        <div class="sticky top-0 bg-white border-b p-2">
            <input
                x-ref="search"
                type="text"
                x-model="search"
                placeholder="Buscar..."
                class="w-full px-3 py-2 text-xs border rounded-md focus:ring-green-700 focus:border-green-700"
            >
        </div>

        <!-- Opções -->
        <template x-for="(option, index) in filteredOptions" :key="`${option.value}-${index}`">
            <div
                @click="select(option)"
                @mouseenter="highlighted = index"
                class="px-3 py-2 text-xs cursor-pointer transition"
                :class="{
                    'bg-green-700 text-white': highlighted === index,
                    'bg-green-600 text-white': selectedValue == option.value
                }"
            >
                <span x-text="option.label"></span>
            </div>
        </template>

        <!-- Vazio -->
        <div
            x-show="filteredOptions.length === 0"
            class="px-3 py-2 text-xs text-gray-500 italic text-center"
        >
            Nenhum resultado encontrado
        </div>
    </div>
</div>
