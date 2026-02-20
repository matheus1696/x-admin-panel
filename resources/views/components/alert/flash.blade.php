<div
    x-data="{ show: false }"
    x-on:flash-show.window="show = true; setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed bottom-5 right-5 z-50 space-y-2"
>
    {{-- Mensagem de Sucesso --}}
    @if (session('success'))
        <div class="group relative flex items-center gap-3 min-w-[320px] max-w-md bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 px-4 py-3.5 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <!-- Gradiente de fundo -->
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 to-transparent"></div>
            
            <!-- Ícone com fundo -->
            <div class="relative flex items-center justify-center w-8 h-8 bg-emerald-100 rounded-full">
                <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
            </div>
            
            <!-- Mensagem -->
            <span class="relative flex-1 text-sm font-medium">{{ session('success') }}</span>
            
            <!-- Botão fechar -->
            <button @click="show = false" class="relative w-6 h-6 flex items-center justify-center text-emerald-400 hover:text-emerald-600 hover:bg-emerald-100 rounded-full transition-all duration-200">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
            
            <!-- Barra de progresso -->
            <div class="absolute bottom-0 left-0 h-0.5 bg-emerald-500 animate-shrink"></div>
        </div>
    @endif

    {{-- Mensagem de Erro --}}
    @if (session('error'))
        <div class="group relative flex items-center gap-3 min-w-[320px] max-w-md bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3.5 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 to-transparent"></div>
            <div class="relative flex items-center justify-center w-8 h-8 bg-red-100 rounded-full">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-base"></i>
            </div>
            <span class="relative flex-1 text-sm font-medium">{{ session('error') }}</span>
            <button @click="show = false" class="relative w-6 h-6 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-100 rounded-full transition-all duration-200">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
            <div class="absolute bottom-0 left-0 h-0.5 bg-red-500 animate-shrink"></div>
        </div>
    @endif

    {{-- Mensagem de Alerta --}}
    @if (session('warning'))
        <div class="group relative flex items-center gap-3 min-w-[320px] max-w-md bg-yellow-50 border-l-4 border-amber-500 text-amber-800 px-4 py-3.5 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/5 to-transparent"></div>
            <div class="relative flex items-center justify-center w-8 h-8 bg-amber-100 rounded-full">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base"></i>
            </div>
            <span class="relative flex-1 text-sm font-medium">{{ session('warning') }}</span>
            <button @click="show = false" class="relative w-6 h-6 flex items-center justify-center text-amber-400 hover:text-amber-600 hover:bg-amber-100 rounded-full transition-all duration-200">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
            <div class="absolute bottom-0 left-0 h-0.5 bg-amber-500 animate-shrink"></div>
        </div>
    @endif

    {{-- Mensagem de Informação --}}
    @if (session('info'))
        <div class="group relative flex items-center gap-3 min-w-[320px] max-w-md bg-sky-50 border-l-4 border-sky-500 text-sky-800 px-4 py-3.5 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-sky-500/5 to-transparent"></div>
            <div class="relative flex items-center justify-center w-8 h-8 bg-sky-100 rounded-full">
                <i class="fa-solid fa-circle-info text-sky-500 text-base"></i>
            </div>
            <span class="relative flex-1 text-sm font-medium">{{ session('info') }}</span>
            <button @click="show = false" class="relative w-6 h-6 flex items-center justify-center text-sky-400 hover:text-sky-600 hover:bg-sky-100 rounded-full transition-all duration-200">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
            <div class="absolute bottom-0 left-0 h-0.5 bg-sky-500 animate-shrink"></div>
        </div>
    @endif
</div>

@push('styles')
<style>
    @keyframes shrink {
        from { width: 100%; }
        to { width: 0%; }
    }
    .animate-shrink {
        animation: shrink 4s linear forwards;
    }
</style>
@endpush