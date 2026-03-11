@php
    $initialToasts = collect(['success', 'error', 'warning', 'info'])
        ->filter(fn (string $type): bool => session()->has($type))
        ->map(fn (string $type): array => [
            'type' => $type,
            'message' => (string) session($type),
        ])
        ->values()
        ->all();
@endphp

<div
    x-data="{
        toasts: [],
        seed: 0,
        boot(initial) {
            initial.forEach((toast) => this.enqueue(toast.type, toast.message));
        },
        enqueue(type, message) {
            const id = ++this.seed;
            this.toasts.push({ id, type, message });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter((toast) => toast.id !== id);
        },
        classes(type) {
            if (type === 'success') return 'bg-emerald-50 border-emerald-500 text-emerald-800';
            if (type === 'error') return 'bg-red-50 border-red-500 text-red-800';
            if (type === 'warning') return 'bg-yellow-50 border-amber-500 text-amber-800';
            return 'bg-sky-50 border-sky-500 text-sky-800';
        },
        icon(type) {
            if (type === 'success') return 'fa-solid fa-circle-check text-emerald-500';
            if (type === 'error') return 'fa-solid fa-circle-exclamation text-red-500';
            if (type === 'warning') return 'fa-solid fa-triangle-exclamation text-amber-500';
            return 'fa-solid fa-circle-info text-sky-500';
        }
    }"
    x-init='boot(@json($initialToasts))'
    x-on:app-flash.window="enqueue($event.detail.type, $event.detail.message)"
    class="fixed bottom-5 right-5 z-50 space-y-2"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true" x-transition class="group relative flex items-center gap-3 min-w-[320px] max-w-md border-l-4 px-4 py-3.5 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" :class="classes(toast.type)">
            <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-white/70">
                <i class="text-base" :class="icon(toast.type)"></i>
            </div>

            <span class="relative flex-1 text-sm font-medium" x-text="toast.message"></span>

            <button type="button" x-on:click="remove(toast.id)" class="relative w-6 h-6 flex items-center justify-center rounded-full transition-all duration-200 hover:bg-white/70">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>

            <div class="absolute bottom-0 left-0 h-0.5 bg-current animate-shrink"></div>
        </div>
    </template>
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
