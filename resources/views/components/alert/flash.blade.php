<div
    x-data="{ show: false }"
    x-on:flash-show.window="show = true; setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition
    class="fixed bottom-5 right-5 z-50"
>
    {{-- Mensagem de Sucesso --}}
    @if (session('success'))
        <div class="flex items-center gap-3 bg-green-100 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg shadow-md">
            <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-green-700 hover:text-green-900">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Mensagem de Erro --}}
    @if (session('error'))
        <div class="flex items-center gap-3 bg-red-100 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg shadow-md">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
            <button @click="show = false" class="ml-auto text-red-700 hover:text-red-900">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Mensagem de Alerta --}}
    @if (session('warning'))
        <div class="flex items-center gap-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 px-4 py-3 rounded-lg shadow-md">
            <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-xl"></i>
            <span class="text-sm font-medium">{{ session('warning') }}</span>
            <button @click="show = false" class="ml-auto text-yellow-700 hover:text-yellow-900">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Mensagem de Informação --}}
    @if (session('info'))
        <div class="flex items-center gap-3 bg-sky-100 border-l-4 border-sky-500 text-sky-800 px-4 py-3 rounded-lg shadow-md">
            <i class="fa-solid fa-triangle-exclamation text-sky-600 text-xl"></i>
            <span class="text-sm font-medium">{{ session('info') }}</span>
            <button @click="show = false" class="ml-auto text-sky-700 hover:text-sky-900">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
</div>
