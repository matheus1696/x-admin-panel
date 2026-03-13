<div wire:poll.30s class="relative" x-data="{ openNotifications: false }" @click.outside="openNotifications = false">
    <button @click="openNotifications = !openNotifications" class="relative p-2 rounded-lg transition-all duration-200 hover:bg-emerald-800 active:scale-95">
        <i class="fa-regular fa-bell text-white text-lg"></i>
        @if (($notificationSummary['unread_count'] ?? 0) > 0)
            <span class="absolute -top-1 -right-1 min-w-[18px] rounded-full border border-white bg-red-500 px-1 py-0.5 text-[10px] font-bold leading-none text-white">
                {{ min((int) $notificationSummary['unread_count'], 99) }}
            </span>
        @endif
    </button>

    <div x-show="openNotifications"
         x-cloak
         x-transition
         class="absolute right-0 z-30 mt-3 w-[360px] max-w-[calc(100vw-2rem)] overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-2xl">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-4 py-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-gray-400">Central</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">Notificacoes</p>
                </div>
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                    {{ $notificationSummary['unread_count'] ?? 0 }} novas
                </span>
            </div>

            @if (($notificationSummary['unread_count'] ?? 0) > 0)
                <div class="mt-3 flex justify-end">
                    <x-button
                        type="button"
                        text="Ler todas"
                        icon="fa-solid fa-check-double"
                        variant="gray_text"
                        class="!text-xs"
                        wire:click="markAllAsRead"
                    />
                </div>
            @endif
        </div>

        <div class="max-h-[420px] overflow-y-auto p-3">
            @forelse (($notificationSummary['recent'] ?? collect()) as $notification)
                <a href="{{ $notification['url'] ?? route('notifications.index') }}"
                   class="mb-2 block rounded-2xl border {{ $notification['is_read'] ? 'border-gray-200 bg-white' : 'border-emerald-200 bg-emerald-50/40' }} px-4 py-3 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl {{ $notification['is_read'] ? 'bg-gray-100 text-gray-500' : 'bg-emerald-100 text-emerald-700' }}">
                            <i class="{{ $notification['icon'] }}"></i>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <p class="truncate text-xs font-semibold text-gray-900">{{ $notification['title'] }}</p>
                                @if (! $notification['is_read'])
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.2em] text-emerald-700">Nova</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs leading-relaxed text-gray-600">{{ $notification['message'] }}</p>
                            <p class="mt-2 text-[11px] text-gray-400">{{ $notification['created_at']?->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center rounded-2xl bg-gray-50 px-4 py-8 text-center">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-gray-400 shadow-sm">
                        <i class="fa-regular fa-bell-slash text-lg"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700">Nenhuma notificacao</p>
                    <p class="mt-1 text-xs text-gray-500">Quando houver novas mensagens, elas aparecerao aqui.</p>
                </div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 bg-gray-50/70 px-4 py-3">
            <a href="{{ route('notifications.index') }}" class="flex items-center justify-center gap-2 text-xs font-semibold text-emerald-700 transition-colors hover:text-emerald-800">
                <i class="fa-solid fa-list"></i>
                Ver todas as notificacoes
            </a>
        </div>
    </div>
</div>
