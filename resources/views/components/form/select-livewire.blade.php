@props([
    'name' => 'no-name',
    'options' => [],
    'collection' => null,
    'classes' => '',
    'labelField' => 'title',
    'labelAcronym' => null,
    'valueField' => 'id',
    'selected' => null,
    'placeholder' => 'Selecione uma opcao',
    'default' => null,
    'disabled' => false,
    'variant' => 'default',
    'size' => 'sm',
    'withIcon' => false,
    'icon' => null,
    'borderColor' => 'green',
    'searchable' => true,
    'rounded' => null,
    'shadow' => true,
])

@php
    $normalizeBoolean = static fn (mixed $value): bool => filter_var($value, FILTER_VALIDATE_BOOLEAN) || $value === true || $value === 1 || $value === '1';

    $disabled = $normalizeBoolean($disabled);
    $withIcon = $normalizeBoolean($withIcon);
    $searchable = $normalizeBoolean($searchable);
    $shadow = $normalizeBoolean($shadow);
    $placeholder = $default ?? $placeholder;

    if ($collection) {
        $options = $collection
            ->map(function ($item) use ($labelAcronym, $labelField, $valueField) {
                return [
                    'value' => data_get($item, $valueField),
                    'label' => $labelAcronym
                        ? data_get($item, $labelAcronym) . ' - ' . data_get($item, $labelField)
                        : data_get($item, $labelField),
                ];
            })
            ->toArray();
    }

    $wireModel = $attributes->wire('model')->value();

    if (! $wireModel) {
        throw new Exception('x-form.select-livewire requer wire:model');
    }

    $initialValue = $selected ?? old($name) ?? null;

    $sizeConfig = [
        'xs' => [
            'trigger' => 'text-[12px] py-1 px-3',
            'option' => 'text-[12px] py-1 px-3',
            'search' => 'text-[12px] py-1 px-8',
            'iconSize' => 'text-[9px]',
            'chevronSize' => 'text-[8px]',
        ],
        'sm' => [
            'trigger' => 'text-[12px] py-2 px-3',
            'option' => 'text-[12px] py-2 px-3',
            'search' => 'text-[12px] py-2 px-8',
            'iconSize' => 'text-[10px]',
            'chevronSize' => 'text-[9px]',
        ],
        'md' => [
            'trigger' => 'text-sm py-2.5 px-4',
            'option' => 'text-sm py-2.5 px-4',
            'search' => 'text-sm py-2.5 px-9',
            'iconSize' => 'text-xs',
            'chevronSize' => 'text-[10px]',
        ],
        'lg' => [
            'trigger' => 'text-base py-3 px-4',
            'option' => 'text-base py-3 px-4',
            'search' => 'text-base py-3 px-10',
            'iconSize' => 'text-sm',
            'chevronSize' => 'text-xs',
        ],
    ];

    $currentSize = $sizeConfig[$size] ?? $sizeConfig['sm'];

    $roundedConfig = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        'full' => 'rounded-full',
        'default' => 'rounded-lg',
    ];

    $roundedClass = $roundedConfig[$rounded] ?? $roundedConfig['default'];

    if ($variant === 'pills') {
        $roundedClass = 'rounded-full';
    }

    $borderColors = [
        'green' => [
            'base' => 'border-gray-200 focus:border-emerald-700 focus:ring-emerald-700/30',
            'error' => 'border-red-400 focus:border-red-700 focus:ring-red-700/30',
            'selected' => 'bg-emerald-50 border-emerald-200',
            'highlight' => 'bg-gradient-to-r from-emerald-50 to-emerald-50 border-emerald-300',
            'text' => 'text-gray-700',
            'icon' => 'text-emerald-700',
            'pill' => 'bg-emerald-200 text-emerald-800 border-emerald-300',
            'search' => 'focus:ring-transparent border-2 border-gray-200 focus:border-emerald-700/80',
            'ring' => 'ring-emerald-500/30',
            'glassSelected' => 'bg-emerald-50/30 border border-emerald-300/50',
            'hoverOption' => 'hover:bg-emerald-50 hover:text-emerald-700',
        ],
        'blue' => [
            'base' => 'border-gray-200 focus:border-blue-500 focus:ring-blue-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-blue-50 border-blue-200',
            'highlight' => 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-300',
            'text' => 'text-gray-700',
            'icon' => 'text-blue-600',
            'pill' => 'bg-blue-100 text-blue-800 border-blue-300',
            'search' => 'focus:ring-transparent border-2 border-gray-200 focus:border-blue-600/80',
            'ring' => 'ring-blue-500/30',
            'glassSelected' => 'bg-blue-50/30 border border-blue-300/50',
            'hoverOption' => 'hover:bg-blue-50 hover:text-blue-700',
        ],
        'purple' => [
            'base' => 'border-gray-200 focus:border-purple-500 focus:ring-purple-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-purple-50 border-purple-200',
            'highlight' => 'bg-gradient-to-r from-purple-50 to-fuchsia-50 border-purple-300',
            'text' => 'text-gray-700',
            'icon' => 'text-purple-600',
            'pill' => 'bg-purple-100 text-purple-800 border-purple-300',
            'search' => 'focus:ring-transparent border-2 border-gray-200 focus:border-purple-600/80',
            'ring' => 'ring-purple-500/30',
            'glassSelected' => 'bg-purple-50/30 border border-purple-300/50',
            'hoverOption' => 'hover:bg-purple-50 hover:text-purple-700',
        ],
        'red' => [
            'base' => 'border-gray-200 focus:border-red-500 focus:ring-red-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-red-50 border-red-200',
            'highlight' => 'bg-gradient-to-r from-red-50 to-rose-50 border-red-300',
            'text' => 'text-gray-700',
            'icon' => 'text-red-600',
            'pill' => 'bg-red-100 text-red-800 border-red-300',
            'search' => 'focus:ring-transparent border-2 border-gray-200 focus:border-red-600/80',
            'ring' => 'ring-red-500/30',
            'glassSelected' => 'bg-red-50/30 border border-red-300/50',
            'hoverOption' => 'hover:bg-red-50 hover:text-red-700',
        ],
        'yellow' => [
            'base' => 'border-gray-200 focus:border-yellow-500 focus:ring-yellow-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-yellow-50 border-yellow-200',
            'highlight' => 'bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-300',
            'text' => 'text-yellow-700',
            'icon' => 'text-yellow-600',
            'pill' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'search' => 'focus:ring-transparent border-2 border-gray-200 focus:border-yellow-600/80',
            'ring' => 'ring-yellow-500/30',
            'glassSelected' => 'bg-yellow-50/30 border border-yellow-300/50',
            'hoverOption' => 'hover:bg-yellow-50 hover:text-yellow-700',
        ],
        'gray' => [
            'base' => 'border-gray-200 focus:border-gray-500 focus:ring-gray-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-gray-50 border-gray-200',
            'highlight' => 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-300',
            'text' => 'text-gray-700',
            'icon' => 'text-gray-600',
            'pill' => 'bg-gray-100 text-gray-800 border-gray-300',
            'search' => 'focus:ring-transparent border-2 border-gray-200 focus:border-gray-600/80',
            'ring' => 'ring-gray-500/30',
            'glassSelected' => 'bg-gray-50/30 border border-gray-300/50',
            'hoverOption' => 'hover:bg-gray-50 hover:text-gray-700',
        ],
        'sky' => [
            'base' => 'border-gray-200 focus:border-sky-500 focus:ring-sky-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-sky-50 border-sky-200',
            'highlight' => 'bg-gradient-to-r from-sky-50 to-cyan-50 border-sky-300',
            'text' => 'text-sky-700',
            'icon' => 'text-sky-600',
            'pill' => 'bg-sky-100 text-sky-800 border-sky-300',
            'search' => 'focus:ring-transparent border-2 border-gray-200 focus:border-sky-600/80',
            'ring' => 'ring-sky-500/30',
            'glassSelected' => 'bg-sky-50/30 border border-sky-300/50',
            'hoverOption' => 'hover:bg-sky-50 hover:text-sky-700',
        ],
        'indigo' => [
            'base' => 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-indigo-50 border-indigo-200',
            'highlight' => 'bg-gradient-to-r from-indigo-50 to-purple-50 border-indigo-300',
            'text' => 'text-indigo-700',
            'icon' => 'text-indigo-600',
            'pill' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'search' => 'focus:ring-transparent border-2 border-gray-200 focus:border-indigo-600/80',
            'ring' => 'ring-indigo-500/30',
            'glassSelected' => 'bg-indigo-50/30 border border-indigo-300/50',
            'hoverOption' => 'hover:bg-indigo-50 hover:text-indigo-700',
        ],
    ];

    $currentColor = $borderColors[$borderColor] ?? $borderColors['green'];

    $variants = [
        'default' => [
            'trigger' => [
                'base' => "bg-white/80 backdrop-blur-sm border {$currentColor['base']} shadow-sm hover:shadow transition-all duration-200",
                'error' => "bg-white/80 backdrop-blur-sm border {$currentColor['error']} shadow-sm",
                'selected' => "{$currentColor['selected']} border",
            ],
            'dropdown' => 'rounded-xl border border-gray-200',
            'option' => 'rounded-lg',
        ],
        'inline' => [
            'trigger' => [
                'base' => 'bg-transparent border-transparent',
                'error' => "bg-transparent border {$currentColor['error']}",
                'selected' => "border {$currentColor['selected']}",
            ],
            'dropdown' => 'rounded-lg border border-gray-200',
            'option' => 'rounded-lg',
        ],
        'outline' => [
            'trigger' => [
                'base' => "bg-transparent border {$currentColor['base']} hover:bg-white/50 hover:shadow-sm transition-all duration-200",
                'error' => "bg-transparent border {$currentColor['error']}",
                'selected' => "border {$currentColor['selected']}",
            ],
            'dropdown' => 'rounded-lg border border-gray-200',
            'option' => 'rounded-lg',
        ],
        'filled' => [
            'trigger' => [
                'base' => "bg-gray-50 border border-gray-200 {$currentColor['base']} hover:bg-white transition-all duration-200",
                'error' => 'bg-red-50 border border-red-300',
                'selected' => "{$currentColor['selected']} border",
            ],
            'dropdown' => 'rounded-lg border border-gray-200',
            'option' => 'rounded-lg',
        ],
        'pills' => [
            'trigger' => [
                'base' => "bg-gray-50 border border-gray-200 {$currentColor['base']} hover:bg-white transition-all duration-200",
                'error' => 'bg-red-50 border border-red-300',
                'selected' => "{$currentColor['pill']} border",
            ],
            'dropdown' => 'rounded-lg border border-gray-200',
            'option' => 'rounded-lg',
        ],
        'minimal' => [
            'trigger' => [
                'base' => 'bg-transparent border-0 hover:bg-gray-50/50 px-0 focus:ring-0 transition-colors duration-200',
                'error' => 'bg-transparent border-0 text-red-600',
                'selected' => "{$currentColor['text']} font-semibold",
            ],
            'dropdown' => 'rounded-lg border border-gray-200 shadow-lg',
            'option' => 'rounded-lg',
        ],
        'glass' => [
            'trigger' => [
                'base' => "bg-white/20 backdrop-blur-lg border border-white/30 {$currentColor['base']} hover:bg-white/30 hover:backdrop-blur-xl transition-all duration-300",
                'error' => 'bg-red-50/20 backdrop-blur-lg border border-red-300/30',
                'selected' => $currentColor['glassSelected'],
            ],
            'dropdown' => 'rounded-lg border border-white/20 bg-white/30 backdrop-blur-xl',
            'option' => 'rounded-lg',
        ],
    ];

    $variantConfig = $variants[$variant] ?? $variants['default'];
    $shadowClass = $shadow ? '' : 'shadow-none hover:shadow-none';

    $triggerClasses = [
        'base' => $variantConfig['trigger']['base'] . ' ' . $roundedClass . ' ' . $shadowClass,
        'error' => $variantConfig['trigger']['error'] . ' ' . $roundedClass . ' ' . $shadowClass,
        'selected' => $variantConfig['trigger']['selected'] . ' ' . $roundedClass . ' ' . $shadowClass,
    ];
@endphp

<div
    x-data="{
        open: false,
        search: '',
        highlighted: 0,
        livewireValue: @entangle($wireModel).live,
        initialValue: {{ json_encode($initialValue) }},
        options: {{ Js::from($options) }},
        placeholder: {{ json_encode($placeholder) }},
        disabled: {{ json_encode($disabled) }},
        get filteredOptions() {
            const result = !this.search
                ? this.options
                : this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));

            if (this.highlighted >= result.length) {
                this.highlighted = 0;
            }

            return result;
        },
        get currentValue() {
            return this.livewireValue !== undefined && this.livewireValue !== null
                ? this.livewireValue
                : this.initialValue;
        },
        get selectedOption() {
            return this.options.find(o => this.isEqual(o.value, this.currentValue)) || null;
        },
        isEqual(a, b) {
            if (a === null || b === null || a === undefined || b === undefined) {
                return a === b;
            }

            return String(a) === String(b);
        },
        get displayLabel() {
            return this.selectedOption ? this.selectedOption.label : this.placeholder;
        },
        select(option) {
            this.livewireValue = option.value;
            this.search = '';
            this.close();
        },
        openDropdown() {
            if (this.disabled) return;

            this.open = true;

            this.$nextTick(() => {
                const index = this.filteredOptions.findIndex(o => this.isEqual(o.value, this.currentValue));
                this.highlighted = index >= 0 ? index : 0;
                this.scrollToHighlighted();

                if (this.$refs.search) {
                    this.$refs.search.focus();
                }
            });
        },
        close() {
            this.open = false;
            this.search = '';
            this.highlighted = 0;
        },
        toggle() {
            if (this.disabled) return;
            this.open ? this.close() : this.openDropdown();
        },
        moveNext() {
            if (this.highlighted < this.filteredOptions.length - 1) {
                this.highlighted++;
                this.scrollToHighlighted();
            }
        },
        movePrev() {
            if (this.highlighted > 0) {
                this.highlighted--;
                this.scrollToHighlighted();
            }
        },
        scrollToHighlighted() {
            this.$nextTick(() => {
                const highlighted = this.$refs['option_' + this.highlighted];
                if (highlighted) {
                    highlighted.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            });
        },
        selectHighlighted() {
            const option = this.filteredOptions[this.highlighted];
            if (option) {
                this.select(option);
            }
        },
    }"
    x-init="
        if (this.livewireValue === undefined || this.livewireValue === null) {
            this.livewireValue = this.initialValue
        }
        this.initialValue = null
    "
    class="relative w-full select-none {{ $classes }}"
    @click.outside="close"
    @keydown.escape.window="close"
    @keydown.arrow-down.prevent="!disabled && (open ? moveNext() : openDropdown())"
    @keydown.arrow-up.prevent="!disabled && open && movePrev()"
    @keydown.enter.prevent.stop="open && selectHighlighted()"
    @keydown.space.prevent="!disabled && openDropdown()"
>
    <div
        tabindex="{{ $disabled ? '-1' : '0' }}"
        role="combobox"
        :aria-expanded="open"
        @click="toggle()"
        class="{{ $currentSize['trigger'] }} {{ $errors->has($name) && ! $disabled ? $triggerClasses['error'] : $triggerClasses['base'] }}"
    >
        <div class="flex items-center justify-between gap-2">
            @if ($withIcon && $icon)
                <div class="flex-shrink-0">
                    <i class="{{ $icon }} {{ $currentSize['iconSize'] }} {{ $currentColor['icon'] }}"></i>
                </div>
            @endif

            <span class="flex-1 truncate font-medium" :class="selectedOption ? '{{ $currentColor['text'] }}' : 'text-gray-300'">
                <span x-text="displayLabel"></span>
            </span>

            <div class="flex flex-shrink-0 items-center gap-1.5">
                <div class="relative">
                    <i
                        class="fa-solid fa-chevron-down {{ $currentSize['chevronSize'] }} text-gray-400 transition-all duration-300"
                        :class="open ? 'rotate-180' : ''"
                    ></i>
                </div>
            </div>
        </div>
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="transform -translate-y-2 scale-95 opacity-0"
        x-transition:enter-end="transform translate-y-0 scale-100 opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="transform translate-y-0 scale-100 opacity-100"
        x-transition:leave-end="transform -translate-y-2 scale-95 opacity-0"
        @click.outside="close"
        role="listbox"
        :id="'listbox-{{ $name }}'"
        class="absolute z-50 mt-1.5 max-h-64 w-full overflow-auto {{ $variantConfig['dropdown'] }} bg-white/95 shadow-xl shadow-black/5 backdrop-blur-md"
    >
        @if ($searchable)
            <div class="sticky top-0 z-10 border-b border-gray-100 bg-gradient-to-b from-white to-white/95 p-3">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fa-solid fa-magnifying-glass {{ $currentSize['iconSize'] }} text-gray-400"></i>
                    </div>

                    <input
                        x-ref="search"
                        x-model="search"
                        type="text"
                        placeholder="Digite para buscar..."
                        class="w-full {{ $currentSize['search'] }} {{ $currentColor['search'] }} rounded-lg border border-gray-200 bg-white/80 text-gray-700 outline-none transition-all duration-200"
                        role="searchbox"
                        @keydown.arrow-down.prevent="moveNext()"
                        @keydown.arrow-up.prevent="movePrev()"
                        @keydown.enter.prevent.stop="selectHighlighted()"
                    />

                    <div x-show="search" @click="search = ''" class="absolute inset-y-0 right-0 flex cursor-pointer items-center pr-3">
                        <i class="fa-solid fa-times {{ $currentSize['iconSize'] }} text-gray-400 hover:text-gray-600"></i>
                    </div>
                </div>
            </div>
        @endif

        <div class="py-1">
            <template x-if="filteredOptions.length > 0">
                <div class="space-y-0.5 p-1">
                    <template x-for="(option, index) in filteredOptions" :key="option.value">
                        <div
                            :id="'option-' + index"
                            :ref="'option_' + index"
                            role="option"
                            :aria-selected="isEqual(currentValue, option.value)"
                            @click="select(option)"
                            @mouseenter="highlighted = index"
                            class="{{ $currentSize['option'] }} {{ $variantConfig['option'] }} flex cursor-pointer items-center gap-3 transition-all duration-100"
                            :class="{
                                '{{ $currentColor['highlight'] }} border shadow-sm': index === highlighted,
                                '{{ $currentColor['selected'] }}': isEqual(currentValue, option.value),
                                '{{ $currentColor['hoverOption'] }}': !isEqual(currentValue, option.value) && index !== highlighted
                            }"
                        >
                            <div class="min-w-0 flex-1">
                                <span
                                    x-text="option.label"
                                    class="block truncate font-medium leading-tight"
                                    :class="{
                                        '{{ $currentColor['text'] }}': isEqual(currentValue, option.value),
                                        'text-gray-700': !isEqual(currentValue, option.value)
                                    }"
                                ></span>
                            </div>

                            <div x-show="isEqual(currentValue, option.value)" class="flex-shrink-0">
                                <i class="fa-solid fa-check-circle {{ $currentSize['iconSize'] }} {{ $currentColor['icon'] }}"></i>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div x-show="filteredOptions.length === 0" class="flex flex-col items-center justify-center p-6 text-center">
                <div class="mb-3 flex size-5 items-center justify-center rounded-full bg-gray-100">
                    <i class="fa-solid fa-search text-lg text-gray-400"></i>
                </div>
                <p class="mb-1 text-xs font-medium text-gray-700">Nenhum resultado encontrado</p>
            </div>
        </div>
    </div>

    <input
        type="hidden"
        name="{{ $name }}"
        :value="livewireValue !== undefined ? livewireValue : initialValue"
        wire:model="{{ $wireModel }}"
    >
</div>
