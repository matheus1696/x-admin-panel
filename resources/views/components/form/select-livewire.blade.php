@props([
    'name' => null,
    'options' => [],
    'collection' => null,
    'classes' => 'border-gray-300 bg-gray-100 text-gray-700 placeholder-gray-400 focus:border-green-700 focus:border-green-700',

    'labelField' => 'title',
    'labelAcronym' => null,
    'valueField' => 'id',

    'selected' => null,
    'placeholder' => 'Selecione uma opção',

    'disabled' => false,
    'variant' => 'default',
])

@php
    if ($collection) {
        $options = $collection
            ->map(function ($item) use ($labelAcronym, $labelField, $valueField,) {
                return [
                    'value' => data_get($item, $valueField),
                    'label' => $labelAcronym
                        ? data_get($item, $labelAcronym) . ' - ' . data_get($item, $labelField)
                        : data_get($item, $labelField),
                ];
            })
            ->toArray();
    }
@endphp

@php
    //Estilo Select
    $defaultTailwind = 'w-full py-2 text-xs transition-all duration-200 cursor-pointer';

    $variants = [
        'default' => [
            'base' =>
                'border-gray-300 bg-gray-50 text-gray-700 placeholder-gray-400 focus:border-green-700 focus:border-green-700 shadow-sm rounded-md border px-3',
            'error' =>
                'border-red-500 bg-red-50 text-red-700 placeholder-red-400 focus:border-red-500 focus:border-red-500 shadow-sm rounded-md border px-3',
        ],

        'inline' => [
            'base' => 'border-transparent bg-transparent text-gray-700 px-0 py-1 focus:border-green-600 focus:bg-white',
            'error' => 'border-red-500 bg-transparent text-red-700 px-0 py-1',
        ],

        'rounded' => [
            'base' =>
                'rounded-full px-3 '. $classes,
            'error' =>
                'rounded-full px-3 '. $classes,
        ],
    ];

    $variantConfig = $variants[$variant] ?? $variants['default'];

    $baseBorder = $defaultTailwind . ' ' . $variantConfig['base'];
    $errorBorder = $defaultTailwind . ' ' . $variantConfig['error'];
@endphp

@php
    $wireModel = $attributes->wire('model')->value();

    if (!$wireModel) {
        throw new Exception('x-form.select-livewire requer wire:model');
    }
@endphp

<div 
    x-data="{
        open: false,
        search: '',
        highlighted: 0,
        selectedValue: @entangle($wireModel).live,
        initialSelected: {{ json_encode($selected) }},
        options: {{ Js::from($options) }},
        placeholder: {{ json_encode($placeholder) }},
        disabled: {{ json_encode($disabled) }},

        get filteredOptions() {
            if (!this.search) return this.options
            return this.options.filter(o =>
                o.label.toLowerCase().includes(this.search.toLowerCase())
            )
        },

        get selectedOption() {
            const value =
                this.initialSelected !== null && this.initialSelected !== ''
                    ? String(this.initialSelected)
                    : String(this.selectedValue)

            return this.options.find(o => String(o.value) === value) || null
        },

        select(option) {
            this.selectedValue = option.value
            this.initialSelected = null

            this.$nextTick(() => {
                this.$refs.hidden.dispatchEvent(
                    new Event('input', { bubbles: true })
                )
            })

            this.close()
        },

        openDropdown() {
            if (this.disabled) return
            this.open = true
        },

        close() {
            this.open = false
            this.search = ''
            this.highlighted = 0
        },

        toggle() {
            if (this.disabled) return
            this.open ? this.close() : this.openDropdown()
        },

        moveNext() {
            if (this.highlighted < this.filteredOptions.length - 1) { 
                    this.highlighted++;
                    this.scrollToHighlighted();    
                }
            },

        movePrev() {
            if (this.highlighted > 0) {
                this.highlighted--
            }
        },

        scrollToHighlighted() {
            this.$nextTick(() => {
                const highlighted = this.$refs['option_' + this.highlighted]; 
                if (highlighted) { 
                    highlighted.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        },

        selectHighlighted() {
            const option = this.filteredOptions[this.highlighted]
            if (option) this.select(option)
        },
        
    }" 
    x-init="$watch('open', value => {
        if (value) {
            $nextTick(() => $refs.search?.focus())
        } else {
            search = ''
            highlighted = 0
        }
    })" 
    class="relative w-full" @click.outside="close"
    @focusout="
        if (!$el.contains($event.relatedTarget)) {
            close()
        }
    "
    @keydown.escape.window="close" @keydown.arrow-down.prevent="!disabled && (open ? moveNext() : openDropdown())"
    @keydown.arrow-up.prevent="!disabled && open && movePrev()" @keydown.enter.prevent.stop="open && selectHighlighted()">
    <!-- Campo principal -->
    <div tabindex="0" role="combobox" :aria-expanded="open" @click="toggle()"
        class=" {{ $errors->has($name) && !$disabled ? $errorBorder : $baseBorder }}">
        <div class="flex justify-between">
            <span class="flex-1 truncate" :class="selectedOption ? 'text-gray-700' : 'text-gray-400'" x-text="selectedOption ? selectedOption.label : placeholder">
            </span>
            <div>
                <i class="fa-solid fa-chevron-down text-gray-400 ml-2 text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </div>
        </div>
    </div>


    <!-- Dropdown -->
    <div x-show="open" x-transition @click.outside="close" role="listbox" :id="'listbox-{{ $name }}'"
        class="absolute z-50 mt-1.5 w-full max-h-60 overflow-auto rounded-lg border border-gray-300 bg-white shadow-lg">
        <!-- Campo de busca -->
        <div class="sticky top-0 bg-white border-b p-2">
            <div class="relative">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>

                <input x-ref="search" x-model="search" type="text" placeholder="Buscar..."
                    class="w-full pl-8 pr-3 py-2 text-xs border rounded-md focus:ring-green-700 focus:border-green-700"
                    role="searchbox" />
            </div>
        </div>

        <!-- Opções -->
        <template x-for="(option, index) in filteredOptions" :key="option.value">
            <div :id="'option-' + index" :ref="'option_' + index" role="option"
                :aria-selected="selectedValue == option.value" @click="select(option)"
                class="flex items-center gap-2 px-3 py-2 text-xs cursor-pointer transition"
                :class="{
                    'bg-green-700 text-white': index === highlighted,
                    'text-gray-700 hover:bg-green-600 hover:text-white': index !== highlighted
                }">

                {{ $avatar ?? null }}
                <span x-text="option.label" class="line-clamp-1"></span>
            </div>
        </template>

        <!-- Nenhum resultado -->
        <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-xs text-gray-500 italic text-center">
            Nenhum resultado encontrado
        </div>
    </div>

    <input
        x-ref="hidden"
        type="hidden"
        :value="selectedValue"
        wire:model.live="{{ $attributes->wire('model')->value() ?? '' }}"
    >

</div>
