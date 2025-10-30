@props(['value' => 'value', 'color' => 'green'])

<div x-data="{ 
    loading: false,
    submit() {
        if (!this.loading) {
            this.loading = true;
            $el.querySelector('button[type=button]').form.submit();
        }
    }
}" x-init="
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.altKey) {
            e.preventDefault();
            submit();
        }
    });
"
    class="w-full flex justify-center items-center space-x-2"
>
    <button 
        x-bind:disabled="loading"
        type="button"
        x-on:click="loading = true; $el.form.submit()"
        class ='w-full bg-{{$color}}-700 hover:bg-{{$color}}-600 text-white px-2.5 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl transform  ease-in-out focus:outline-none focus:ring-4 focus:ring-{{$color}}-500/30 border-0 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none text-xs'
    >
        <!-- Conteúdo normal -->
        <div x-show="!loading" class="flex items-center justify-center gap-2 w-full">
            <span>{{ $value }}</span>
        </div>
        
        <!-- Loading state -->
        <div x-show="loading" class="flex items-center justify-center gap-2 w-full">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-medium">{{ __('Aguarde...') }}</span>
        </div>
    </button>
</div>