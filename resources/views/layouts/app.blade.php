<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

        <!-- Fontawesome -->
        <script src="https://kit.fontawesome.com/04fdd6b99f.js" crossorigin="anonymous"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Scripts Próprios, Máscaras de Inputs -->
        <script src="{{asset('asset/js/maskInput.js')}}"></script>
        
        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased" x-data="{ open: false, profile: false, sidebarExpanded: false }">

        <div class="flex">
            <!-- Sidebar Desktop -->
            <aside class="hidden lg:flex flex-col bg-gradient-to-b from-blue-50 to-white border-r border-blue-200/50 shadow-lg z-30 transition-all duration-300 ease-in-out overflow-hidden"
                   :class="sidebarExpanded ? 'w-72' : 'w-20'"
                   @mouseenter="sidebarExpanded = true"
                   @mouseleave="sidebarExpanded = false">
                
                <!-- Logo Compacta -->
                <div class="h-16 flex items-center justify-center border-b border-blue-200/50 bg-gradient-to-r from-blue-50 to-blue-100">
                    <x-application-logo class="w-8 h-8"/>
                </div>

                <!-- Navigation Desktop -->
                <nav class="flex-1 py-1.5 space-y-1 overflow-hidden pb-2" :class="sidebarExpanded ? 'px-2' : 'px-1'" x-data="{ activeDropdown: null }">
                    <!-- Dashboard -->
                    @include('layouts.navigation')
                </nav>

                <!-- Footer Desktop -->
                <div class="p-3 border-t border-blue-100/50 bg-white/80 text-center">
                    <p class="text-[10px] text-gray-500 whitespace-nowrap transition-all duration-300 ease-in-out"
                       :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 overflow-hidden'">
                        {{ config('app.name') }} v{{ config('app.version', '1.0.0') }}
                    </p>
                    
                </div>
            </aside>

            <!-- Sidebar Mobile -->
            <aside x-show="open" x-cloak 
                x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="-translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0 opacity-100"
                x-transition:leave-end="-translate-x-full opacity-0" 
                class="fixed inset-y-0 left-0 w-80 bg-gradient-to-b from-blue-50 to-white z-50 shadow-2xl overflow-y-auto border-r border-blue-200/50 lg:hidden">

                <!-- Topo -->
                <div class="h-16 flex items-center justify-between px-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-600/30">
                    <div class="flex-1">
                        <x-application-logo class="w-8 h-8"/>
                    </div>

                    <button @click="open = false" class="rounded-lg p-2.5 transition-all duration-200 hover:scale-110 active:scale-95">
                        <i class="fa-solid fa-xmark text-blue-800 text-lg"></i>
                    </button>
                </div>

                <!-- Navigation Mobile -->
                <nav class="py-1.5 px-2 space-y-1.5 overflow-hidden pb-2" x-data="{ activeDropdown: null }">
                    @include('layouts.navigation')
                </nav>

                <!-- Footer Mobile -->
                <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-100/50 bg-white/80">
                    <div class="text-center">
                        <p class="text-xs text-gray-600">
                            {{ config('app.name') }} v{{ config('app.version', '1.0.0') }}
                        </p>
                        <p class="text-[10px] text-gray-500 mt-1">
                            © {{ date('Y') }} Todos os direitos reservados
                        </p>
                    </div>
                </div>
            </aside>

            <!-- Profile Aside -->
            <aside x-show="profile" 
                x-cloak 
                x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full" 
                class="fixed inset-y-0 right-0 w-80 bg-white z-50 shadow-lg overflow-y-auto">
                
                <div class="max-w-xs mx-auto overflow-hidden">
                    <!-- Header com avatar -->
                    <div class="relative bg-gradient-to-r from-blue-600 to-blue-800 h-20 flex items-center justify-end px-4">
                        <button @click="profile = !profile" 
                                class="p-2 rounded-full transition-all duration-200 hover:scale-110">
                            <i class="fa-solid fa-times text-white text-lg"></i>
                        </button>
                        
                        <!-- Avatar no centro -->
                        <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 
                                    size-16 flex justify-center items-center rounded-full 
                                    bg-gradient-to-br from-blue-500 to-blue-700 text-white 
                                    shadow-xl border border-white text-2xl uppercase font-semibold">
                            {{ Str::substr(Auth::user()->name, 0, 2) }}
                        </div>
                    </div>

                    <!-- Informações do usuário -->
                    <div class="mt-8 pb-6 text-center px-6 space-y-1.5 border-b border-blue-300">
                        <p class="mb-3 font-bold text-gray-900 text-lg truncate">{{ Auth::user()->name }}</p>
                        <div class="flex flex-col justify-center items-center gap-2 text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-user text-blue-500"></i>
                                <span class="truncate">{{ Auth::user()->name }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-building text-blue-500"></i>
                                <span class="truncate">{{ Auth::user()->Unit->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-users text-blue-500"></i>
                                <span class="truncate">{{ Auth::user()->Queue->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Links de perfil -->
                    <div class="space-y-2 px-4 py-4">
                        @if (Route::has('profile.edit'))
                            <a href="{{ route('profile.edit') }}" 
                            class="flex items-center gap-3 w-full py-3 px-4 rounded-xl transition-all duration-200
                                    hover:bg-blue-50 hover:text-blue-700 text-gray-700 font-medium
                                    hover:translate-x-1">
                                <i class="fa-solid fa-user text-blue-500 w-5"></i>
                                <span>Meus Dados</span>
                            </a>
                        @endif
                        
                        @if (Route::has('profile.password.edit'))
                            <a href="{{ route('profile.password.edit') }}" 
                            class="flex items-center gap-3 w-full py-3 px-4 rounded-xl transition-all duration-200 
                                    hover:bg-blue-50 hover:text-blue-700 text-gray-700 font-medium 
                                    hover:translate-x-1">
                                <i class="fa-solid fa-lock text-blue-500 w-5"></i>
                                <span>Alterar Senha</span>
                            </a>
                        @endif
                        
                        <!-- Botão de sair -->
                        <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-gray-100">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center gap-3 w-full py-3 px-4 rounded-xl transition-all duration-200
                                        hover:bg-red-50 hover:text-red-700 text-gray-700 font-semibold
                                        hover:translate-x-1">
                                <i class="fa-solid fa-right-from-bracket text-red-500 w-5"></i>
                                <span>Sair</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div x-show="profile || open" x-cloak x-transition.opacity class="fixed inset-0 bg-black/80 backdrop-blur-sm z-40" @click="profile = false; open = false;"></div>

            <!-- Conteúdo -->
            <div class="flex-1 flex flex-col min-h-screen">
                <!-- Header -->
                <header class="w-full flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-600/30">
                    <!-- Left Section: Menu Hamburger + Logo -->
                    <div class="w-full flex items-center gap-3 sm:gap-4">
                        <!-- Menu Hamburger (mobile) -->
                        <button @click="open = true; sidebarExpanded = true" 
                                class="lg:hidden py-2 px-3 rounded-lg transition-all duration-200 hover:bg-blue-200/50">
                            <i class="fa-solid fa-bars text-blue-700 text-xl"></i>
                        </button>

                        <!-- Logo Mobile -->
                        <div class="lg:hidden flex-1 flex items-center justify-center gap-1">
                            <div class="flex items-center gap-2">
                                <x-application-logo class="w-8 h-8"/>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: User Actions -->
                    <div class="flex items-center gap-2 sm:gap-4">
                        <!-- User Avatar -->
                        <div class="relative">
                            <button @click="profile = !profile" 
                                    class="size-10 rounded-full font-semibold uppercase transition-all duration-200
                                        bg-gradient-to-br from-blue-600 to-blue-800 text-white shadow-lg
                                        hover:from-blue-700 hover:to-blue-900 hover:scale-105 active:scale-95
                                        border-2 border-white/20">
                                {{ Str::substr(Auth::user()->name, 0, 2) }}
                            </button>
                            
                            <!-- Online Status Indicator -->
                            <div class="absolute -bottom-0.5 -right-0.5 size-3 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                    </div>
                </header>

                <!-- Conteúdo Principal -->
                <div class="flex-1 bg-gray-50" style="min-height: calc(100vh - 4rem);">
                    <main class="p-4 sm:p-6 lg:px-8 lg:py-6 mx-auto max-w-7xl">
                        {{ $slot }}
                    </main>
                </div>

                <x-alert.flash />
            </div>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts
    </body>
</html>