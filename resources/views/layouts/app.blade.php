<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- SEO Meta Tags -->
        <meta name="description" content="@yield('meta_description', config('app.description'))">
        <meta name="keywords" content="@yield('meta_keywords', config('app.keywords'))">
        <meta name="author" content="{{ config('app.author') }}">
        <meta name="robots" content="index, follow">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@yield('title', config('app.name', 'Laravel'))">
        <meta property="og:description" content="@yield('meta_description', config('app.description', 'Descrição padrão do seu site'))">
        <meta property="og:image" content="@yield('meta_image', asset('assets/img/og-image.jpg'))">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="@yield('title', config('app.name', 'Laravel'))">
        <meta name="twitter:description" content="@yield('meta_description', config('app.description', 'Descrição padrão do seu site'))">
        <meta name="twitter:image" content="@yield('meta_image', asset('assets/img/twitter-image.jpg'))">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">
        
        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('assets/img/site.webmanifest') }}">

        <!-- Preconnect e DNS Prefetch -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://kit.fontawesome.com">
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
        
        <!-- DNS Prefetch para recursos externos -->
        <link rel="dns-prefetch" href="https://fonts.googleapis.com">
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link rel="dns-prefetch" href="https://kit.fontawesome.com">
        <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

        <!-- Fonts otimizadas -->
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
        <noscript>
            <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
        </noscript>

        <!-- Fontawesome otimizado (carregamento assíncrono) -->
        <script>
            (function() {
                var kit = document.createElement('script');
                kit.src = 'https://kit.fontawesome.com/04fdd6b99f.js';
                kit.crossOrigin = 'anonymous';
                kit.async = true;
                document.head.appendChild(kit);
            })();
        </script>
        <noscript>
            <script src="https://kit.fontawesome.com/04fdd6b99f.js" crossorigin="anonymous"></script>
        </noscript>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Scripts Próprios com defer -->
        <script src="{{ asset('asset/js/maskInput.js') }}" defer></script>
        
        <!-- Livewire Styles -->
        @livewireStyles
        
        <!-- Styles adicionais -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased h-full" x-data="{ openAside: false, openProfile: false, sidebarExpanded: false, isMobile: window.innerWidth < 1024 }" x-init=" window.addEventListener('resize', () => isMobile = window.innerWidth < 1024);" :class="{ 'overflow-hidden': openAside || openProfile }">

        <div class="flex h-screen">
            <!-- Sidebar Desktop -->
            <aside class="hidden lg:flex flex-col bg-white shadow-xl z-10 transition-all duration-500 ease-in-out overflow-hidden relative" :class="sidebarExpanded ? 'w-80' : 'w-20'" @mouseenter="sidebarExpanded = true" @mouseleave="sidebarExpanded = false">
                
                <!-- Logo Compacta com efeito melhorado -->
                <div class="h-16 flex items-center justify-center gap-2 border-b border-emerald-200/50 bg-gradient-to-l from-emerald-700 via-emerald-800 to-emerald-800 uppercase font-semibold text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 -translate-x-full animate-shimmer"></div>
                    <x-application-logo class="w-8 h-8 relative z-10"/>
                    <span 
                        x-show="sidebarExpanded"
                        x-transition:enter="transition ease-out duration-[2s]"
                        x-transition:enter-start="opacity-0 scale-[0]"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-0"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0"
                        class="text-lg relative z-10"
                    >
                        {{ config('app.name') }}
                    </span>
                </div>

                <!-- Navigation Desktop com scroll customizado -->
                <nav class="flex-1 py-3 space-y-1.5 overflow-y-auto pb-2" :class="sidebarExpanded ? 'px-2' : 'px-1'">
                    @include('layouts.navigation')
                </nav>

                <!-- Footer Aside -->
                @include('layouts._partials.footer-aside')
            </aside>

            <!-- Sidebar Mobile com overlay melhorado -->
            <template x-teleport="body">
                <div x-show="openAside" x-cloak>
                    <!-- Backdrop -->
                    <div x-show="openAside" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40"
                         @click="openAside = false">
                    </div>
                    
                    <!-- Sidebar -->
                    <aside x-show="openAside" 
                           x-transition:enter="transition ease-out duration-300" 
                           x-transition:enter-start="-translate-x-full opacity-0"
                           x-transition:enter-end="translate-x-0 opacity-100"
                           x-transition:leave="transition ease-in duration-200"
                           x-transition:leave-start="translate-x-0 opacity-100"
                           x-transition:leave-end="-translate-x-full opacity-0" 
                           class="fixed inset-y-0 left-0 w-80 bg-gradient-to-b from-emerald-50 to-white z-50 shadow-2xl overflow-y-auto border-r border-emerald-200/50">
                        
                        <!-- Topo com animação -->
                        <div class="h-16 flex items-center justify-between px-6 bg-gradient-to-r from-emerald-700 via-emerald-800 to-emerald-800 border-b border-emerald-700/30">
                            <div class="flex-1 flex items-center gap-2 uppercase font-semibold text-white">
                                <x-application-logo class="w-8 h-8"/>
                                <span class="text-lg">{{ config('app.name') }}</span>
                            </div>

                            <div @click="openAside = false">
                                @include('layouts._partials.button-closed-aside')
                            </div>
                        </div>

                        <!-- Navigation Mobile -->
                        <nav class="py-1.5 px-2 space-y-1.5 overflow-y-auto pb-20">
                            @include('layouts.navigation')
                        </nav>

                        <!-- Footer Aside -->
                        @include('layouts._partials.footer-aside')      
                    </aside>
                </div>
            </template>

            @auth
                <!-- Profile Aside melhorado -->
                <template x-teleport="body">
                    <div x-show="openProfile" x-cloak>
                        <!-- Backdrop -->
                        <div x-show="openProfile" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40"
                             @click="openProfile = false">
                        </div>
                        
                        <!-- Profile Panel -->
                        <aside x-show="openProfile" 
                               x-transition:enter="transition ease-out duration-300" 
                               x-transition:enter-start="translate-x-full opacity-0"
                               x-transition:enter-end="translate-x-0 opacity-100"
                               x-transition:leave="transition ease-in duration-200"
                               x-transition:leave-start="translate-x-0 opacity-100"
                               x-transition:leave-end="translate-x-full opacity-0" 
                               class="fixed inset-y-0 right-0 w-80 bg-white z-50 shadow-lg overflow-y-auto">
                            
                            <div class="max-w-xs mx-auto overflow-hidden">
                                <!-- Header com avatar e gradiente -->
                                <div class="relative bg-gradient-to-r from-emerald-700 to-emerald-800 h-20">
                                    <div @click="openProfile = false">
                                        @include('layouts._partials.button-closed-aside')
                                    </div>
                                    
                                    <!-- Avatar com borda animada -->
                                    <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2">
                                        <div class="relative group">
                                            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-emerald-300 to-emerald-700 animate-pulse blur"></div>
                                            <div class="relative size-20 flex justify-center items-center rounded-full 
                                                        bg-gradient-to-br from-emerald-700 to-emerald-800 text-white 
                                                        shadow-xl border-4 border-white text-3xl uppercase font-semibold
                                                        group-hover:scale-105 transition-transform duration-300">
                                                {{ Str::substr(Auth::user()->name, 0, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informações do usuário com ícones -->
                                <div class="mt-14 pb-6 text-center px-6 space-y-3 border-b-2 border-gray-100">
                                    <p class="font-bold text-gray-900 text-lg truncate">{{ Auth::user()->name }}</p>
                                    <div class="flex flex-col justify-center items-center gap-2 text-xs">
                                        <div class="flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-full w-full">
                                            <i class="fa-solid fa-envelope text-emerald-700 w-4"></i>
                                            <span class="text-gray-600 truncate">{{ Auth::user()->email }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-full w-full">
                                            <i class="fa-solid fa-building text-emerald-700 w-4"></i>
                                            <span class="text-gray-600 truncate">{{ Auth::user()->Unit->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-full w-full">
                                            <i class="fa-solid fa-users text-emerald-700 w-4"></i>
                                            <span class="text-gray-600 truncate">{{ Auth::user()->Queue->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Links de perfil com ícones -->
                                <div class="space-y-1 px-4 py-4">
                                    @if (Route::has('profile.edit'))
                                        <a href="{{ route('profile.edit') }}" 
                                           class="flex items-center gap-3 w-full py-3 px-4 rounded-xl transition-all duration-200 
                                                  hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 group">
                                            <i class="fa-solid fa-user w-5 text-emerald-700 group-hover:scale-110 transition-transform"></i>
                                            <span class="font-medium">Meus Dados</span>
                                        </a>
                                    @endif
                                    
                                    @if (Route::has('profile.password.edit'))
                                        <a href="{{ route('profile.password.edit') }}" 
                                           class="flex items-center gap-3 w-full py-3 px-4 rounded-xl transition-all duration-200 
                                                  hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 group">
                                            <i class="fa-solid fa-lock w-5 text-emerald-700 group-hover:scale-110 transition-transform"></i>
                                            <span class="font-medium">Alterar Senha</span>
                                        </a>
                                    @endif
                                    
                                    <!-- Botão de sair -->
                                    <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-gray-100">
                                        @csrf
                                        <button type="submit" 
                                                class="flex items-center gap-3 w-full py-3 px-4 rounded-xl transition-all duration-200
                                                       hover:bg-red-50 text-gray-700 hover:text-red-700 group">
                                            <i class="fa-solid fa-right-from-bracket w-5 text-red-500 group-hover:scale-110 transition-transform"></i>
                                            <span class="font-medium">Sair da conta</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </aside>
                    </div>
                </template>
            @endauth

            <!-- Conteúdo Principal -->
            <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
                <!-- Header fixo -->
                <header class="w-full flex items-center justify-between py-3 px-4 lg:px-8 h-16 bg-gradient-to-r from-emerald-700 via-emerald-800 to-emerald-800 shadow-lg sticky top-0 z-10">
                    <!-- Left Section -->
                    <div>
                        <!-- Menu Hamburger -->
                        <button @click="openAside = true" 
                                class="lg:hidden p-2 rounded-lg transition-all duration-200 hover:bg-emerald-800 active:scale-95">
                            <i class="fa-solid fa-bars text-white text-xl"></i>
                        </button>
                    </div>

                    @auth
                        <!-- Right Section -->
                        <div class="flex items-center gap-2 sm:gap-4">
                            <!-- Notificações (placeholder) -->
                            <button class="relative p-2 rounded-lg transition-all duration-200 hover:bg-emerald-800 active:scale-95">
                                <i class="fa-regular fa-bell text-white text-lg"></i>
                                <span class="absolute top-1 right-1 size-2 bg-red-500 rounded-full border border-white"></span>
                            </button>
                            
                            <!-- User Avatar -->
                            <div class="relative">
                                <button @click="openProfile = !openProfile" 
                                        class="size-10 rounded-full font-semibold uppercase transition-all duration-200
                                               bg-gradient-to-br from-emerald-700 to-emerald-800 text-white shadow-lg
                                               hover:from-emerald-700 hover:to-emerald-900 hover:scale-105 active:scale-95
                                               border-2 border-white/50 hover:border-white">
                                    {{ Str::substr(Auth::user()->name, 0, 2) }}
                                </button>
                                
                                <!-- Online Status com tooltip -->
                                <div class="absolute -bottom-0.5 -right-0.5">
                                    <div class="size-3 bg-emerald-500 rounded-full border-2 border-white"></div>
                                    <span class="absolute top-full mt-1 right-0 text-[10px] bg-gray-800 text-white px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Online</span>
                                </div>
                            </div>
                        </div>
                    @endauth
                </header>

                <!-- Conteúdo Principal com padding ajustado -->
                <main class="flex-1 bg-gray-50 py-4 px-4 lg:px-8">
                    <div class="mx-auto max-w-7xl">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Alertas -->
                <x-alert.flash />
            </div>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts
    </body>
</html>