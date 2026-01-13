@props([
    'show' => false,
    'maxWidth' => 'max-w-xl',
    'close' => null,
])

@if($show)
    <div
        x-data
        x-transition:enter="ease-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed -top-10 inset-0 z-50 flex items-center justify-center bg-black/75 p-5"
    >

        {{-- Modal --}}
        <div
            x-transition:enter="ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="bg-white w-full {{ $maxWidth }} rounded-lg shadow-lg"
            @click.outside="{{ $close }}"
            @keydown.escape.window="{{ $close }}"
        >

            {{-- Header --}}
            @isset($header)
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    {{ $header }}
                </div>
            @endisset

            {{-- Body --}}
            <div class="p-6">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @isset($footer)
                <div class="px-6 py-4 border-t">
                    {{ $footer }}
                </div>
            @endisset

        </div>
    </div>
@endif
