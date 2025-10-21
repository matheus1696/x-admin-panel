@props([
    'title' => 'Title',
    'active' => false,
])

<a {{ $attributes->merge([ 
    'class' => "flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all duration-200 " . 
    ($active 
        ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-600 font-semibold' 
        : 'text-gray-600 hover:bg-blue-100 hover:text-blue-700 hover:translate-x-1'
    )
]) }}>
    <div class="w-1.5 h-1.5 rounded-full bg-blue-300 {{ $active ? 'bg-blue-600' : '' }}"></div>
    <span class="text-xs whitespace-nowrap">{{ $title }}</span>
    
    @if($active)
        <div class="ml-auto w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></div>
    @endif
</a>