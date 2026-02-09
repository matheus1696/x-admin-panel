@props([
    'show' => false,
    'maxWidth' => 'max-w-5xl',
])

@if($show)
    <div
        x-data="{ 
            closing: false,
            handleClose() {
                if (!this.closing && typeof $wire !== 'undefined') {
                    this.closing = true;
                    $wire.closeModal();
                }
            }
        }"
        x-init="
            // Bloquear scroll do body
            document.body.style.overflow = 'hidden';
            
            // Fechar ao pressionar ESC
            $watch('show', value => {
                if (!value) {
                    document.body.style.overflow = '';
                }
            });
        "
        x-transition:enter="ease-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-6"
        :class="{ 'items-start md:items-center': true }"
        style="background: linear-gradient(135deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.92) 100%); backdrop-filter: blur(8px);"
    >
        <!-- Overlay com gradiente sutil -->
        <div 
            class="absolute inset-0 bg-gradient-to-br from-gray-900/95 via-gray-900/90 to-black/95"
            @click="handleClose()"
        ></div>

        <div
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title"
            aria-describedby="modal-description"
            x-transition:enter="ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4"
            class="relative w-full {{ $maxWidth }} bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200/50"
            style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);"
            @click.outside="handleClose()"
            @keydown.escape.window="handleClose()"
        >

            {{-- Header premium --}}
            @isset($header)
                <div class="relative flex items-center justify-between px-8 py-3 border-b border-gray-200/70 bg-gradient-to-r from-gray-50/95 to-white/95 backdrop-blur-sm">
                    <div class="flex-1 min-w-0">
                        {{ $header }}
                    </div>

                    <button 
                        wire:click="closeModal" 
                        class="group relative ml-4 flex-shrink-0 w-10 h-10 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100/80 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:ring-offset-2 hover:rotate-90"
                        aria-label="Fechar modal"
                        x-on:click="closing = true"
                    >
                        <span class="absolute inset-0 rounded-full bg-gradient-to-r from-gray-200/0 to-gray-200/0 group-hover:from-gray-200/50 group-hover:to-gray-200/30 transition-all duration-300"></span>
                        <span class="relative text-xl font-light group-hover:scale-110 transition-transform duration-200">✕</span>
                    </button>
                </div>
            @endisset

            {{-- Body com scroll elegante --}}
            <div 
                class="px-8 pt-6 pb-8 min-h-[calc(100vh-500px)] max-h-[calc(100vh-180px)] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hover:scrollbar-thumb-gray-400"
                style="scrollbar-width: thin;"
            >
                <div class="space-y-6">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer premium --}}
            @isset($footer)
                <div class="relative px-8 py-5 border-t border-gray-200/70 bg-gradient-to-r from-white/95 to-gray-50/95 backdrop-blur-sm">
                    <div class="absolute inset-0 bg-gradient-to-t from-white/20 via-transparent to-transparent pointer-events-none"></div>
                    <div class="relative">
                        {{ $footer }}
                    </div>
                </div>
            @endisset

            <!-- Elementos decorativos -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 via-emerald-500 to-green-500 opacity-80"></div>
            <div class="absolute top-3 left-3 right-3 h-px bg-gradient-to-r from-transparent via-gray-300/30 to-transparent"></div>
            
            <!-- Cantos arredondados com gradiente -->
            <div class="absolute top-0 left-0 w-4 h-4 rounded-tl-2xl border-t border-l border-gray-300/30"></div>
            <div class="absolute top-0 right-0 w-4 h-4 rounded-tr-2xl border-t border-r border-gray-300/30"></div>
            <div class="absolute bottom-0 left-0 w-4 h-4 rounded-bl-2xl border-b border-l border-gray-300/30"></div>
            <div class="absolute bottom-0 right-0 w-4 h-4 rounded-br-2xl border-b border-r border-gray-300/30"></div>
        </div>
    </div>
@endif