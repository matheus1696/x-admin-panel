<x-app-layout>
    <div class="space-y-6 py-4">
        <section class="overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-sm">
            <div class="border-b border-emerald-100 bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 px-6 py-5 text-white">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-white/80">Central</p>
                        <h1 class="mt-2 text-xl font-bold">Notificacoes</h1>
                        <p class="mt-1 text-sm text-white/80">Acompanhe avisos internos e eventos enviados por e-mail quando aplicavel.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                            {{ $notificationSummary['unread_count'] }} nao lidas
                        </span>

                        @if ($notificationSummary['unread_count'] > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <x-button
                                    type="submit"
                                    text="Marcar todas"
                                    icon="fa-solid fa-check-double"
                                    variant="gray_outline"
                                />
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    @forelse ($notifications as $notification)
                        @php
                            $levelTheme = match ($notification['level']) {
                                'success' => 'border-emerald-200 bg-emerald-50/60 text-emerald-700',
                                'warning' => 'border-amber-200 bg-amber-50/60 text-amber-700',
                                'danger' => 'border-rose-200 bg-rose-50/60 text-rose-700',
                                default => 'border-sky-200 bg-sky-50/60 text-sky-700',
                            };
                        @endphp

                        <article class="rounded-3xl border {{ $notification['is_read'] ? 'border-gray-200 bg-white' : 'border-emerald-200 bg-emerald-50/30' }} p-5 shadow-sm">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $levelTheme }}">
                                            <i class="{{ $notification['icon'] }}"></i>
                                            {{ $notification['is_read'] ? 'Lida' : 'Nova' }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $notification['created_at']?->format('d/m/Y H:i') }}
                                        </span>
                                    </div>

                                    <h2 class="mt-3 text-sm font-semibold text-gray-900">{{ $notification['title'] }}</h2>
                                    <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $notification['message'] }}</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    @if (! $notification['is_read'])
                                        <form method="POST" action="{{ route('notifications.read', $notification['id']) }}">
                                            @csrf
                                            <x-button
                                                type="submit"
                                                text="Marcar como lida"
                                                icon="fa-solid fa-check"
                                                variant="gray_outline"
                                            />
                                        </form>
                                    @endif

                                    @if ($notification['url'])
                                        <x-button
                                            href="{{ $notification['url'] }}"
                                            text="Abrir"
                                            icon="fa-solid fa-arrow-up-right-from-square"
                                            variant="green_text"
                                        />
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-gray-200 bg-gray-50 px-6 py-16 text-center">
                            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-white text-gray-400 shadow-sm">
                                <i class="fa-regular fa-bell-slash text-2xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Nenhuma notificacao registrada.</p>
                            <p class="mt-2 text-xs text-gray-500">Quando houver avisos internos ou externos, eles aparecerao aqui.</p>
                        </div>
                    @endforelse
                </div>

                @if ($notifications->hasPages())
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
