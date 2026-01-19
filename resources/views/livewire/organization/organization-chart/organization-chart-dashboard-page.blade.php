<div
    x-data="organograma()"
    @wheel.prevent="handleWheelZoom($event)"
    class="relative w-full"
>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('organograma', () => ({
                scale: 1,
                minScale: 0.3,
                maxScale: 2,

                translateX: 0,
                translateY: 0,

                isDragging: false,
                startX: 0,
                startY: 0,

                pointers: new Map(),
                initialPinchDistance: null,
                initialScale: 1,

                get zoomLevel() {
                    return Math.round(this.scale * 100);
                },

                zoomIn() {
                    if (this.scale < this.maxScale) this.scale += 0.1;
                },

                zoomOut() {
                    if (this.scale > this.minScale) this.scale -= 0.1;
                },

                resetPosition() {
                    this.scale = 1;
                    this.translateX = 0;
                    this.translateY = 0;
                },

                fitToScreen() {
                    this.scale = 0.7;
                    this.translateX = 0;
                    this.translateY = 0;
                },

                handleWheelZoom(event) {
                    const delta = event.deltaY < 0 ? 0.1 : -0.1;
                    const newScale = this.scale + delta;

                    if (newScale < this.minScale || newScale > this.maxScale) return;

                    const rect = this.$refs.container.getBoundingClientRect();
                    const mouseX = event.clientX - rect.left;
                    const mouseY = event.clientY - rect.top;

                    const scaleRatio = newScale / this.scale;

                    this.translateX -= (mouseX - this.translateX) * (scaleRatio - 1);
                    this.translateY -= (mouseY - this.translateY) * (scaleRatio - 1);

                    this.scale = newScale;
                },

                onPointerDown(event) {
                    this.pointers.set(event.pointerId, event);

                    if (this.pointers.size === 1) {
                        this.isDragging = true;
                        this.startX = event.clientX - this.translateX;
                        this.startY = event.clientY - this.translateY;
                    }

                    if (this.pointers.size === 2) {
                        const [p1, p2] = [...this.pointers.values()];
                        this.initialPinchDistance = this.getDistance(p1, p2);
                        this.initialScale = this.scale;
                    }

                    event.target.setPointerCapture(event.pointerId);
                },

                onPointerMove(event) {
                    if (!this.pointers.has(event.pointerId)) return;

                    this.pointers.set(event.pointerId, event);

                    // PINCH ZOOM (2 dedos)
                    if (this.pointers.size === 2) {
                        const [p1, p2] = [...this.pointers.values()];
                        const currentDistance = this.getDistance(p1, p2);
                        const scaleFactor = currentDistance / this.initialPinchDistance;

                        let newScale = this.initialScale * scaleFactor;
                        newScale = Math.min(this.maxScale, Math.max(this.minScale, newScale));

                        this.scale = newScale;
                        return;
                    }

                    // DRAG (1 dedo ou mouse)
                    if (this.isDragging && this.pointers.size === 1) {
                        this.translateX = event.clientX - this.startX;
                        this.translateY = event.clientY - this.startY;
                    }
                },

                onPointerUp(event) {
                    this.pointers.delete(event.pointerId);

                    if (this.pointers.size < 2) {
                        this.initialPinchDistance = null;
                    }

                    if (this.pointers.size === 0) {
                        this.isDragging = false;
                    }

                    if (event.target.releasePointerCapture) {
                        event.target.releasePointerCapture(event.pointerId);
                    }
                },

                getDistance(p1, p2) {
                    return Math.hypot(
                        p2.clientX - p1.clientX,
                        p2.clientY - p1.clientY
                    );
                },

                init() {
                    this.$nextTick(() => this.fitToScreen());
                }
            }));
        });
    </script>

    <!-- Header -->
    <div class="mb-8">
        <x-page.header
            title="Organograma"
            subtitle="Organograma da Secretaria de Saúde de Caruaru"
            icon="fa-solid fa-sitemap"
        >
            <x-slot name="button">
                <div class="flex items-center gap-2">
                    <x-button @click="zoomOut" icon="fa-solid fa-minus"/>
                    <span x-text="zoomLevel + '%'" class="text-sm font-semibold"></span>
                    <x-button @click="zoomIn" icon="fa-solid fa-plus"/>
                    <x-button @click="resetPosition" text="Centralizar"/>
                </div>
            </x-slot>
        </x-page.header>
    </div>

    <!-- Organograma -->
    <div class="mx-auto bg-green-700/10 rounded-xl shadow border border-green-700">
        <div
            x-ref="container"
            class="relative overflow-hidden"
            style="height: 700px; cursor: grab; touch-action: none;"
            @pointerdown="onPointerDown($event)"
            @pointermove="onPointerMove($event)"
            @pointerup="onPointerUp($event)"
            @pointercancel="onPointerUp($event)"
            @pointerleave="onPointerUp($event)"
        >

            <!-- Hint Mobile -->
            <div class="absolute bottom-4 left-4 z-10 bg-white/90 rounded-lg px-3 py-2 text-xs shadow sm:hidden">
                Arraste com 1 dedo • Zoom com 2 dedos
            </div>

            <!-- Hint Desktop -->
            <div class="absolute bottom-4 left-4 z-10 bg-white/90 rounded-lg px-3 py-2 text-xs shadow hidden sm:block">
                Arraste com o mouse • Scroll para zoom
            </div>
            
            <!-- Conteúdo -->
            <div class="absolute inset-0 flex justify-center items-start">
                <div
                    class="relative p-12 transition-transform"
                    :style="`
                        transform:
                            translate(${translateX}px, ${translateY}px)
                            scale(${scale});
                        transform-origin: top center;
                        transition: ${isDragging ? 'none' : 'transform 0.15s ease'};
                    `"
                >
                    @foreach($organizationCharts as $node)
                        @include('livewire.organization.organization-chart._partials.organization-chart-org-node', ['node' => $node])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
