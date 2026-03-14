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
            'base' => 'border-gray-200 focus:border-emerald-600 focus:ring-emerald-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-emerald-50 border-emerald-200',
            'highlight' => 'bg-gradient-to-r from-emerald-50 to-emerald-50 border-emerald-300',
            'text' => 'text-gray-800',
            'icon' => 'text-emerald-600',
            'pill' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'search' => 'focus:ring-transparent border border-gray-200 focus:border-emerald-600',
            'ring' => 'ring-emerald-500/20',
            'glassSelected' => 'bg-emerald-50/30 border border-emerald-300/50',
            'hoverOption' => 'hover:bg-emerald-50 hover:text-emerald-700',
        ],
        'blue' => [
            'base' => 'border-gray-200 focus:border-blue-600 focus:ring-blue-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-blue-50 border-blue-200',
            'highlight' => 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-300',
            'text' => 'text-blue-700',
            'icon' => 'text-blue-600',
            'pill' => 'bg-blue-100 text-blue-800 border-blue-300',
            'search' => 'focus:ring-transparent border border-gray-200 focus:border-blue-600',
            'ring' => 'ring-blue-500/20',
            'glassSelected' => 'bg-blue-50/30 border border-blue-300/50',
            'hoverOption' => 'hover:bg-blue-50 hover:text-blue-700',
        ],
        'purple' => [
            'base' => 'border-gray-200 focus:border-purple-600 focus:ring-purple-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-purple-50 border-purple-200',
            'highlight' => 'bg-gradient-to-r from-purple-50 to-fuchsia-50 border-purple-300',
            'text' => 'text-purple-700',
            'icon' => 'text-purple-600',
            'pill' => 'bg-purple-100 text-purple-800 border-purple-300',
            'search' => 'focus:ring-transparent border border-gray-200 focus:border-purple-600',
            'ring' => 'ring-purple-500/20',
            'glassSelected' => 'bg-purple-50/30 border border-purple-300/50',
            'hoverOption' => 'hover:bg-purple-50 hover:text-purple-700',
        ],
        'red' => [
            'base' => 'border-gray-200 focus:border-red-600 focus:ring-red-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-red-50 border-red-200',
            'highlight' => 'bg-gradient-to-r from-red-50 to-rose-50 border-red-300',
            'text' => 'text-red-700',
            'icon' => 'text-red-600',
            'pill' => 'bg-red-100 text-red-800 border-red-300',
            'search' => 'focus:ring-transparent border border-gray-200 focus:border-red-600',
            'ring' => 'ring-red-500/20',
            'glassSelected' => 'bg-red-50/30 border border-red-300/50',
            'hoverOption' => 'hover:bg-red-50 hover:text-red-700',
        ],
        'yellow' => [
            'base' => 'border-gray-200 focus:border-yellow-600 focus:ring-yellow-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-yellow-50 border-yellow-200',
            'highlight' => 'bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-300',
            'text' => 'text-yellow-700',
            'icon' => 'text-yellow-600',
            'pill' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'search' => 'focus:ring-transparent border border-gray-200 focus:border-yellow-600',
            'ring' => 'ring-yellow-500/20',
            'glassSelected' => 'bg-yellow-50/30 border border-yellow-300/50',
            'hoverOption' => 'hover:bg-yellow-50 hover:text-yellow-700',
        ],
        'gray' => [
            'base' => 'border-gray-200 focus:border-gray-600 focus:ring-gray-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-gray-50 border-gray-200',
            'highlight' => 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-300',
            'text' => 'text-gray-700',
            'icon' => 'text-gray-600',
            'pill' => 'bg-gray-100 text-gray-800 border-gray-300',
            'search' => 'focus:ring-transparent border border-gray-200 focus:border-gray-600',
            'ring' => 'ring-gray-500/20',
            'glassSelected' => 'bg-gray-50/30 border border-gray-300/50',
            'hoverOption' => 'hover:bg-gray-50 hover:text-gray-700',
        ],
        'sky' => [
            'base' => 'border-gray-200 focus:border-sky-600 focus:ring-sky-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-sky-50 border-sky-200',
            'highlight' => 'bg-gradient-to-r from-sky-50 to-cyan-50 border-sky-300',
            'text' => 'text-sky-700',
            'icon' => 'text-sky-600',
            'pill' => 'bg-sky-100 text-sky-800 border-sky-300',
            'search' => 'focus:ring-transparent border border-gray-200 focus:border-sky-600',
            'ring' => 'ring-sky-500/20',
            'glassSelected' => 'bg-sky-50/30 border border-sky-300/50',
            'hoverOption' => 'hover:bg-sky-50 hover:text-sky-700',
        ],
        'indigo' => [
            'base' => 'border-gray-200 focus:border-indigo-600 focus:ring-indigo-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-indigo-50 border-indigo-200',
            'highlight' => 'bg-gradient-to-r from-indigo-50 to-purple-50 border-indigo-300',
            'text' => 'text-indigo-700',
            'icon' => 'text-indigo-600',
            'pill' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'search' => 'focus:ring-transparent border border-gray-200 focus:border-indigo-600',
            'ring' => 'ring-indigo-500/20',
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
        selectedValue: {{ json_encode($selected) }},
        options: {{ Js::from($options) }},
        placeholder: {{ json_encode($placeholder) }},
        disabled: {{ json_encode($disabled) }},
        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
        },
        get selectedOption() {
            return this.options.find(o => String(o.value) === String(this.selectedValue)) || null;
        },
        get displayLabel() {
            return this.selectedOption ? this.selectedOption.label : this.placeholder;
        },
        select(option) {
            this.selectedValue = option.value;
            this.$refs.hidden.value = option.value;
            this.$refs.hidden.dispatchEvent(new Event('change', { bubbles: true }));
            this.close();
        },
        openDropdown() {
            if (this.disabled) return;
            this.open = true;
            this.$nextTick(() => {
                const index = this.filteredOptions.findIndex(o => String(o.value) === String(this.selectedValue));
                this.highlighted = index >= 0 ? index : 0;
                this.scrollToHighlighted();
                if (this.$refs.search) this.$refs.search.focus();
            });
        },
        close() {
            this.open = false;
            this.search = '';
            this.highlighted = 0;
        },
        toggle() {
            this.disabled ? null : this.open ? this.close() : this.openDropdown();
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
                const el = this.$refs['option_' + this.highlighted];
                if (el) el.scrollIntoView({ block: 'nearest' });
            });
        },
        selectHighlighted() {
            const option = this.filteredOptions[this.highlighted];
            if (option) this.select(option);
        }
    }"
    x-init="
        $watch('open', value => {
            if (!value) {
                search = '';
                highlighted = 0;
            }
        });
    "
    class="relative w-full select-none {{ $classes }}"
    @click.outside="close"
    @keydown.escape.window="close"
    @keydown.arrow-down.prevent="!disabled && (open ? moveNext() : openDropdown())"
    @keydown.arrow-up.prevent="!disabled && open && movePrev()"
    @keydown.enter.prevent.stop="open && selectHighlighted()"
    @keydown.space.prevent="!disabled && !open && openDropdown()"
>
    <div
        tabindex="{{ $disabled ? '-1' : '0' }}"
        role="combobox"
        :aria-expanded="open"
        @click="toggle()"
        class="{{ $currentSize['trigger'] }} {{ $errors->has($name) ? $triggerClasses['error'] : $triggerClasses['base'] }} flex w-full items-center justify-between"
        :class="{
            'opacity-50 cursor-not-allowed': disabled,
            'cursor-pointer': !disabled,
            'ring-2 {{ $errors->has($name) ? 'ring-red-500/20' : $currentColor['ring'] }}': open,
            '{{ $triggerClasses['selected'] }}': selectedOption
        }"
    >
        <div class="flex min-w-0 flex-1 items-center gap-2">
            @if ($withIcon && $icon)
                <div class="flex-shrink-0">
                    <i class="{{ $icon }} {{ $currentSize['iconSize'] }} {{ $currentColor['icon'] }}"></i>
                </div>
            @endif

            <span class="truncate" :class="selectedOption ? '{{ $currentColor['text'] }}' : 'text-gray-400'">
                <span x-text="displayLabel"></span>
            </span>
        </div>

        <div class="ml-2 flex-shrink-0">
            <i
                class="fas fa-chevron-down {{ $currentSize['chevronSize'] }} text-gray-400 transition-transform duration-200"
                :class="open ? 'rotate-180 {{ $currentColor['text'] }}' : ''"
            ></i>
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
        role="listbox"
        :id="'listbox-{{ $name }}'"
        class="custom-scrollbar absolute z-50 mt-1.5 max-h-64 w-full overflow-auto {{ $variantConfig['dropdown'] }} bg-white shadow-xl"
    >
        @if ($searchable)
            <div class="sticky top-0 z-10 border-b border-gray-100 bg-white px-2">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2">
                        <i class="fas fa-search {{ $currentSize['iconSize'] }} text-gray-400"></i>
                    </div>

                    <input
                        x-ref="search"
                        x-model="search"
                        type="text"
                        placeholder="Buscar..."
                        class="w-full {{ $currentSize['search'] }} {{ $currentColor['search'] }} rounded-lg bg-gray-50 outline-none transition-all duration-200"
                        @keydown.arrow-down.prevent="moveNext()"
                        @keydown.arrow-up.prevent="movePrev()"
                        @keydown.enter.prevent.stop="selectHighlighted()"
                    />

                    <div x-show="search" @click="search = ''" class="absolute inset-y-0 right-0 flex cursor-pointer items-center pr-3">
                        <i class="fas fa-times {{ $currentSize['iconSize'] }} text-gray-400 hover:text-gray-600"></i>
                    </div>
                </div>
            </div>
        @endif

        <div class="py-1">
            <template x-if="filteredOptions.length > 0">
                <div class="space-y-0.5 p-1">
                    <template x-for="(option, index) in filteredOptions" :key="option.value">
                        <div
                            :ref="'option_' + index"
                            role="option"
                            :aria-selected="String(selectedValue) === String(option.value)"
                            @click="select(option)"
                            @mouseenter="highlighted = index"
                            class="{{ $currentSize['option'] }} {{ $variantConfig['option'] }} flex cursor-pointer items-center justify-between gap-2 transition-all duration-150"
                            :class="{
                                '{{ $currentColor['highlight'] }}': highlighted === index,
                                '{{ $currentColor['selected'] }}': String(selectedValue) === String(option.value) && highlighted !== index,
                                '{{ $currentColor['hoverOption'] }}': String(selectedValue) !== String(option.value) && highlighted !== index
                            }"
                        >
                            <span
                                class="truncate"
                                :class="{
                                    '{{ $currentColor['text'] }}': String(selectedValue) === String(option.value),
                                    'text-gray-700': String(selectedValue) !== String(option.value)
                                }"
                                x-text="option.label"
                            ></span>

                            <div x-show="String(selectedValue) === String(option.value)" class="flex-shrink-0">
                                <i class="fas fa-check {{ $currentSize['iconSize'] }} {{ $currentColor['icon'] }}"></i>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div x-show="filteredOptions.length === 0" class="flex flex-col items-center justify-center px-4 py-6 text-center">
                <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <p class="text-xs font-medium text-gray-700">Nenhum resultado encontrado</p>
                <p class="text-xs text-gray-500">Tente buscar com outros termos</p>
            </div>
        </div>
    </div>

    <input
        x-ref="hidden"
        type="hidden"
        name="{{ $name }}"
        :value="selectedValue"
        value="{{ $selected }}"
    />
</div>
