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
            'base' => "border-gray-300 bg-gray-50 text-gray-700 focus:border-green-700",
            'error' => "border-red-500 bg-red-50 text-red-700",
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
        selectedValue: {{ json_encode($selected) }},
        placeholder: {{ json_encode($placeholder) }},
        disabled: {{ json_encode($disabled) }},

        get filteredOptions() {
            if (!this.search) return this.options
            return this.options.filter(o =>
                o.label.toLowerCase().includes(this.search.toLowerCase())
            )
        },

        get selectedOption() {
            return this.options.find(o => o.value == this.selectedValue) || null
        },

        select(option) {
            this.selectedValue = option.value
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
                this.highlighted++
            }
        },

        movePrev() {
            if (this.highlighted > 0) {
                this.highlighted--
            }
        },

        selectHighlighted() {
            const option = this.filteredOptions[this.highlighted]
            if (option) this.select(option)
        },
    }"
    x-init="
        if (selectedValue !== null && selectedValue !== '') {
            $nextTick(() => {
                $refs.hidden.dispatchEvent(
                    new Event('input', { bubbles: true })
                )
            })
        }

        $watch('open', value => {
            if (value) {
                $nextTick(() => $refs.search?.focus())
            } else {
                search = ''
                highlighted = 0
            }
        })
    "
    class="relative w-full"
    @click.outside="close"
    @focusout="
        if (!$el.contains($event.relatedTarget)) {
            close()
        }
    "
    @keydown.escape.window="close"
    @keydown.arrow-down.prevent="!disabled && (open ? moveNext() : openDropdown())"
    @keydown.arrow-up.prevent="!disabled && open && movePrev()"
    @keydown.enter.prevent="open && selectHighlighted()"
    @keydown.enter.prevent.stop
>

    <!-- Campo -->
    <div
        tabindex="0"
        role="combobox"
        :aria-expanded="open"
        @click="toggle()"
        class="{{ $errors->has($name) && !$disabled ? $errorBorder : $baseBorder }}
               {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
    >
        <div class="flex-1 truncate"
            :class="selectedValue ? 'text-gray-700' : 'text-gray-400'"
            x-text="selectedOption ? selectedOption.label : placeholder">
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
    >

    <div
        x-show="open"
        x-transition
        @click.outside="close"
        class="absolute z-50 mt-0.5 w-full bg-white border border-gray-300 shadow-lg max-h-60 overflow-y-scroll rounded-lg"
    >
        <!-- Busca -->
        <div class="sticky top-0 bg-white border-b p-2">
            <input
                x-ref="search"
                type="text"
                x-model="search"
                placeholder="Buscar..."
                class="w-full px-3 py-2 text-xs border rounded-md focus:ring-0 focus:border-green-700"
            >
        </div>

        <!-- Opções -->
        <template x-for="(option, index) in filteredOptions" :key="option.value">
            <div
                @click="select(option)"
                @mouseenter="highlighted = index"
                class="px-3 py-2.5 text-xs cursor-pointer transition border-b border-gray-200"
                :class="{
                    'bg-green-700 text-white': highlighted === index,
                    'bg-green-50 text-green-800 font-semibold': selectedValue == option.value && highlighted !== index
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
