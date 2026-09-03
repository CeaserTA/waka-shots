<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Private Gallery') · Waka Shots Photography</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <style>
        .gallery-shell { background: #0a0908; color: #ece7db; min-height: 100svh; }
        .gallery-photo { background: #151316; }
        .gallery-photo img { transition: transform 500ms ease, opacity 300ms ease; }
        .gallery-photo:hover img { transform: scale(1.035); }
        .gallery-icon { background: rgba(10, 9, 8, .78); border: 1px solid rgba(236, 231, 219, .2); }
        .gallery-icon:hover { background: #c6a15b; color: #0a0908; }
        /* Keep the download icon always visible on touch (no hover there to
           reveal it) — only hide-then-reveal-on-hover for fine-pointer
           devices that can actually hover. */
        @media (hover: hover) and (pointer: fine) {
            .gallery-icon { opacity: 0; transition: opacity 300ms ease, background 300ms ease, color 300ms ease; }
            .gallery-photo:hover .gallery-icon, .gallery-icon:focus-visible { opacity: 1; }
        }
    </style>
</head>
<body class="gallery-shell font-sans">
    <div class="grain" aria-hidden="true"></div>
    @yield('content')
    @stack('scripts')
</body>
</html>
