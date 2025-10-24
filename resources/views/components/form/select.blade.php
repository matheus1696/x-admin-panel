@props([
    'name' => null,
    'options' => [],
    'collection' => null,
    'labelField' => 'name',
    'valueField' => 'id',
    'default' => 'Selecione uma opção',
    'disabled' => false,
    'selected' => null
])

@php
// Se collection for passada, transforma em options
if($collection) {
    $options = $collection->map(fn($item) => [
        'value' => $item[$valueField],
        'label' => $item[$labelField]
    ])->toArray();
}
@endphp

<div x-data="{
        open: false,
        search: '',
        selectedValue: '{{ $selected }}',
        options: {{ json_encode($options) }},
        get filteredOptions() {
            return this.options.filter(opt => opt.label.toLowerCase().includes(this.search.toLowerCase()))
        },
        selectOption(option) {
            this.selectedValue = option.value
            this.open = false
            $refs.input.value = option.value
            this.$dispatch('input', option.value)
        }
    }"
    class="relative w-full"
>
    <!-- Input falso -->
    <div @click="open = !open" 
         class="w-full rounded-md border px-2.5 py-2 text-xs shadow-sm transition-all duration-200 cursor-pointer 
                {{ $errors->has($name) && !$disabled ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-300 bg-gray-50 text-gray-700' }}">
        <span x-text="selectedValue ? options.find(o => o.value === selectedValue)?.label : '{{ $default }}'"></span>
        <i class="fa-solid fa-chevron-down float-right text-gray-400"></i>
    </div>

    <!-- Dropdown -->
    <div x-show="open" @click.outside="open = false" 
         class="absolute mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-60 overflow-auto">
        
        <!-- Campo de busca -->
        <input type="text" x-model="search" placeholder="Buscar..." 
               class="w-full border-b px-2 py-1 text-xs focus:outline-none">

        <!-- Opções -->
        <template x-for="option in filteredOptions" :key="option.value">
            <div @click="selectOption(option)" 
                 class="px-2 py-2 text-xs cursor-pointer hover:bg-blue-500 hover:text-white"
                 :class="{'bg-blue-600 text-white': selectedValue === option.value}">
                <span x-text="option.label"></span>
            </div>
        </template>

    </div>

    <!-- Input real -->
    <input type="hidden" 
           name="{{ $name }}" 
           x-ref="input" 
           x-model="selectedValue"
           {{ $attributes->whereStartsWith('wire:model') }}>
</div>
