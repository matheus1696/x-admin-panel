@php
$level = $level ?? 0;

$levels = [
    ['label' => 'Diretoria',   'color' => 'bg-emerald-700'],
    ['label' => 'Gerência',    'color' => 'bg-green-500'],
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
        <div class="h-10 w-0.5 bg-emerald-800"></div>
    @endif

    <div class="relative w-96 rounded-2xl bg-white shadow-md hover:shadow-xl border border-emerald-100 hover:border-emerald-300 transition-all duration-300 hover:-translate-y-1 group" title="{{ $node->acronym }} - {{ $node->title }}" @click="open = !open">
        
        {{-- Botão de expandir com design melhorado --}}
        @if($node->children->isNotEmpty())
            <button 
                x-show="true"
                class="absolute -top-2 -right-2 z-10 size-7 bg-emerald-600 hover:bg-emerald-700 rounded-full shadow-lg hover:shadow-emerald-200/50 text-white text-xs flex items-center justify-center transition-all duration-300 hover:scale-110 border-2 border-white"
                @click.stop
            >
                <i class="fa-solid fa-chevron-up transition-transform duration-300 text-[10px]" :class="open ? 'rotate-180' : ''"></i>
            </button>
        @endif

        {{-- Barra lateral colorida com gradiente --}}
        <div class="absolute left-0 top-0 h-full w-1.5 rounded-l-2xl bg-gradient-to-b {{ $levelData['color'] }}"></div>

        <div class="py-5 pl-5 pr-4">
            {{-- Card do responsável com layout refinado --}}
            <div class="flex flex-col items-center mb-4">
                {{-- Container da foto com efeito de moldura --}}
                <div class="relative mb-3">
                    <div class="absolute inset-0 bg-emerald-100 rounded-full blur-md opacity-50 group-hover:opacity-75 transition-opacity"></div>
                    <img src="{{ $node->responsible_photo ? asset('storage/' . $node->responsible_photo) : 'https://tse4.mm.bing.net/th/id/OIP.dDKYQqVBsG1tIt2uJzEJHwHaHa?rs=1&pid=ImgDetMain&o=7&rm=3' }}" alt="{{ $node->responsible_name ?? '-' }}" class="relative size-24 rounded-full border-2 border-white shadow-md object-cover object-center group-hover:scale-105 transition-transform duration-300" />
                </div>

                {{-- Informações do responsável com design melhorado --}}
                <div class="text-center space-y-1">
                    <div class="text-sm font-semibold text-slate-800 px-2">
                        {{ $node->responsible_name ?? 'Responsável não cadastrado' }}
                    </div>
                    
                    @if($node->responsible_role)
                        <div class="text-[11px] text-emerald-600 font-medium bg-emerald-50 px-2 py-0.5 rounded-full inline-block">
                            {{ $node->responsible_role }}
                        </div>
                    @endif

                    @if($node->contact)
                        <div class="flex items-center justify-center gap-1.5 text-xs text-slate-600">
                            <i class="fa-solid fa-phone text-emerald-500 text-[10px]"></i>
                            <span>{{ $node->contact }}</span>
                        </div>
                    @endif

                    @if($node->email)
                        <div class="flex items-center justify-center gap-1.5 text-xs text-slate-600">
                            <i class="fa-solid fa-envelope text-emerald-500 text-[10px]"></i>
                            <span class="truncate max-w-[180px]">{{ $node->email }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Divisor elegante --}}
            <div class="relative my-3">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-emerald-100"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-2 text-[10px] uppercase tracking-wider text-emerald-500 font-medium">
                        {{ $node->acronym }}
                    </span>
                </div>
            </div>

            {{-- Título do nó com melhor tratamento --}}
            <div class="text-center">
                <div class="text-sm font-semibold text-slate-800 px-2 group-hover:text-emerald-700 transition-colors line-clamp-1">
                    {{ $node->title }}
                </div>
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
            class="h-10 w-0.5 bg-emerald-700 mt-2"></div>
    @endif

    @if($node->children->isNotEmpty() && $node->children->count() > 1)
        <div x-show="open"
            x-transition:enter="transition-all duration-300"
            x-transition:enter-start="opacity-0 scale-y-0"
            x-transition:enter-end="opacity-100 scale-y-100"
            x-transition:leave="transition-all duration-200"
            x-transition:leave-start="opacity-100 scale-y-100"
            x-transition:leave-end="opacity-0 scale-y-0"
            class="w-full h-0.5 bg-emerald-700"></div>
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