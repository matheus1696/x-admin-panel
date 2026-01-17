<div class="" 
     x-data="organograma()" 
     @wheel.prevent="handleZoom($event)" 
     x-on:keydown.window="ctrlPressed = $event.ctrlKey"
     x-on:keyup.window="ctrlPressed = $event.ctrlKey">

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('organograma', () => ({
                scale: 1,
                ctrlPressed: false,
                isDragging: false,
                startX: 0,
                startY: 0,
                translateX: 0,
                translateY: 0,
                lastTranslateX: 0,
                lastTranslateY: 0,
                
                get zoomLevel() {
                    return Math.round(this.scale * 100);
                },
                
                zoomIn() {
                    if (this.scale < 2) {
                        this.scale += 0.05;
                    }
                },
                
                zoomOut() {
                    if (this.scale > 0.3) {
                        this.scale -= 0.05;
                    }
                },
                
                resetZoom() {
                    this.scale = 1;
                    this.translateX = 0;
                    this.translateY = 0;
                    this.lastTranslateX = 0;
                    this.lastTranslateY = 0;
                },
                
                fitToScreen() {
                    this.scale = 0.7;
                    this.translateX = 0;
                    this.translateY = 0;
                    this.lastTranslateX = 0;
                    this.lastTranslateY = 0;
                },
                
                handleZoom(event) {
                    if (this.ctrlPressed) {
                        event.preventDefault();
                        
                        // Obtém a posição do mouse antes do zoom
                        const rect = this.$refs.organogramaContainer.getBoundingClientRect();
                        const mouseX = event.clientX - rect.left;
                        const mouseY = event.clientY - rect.top;
                        
                        // Calcula o ponto relativo antes do zoom
                        const oldScale = this.scale;
                        
                        if (event.deltaY < 0) {
                            // Scroll up - zoom in
                            if (this.scale < 2) {
                                this.scale += 0.1;
                            }
                        } else {
                            // Scroll down - zoom out
                            if (this.scale > 0.3) {
                                this.scale -= 0.1;
                            }
                        }
                        
                        // Ajusta a posição para manter o ponto sob o mouse
                        this.translateX += (mouseX - this.translateX) * (1 - this.scale / oldScale);
                        this.translateY += (mouseY - this.translateY) * (1 - this.scale / oldScale);
                    }
                },
                
                startDrag(event) {
                    // Verifica se é clique esquerdo
                    if (event.button !== 0) return;
                    
                    this.isDragging = true;
                    this.startX = event.clientX - this.translateX;
                    this.startY = event.clientY - this.translateY;
                    
                    // Altera o cursor para "mover"
                    this.$refs.organogramaContainer.style.cursor = 'grabbing';
                    document.body.style.userSelect = 'none';
                },
                
                doDrag(event) {
                    if (!this.isDragging) return;
                    
                    this.translateX = event.clientX - this.startX;
                    this.translateY = event.clientY - this.startY;
                },
                
                stopDrag() {
                    if (this.isDragging) {
                        this.isDragging = false;
                        this.lastTranslateX = this.translateX;
                        this.lastTranslateY = this.translateY;
                        
                        // Restaura o cursor
                        this.$refs.organogramaContainer.style.cursor = 'grab';
                        document.body.style.userSelect = '';
                    }
                },
                
                resetPosition() {
                    this.translateX = 0;
                    this.translateY = 0;
                    this.lastTranslateX = 0;
                    this.lastTranslateY = 0;
                },
                
                init() {
                    this.$nextTick(() => {
                        this.fitToScreen();
                    });
                    
                    // Detecta se CTRL está pressionado
                    window.addEventListener('keydown', (e) => {
                        if (e.ctrlKey) {
                            this.ctrlPressed = true;
                        }
                    });
                    
                    window.addEventListener('keyup', (e) => {
                        if (!e.ctrlKey) {
                            this.ctrlPressed = false;
                        }
                    });
                    
                    // Para o arrastar quando o mouse sair da área
                    this.$el.addEventListener('mouseleave', () => {
                        this.stopDrag();
                    });
                }
            }));
        });
    </script>

    <div class="mb-8">
        <x-page.header title="Organograma" subtitle="Organograma da Secretária de Saúde de Caruaru" icon="fa-solid fa-sitemap">
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

    @if($organizationCharts->isNotEmpty())
        <div class="mx-auto bg-green-700/10 rounded-xl shadow border border-green-700">
            <!-- Container do Organograma com área de arraste -->
            <div class="overflow-hidden relative"
                 x-ref="organogramaContainer"
                 @mousedown="startDrag($event)"
                 @mousemove="doDrag($event)"
                 @mouseup="stopDrag()"
                 @mouseleave="stopDrag()"
                 style="cursor: grab; height: 700px; position: relative;">
                 
                <!-- Instrução de uso -->
                <div class="absolute bottom-4 left-4 z-10 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-2 text-xs text-gray-600 shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-arrows-alt text-green-700"></i>
                        <span>Arraste para mover | CTRL + Scroll para zoom</span>
                    </div>
                </div>
                
                <!-- Área do organograma -->
                <div class="absolute inset-0 overflow-auto">
                    <div class="relative min-w-full min-h-full flex justify-center p-12 transition-transform"
                         :style="`
                             transform: 
                                 translate(${translateX}px, ${translateY}px) 
                                 scale(${scale});
                             transform-origin: top center;
                             transition: ${isDragging ? 'none' : 'transform 0.2s ease'};
                         `">
                        
                        @foreach($organizationCharts as $node)
                            @include('livewire.organization.organization-chart._partials.organization-chart-org-node', ['node' => $node])
                        @endforeach
                        
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="max-w-md mx-auto mt-40 text-center">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-10 h-10 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-800 mb-2">Nenhum cargo cadastrado</h3>
            <p class="text-gray-600">Adicione cargos para visualizar o organograma.</p>
        </div>
    @endif
</div>