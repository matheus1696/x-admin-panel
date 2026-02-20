@props([
    'name' => 'no-name',
    'options' => [],
    'collection' => null,
    'classes' => '',
    
    'labelField' => 'title',
    'labelAcronym' => null,
    'valueField' => 'id',
    
    'selected' => null,
    'placeholder' => 'Selecione uma opção',
    
    'disabled' => false,
    'variant' => 'default',
    'size' => 'sm', // xs, sm, md, lg
    'withIcon' => false,
    'icon' => null,
    'borderColor' => 'green', // green, blue, purple, red, etc
    'searchable' => true,
    'rounded' => null, // null, 'sm', 'md', 'lg', 'full', 'none'
])

@php
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
    
    // Configurações de tamanho
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
    
    // Sistema de rounded
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
    
    // Cores das bordas
    $borderColors = [
        'green' => [
            'base' => 'border-gray-200 focus:border-emerald-600 focus:ring-emerald-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-emerald-50 border-emerald-200',
            'hover' => 'hover:bg-emerald-50 hover:border-emerald-300',
            'highlight' => 'bg-gradient-to-r from-emerald-50 to-emerald-50 border-emerald-300',
            'text' => 'text-gray-800',
            'icon' => 'text-emerald-600',
            'pill' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'search' => 'focus:ring-transparent border border-gray-200 focus:border-emerald-600'
        ],
        'blue' => [
            'base' => 'border-gray-200 focus:border-blue-600 focus:ring-blue-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-blue-50 border-blue-200',
            'hover' => 'hover:bg-blue-50 hover:border-blue-300',
            'highlight' => 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-300',
            'text' => 'text-blue-700',
            'icon' => 'text-blue-600',
            'pill' => 'bg-blue-100 text-blue-800 border-blue-300',
        ],
        'purple' => [
            'base' => 'border-gray-200 focus:border-purple-600 focus:ring-purple-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-purple-50 border-purple-200',
            'hover' => 'hover:bg-purple-50 hover:border-purple-300',
            'highlight' => 'bg-gradient-to-r from-purple-50 to-fuchsia-50 border-purple-300',
            'text' => 'text-purple-700',
            'icon' => 'text-purple-600',
            'pill' => 'bg-purple-100 text-purple-800 border-purple-300',
        ],
        'red' => [
            'base' => 'border-gray-200 focus:border-red-600 focus:ring-red-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-red-50 border-red-200',
            'hover' => 'hover:bg-red-50 hover:border-red-300',
            'highlight' => 'bg-gradient-to-r from-red-50 to-rose-50 border-red-300',
            'text' => 'text-red-700',
            'icon' => 'text-red-600',
            'pill' => 'bg-red-100 text-red-800 border-red-300',
        ],
        'yellow' => [
            'base' => 'border-gray-200 focus:border-yellow-600 focus:ring-yellow-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-yellow-50 border-yellow-200',
            'hover' => 'hover:bg-yellow-50 hover:border-yellow-300',
            'highlight' => 'bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-300',
            'text' => 'text-yellow-700',
            'icon' => 'text-yellow-600',
            'pill' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        ],
        'gray' => [
            'base' => 'border-gray-200 focus:border-gray-600 focus:ring-gray-600/20',
            'error' => 'border-red-400 focus:border-red-600 focus:ring-red-600/20',
            'selected' => 'bg-gray-50 border-gray-200',
            'hover' => 'hover:bg-gray-50 hover:border-gray-300',
            'highlight' => 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-300',
            'text' => 'text-gray-700',
            'icon' => 'text-gray-600',
            'pill' => 'bg-gray-100 text-gray-800 border-gray-300',
        ],
    ];
    
    $currentColor = $borderColors[$borderColor] ?? $borderColors['green'];
    
    // Variantes de estilo
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
                'base' => "bg-transparent border-transparent",
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
                'error' => "bg-red-50 border border-red-300",
                'selected' => "{$currentColor['selected']} border",
            ],
            'dropdown' => 'rounded-lg border border-gray-200',
            'option' => 'rounded-lg',
        ],
        'pills' => [
            'trigger' => [
                'base' => "bg-gray-50 border border-gray-200 {$currentColor['base']} hover:bg-white transition-all duration-200",
                'error' => "bg-red-50 border border-red-300",
                'selected' => "{$currentColor['pill']} border",
            ],
            'dropdown' => 'rounded-lg border border-gray-200',
            'option' => 'rounded-lg',
        ],
        'minimal' => [
            'trigger' => [
                'base' => "bg-transparent border-0 hover:bg-gray-50/50 px-0 focus:ring-0 transition-colors duration-200",
                'error' => "bg-transparent border-0 text-red-600",
                'selected' => "{$currentColor['text']} font-semibold",
            ],
            'dropdown' => 'rounded-lg border border-gray-200 shadow-lg',
            'option' => 'rounded-lg',
        ],
    ];
    
    $variantConfig = $variants[$variant] ?? $variants['default'];
    
    $triggerClasses = [
        'base' => $variantConfig['trigger']['base'] . ' ' . $roundedClass,
        'error' => $variantConfig['trigger']['error'] . ' ' . $roundedClass,
        'selected' => $variantConfig['trigger']['selected'] . ' ' . $roundedClass,
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
            return this.options.filter(o =>
                o.label.toLowerCase().includes(this.search.toLowerCase())
            );
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
                const index = this.filteredOptions.findIndex(o => 
                    String(o.value) === String(this.selectedValue)
                );
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
    <!-- Campo principal -->
    <div
        tabindex="{{ $disabled ? '-1' : '0' }}"
        role="combobox"
        :aria-expanded="open"
        @click="toggle()"
        class="{{ $currentSize['trigger'] }} {{ $errors->has($name) ? $triggerClasses['error'] : $triggerClasses['base'] }} w-full flex items-center justify-between"
        :class="{
            'opacity-50 cursor-not-allowed': disabled,
            'cursor-pointer': !disabled,
            'ring-2 ' + ($errors->has($name) ? 'ring-red-500/20' : 'ring-emerald-500/20'): open,
            '{{ $triggerClasses['selected'] }}': selectedOption
        }"
    >
        <!-- Conteúdo esquerdo -->
        <div class="flex items-center gap-2 min-w-0 flex-1">
            @if($withIcon && $icon)
                <div class="flex-shrink-0">
                    <i class="{{ $icon }} {{ $currentSize['iconSize'] }} {{ $currentColor['icon'] }}"></i>
                </div>
            @endif
            
            <span class="truncat" :class="selectedOption ? '{{ $currentColor['text'] }}' : 'text-gray-400'">
                <span x-text="displayLabel"></span>
            </span>
        </div>
        
        <!-- Ícone dropdown -->
        <div class="flex-shrink-0 ml-2">
            <i class="fas fa-chevron-down {{ $currentSize['chevronSize'] }} text-gray-400 transition-transform duration-200"
               :class="open ? 'rotate-180 {{ $currentColor['text'] }}' : ''"></i>
        </div>
    </div>

    <!-- Dropdown -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
        role="listbox"
        :id="'listbox-{{ $name }}'"
        class="absolute z-50 mt-1.5 w-full max-h-64 overflow-auto {{ $variantConfig['dropdown'] }} bg-white shadow-xl custom-scrollbar"
    >
        <!-- Busca -->
        @if($searchable)
        <div class="sticky top-0 bg-white border-b border-gray-100 px-2 z-10">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                    <i class="fas fa-search {{ $currentSize['iconSize'] }} text-gray-400"></i>
                </div>
                <input
                    x-ref="search"
                    x-model="search"
                    type="text"
                    placeholder="Buscar..."
                    class="w-full {{ $currentSize['search'] }} border border-gray-200 rounded-lg bg-gray-50 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600/20 outline-none transition-all duration-200"
                    @keydown.arrow-down.prevent="moveNext()"
                    @keydown.arrow-up.prevent="movePrev()"
                    @keydown.enter.prevent.stop="selectHighlighted()"
                />
                <div x-show="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                    <i class="fas fa-times {{ $currentSize['iconSize'] }} text-gray-400 hover:text-gray-600"></i>
                </div>
            </div>
        </div>
        @endif

        <!-- Opções -->
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
                            class="{{ $currentSize['option'] }} {{ $variantConfig['option'] }} cursor-pointer transition-all duration-150 flex items-center justify-between gap-2"
                            :class="{
                                '{{ $currentColor['highlight'] }}': highlighted === index,
                                '{{ $currentColor['selected'] }}': String(selectedValue) === String(option.value) && highlighted !== index,
                                'hover:bg-emerald-50 hover:text-emerald-700': String(selectedValue) !== String(option.value) && highlighted !== index
                            }"
                        >
                            <span class="truncate"
                                  :class="{
                                      '{{ $currentColor['text'] }}': String(selectedValue) === String(option.value),
                                      'text-gray-700': String(selectedValue) !== String(option.value)
                                  }"
                                  x-text="option.label">
                            </span>
                            
                            <div x-show="String(selectedValue) === String(option.value)" class="flex-shrink-0">
                                <i class="fas fa-check {{ $currentSize['iconSize'] }} {{ $currentColor['icon'] }}"></i>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div x-show="filteredOptions.length === 0"
                 class="flex flex-col items-center justify-center py-6 px-4 text-center">
                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <p class="text-xs font-medium text-gray-700 mb-1">Nenhum resultado encontrado</p>
                <p class="text-xs text-gray-500">Tente buscar com outros termos</p>
            </div>
        </div>
    </div>

    <!-- Input Hidden -->
    <input
        x-ref="hidden"
        type="hidden"
        name="{{ $name }}"
        :value="selectedValue"
        value="{{ $selected }}"
    />
</div>