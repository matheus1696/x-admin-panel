<div class="space-y-4">

    <x-page.header title="Registrar Ponto" subtitle="Controle de Ponto" icon="fa-solid fa-clock" />

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        <aside class="space-y-4 xl:col-span-4">
            <section class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-cyan-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Resumo do registro</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-500">Usuario</p>
                        <p class="font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Data / Hora</p>
                        <p class="font-semibold text-slate-900">{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Ultimos registros</p>
                        <div class="mt-2 space-y-2">
                            @forelse ($recentEntries as $entry)
                                <div class="rounded-2xl border border-white/70 bg-white/80 px-3 py-2 text-xs shadow-sm">
                                    <p class="font-semibold text-slate-800">{{ $entry->occurred_at?->format('d/m/Y H:i') }}</p>
                                    <p class="text-slate-500">{{ $entry->status }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">Nenhum registro encontrado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
        </aside>

        <main class="xl:col-span-8">
            <form wire:submit.prevent="register" x-data="timeClockRegister($wire)" x-init="init()" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="rounded-2xl border border-cyan-100 bg-cyan-50/70 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Registro guiado</p>
                            <p class="mt-1 text-sm text-slate-600">A foto pode ser feita direto pela camera. A localizacao pode ser ativada pelo navegador quando o usuario permitir.</p>
                        </div>
                        <div class="rounded-2xl border px-3 py-2 text-xs font-medium"
                             :class="locationStatusClass">
                            <span x-text="locationStatusLabel"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="space-y-4 lg:col-span-2">
                        <div>
                            <x-form.label value="Local de trabalho" />
                            <x-form.select-livewire
                                wire:model.live="locationId"
                                name="locationId"
                                :options="$locations->map(fn ($location) => ['value' => $location->id, 'label' => $location->establishment?->title ?? $location->name])->prepend(['value' => '', 'label' => 'Selecionar depois'])->values()->all()"
                            />
                            <p class="mt-1 text-xs text-slate-500">Mostramos os estabelecimentos vinculados ao controle de ponto. Se o local ainda nao estiver definido, o registro continua disponivel conforme a configuracao do modulo.</p>
                            <x-form.error for="locationId" />
                        </div>

                        <template x-if="selectedLocationSummary">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900" x-text="selectedLocationSummary.name"></p>
                                <p class="mt-1" x-text="selectedLocationSummary.address"></p>
                                <p class="mt-1 text-xs text-slate-500" x-text="selectedLocationSummary.radius"></p>
                            </div>
                        </template>
                    </div>

                    <div class="md:col-span-2">
                        <x-form.label value="Foto" />
                        <input x-ref="photoInput" type="file" wire:model="photo" accept="image/*" capture="environment" class="hidden" @change="previewFile($event)" />

                        <div class="mt-2 space-y-3">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <x-button type="button" text="Abrir camera" icon="fa-solid fa-camera" x-on:click="openCamera()" />
                                <x-button type="button" text="Inverter camera" icon="fa-solid fa-rotate" variant="blue_outline" x-on:click="switchCamera()" x-bind:disabled="!canSwitchCamera" />
                            </div>

                            <div x-show="cameraOpen" class="space-y-3">
                                <video x-ref="video" autoplay playsinline class="w-full rounded-2xl border border-slate-200 bg-slate-950"></video>
                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <x-button type="button" text="Capturar foto" icon="fa-solid fa-check" variant="blue_outline" x-on:click="capturePhoto()" />
                                    <x-button type="button" text="Fechar camera" icon="fa-solid fa-xmark" variant="red_outline" x-on:click="stopCamera()" />
                                </div>
                            </div>
                            <canvas x-ref="canvas" class="hidden"></canvas>

                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Preview da foto" class="w-full rounded-2xl border border-gray-200 object-cover max-h-80" />
                            </template>
                        </div>
                        <x-form.error for="photo" />
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 lg:col-span-2">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Localizacao do dispositivo</p>
                                <p class="mt-1 text-sm text-slate-600">Em alguns navegadores a permissao so aparece apos clicar no botao abaixo. Se nada acontecer, verifique se a pagina esta em contexto seguro e se a localizacao nao foi bloqueada antes.</p>
                            </div>
                            <x-button type="button" text="Ativar localizacao" icon="fa-solid fa-location-dot" variant="blue_outline" x-on:click="captureLocation(true)" />
                        </div>

                        <template x-if="locationHelpText">
                            <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" x-text="locationHelpText"></div>
                        </template>

                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl bg-white px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Latitude</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900" x-text="formattedLatitude"></p>
                            </div>
                            <div class="rounded-2xl bg-white px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Longitude</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900" x-text="formattedLongitude"></p>
                            </div>
                            <div class="rounded-2xl bg-white px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Precisao</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900" x-text="formattedAccuracy"></p>
                            </div>
                        </div>

                        <x-form.error for="latitude" />
                        <x-form.error for="longitude" />
                        <x-form.error for="accuracy" />
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <x-button type="button" text="Ativar localizacao" icon="fa-solid fa-location-crosshairs" variant="blue_outline" x-on:click="captureLocation(true)" />
                    <x-button type="submit" text="Registrar ponto" icon="fa-solid fa-check" x-bind:disabled="submitting" />
                </div>
            </form>
        </main>
    </div>
</div>

@script
<script>
    Alpine.data('timeClockRegister', ($wire) => ({
        cameraOpen: false,
        previewUrl: null,
        stream: null,
        submitting: false,
        cameraFacingMode: 'environment',
        canSwitchCamera: false,
        locationStatusLabel: 'Aguardando captura da localizacao',
        locationStatusClass: 'border-slate-200 bg-white text-slate-600',
        locationHelpText: '',
        locations: @js($locations->map(fn ($location) => [
            'id' => $location->id,
            'name' => $location->establishment?->title ?? $location->name,
            'address' => $location->establishment
                ? trim(collect([$location->establishment->address, $location->establishment->number, $location->establishment->district])->filter()->implode(', '))
                : 'Local configurado para registro de ponto',
            'radius' => 'Raio permitido: '.$location->radius_meters.' m',
        ])->values()->all()),
        get formattedLatitude() {
            return this.formatCoordinate($wire.latitude);
        },
        get formattedLongitude() {
            return this.formatCoordinate($wire.longitude);
        },
        get formattedAccuracy() {
            return $wire.accuracy ? `${Number($wire.accuracy).toFixed(2)} m` : 'Nao capturada';
        },
        get selectedLocationSummary() {
            return this.locations.find((location) => Number(location.id) === Number($wire.locationId)) ?? null;
        },
        async init() {
            this.canSwitchCamera = this.isLikelyMobile();
            await this.inspectLocationPermission();
        },
        async openCamera() {
            if (!navigator.mediaDevices?.getUserMedia) {
                this.$refs.photoInput.click();
                return;
            }

            try {
                this.stopCamera();
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: this.cameraFacingMode }, audio: false });
                this.$refs.video.srcObject = this.stream;
                this.cameraOpen = true;
            } catch {
                this.$refs.photoInput.click();
            }
        },
        async switchCamera() {
            this.cameraFacingMode = this.cameraFacingMode === 'environment' ? 'user' : 'environment';

            if (this.cameraOpen) {
                await this.openCamera();
            }
        },
        capturePhoto() {
            if (!this.cameraOpen || !this.stream) {
                this.$refs.photoInput.click();
                return;
            }

            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob((blob) => {
                if (!blob) return;

                const file = new File([blob], `time-clock-${Date.now()}.jpg`, { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                this.$refs.photoInput.files = dataTransfer.files;
                this.previewUrl = URL.createObjectURL(file);
                this.$refs.photoInput.dispatchEvent(new Event('change', { bubbles: true }));
                this.stopCamera();
            }, 'image/jpeg', 0.92);
        },
        previewFile(event) {
            const [file] = event.target.files || [];
            if (!file) return;
            this.previewUrl = URL.createObjectURL(file);
        },
        async inspectLocationPermission() {
            if (!window.isSecureContext) {
                this.setLocationStatus('Localizacao indisponivel fora de contexto seguro', 'warning');
                this.locationHelpText = 'Use HTTPS ou acesse por localhost. Em HTTP comum, o navegador pode ocultar a permissao de GPS.';
                return;
            }

            if (!navigator.geolocation) {
                this.setLocationStatus('Geolocalizacao indisponivel neste dispositivo', 'warning');
                this.locationHelpText = 'Este navegador ou dispositivo nao oferece suporte a geolocalizacao.';
                return;
            }

            if (!navigator.permissions?.query) {
                this.setLocationStatus('Clique em ativar localizacao para solicitar permissao', 'loading');
                this.locationHelpText = 'Alguns navegadores so mostram o pedido de permissao apos interacao direta do usuario.';
                return;
            }

            try {
                const permission = await navigator.permissions.query({ name: 'geolocation' });
                this.syncPermissionState(permission.state);
                permission.onchange = () => this.syncPermissionState(permission.state);

                if (permission.state === 'granted') {
                    this.captureLocation(false);
                }
            } catch {
                this.setLocationStatus('Clique em ativar localizacao para solicitar permissao', 'loading');
                this.locationHelpText = 'Nao foi possivel consultar o estado da permissao. Tente ativar manualmente.';
            }
        },
        syncPermissionState(state) {
            if (state === 'granted') {
                this.setLocationStatus('Permissao de localizacao liberada', 'success');
                this.locationHelpText = '';
                return;
            }

            if (state === 'denied') {
                this.setLocationStatus('Localizacao bloqueada no navegador', 'warning');
                this.locationHelpText = 'Abra as permissoes do navegador para esta pagina e permita o acesso a localizacao.';
                return;
            }

            this.setLocationStatus('Clique em ativar localizacao para solicitar permissao', 'loading');
            this.locationHelpText = 'O navegador ainda nao exibiu a permissao. Use o botao para iniciar a solicitacao.';
        },
        captureLocation(fromUserAction = false) {
            if (!navigator.geolocation) {
                this.setLocationStatus('Geolocalizacao indisponivel neste dispositivo', 'warning');
                this.locationHelpText = 'Este navegador ou dispositivo nao oferece suporte a geolocalizacao.';
                return;
            }

            if (!window.isSecureContext) {
                this.setLocationStatus('Localizacao indisponivel fora de contexto seguro', 'warning');
                this.locationHelpText = 'Use HTTPS ou localhost para que o navegador permita GPS.';
                return;
            }

            this.setLocationStatus('Capturando localizacao...', 'loading');
            this.locationHelpText = fromUserAction ? '' : this.locationHelpText;

            navigator.geolocation.getCurrentPosition((position) => {
                $wire.set('latitude', Number(position.coords.latitude.toFixed(7)));
                $wire.set('longitude', Number(position.coords.longitude.toFixed(7)));
                $wire.set('accuracy', Number(position.coords.accuracy.toFixed(2)));
                this.setLocationStatus('Localizacao capturada com sucesso', 'success');
                this.locationHelpText = '';
            }, () => {
                this.setLocationStatus('Nao foi possivel obter a localizacao. Verifique a permissao do navegador.', 'warning');
                this.locationHelpText = 'Se a permissao nao apareceu, confira se o navegador bloqueou a localizacao, se o sistema liberou GPS para o navegador e se a pagina esta em HTTPS.';
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0,
            });
        },
        formatCoordinate(value) {
            return value !== null && value !== undefined
                ? Number(value).toFixed(7)
                : 'Nao capturada';
        },
        setLocationStatus(message, variant) {
            this.locationStatusLabel = message;
            this.locationStatusClass = {
                loading: 'border-cyan-200 bg-cyan-50 text-cyan-700',
                success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
                warning: 'border-amber-200 bg-amber-50 text-amber-700',
            }[variant] ?? 'border-slate-200 bg-white text-slate-600';
        },
        stopCamera() {
            if (!this.stream) return;
            this.stream.getTracks().forEach((track) => track.stop());
            this.stream = null;
            this.cameraOpen = false;
        },
        isLikelyMobile() {
            return /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);
        },
    }));
</script>
@endscript
