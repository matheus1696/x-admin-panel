@props([
    'icon' => 'fa-solid fa-circle-question',
    'title' => 'Título',
    'active' => false,
    'href' => '#',
])
<a 
    href="{{ $href }}" 
    class="flex items-center gap-3 px-4 py-2 rounded-lg mx-2 font-medium text-xs transition-all duration-200  
        {{ $active 
            ? 'bg-green-700 text-white shadow-md' 
            : 'text-green-700 hover:bg-green-50 hover:text-green-700 border border-transparent hover:border-green-700 hover:translate-x-1' }}"
>
    <!-- Ícone e título -->
    <span class="flex items-center gap-3">
        <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-white' : 'text-green-700' }}"></i>
        <span 
            class="transition-all duration-200"
            :class="sidebarExpanded ? 'opacity-100 whitespace-nowrap' : 'hidden opacity-0'"
        > 
            {{ $title }} 
        </span>
    </span>
    
    <!-- Indicador de página ativa -->
    @if($active)
        <div 
            :class="sidebarExpanded ? 'opacity-100 ml-auto w-2 h-2 bg-white rounded-full animate-pulse' : 'hidden opacity-0'"
        ></div>
    @endif
</a>
