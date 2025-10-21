@props([
    'icon' => 'fa-solid fa-circle-question',
    'title' => 'Title',
    'active' => false,
])

<a {{ $attributes->merge([ 
    'class' => "flex items-center gap-3 px-5 py-2 rounded-xl mx-2 font-medium transition-all duration-200 " . 
    ($active 
        ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-100' 
        : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700 hover:translate-x-1 border border-transparent hover:border-blue-700'
    )
]) }}>
    <span class="flex items-center gap-3">
        <i class="{{ $icon }} text-center text-sm {{ $active ? 'text-white' : 'text-blue-500' }}"></i>
        <span class="text-xs" :class="sidebarExpanded ? 'opacity-100 whitespace-nowrap transition-all duration-200' : 'hidden opacity-0'"> 
            {{ $title }} 
        </span>
    </span>
    
    <!-- Indicador de página ativa -->
    @if($active)
        <div :class="sidebarExpanded ? 'opacity-100 ml-auto w-2 h-2 bg-white rounded-full animate-pulse' : 'hidden opacity-0'"></div>
    @endif
</a>