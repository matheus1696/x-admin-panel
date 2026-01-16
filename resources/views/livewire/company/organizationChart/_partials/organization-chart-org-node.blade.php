@php
$level = $level ?? 0;

$levels = [
    ['label' => 'Diretoria',   'color' => 'bg-blue-500'],
    ['label' => 'Gerência',    'color' => 'bg-emerald-500'],
    ['label' => 'Supervisão',  'color' => 'bg-amber-500'],
    ['label' => 'Coordenação', 'color' => 'bg-purple-500'],
    ['label' => 'Liderança',   'color' => 'bg-rose-500'],
    ['label' => 'Sênior',      'color' => 'bg-cyan-500'],
    ['label' => 'Pleno',       'color' => 'bg-orange-500'],
    ['label' => 'Júnior',      'color' => 'bg-lime-500'],
    ['label' => 'Estagiário',  'color' => 'bg-pink-500'],
];

$levelData = $levels[$level] ?? ['color' => 'bg-slate-500'];
@endphp

<div class="flex flex-col items-center">

    <div class="w-full relative rounded-xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition" title="{{ $node->acronym }} - {{ $node->name }}">
        <span class="absolute left-0 top-0 h-full w-1 {{ $levelData['color'] }} rounded-l-xl"></span>

        <div class=" p-4 pl-5 text-left">
            <div class="text-sm font-semibold text-slate-800 truncate">
                {{ $node->acronym }} - {{ $node->name }}
            </div>
        </div>
    </div>

    @if($node->children->isNotEmpty())
        <div class="h-6 w-px bg-slate-300 my-2"></div>
    @endif

    @if($node->children->isNotEmpty())
        <div class="flex gap-6 justify-center">
            @foreach($node->children as $child)
                @include('livewire.company.organizationChart._partials.organization-chart-org-node', [
                    'node' => $child,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif

</div>