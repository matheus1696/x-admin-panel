@props([
    'show' => false,
    'maxWidth' => 'max-w-5xl',
    'closeable' => true,
])

@if($show)
    <div
        x-data="{ 
            closing: false,
            handleClose() {
                if ({{ $closeable ? 'true' : 'false' }} && !this.closing) {
                    if (typeof $wire !== 'undefined') {
                        this.closing = true;
                        $wire.closeModal();
                    } else {
                        this.closing = true;
                        @this.call('closeModal');
                    }
                }
            }
        }"
        x-init="
            $watch('show', value => {
                if (!value) {
                    document.body.style.overflow = '';
                    closing = false;
                } else {
                    document.body.style.overflow = 'hidden';
                }
            });
        "
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[999] flex items-center justify-center p-4 md:p-6"
    >
        <!-- Overlay com blur e fundo escuro -->
        <div 
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
            @click="handleClose()"
        ></div>

        <!-- Container do Modal -->
        <div
            role="dialog"
            aria-modal="true"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="relative w-full {{ $maxWidth }} bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200"
            @click.outside="handleClose()"
            @keydown.escape.window="handleClose()"
        >
            <!-- Barra superior decorativa (sutil) -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>

            {{-- Header --}}
            @isset($header)
                <div class="relative flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5 bg-emerald-600 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $header }}
                        </h3>
                    </div>

                    @if($closeable)
                        <!-- Botão Fechar -->
                        <button 
                            wire:click="closeModal" 
                            class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                            aria-label="Fechar modal"
                            x-on:click="closing = true"
                        >
                            <i class="fas fa-times text-base"></i>
                        </button>
                    @endif
                </div>
            @endisset

            {{-- Body --}}
            <div class="px-6 py-5 min-h-60 max-h-[calc(100vh-200px)] overflow-y-auto custom-scrollbar">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @isset($footer)
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    <div class="flex items-center justify-end gap-3">
                        {{ $footer }}
                    </div>
                </div>
            @endisset
        </div>
    </div>
@endif