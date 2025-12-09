@props([
    'title' => 'Title',
    'active' => false,
    'icon' => 'fa-solid fa-circle-notch',
])

<a {{ $attributes->merge([ 
    'class' => "flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all duration-200 " . 
    ($active 
        ? 'bg-green-100 text-green-700 border-r-2 border-green-600 font-semibold' 
        : 'text-gray-600 hover:bg-green-100 hover:text-green-700 hover:translate-x-1'
    )
]) }}>
    <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-green-800' : 'text-green-700' }}"></i>
    <span class="text-xs whitespace-nowrap">{{ $title }}</span>
    
    @if($active)
        <div class="ml-auto w-1.5 h-1.5 bg-green-700 rounded-full animate-pulse"></div>
    @endif
</a>