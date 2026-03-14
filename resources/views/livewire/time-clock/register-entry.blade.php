<div class="space-y-4">
    <x-page.header title="Registrar Ponto" subtitle="Controle de Ponto" icon="fa-solid fa-clock" />

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-4 py-4 sm:px-5">
            <h2 class="text-lg font-semibold text-slate-900">Meus registros do mes atual</h2>
            <p class="mt-1 text-sm text-slate-500">{{ ucfirst($monthLabel) }}</p>
        </div>

        <div class="space-y-3 p-4 sm:p-5">
            @foreach ($monthlyEntries as $row)
                <div class="rounded-2xl border px-4 py-4 sm:px-5 {{ $row['date']->isToday() ? 'border-cyan-200 bg-cyan-50/50' : 'border-slate-200 bg-slate-50/40' }}">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-base font-semibold text-slate-900">{{ $row['day_label'] }}</p>
                                <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-medium text-slate-500">{{ $row['week_day'] }}</span>
                                @if ($row['date']->isToday())
                                    <span class="rounded-full bg-cyan-600 px-2.5 py-1 text-[11px] font-medium text-white">Hoje</span>
                                @endif
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Manha</p>
                                    <div class="mt-2 flex items-center justify-between gap-4 text-sm">
                                        <div>
                                            <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Entrada</p>
                                            <p class="mt-1 font-semibold text-slate-900">{{ $row['morning_entry'] ?? '-' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Saida</p>
                                            <p class="mt-1 font-semibold text-slate-900">{{ $row['morning_exit'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Tarde</p>
                                    <div class="mt-2 flex items-center justify-between gap-4 text-sm">
                                        <div>
                                            <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Entrada</p>
                                            <p class="mt-1 font-semibold text-slate-900">{{ $row['afternoon_entry'] ?? '-' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Saida</p>
                                            <p class="mt-1 font-semibold text-slate-900">{{ $row['afternoon_exit'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Tempo de atividade</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $row['activity_duration'] ?? '-' }}</p>
                                </div>

                                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Observacao</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $row['observation'] ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col gap-2 lg:w-48 lg:pl-4">
                            @if ($row['date']->isToday())
                                <x-button type="button" text="Registrar ponto" icon="fa-solid fa-clock" size="sm" wire:click="openRegisterModal" fullWidth="true" />
                                <x-button type="button" text="Solicitar abono" icon="fa-solid fa-file-circle-plus" size="sm" variant="secondary" wire:click="requestAllowance" fullWidth="true" />
                            @else
                                <div class="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-3 text-center text-sm text-slate-400">
                                    Sem acoes
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <x-modal :show="$showRegisterModal" size="lg" title="Registrar ponto" description="Registre o ponto do dia atual." closeMethod="closeRegisterModal" wire:key="time-clock-register-modal">
        <div x-data="timeClockRegister($wire)" x-init="init()" class="space-y-4">
            <div>
                <x-form.label value="Foto" />
                <input x-ref="photoInput" type="file" wire:model="photo" accept="image/*" capture="user" class="hidden" @change="previewFile($event)" />

                <div class="mt-2 flex flex-wrap gap-3">
                    <x-button type="button" text="Abrir camera" icon="fa-solid fa-camera" x-on:click="openCamera()" />
                    <x-button type="button" text="Inverter camera" icon="fa-solid fa-rotate" variant="blue_outline" x-on:click="switchCamera()" x-bind:disabled="!canSwitchCamera" />
                    <x-button type="button" text="Capturar localizacao" icon="fa-solid fa-location-crosshairs" variant="blue_outline" x-on:click="captureLocation(true)" />
                </div>

                <div x-show="cameraOpen" class="mt-3 space-y-3">
                    <video x-ref="video" autoplay playsinline class="w-full rounded-2xl border border-slate-200 bg-slate-950"></video>
                    <div class="flex flex-wrap gap-3">
                        <x-button type="button" text="Capturar foto" icon="fa-solid fa-check" variant="blue_outline" x-on:click="capturePhoto()" />
                        <x-button type="button" text="Fechar camera" icon="fa-solid fa-xmark" variant="red_outline" x-on:click="stopCamera()" />
                    </div>
                </div>

                <canvas x-ref="canvas" class="hidden"></canvas>

                <template x-if="previewUrl">
                    <img :src="previewUrl" alt="Preview da foto" class="mt-3 max-h-72 w-full rounded-2xl border border-slate-200 object-cover" />
                </template>

                <x-form.error for="photo" />
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status da localizacao</p>
                <div class="mt-3 inline-flex rounded-xl border px-3 py-2 text-xs font-medium" :class="locationStatusClass">
                    <span x-text="locationStatusLabel"></span>
                </div>

                <template x-if="locationHelpText">
                    <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" x-text="locationHelpText"></div>
                </template>

                <template x-if="accuracyWarning">
                    <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" x-text="accuracyWarning"></div>
                </template>

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl bg-white px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Latitude</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="formattedLatitude"></p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Longitude</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="formattedLongitude"></p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Precisao</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="formattedAccuracy"></p>
                    </div>
                </div>

                <x-form.error for="latitude" />
                <x-form.error for="longitude" />
                <x-form.error for="accuracy" />
            </div>

            <div class="flex justify-end gap-3">
                <x-button type="button" text="Cancelar" variant="secondary" wire:click="closeRegisterModal" />
                <x-button type="button" text="Salvar registro" icon="fa-solid fa-check" x-on:click="$wire.call('register')" x-bind:disabled="submitting" />
            </div>
        </div>
    </x-modal>
</div>

@script
<script>
    Alpine.data('timeClockRegister', ($wire) => ({
        cameraOpen: false,
        previewUrl: null,
        stream: null,
        submitting: false,
        cameraFacingMode: 'user',
        canSwitchCamera: false,
        locationStatusLabel: 'Aguardando captura da localizacao',
        locationStatusClass: 'border-slate-200 bg-white text-slate-600',
        locationHelpText: '',
        maxAllowedAccuracy: @js(config('time_clock.max_allowed_accuracy_meters')),
        get formattedLatitude() {
            return this.formatCoordinate($wire.latitude);
        },
        get formattedLongitude() {
            return this.formatCoordinate($wire.longitude);
        },
        get formattedAccuracy() {
            return $wire.accuracy ? `${Number($wire.accuracy).toFixed(2)} m` : 'Nao capturada';
        },
        get accuracyWarning() {
            if (!this.maxAllowedAccuracy || !$wire.accuracy) {
                return '';
            }

            if (Number($wire.accuracy) <= Number(this.maxAllowedAccuracy)) {
                return '';
            }

            return `Precisao atual em ${Number($wire.accuracy).toFixed(2)} m. Tente recapturar a localizacao ate ficar em ${Number(this.maxAllowedAccuracy).toFixed(0)} m ou menos.`;
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
                this.setLocationStatus('Clique em capturar localizacao para solicitar permissao', 'loading');
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
                this.setLocationStatus('Clique em capturar localizacao para solicitar permissao', 'loading');
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

            this.setLocationStatus('Clique em capturar localizacao para solicitar permissao', 'loading');
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
