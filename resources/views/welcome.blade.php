<x-guest-layout>
    <div class="flex flex-col min-h-screen bg-gradient-to-br from-purple-600 via-blue-500 to-cyan-500">
        <!-- Header -->
        <nav class="bg-white/20 blur-3xlborder-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <img src="{{ asset('asset\img\logo_white_full.png') }}" alt="X-AdminPanel Logo" class="h-8">
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-sm">
                        <a href="{{ route('login') }}" class="text-white hover:text-indigo-300 transition duration-200 font-medium">
                            Entrar
                        </a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition duration-200 font-medium shadow-sm hover:shadow-md">
                                Começar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="flex-1 flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
                        Painel Administrativo
                        <span class="block text-blue-200 mt-2">Moderno & Elegante</span>
                    </h1>
                    <p class="text-lg md:text-xl text-white/90 mb-8 max-w-3xl mx-auto leading-relaxed">
                        Desenvolvido com Laravel, Tailwind CSS e Livewire. 
                        O X-AdminPanel traz uma experiência incrível para gerenciar seus sistemas.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-white text-gray-900 px-8 py-4 rounded-lg font-semibold hover:bg-gray-50 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-white/20">
                                Começar Agora
                            </a>
                        @endif
                        <a href="#features" class="border-2 border-white/60 text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/10 transition duration-200 backdrop-blur-sm">
                            Ver Características
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="bg-slate-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                    Construído com as Melhores Tecnologias
                </h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Uma stack moderna e poderosa para desenvolvimento rápido e eficiente
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Laravel Card -->
                <div class="bg-white p-8 rounded-xl shadow-lg border border-blue-100 hover:border-blue-200 transition-all duration-300 hover:shadow-xl">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mb-4">
                        <span class="text-white font-bold text-lg">L</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Laravel</h3>
                    <p class="text-slate-600 mb-4">
                        Backend robusto e elegante com toda a potência do framework PHP mais amado.
                    </p>
                    <ul class="text-sm text-slate-500 space-y-2">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                            Estrutura MVC organizada
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                            Migrations e Eloquent ORM
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                            Sistema de autenticação
                        </li>
                    </ul>
                </div>

                <!-- Tailwind CSS Card -->
                <div class="bg-white p-8 rounded-xl shadow-lg border border-indigo-100 hover:border-indigo-200 transition-all duration-300 hover:shadow-xl">
                    <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.001 4.8c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624C13.666 10.618 15.027 12 18.001 12c3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C16.337 6.182 14.976 4.8 12.001 4.8zm-6 7.2c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624 1.177 1.194 2.538 2.576 5.512 2.576 3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C10.337 13.382 8.976 12 6.001 12z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Tailwind CSS</h3>
                    <p class="text-slate-600 mb-4">
                        Design utility-first para interfaces modernas e responsivas.
                    </p>
                    <ul class="text-sm text-slate-500 space-y-2">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-indigo-400 rounded-full mr-3"></span>
                            Design system consistente
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-indigo-400 rounded-full mr-3"></span>
                            Totalmente responsivo
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-indigo-400 rounded-full mr-3"></span>
                            Customização fácil
                        </li>
                    </ul>
                </div>

                <!-- Livewire Card -->
                <div class="bg-white p-8 rounded-xl shadow-lg border border-purple-100 hover:border-purple-200 transition-all duration-300 hover:shadow-xl">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mb-4">
                        <span class="text-white font-bold text-lg">LW</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Livewire</h3>
                    <p class="text-slate-600 mb-4">
                        Interfaces dinâmicas e reativas sem a complexidade do JavaScript.
                    </p>
                    <ul class="text-sm text-slate-500 space-y-2">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                            Componentes reativos
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                            Atualizações em tempo real
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                            Experiência SPA-like
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-slate-900 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">
                Pronto para Começar?
            </h2>
            <p class="text-slate-300 mb-8 max-w-2xl mx-auto text-lg">
                Junte-se a desenvolvedores que já estão usando o X-AdminPanel para acelerar seus projetos.
            </p>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Criar Minha Conta
                </a>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-slate-300">&copy; {{ date('Y') }} X-AdminPanel. Desenvolvido com 💙 para a comunidade Laravel.</p>
        </div>
    </footer>
</x-guest-layout>