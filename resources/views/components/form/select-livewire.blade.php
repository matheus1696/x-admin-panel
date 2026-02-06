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
    
    $wireModel = $attributes->wire('model')->value();
    
    if (!$wireModel) {
        throw new Exception('x-form.select-livewire requer wire:model');
    }
    
    // ✅ Obter o valor inicial do Livewire
    $initialValue = $selected ?? old($name) ?? null;
@endphp

@php
    // Estilo Select
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

<div 
    x-data="{
        open: false,
        search: '',
        highlighted: 0,
        
        // ✅ Usar live e passar valor inicial
        livewireValue: @entangle($wireModel).live,
        
        // ✅ Valor inicial para comparação
        initialValue: {{ json_encode($initialValue) }},
        
        options: {{ Js::from($options) }},
        placeholder: {{ json_encode($placeholder) }},
        disabled: {{ json_encode($disabled) }},
        
        // Computed properties
        get filteredOptions() {
            if (!this.search) return this.options;
            
            return this.options.filter(o =>
                o.label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        
        get selectedOption() {
            // ✅ Primeiro usar o valor do Livewire, depois o inicial
            const currentValue = this.livewireValue !== undefined && this.livewireValue !== null 
                ? this.livewireValue 
                : this.initialValue;
            
            return this.options.find(o => this.isEqual(o.value, currentValue)) || null;
        },
        
        isEqual(a, b) {
            // ✅ Comparação segura para diferentes tipos
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
            // ✅ Atualizar valor e fechar dropdown
            this.livewireValue = option.value;
            this.search = '';
            this.close();
        },
        
        openDropdown() {
            if (this.disabled) return;
            this.open = true;
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
        if (this.initialValue !== null && this.initialValue !== undefined && this.livewireValue === undefined) {
            this.livewireValue = this.initialValue;
        }
    "
    
    class="relative w-full" 
    @click.outside="close"
    @keydown.escape.window="close" 
    @keydown.arrow-down.prevent="!disabled && (open ? moveNext() : openDropdown())"
    @keydown.arrow-up.prevent="!disabled && open && movePrev()" 
    @keydown.enter.prevent.stop="open && selectHighlighted()"
    x-on:keydown.space.prevent="!disabled && openDropdown()">
    
    <!-- Campo principal -->
    <div 
        tabindex="0" 
        role="combobox" 
        :aria-expanded="open" 
        @click="toggle()"
        class="{{ $errors->has($name) && !$disabled ? $errorBorder : $baseBorder }}"
        :class="disabled ? 'opacity-50 cursor-not-allowed' : ''">
        <div class="flex justify-between items-center">
            <!-- Label exibido -->
            <span 
                class="flex-1 truncate" 
                :class="selectedOption ? 'text-gray-700' : 'text-gray-400'">
                <span x-text="displayLabel"></span>
            </span>
            
            <!-- Ícone dropdown -->
            <div class="flex items-center gap-1">                
                <!-- Ícone dropdown -->
                <i class="fa-solid fa-chevron-down text-gray-400 text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </div>
        </div>
    </div>

    <!-- Dropdown -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        @click.outside="close" 
        role="listbox" 
        :id="'listbox-{{ $name }}'"
        class="absolute z-50 mt-1 w-full max-h-60 overflow-auto rounded-lg border border-gray-300 bg-white shadow-lg">
        
        <!-- Campo de busca -->
        <div class="sticky top-0 bg-white border-b p-2 z-10">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input 
                    x-ref="search" 
                    x-model="search" 
                    type="text" 
                    placeholder="Buscar..."
                    class="w-full pl-8 pr-3 py-2 text-xs border rounded-md focus:ring-green-700 focus:border-green-700"
                    role="searchbox"
                    @keydown.arrow-down.prevent="moveNext()"
                    @keydown.arrow-up.prevent="movePrev()"
                    @keydown.enter.prevent.stop="selectHighlighted()" />
            </div>
        </div>

        <!-- Opções -->
        <template x-if="filteredOptions.length > 0">
            <div class="py-1">
                <template x-for="(option, index) in filteredOptions" :key="option.value">
                    <div 
                        :id="'option-' + index" 
                        :ref="'option_' + index" 
                        role="option"
                        :aria-selected="isEqual(livewireValue, option.value) || isEqual(initialValue, option.value)"
                        @click="select(option)"
                        @mouseenter="highlighted = index"
                        class="flex items-center gap-2 px-3 py-2 text-xs cursor-pointer transition"
                        :class="{
                            'bg-green-700 text-white': index === highlighted,
                            'bg-green-50': (isEqual(livewireValue, option.value) || isEqual(initialValue, option.value)) && index !== highlighted,
                            'text-gray-700 hover:bg-green-100': index !== highlighted && !isEqual(livewireValue, option.value) && !isEqual(initialValue, option.value)
                        }">
                        
                        {{ $avatar ?? null }}
                        
                        <!-- Checkbox para item selecionado -->
                        <div class="w-4 h-4 flex items-center justify-center">
                            <i 
                                class="fa-solid fa-check text-xs" 
                                :class="(isEqual(livewireValue, option.value) || isEqual(initialValue, option.value)) ? 'text-green-600' : 'text-transparent'">
                            </i>
                        </div>
                        
                        <span x-text="option.label" class="line-clamp-1"></span>
                    </div>
                </template>
            </div>
        </template>

        <!-- Nenhum resultado -->
        <div 
            x-show="filteredOptions.length === 0" 
            class="px-3 py-4 text-xs text-gray-500 italic text-center">
            Nenhum resultado encontrado
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