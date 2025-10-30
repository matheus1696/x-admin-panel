@props([
    'title' => 'Title',
    'active' => false,
])

<a {{ $attributes->merge([ 
    'class' => "flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all duration-200 " . 
    ($active 
        ? 'bg-green-100 text-green-700 border-r-2 border-green-600 font-semibold' 
        : 'text-gray-600 hover:bg-green-100 hover:text-green-700 hover:translate-x-1'
    )
]) }}>
    <div class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-green-700' : 'bg-green-400' }}"></div>
    <span class="text-xs whitespace-nowrap">{{ $title }}</span>
    
    @if($active)
        <div class="ml-auto w-1.5 h-1.5 bg-green-700 rounded-full animate-pulse"></div>
    @endif
</a>