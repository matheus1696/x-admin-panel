@php
$level = $level ?? 0;

$levels = [
    ['label' => 'Diretoria',   'color' => 'bg-green-700'],
    ['label' => 'Gerência',    'color' => 'bg-emerald-500'],
    ['label' => 'Supervisão',  'color' => 'bg-teal-500'],
    ['label' => 'Coordenação', 'color' => 'bg-purple-500'],
    ['label' => 'Liderança',   'color' => 'bg-rose-500'],
    ['label' => 'Sênior',      'color' => 'bg-cyan-500'],
    ['label' => 'Pleno',       'color' => 'bg-orange-500'],
    ['label' => 'Júnior',      'color' => 'bg-lime-500'],
    ['label' => 'Estagiário',  'color' => 'bg-pink-500'],
];

$levelData = $levels[$level] ?? ['color' => 'bg-slate-500'];
@endphp

<div class="flex flex-col items-center" x-data="{ open: false }">

    @if($level > 0)
        <div class="h-10 w-0.5 bg-slate-400/30"></div>
    @endif

    <div class="relative w-72 py-2 rounded-xl bg-white/80 backdrop-blur border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition" title="{{ $node->acronym }} - {{ $node->title }}" @click="open = !open">
        <button x-show="{{ $node->children->isNotEmpty() ? 'true' : 'false' }}" class="size-5 bg-green-700 hover:bg-green-800 rounded-full absolute top-1 right-1 text-white text-xs">
            <i class="fa-solid fa-chevron-up transition-transform duration-300 text-[10px]" :class="open ? 'rotate-180' : ''"></i>
        </button>
        <span class="absolute left-0 top-0 h-full w-1 {{ $levelData['color'] }} rounded-l-xl"></span>

        <div class="py-3 pl-4 pr-2 text-left">
            {{-- Card do responsável (foto, nome, contato, email) --}}
            <div class="flex gap-4 items-center mb-2">
                {{-- Foto --}}
                <img 
                    src="{{ $node->responsible_photo ? asset('storage/' . $node->responsible_photo) : 'https://tse4.mm.bing.net/th/id/OIP.dDKYQqVBsG1tIt2uJzEJHwHaHa?rs=1&pid=ImgDetMain&o=7&rm=3' }}" 
                    alt="{{ $node->responsible_name ?? '-' }}" 
                    class="w-12 h-12 rounded-full mb-1 border border-slate-300"
                />

                <div class="text-center text-xs font-semibold text-slate-800 space-y-1">
                    {{-- Nome --}}
                    <div class="text-center text-xs font-semibold text-slate-800 truncate">
                        {{ $node->responsible_name ?? 'Sem Responsável pelo setor' }}
                    </div>
                    @if ( $node->contact )
                        <div>{{ $node->contact }}</div>
                    @endif
                </div>
            </div>

            <div class="text-center text-sm font-semibold text-slate-800 truncate">
                {{ $node->acronym }} - {{ $node->title }}
            </div>
        </div>
    </div>

    @if($node->children->isNotEmpty() && $node->children->count() > 1)
        <div x-show="open"
            x-transition:enter="transition-all duration-300"
            x-transition:enter-start="opacity-0 scale-y-0"
            x-transition:enter-end="opacity-100 scale-y-100"
            x-transition:leave="transition-all duration-200"
            x-transition:leave-start="opacity-100 scale-y-100"
            x-transition:leave-end="opacity-0 scale-y-0"
            class="h-10 w-0.5 bg-slate-400/30 mt-2"></div>
    @endif

    @if($node->children->isNotEmpty() && $node->children->count() > 1)
        <div x-show="open"
            x-transition:enter="transition-all duration-300"
            x-transition:enter-start="opacity-0 scale-y-0"
            x-transition:enter-end="opacity-100 scale-y-100"
            x-transition:leave="transition-all duration-200"
            x-transition:leave-start="opacity-100 scale-y-100"
            x-transition:leave-end="opacity-0 scale-y-0"
            class="w-full h-0.5 bg-slate-400/30"></div>
    @endif

    @if($node->children->isNotEmpty())
        <div class="flex gap-6 justify-center" x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            @foreach($node->children as $child)
                @include('livewire.organization.organization-chart._partials.organization-chart-org-node', [
                    'node' => $child,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif

</div>