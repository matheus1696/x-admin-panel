<div class="" 
     x-data="organograma()" 
     @wheel.prevent="handleZoom($event)" 
     x-on:keydown.window="ctrlPressed = $event.ctrlKey"
     x-on:keyup.window="ctrlPressed = $event.ctrlKey">

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('organograma', () => ({
                scale: 0.5,
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
                        this.scale += 0.1;
                    }
                },
                
                zoomOut() {
                    if (this.scale > 0.3) {
                        this.scale -= 0.1;
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
                    this.scale = 0.5;
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
                <div class="flex flex-wrap justify-center items-center gap-4 mb-6">
                    <div class="flex items-center">                      
                        <x-button @click="zoomOut" icon="fa-solid fa-minus"/>
                        <span class="text-sm text-gray-700 font-medium min-w-[60px] text-center" x-text="zoomLevel + '%'"></span>
                        <x-button @click="zoomIn" icon="fa-solid fa-plus"/>
                    </div>
                    
                    <div class="flex gap-2">
                        <x-button @click="resetZoom" text="100%" variant="sky"/>
                        <x-button @click="resetPosition" text="Centralizar" variant="gray" />
                    </div>
                </div>
            </x-slot>
        </x-page.header>
    </div>

    @if($organizationCharts->isNotEmpty())
        <div class="mx-auto">
            <!-- Container do Organograma com área de arraste -->
            <div class="bg-white rounded-2xl shadow-lg p-2 md:p-4 overflow-hidden relative"
                 x-ref="organogramaContainer"
                 @mousedown="startDrag($event)"
                 @mousemove="doDrag($event)"
                 @mouseup="stopDrag()"
                 @mouseleave="stopDrag()"
                 style="cursor: grab; height: 600px; position: relative;">
                 
                <!-- Instrução de uso -->
                <div class="absolute bottom-4 left-4 z-10 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-2 text-sm text-gray-600 shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-arrows-alt text-blue-500"></i>
                        <span>Arraste para mover • CTRL + Scroll para zoom</span>
                    </div>
                </div>
                
                <!-- Área do organograma -->
                <div class="absolute inset-0 overflow-auto">
                    <div class="min-w-full min-h-full flex items-start justify-center p-8"
                         :style="`
                             transform: 
                                 translate(${translateX}px, ${translateY}px) 
                                 scale(${scale});
                             transform-origin: top center;
                             transition: ${isDragging ? 'none' : 'transform 0.2s ease'};
                         `">
                        
                        @foreach($organizationCharts as $node)
                            <div class="flex-shrink-0">
                                <div class="text-center">
                                    <div class="w-full px-8 py-6 text-white rounded-lg shadow-md inline-block mb-2 bg-blue-500">
                                        <div class="font-bold text-lg">{{ $node->name }}</div>
                                        <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Diretoria</div>
                                    </div>
                                    
                                    @if($node->children->isNotEmpty())
                                        <div class="flex justify-center gap-8">
                                            @foreach($node->children as $child1)
                                                <div class="text-center">
                                                    <div class="h-8 w-0.5 bg-gray-300 mx-auto"></div>
                                                    <div class="text-center mb-10">
                                                        <div class="w-full px-7 py-5 text-white rounded-lg shadow-md inline-block mb-2 bg-green-500">
                                                            <div class="font-bold text-base">{{ $child1->name }}</div>
                                                            <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Gerência</div>
                                                        </div>
                                                        
                                                        @if($child1->children->isNotEmpty())
                                                            <div class="flex justify-center gap-6">
                                                                @foreach($child1->children as $child2)
                                                                    <div class="text-center">
                                                                        <div class="h-8 w-0.5 bg-gray-300 mx-auto"></div>
                                                                        <div class="text-center mb-8">
                                                                            <div class="w-full px-6 py-4 text-white rounded-lg shadow-md inline-block mb-2 bg-yellow-500">
                                                                                <div class="font-bold text-sm">{{ $child2->name }}</div>
                                                                                <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Supervisão</div>
                                                                            </div>
                                                                            
                                                                            @if($child2->children->isNotEmpty())
                                                                                <div class="flex justify-center gap-5">
                                                                                    @foreach($child2->children as $child3)
                                                                                        <div class="text-center">
                                                                                            <div class="h-7 w-0.5 bg-gray-300 mx-auto"></div>
                                                                                            <div class="text-center mb-6">
                                                                                                <div class="w-full px-5 py-3 text-white rounded-lg shadow-md inline-block mb-2 bg-purple-500">
                                                                                                    <div class="font-bold text-sm">{{ $child3->name }}</div>
                                                                                                    <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Coordenação</div>
                                                                                                </div>
                                                                                                
                                                                                                @if($child3->children->isNotEmpty())
                                                                                                    <div class="flex justify-center gap-4">
                                                                                                        @foreach($child3->children as $child4)
                                                                                                            <div class="text-center">
                                                                                                                <div class="h-6 w-0.5 bg-gray-300 mx-auto"></div>
                                                                                                                <div class="text-center mb-5">
                                                                                                                    <div class="w-full px-5 py-3 text-white rounded-lg shadow-md inline-block mb-2 bg-red-500">
                                                                                                                        <div class="font-bold text-xs">{{ $child4->name }}</div>
                                                                                                                        <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Liderança</div>
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    @if($child4->children->isNotEmpty())
                                                                                                                        <div class="flex justify-center gap-3">
                                                                                                                            @foreach($child4->children as $child5)
                                                                                                                                <div class="text-center">
                                                                                                                                    <div class="h-5 w-0.5 bg-gray-300 mx-auto"></div>
                                                                                                                                    <div class="text-center mb-4">
                                                                                                                                        <div class="w-full px-4 py-3 text-white rounded-lg shadow-md inline-block mb-2 bg-cyan-500">
                                                                                                                                            <div class="font-bold text-xs">{{ $child5->name }}</div>
                                                                                                                                            <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Senior</div>
                                                                                                                                        </div>
                                                                                                                                        
                                                                                                                                        @if($child5->children->isNotEmpty())
                                                                                                                                            <div class="flex justify-center gap-3">
                                                                                                                                                @foreach($child5->children as $child6)
                                                                                                                                                    <div class="text-center">
                                                                                                                                                        <div class="h-4 w-0.5 bg-gray-300 mx-auto"></div>
                                                                                                                                                        <div class="text-center mb-3">
                                                                                                                                                            <div class="w-full px-4 py-2 text-white rounded-lg shadow-md inline-block mb-2 bg-orange-500">
                                                                                                                                                                <div class="font-bold text-xs">{{ $child6->name }}</div>
                                                                                                                                                                <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Pleno</div>
                                                                                                                                                            </div>
                                                                                                                                                            
                                                                                                                                                            @if($child6->children->isNotEmpty())
                                                                                                                                                                <div class="flex justify-center gap-2">
                                                                                                                                                                    @foreach($child6->children as $child7)
                                                                                                                                                                        <div class="text-center">
                                                                                                                                                                            <div class="h-4 w-0.5 bg-gray-300 mx-auto"></div>
                                                                                                                                                                            <div class="text-center mb-2">
                                                                                                                                                                                <div class="w-full px-3 py-2 text-white rounded-lg shadow-md inline-block mb-2 bg-lime-500">
                                                                                                                                                                                    <div class="font-bold text-xs">{{ $child7->name }}</div>
                                                                                                                                                                                    <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Júnior</div>
                                                                                                                                                                                </div>
                                                                                                                                                                                
                                                                                                                                                                                @if($child7->children->isNotEmpty())
                                                                                                                                                                                    <div class="flex justify-center gap-2">
                                                                                                                                                                                        @foreach($child7->children as $child8)
                                                                                                                                                                                            <div class="text-center">
                                                                                                                                                                                                <div class="h-3 w-0.5 bg-gray-300 mx-auto"></div>
                                                                                                                                                                                                <div class="text-center mb-2">
                                                                                                                                                                                                    <div class="w-full px-3 py-2 text-white rounded-lg shadow-md inline-block mb-2 bg-pink-500">
                                                                                                                                                                                                        <div class="font-bold text-xs">{{ $child8->name }}</div>
                                                                                                                                                                                                        <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Estagiário</div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    
                                                                                                                                                                                                    @if($child8->children->isNotEmpty())
                                                                                                                                                                                                        <div class="flex justify-center gap-1">
                                                                                                                                                                                                            @foreach($child8->children as $child9)
                                                                                                                                                                                                                <div class="text-center">
                                                                                                                                                                                                                    <div class="h-3 w-0.5 bg-gray-300 mx-auto"></div>
                                                                                                                                                                                                                    <div class="text-center mb-1">
                                                                                                                                                                                                                        <div class="w-full px-2 py-1 text-white rounded-lg shadow-md inline-block mb-2 bg-indigo-500">
                                                                                                                                                                                                                            <div class="font-bold text-xs">{{ $child9->name }}</div>
                                                                                                                                                                                                                            <div class="opacity-90 text-xs" style="color: rgba(255,255,255,0.9)">Auxiliar</div>
                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            @endforeach
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    @endif
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        @endforeach
                                                                                                                                                                                    </div>
                                                                                                                                                                                @endif
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    @endforeach
                                                                                                                                                                </div>
                                                                                                                                                            @endif
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                @endforeach
                                                                                                                                            </div>
                                                                                                                                        @endif
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            @endforeach
                                                                                                                        </div>
                                                                                                                    @endif
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        @endforeach
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="max-w-md mx-auto mt-20 text-center">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-800 mb-2">Nenhum cargo cadastrado</h3>
            <p class="text-gray-600">Adicione cargos para visualizar o organograma.</p>
        </div>
    @endif
</div>