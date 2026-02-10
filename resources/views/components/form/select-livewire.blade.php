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
    'size' => 'xs', // xs, sm, md, lg
    'withIcon' => false,
    'icon' => null,
    'borderColor' => 'green', // green, blue, purple, red, etc
    'searchable' => true,
    'rounded' => null, // null, 'sm', 'md', 'lg', 'full', 'none'
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
    
    $wireModel = $attributes->wire('model')->value();
    
    if (!$wireModel) {
        throw new Exception('x-form.select-livewire requer wire:model');
    }
    
    // ✅ Obter o valor inicial do Livewire
    $initialValue = $selected ?? old($name) ?? null;
    
    // Configurações de tamanho
    $sizeConfig = [
        'xs' => [
            'trigger' => 'text-xs py-1.5 px-3',
            'option' => 'text-xs py-1.5 px-3',
            'search' => 'text-xs py-1.5 px-8',
            'iconSize' => 'text-[9px]',
            'chevronSize' => 'text-[8px]',
        ],
        'sm' => [
            'trigger' => 'text-xs py-2 px-3',
            'option' => 'text-xs py-2 px-3',
            'search' => 'text-xs py-2 px-8',
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
        'default' => 'rounded-lg', // padrão
    ];
    
    $roundedClass = $roundedConfig[$rounded] ?? $roundedConfig['default'];
    
    // Se variant for 'pills', força rounded-full
    if ($variant === 'pills') {
        $roundedClass = 'rounded-full';
    }
    
    // Cores das bordas
    $borderColors = [
        'green' => [
            'base' => 'border-gray-200 focus:border-green-500 focus:ring-green-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-green-50 border-green-200',
            'hover' => 'hover:bg-green-50 hover:border-green-300',
            'highlight' => 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-300',
            'text' => 'text-green-700',
            'icon' => 'text-green-600',
            'pill' => 'bg-green-100 text-green-800 border-green-300',
        ],
        'blue' => [
            'base' => 'border-gray-200 focus:border-blue-500 focus:ring-blue-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-blue-50 border-blue-200',
            'hover' => 'hover:bg-blue-50 hover:border-blue-300',
            'highlight' => 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-300',
            'text' => 'text-blue-700',
            'icon' => 'text-blue-600',
            'pill' => 'bg-blue-100 text-blue-800 border-blue-300',
        ],
        'purple' => [
            'base' => 'border-gray-200 focus:border-purple-500 focus:ring-purple-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-purple-50 border-purple-200',
            'hover' => 'hover:bg-purple-50 hover:border-purple-300',
            'highlight' => 'bg-gradient-to-r from-purple-50 to-fuchsia-50 border-purple-300',
            'text' => 'text-purple-700',
            'icon' => 'text-purple-600',
            'pill' => 'bg-purple-100 text-purple-800 border-purple-300',
        ],
        'red' => [
            'base' => 'border-gray-200 focus:border-red-500 focus:ring-red-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-red-50 border-red-200',
            'hover' => 'hover:bg-red-50 hover:border-red-300',
            'highlight' => 'bg-gradient-to-r from-red-50 to-rose-50 border-red-300',
            'text' => 'text-red-700',
            'icon' => 'text-red-600',
            'pill' => 'bg-red-100 text-red-800 border-red-300',
        ],
        'yellow' => [
            'base' => 'border-gray-200 focus:border-yellow-500 focus:ring-yellow-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-yellow-50 border-yellow-200',
            'hover' => 'hover:bg-yellow-50 hover:border-yellow-300',
            'highlight' => 'bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-300',
            'text' => 'text-yellow-700',
            'icon' => 'text-yellow-600',
            'pill' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        ],
        'gray' => [
            'base' => 'border-gray-200 focus:border-gray-500 focus:ring-gray-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-gray-50 border-gray-200',
            'hover' => 'hover:bg-gray-50 hover:border-gray-300',
            'highlight' => 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-300',
            'text' => 'text-gray-700',
            'icon' => 'text-gray-600',
            'pill' => 'bg-gray-100 text-gray-800 border-gray-300',
        ],
        'sky' => [
            'base' => 'border-gray-200 focus:border-sky-500 focus:ring-sky-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-sky-50 border-sky-200',
            'hover' => 'hover:bg-sky-50 hover:border-sky-300',
            'highlight' => 'bg-gradient-to-r from-sky-50 to-cyan-50 border-sky-300',
            'text' => 'text-sky-700',
            'icon' => 'text-sky-600',
            'pill' => 'bg-sky-100 text-sky-800 border-sky-300',
        ],
        'indigo' => [
            'base' => 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'selected' => 'bg-indigo-50 border-indigo-200',
            'hover' => 'hover:bg-indigo-50 hover:border-indigo-300',
            'highlight' => 'bg-gradient-to-r from-indigo-50 to-purple-50 border-indigo-300',
            'text' => 'text-indigo-700',
            'icon' => 'text-indigo-600',
            'pill' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
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
        
        'glass' => [
            'trigger' => [
                'base' => "bg-white/20 backdrop-blur-lg border border-white/30 {$currentColor['base']} hover:bg-white/30 hover:backdrop-blur-xl transition-all duration-300",
                'error' => "bg-red-50/20 backdrop-blur-lg border border-red-300/30",
                'selected' => "{$currentColor['selected']}/30 border border-{$borderColor}-300/50",
            ],
            'dropdown' => 'rounded-lg border border-white/20 bg-white/30 backdrop-blur-xl',
            'option' => 'rounded-lg',
        ],
    ];
    
    $variantConfig = $variants[$variant] ?? $variants['default'];
    
    // Aplica o rounded à classe do trigger
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
        
        livewireValue: @entangle($wireModel).live,
        initialValue: {{ json_encode($initialValue) }},
        
        options: {{ Js::from($options) }},
        placeholder: {{ json_encode($placeholder) }},
        disabled: {{ json_encode($disabled) }},
        
        // Computed properties
        get filteredOptions() {
            const result = !this.search
                ? this.options
                : this.options.filter(o =>
                    o.label.toLowerCase().includes(this.search.toLowerCase())
                );

            if (this.highlighted >= result.length) {
                this.highlighted = 0;
            }

            return result;
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
        
        // Métodos
        select(option) {
            this.livewireValue = option.value;
            this.search = '';
            this.close();
        },
        
        openDropdown() {
            if (this.disabled) return;

            this.open = true;

            this.$nextTick(() => {
                const index = this.filteredOptions.findIndex(o =>
                    this.isEqual(o.value, this.currentValue)
                );

                this.highlighted = index >= 0 ? index : 0;

                this.scrollToHighlighted();

                if (this.$refs.search) {
                    this.$refs.search.focus();
                }
            });
        },

        get currentValue() {
            return this.livewireValue !== undefined && this.livewireValue !== null
                ? this.livewireValue
                : this.initialValue;
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
    
    class="relative w-full select-none" 
    @click.outside="close"
    @keydown.escape.window="close" 
    @keydown.arrow-down.prevent="!disabled && (open ? moveNext() : openDropdown())"
    @keydown.arrow-up.prevent="!disabled && open && movePrev()" 
    @keydown.enter.prevent.stop="open && selectHighlighted()"
    x-on:keydown.space.prevent="!disabled && openDropdown()">
    
    <!-- Campo principal com efeito premium -->
    <div 
        tabindex="0" 
        role="combobox" 
        :aria-expanded="open" 
        @click="toggle()"
        class="{{ $currentSize['trigger'] }} {{ $errors->has($name) && !$disabled ? $triggerClasses['error'] : $triggerClasses['base'] }}"
        :class="[
            disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
            open ? 'ring-2 ' + ($errors->has($name) ? 'ring-red-500/30' : 'ring-{{ $borderColor }}-500/30') : '',
            selectedOption ? $triggerClasses['selected'] : ''
        ]">
        <div class="flex justify-between items-center gap-2">
            <!-- Ícone opcional -->
            @if($withIcon && $icon)
                <div class="flex-shrink-0">
                    <i class="{{ $icon }} {{ $currentSize['iconSize'] }} {{ $currentColor['icon'] }}"></i>
                </div>
            @endif
            
            <!-- Label exibido -->
            <span 
                class="flex-1 truncate font-medium" 
                :class="selectedOption ? '{{ $currentColor['text'] }}' : 'text-gray-300'">
                <span x-text="displayLabel"></span>
            </span>
            
            <!-- Ícones -->
            <div class="flex items-center gap-1.5 flex-shrink-0">                
                <!-- Ícone dropdown animado -->
                <div class="relative">
                    <i class="fa-solid fa-chevron-down {{ $currentSize['chevronSize'] }} text-gray-400 transition-all duration-300" 
                       :class="open ? 'rotate-180' : ''"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dropdown Premium -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
        @click.outside="close" 
        role="listbox" 
        :id="'listbox-{{ $name }}'"
        class="absolute z-50 mt-1.5 w-full max-h-64 overflow-auto {{ $variantConfig['dropdown'] }} bg-white/95 backdrop-blur-md shadow-xl shadow-black/5">
        
        <!-- Campo de busca premium -->
        @if($searchable)
        <div class="sticky top-0 bg-gradient-to-b from-white to-white/95 border-b border-gray-100 p-3 z-10">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass {{ $currentSize['iconSize'] }} text-gray-400"></i>
                </div>
                <input 
                    x-ref="search" 
                    x-model="search" 
                    type="text" 
                    placeholder="Digite para buscar..."
                    class="w-full {{ $currentSize['search'] }} text-gray-700 border border-gray-200 rounded-lg bg-white/80 focus:ring-2 focus:ring-{{ $borderColor }}-500/30 focus:border-{{ $borderColor }}-500 outline-none transition-all duration-200"
                    role="searchbox"
                    @keydown.arrow-down.prevent="moveNext()"
                    @keydown.arrow-up.prevent="movePrev()"
                    @keydown.enter.prevent.stop="selectHighlighted()" />
                <div x-show="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                    <i class="fa-solid fa-times {{ $currentSize['iconSize'] }} text-gray-400 hover:text-gray-600"></i>
                </div>
            </div>
        </div>
        @endif

        <!-- Opções com scroll suave -->
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
                            class="{{ $currentSize['option'] }} {{ $variantConfig['option'] }} cursor-pointer transition-all duration-100 flex items-center gap-3"
                            :class="{
                                '{{ $currentColor['highlight'] }} border shadow-sm': index === highlighted,
                                '{{ $currentColor['selected'] }}': isEqual(currentValue, option.value)
                            }">
                            
                            <!-- Label com truncate elegante -->
                            <div class="flex-1 min-w-0">
                                <span x-text="option.label" 
                                      class="font-medium leading-tight truncate block"
                                      :class="{
                                        '{{ $currentColor['text'] }}': (isEqual(currentValue, option.value)),
                                        'text-gray-700': !(isEqual(currentValue, option.value))
                                      }">
                                </span>
                            </div>
                            
                            <!-- Ícone de seleção -->
                            <div x-show="isEqual(currentValue, option.value)" 
                                 class="flex-shrink-0">
                                <i class="fa-solid fa-check-circle {{ $currentSize['iconSize'] }} {{ $currentColor['icon'] }}"></i>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Nenhum resultado encontrado -->
            <div 
                x-show="filteredOptions.length === 0" 
                class="flex flex-col items-center justify-center p-6 text-center">
                <div class="size-5 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-search text-gray-400 text-lg"></i>
                </div>
                <p class="text-xs font-medium text-gray-700 mb-1">Nenhum resultado encontrado</p>
            </div>
        </div>
    </div>
    
    <!-- ✅ Input Hidden -->
    <input
        type="hidden"
        name="{{ $name }}"
        :value="livewireValue !== undefined ? livewireValue : initialValue"
        wire:model="{{ $wireModel }}"
    >
</div>