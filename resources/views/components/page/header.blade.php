@props([
    'icon' => 'fas fa-tasks',
    'color' => 'green',
    'title' => 'Título da Página',
    'subtitle' => 'Subtítulo da Página',
    'button' => null,
    'badge' => null,
    'accordionOpen' => false,
])

@php
    $colorConfig = [
        'green' => [
            'iconBg' => 'bg-gradient-to-br from-emerald-700 to-emerald-800',
            'iconGlow' => 'shadow-emerald-700/20',
            'border' => 'border-emerald-200/50',
            'accent' => 'from-emerald-500/5 via-emerald-500/5 to-emerald-600/5',
            'badge' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'text' => 'text-emerald-600',
            'ring' => 'ring-emerald-500/20',
        ],
        'blue' => [
            'iconBg' => 'bg-gradient-to-br from-blue-500 to-indigo-600',
            'iconGlow' => 'shadow-blue-500/20',
            'border' => 'border-blue-200/50',
            'accent' => 'from-blue-500/5 via-blue-500/5 to-indigo-500/5',
            'badge' => 'bg-blue-100 text-blue-700 border-blue-200',
            'text' => 'text-blue-600',
            'ring' => 'ring-blue-500/20',
        ],
        'purple' => [
            'iconBg' => 'bg-gradient-to-br from-purple-500 to-violet-600',
            'iconGlow' => 'shadow-purple-500/20',
            'border' => 'border-purple-200/50',
            'accent' => 'from-purple-500/5 via-purple-500/5 to-violet-500/5',
            'badge' => 'bg-purple-100 text-purple-700 border-purple-200',
            'text' => 'text-purple-600',
            'ring' => 'ring-purple-500/20',
        ],
        'amber' => [
            'iconBg' => 'bg-gradient-to-br from-amber-500 to-orange-600',
            'iconGlow' => 'shadow-amber-500/20',
            'border' => 'border-amber-200/50',
            'accent' => 'from-amber-500/5 via-amber-500/5 to-orange-500/5',
            'badge' => 'bg-amber-100 text-amber-700 border-amber-200',
            'text' => 'text-amber-600',
            'ring' => 'ring-amber-500/20',
        ],
        'red' => [
            'iconBg' => 'bg-gradient-to-br from-red-500 to-rose-600',
            'iconGlow' => 'shadow-red-500/20',
            'border' => 'border-red-200/50',
            'accent' => 'from-red-500/5 via-red-500/5 to-rose-500/5',
            'badge' => 'bg-red-100 text-red-700 border-red-200',
            'text' => 'text-red-600',
            'ring' => 'ring-red-500/20',
        ],
        'gray' => [
            'iconBg' => 'bg-gradient-to-br from-gray-700 to-gray-800',
            'iconGlow' => 'shadow-gray-500/20',
            'border' => 'border-gray-200/50',
            'accent' => 'from-gray-500/5 via-gray-500/5 to-gray-600/5',
            'badge' => 'bg-gray-100 text-gray-700 border-gray-200',
            'text' => 'text-gray-600',
            'ring' => 'ring-gray-500/20',
        ],
    ];
    
    $config = $colorConfig[$color] ?? $colorConfig['green'];
@endphp

<div class="group relative transition-all duration-500 mb-2">
            
    <!-- Conteúdo principal -->
    <div class="relative flex items-center justify-between gap-4 px-1 pb-5 pt-2 transition-all duration-300">
        <div class="flex items-center gap-4">
            <!-- Ícone com glow e animação -->
            <div class="relative">
                <!-- Glow effect -->
                <div class="absolute inset-0 {{ $config['iconBg'] }} rounded-xl blur-lg opacity-40 group-hover:opacity-60 transition-opacity duration-500"></div>
                
                <!-- Ícone principal -->
                <div class="relative {{ $config['iconBg'] }} {{ $config['iconGlow'] }} size-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500 ring-4 {{ $config['ring'] }} ring-offset-2 ring-offset-white/50">
                    <i class="{{ $icon }} text-xl text-white"></i>
                </div>
                
                <!-- Badge de notificação com animação -->
                @if($badge)
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-br from-amber-500 to-red-500 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-bold text-white shadow-lg animate-pulse">
                        {{ $badge }}
                    </span>
                @endif
            </div>
            
            <!-- Título e subtítulo -->
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                        {{ $title }}
                    </h1>
                    
                    <!-- Indicador de página ativa -->
                    <span class="w-1.5 h-1.5 rounded-full {{ $config['iconBg'] }} animate-pulse"></span>
                </div>
                
                @if($subtitle)
                    <p class="text-sm text-gray-500 flex items-center gap-1.5">
                        <i class="fas fa-circle-info {{ $config['text'] }} text-[10px]"></i>
                        <span class="line-clamp-1">{{ $subtitle }}</span>
                    </p>
                @endif
            </div>
        </div>
        
        <!-- Botão de ação com animação -->
        @if($button)
            <div class="relative group/btn">
                {{ $button }}
            </div>
        @endif
    </div>
</div>