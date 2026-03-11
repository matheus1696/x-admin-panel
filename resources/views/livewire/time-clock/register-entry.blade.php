<div class="space-y-4">

    <x-page.header title="Registrar Ponto" subtitle="Controle de Ponto" icon="fa-solid fa-clock" />

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        <aside class="space-y-4 xl:col-span-4">
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Aside</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Usuario</p>
                        <p class="font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Data / Hora</p>
                        <p class="font-semibold text-gray-900">{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Ultimos registros</p>
                        <div class="mt-2 space-y-2">
                            @forelse ($recentEntries as $entry)
                                <div class="rounded-xl border border-gray-100 bg-gray-50 px-3 py-2 text-xs">
                                    <p class="font-semibold text-gray-800">{{ $entry->occurred_at?->format('d/m/Y H:i') }}</p>
                                    <p class="text-gray-500">{{ $entry->status }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500">Nenhum registro encontrado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
        </aside>

        <main class="xl:col-span-8">
            <form wire:submit.prevent="register" x-data="timeClockRegister($wire)" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-5">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <x-form.label value="Foto" />
                        <input x-ref="photoInput" type="file" wire:model="photo" accept="image/*" capture="environment" class="hidden" @change="previewFile($event)" />

                        <div class="mt-2 space-y-3">
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                                <x-button type="button" text="Abrir camera" icon="fa-solid fa-camera" x-on:click="openCamera()" />
                                <x-button type="button" text="Capturar foto" icon="fa-solid fa-check" variant="blue_outline" x-on:click="capturePhoto()" />
                                <x-button type="button" text="Usar arquivo" icon="fa-solid fa-image" variant="gray_outline" x-on:click="$refs.photoInput.click()" />
                            </div>

                            <video x-ref="video" x-show="cameraOpen" autoplay playsinline class="w-full rounded-2xl border border-gray-200 bg-gray-950"></video>
                            <canvas x-ref="canvas" class="hidden"></canvas>

                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Preview da foto" class="w-full rounded-2xl border border-gray-200 object-cover max-h-80" />
                            </template>
                        </div>
                        <x-form.error for="photo" />
                    </div>

                    <div>
                        <x-form.label value="Latitude" />
                        <x-form.input type="number" step="0.0000001" wire:model.defer="latitude" />
                        <x-form.error for="latitude" />
                    </div>

                    <div>
                        <x-form.label value="Longitude" />
                        <x-form.input type="number" step="0.0000001" wire:model.defer="longitude" />
                        <x-form.error for="longitude" />
                    </div>

                    <div>
                        <x-form.label value="Precisao" />
                        <x-form.input type="number" step="0.01" wire:model.defer="accuracy" />
                        <x-form.error for="accuracy" />
                        <div class="mt-3">
                            <x-button type="button" text="Capturar localizacao" icon="fa-solid fa-location-dot" variant="blue_outline" x-on:click="captureLocation()" />
                        </div>
                    </div>

                    <div>
                        <x-form.label value="Local" />
                        <x-form.select-livewire
                            wire:model.live="locationId"
                            name="locationId"
                            :options="$locations->map(fn ($location) => ['value' => $location->id, 'label' => $location->name])->prepend(['value' => '', 'label' => 'Sem local'])->values()->all()"
                        />
                        <x-form.error for="locationId" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-button type="submit" text="Registrar" icon="fa-solid fa-check" />
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
        async openCamera() {
            if (!navigator.mediaDevices?.getUserMedia) {
                this.$refs.photoInput.click();
                return;
            }

            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
            this.$refs.video.srcObject = this.stream;
            this.cameraOpen = true;
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
        captureLocation() {
            if (!navigator.geolocation) {
                return;
            }

            navigator.geolocation.getCurrentPosition((position) => {
                $wire.set('latitude', Number(position.coords.latitude.toFixed(7)));
                $wire.set('longitude', Number(position.coords.longitude.toFixed(7)));
                $wire.set('accuracy', Number(position.coords.accuracy.toFixed(2)));
            });
        },
        stopCamera() {
            if (!this.stream) return;
            this.stream.getTracks().forEach((track) => track.stop());
            this.stream = null;
            this.cameraOpen = false;
        },
    }));
</script>
@endscript
