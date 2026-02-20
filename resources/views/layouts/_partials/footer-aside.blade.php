<div x-show="sidebarExpanded || openAside" class="sticky bottom-0 left-0 right-0 w-80 p-4 border-t border-emerald-100/50 bg-white/95 backdrop-blur-sm">
    <div class="text-center">
        <p class="text-xs text-gray-600 flex items-center justify-center gap-1">
            <i class="fas fa-cube text-emerald-700 text-[8px]"></i>
            {{ config('app.name') }} - {{ config('app.version', '1.0.0') }}
        </p>
        <p class="text-[10px] text-gray-500 mt-1">
            © {{ date('Y') }} Todos os direitos reservados
        </p>
    </div>
</div>