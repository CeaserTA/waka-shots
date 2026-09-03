<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Waka Shots Photography')</title>
    <link rel="preload" href="/fonts/AmericansClassy.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="{{ config('filesystems.disks.r2.url') }}" crossorigin>
    <link rel="dns-prefetch" href="{{ config('filesystems.disks.r2.url') }}">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600&family=Manrope:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&family=Alex+Brush&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-black text-ivory font-sans leading-relaxed overflow-x-hidden">
    <div class="grain" aria-hidden="true"></div>
    @include('components.navbar')
    <main>
        @yield('content')
    </main>
    @include('components.footer')
    @stack('scripts')
</body>
</html>
