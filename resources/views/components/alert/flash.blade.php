<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-init="setTimeout(() => show = false, 8000)" 
    x-transition
    class="fixed bottom-5 right-5 z-50 space-y-3"
>
    {{-- Mensagem de sucesso --}}
    @if (session('success'))
        <div class="flex items-center gap-3 bg-green-100 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg shadow-md">
            <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-green-700 hover:text-green-900">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Mensagem de erro --}}
    @if (session('error'))
        <div class="flex items-center gap-3 bg-red-100 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg shadow-md">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
            <button @click="show = false" class="ml-auto text-red-700 hover:text-red-900">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
</div>
