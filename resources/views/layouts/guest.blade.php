<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
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
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
        
        <!-- DNS Prefetch para recursos externos -->
        <link rel="dns-prefetch" href="https://fonts.googleapis.com">
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
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
        
        <!-- Livewire Styles -->
        @livewireStyles
        
        <!-- Styles adicionais -->
        @stack('styles')
    </head>
    <body class="font-sans text-gray-900 antialiased min-h-screen flex flex-col">

        <!-- Conteúdo principal -->
        <main id="main-content" class="flex-grow" role="main">
            {{ $slot }}
        </main>

        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Scripts adicionais -->
        @stack('scripts')
    </body>
</html>