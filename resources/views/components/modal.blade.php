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
        class="fixed inset-0 z-[999] flex items-center justify-center p-4 md:p-6"
    >
        <!-- Overlay com blur e gradiente premium -->
        <div 
            class="absolute inset-0 bg-gradient-to-br from-gray-900/70 via-gray-900/60 to-black/70 backdrop-blur-md"
            @click="handleClose()"
        ></div>

        <!-- Container do Modal -->
        <div
            role="dialog"
            aria-modal="true"
            x-transition:enter="ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4"
            class="relative w-full {{ $maxWidth }} bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border border-white/20"
            @click.outside="handleClose()"
            @keydown.escape.window="handleClose()"
        >
            <!-- Barra superior decorativa -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-green-500 to-emerald-500 opacity-90"></div>
            
            <!-- Brilho interno -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-green-500/5 pointer-events-none"></div>

            {{-- Header Premium --}}
            @isset($header)
                <div class="relative flex items-center justify-between px-8 py-5 border-b border-gray-200/70 bg-gradient-to-r from-white via-white to-gray-50/50">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <!-- Indicador de título -->
                            <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-green-500 rounded-full"></div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ $header }}
                            </div>
                        </div>
                    </div>

                    <!-- Botão Fechar Premium -->
                    <button 
                        wire:click="closeModal" 
                        class="group relative ml-4 flex-shrink-0 w-10 h-10 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100/80 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:ring-offset-2"
                        aria-label="Fechar modal"
                        x-on:click="closing = true"
                    >
                        <div class="absolute inset-0 rounded-full bg-gradient-to-r from-gray-200/0 to-gray-200/0 group-hover:from-gray-200/50 group-hover:to-gray-200/30 transition-all duration-500 scale-0 group-hover:scale-100"></div>
                        <i class="fas fa-times text-lg relative z-10 transition-all duration-500 group-hover:rotate-180"></i>
                    </button>
                </div>
            @endisset

            {{-- Body Premium com scroll refinado --}}
            <div 
                class="px-8 py-6 max-h-[calc(100vh-200px)] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hover:scrollbar-thumb-gray-400"
                style="scrollbar-width: thin; scrollbar-color: #9CA3AF #F3F4F6;"
            >
                <div class="space-y-6">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer Premium --}}
            @isset($footer)
                <div class="relative px-8 py-5 border-t border-gray-200/70 bg-gradient-to-r from-white to-gray-50/80">
                    <!-- Efeito de brilho superior -->
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent"></div>
                    
                    <!-- Conteúdo do footer -->
                    <div class="relative flex items-center justify-end gap-3">
                        {{ $footer }}
                    </div>
                </div>
            @endisset

            <!-- Elementos decorativos premium -->
            <div class="absolute top-3 left-3 right-3 h-px bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
            
            <!-- Cantos decorativos com gradiente -->
            <div class="absolute top-0 left-0 w-6 h-6 rounded-tl-2xl border-t-2 border-l-2 border-emerald-500/20"></div>
            <div class="absolute top-0 right-0 w-6 h-6 rounded-tr-2xl border-t-2 border-r-2 border-emerald-500/20"></div>
            <div class="absolute bottom-0 left-0 w-6 h-6 rounded-bl-2xl border-b-2 border-l-2 border-emerald-500/20"></div>
            <div class="absolute bottom-0 right-0 w-6 h-6 rounded-br-2xl border-b-2 border-r-2 border-emerald-500/20"></div>
        </div>
    </div>
@endif