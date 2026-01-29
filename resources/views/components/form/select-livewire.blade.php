@props([
    'name',
    'options' => [],
    'collection' => null,

    'labelField' => 'title',
    'labelAcronym' => null,
    'valueField' => 'id',

    'selected' => null,
    'placeholder' => 'Selecione uma opção',

    'disabled' => false,
    'variant' => 'default',
])

@php
    //Estilo Select
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
@endphp

@php
    //Estilo Select
    $defaultTailwind = "w-full rounded-md border px-3 py-2 text-xs shadow-sm transition-all duration-200 cursor-pointer";

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
@endphp

@php
    $wireModel = $attributes->wire('model')->value();

    if (! $wireModel) {
        throw new Exception('x-form.select-livewire requer wire:model');
    }
@endphp

<div
    x-data="{
        open: false,
        search: '',
        highlighted: 0,
        selectedValue: @entangle($wireModel).live,
        options: {{ Js::from($options) }},

        get filteredOptions() {
            if (!this.search) return this.options
            return this.options.filter(opt =>
                opt.label.toLowerCase().includes(this.search.toLowerCase())
            )
        },

        openDropdown() {
            if (this.open) return
            this.open = true
            this.$nextTick(() => this.$refs.search?.focus())
        },

        closeDropdown() {
            this.open = false
            this.search = ''
            this.highlighted = 0
        },

        selectOption(option) {
            this.selectedValue = option.value
            this.closeDropdown()
        },

        moveNext() {
            if (this.highlighted < this.filteredOptions.length - 1) {
                this.highlighted++
                this.scrollIntoView()
            }
        },

        movePrev() {
            if (this.highlighted > 0) {
                this.highlighted--
                this.scrollIntoView()
            }
        },

        scrollIntoView() {
            this.$refs[`option_${this.highlighted}`]?.scrollIntoView({
                block: 'nearest'
            })
        },

        selectHighlighted() {
            const option = this.filteredOptions[this.highlighted]
            if (option) this.selectOption(option)
        },
    }"
    @keydown.arrow-down.prevent="open ? moveNext() : openDropdown()"
    @keydown.arrow-up.prevent="movePrev"
    @keydown.enter.prevent="selectHighlighted"
    @keydown.escape.window="closeDropdown"
    role="combobox"
    aria-haspopup="listbox"
    :aria-expanded="open"
    class="relative w-full"
>
    <!-- Campo principal -->
    <div
        @click="open = !open"
        class=" {{ $errors->has($name) && !$disabled ? $errorBorder : $baseBorder }}"
    >
        <div class="flex justify-between">
            {{$avatar ?? null}}
            <span
                class="truncate"
                :class="selectedValue ? 'text-gray-700' : 'text-gray-400'"
                x-text="selectedValue 
                    ? (options.find(o => o.value == selectedValue)?.label || '{{ $placeholder }}')
                    : '{{ $placeholder }}'">
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
        @click.outside="closeDropdown"
        role="listbox"
        :id="'listbox-{{ $name }}'"
        class="absolute z-50 mt-1.5 w-full max-h-60 overflow-auto rounded-lg border border-gray-300 bg-white shadow-lg"
    >
        <!-- Campo de busca -->
        <div class="sticky top-0 bg-white border-b p-2">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>

                <input
                    x-ref="search"
                    x-model="search"
                    type="text"
                    placeholder="Buscar..."
                    class="w-full pl-8 pr-3 py-2 text-xs border rounded-md focus:ring-green-700 focus:border-green-700"
                    role="searchbox"
                />
            </div>
        </div>

        <!-- Opções -->
        <template x-for="(option, index) in filteredOptions" :key="option.value">
            <div
                :id="'option-' + index"
                :ref="'option_' + index"
                role="option"
                :aria-selected="selectedValue == option.value"
                @click="selectOption(option)"
                class="flex items-center gap-2 px-3 py-2 text-xs cursor-pointer transition"
                :class="{
                    'bg-green-700 text-white': index === highlighted,
                    'text-gray-700 hover:bg-green-600 hover:text-white': index !== highlighted
                }"
            >
                {{ $avatar ?? null }}

                <span x-text="option.label" class="line-clamp-1"></span>
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