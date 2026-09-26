<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Waka Shots Photography')</title>
    @php
        $defaultDescription = 'Kampala-based photography studio for weddings, introduction ceremonies, portraits, graduations and brand campaigns. Patience over performance — every session shaped around your story.';
        $defaultImage = $siteSetting->imageUrl($siteSetting->home_hero_image);

        // Same yields as <title> and the description tag, read once so the
        // social tags below always match them. yieldContent() returns
        // escaped text, hence the {!! !!} output further down.
        $metaTitle = trim($__env->yieldContent('title', 'Waka Shots Photography'));
        $metaDescription = trim($__env->yieldContent('meta_description', $defaultDescription));
        $metaImage = trim($__env->yieldContent('og_image', $defaultImage ?? ''));

        $businessSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $siteSetting->studio_name,
            'url' => url('/'),
            'telephone' => $siteSetting->contact_phone,
            'email' => $siteSetting->contact_email,
            'address' => $siteSetting->address,
            'image' => $defaultImage,
            'sameAs' => array_values(array_filter([
                $siteSetting->instagram_url,
                $siteSetting->youtube_url,
                $siteSetting->facebook_url,
                $siteSetting->tiktok_url,
                $siteSetting->x_url,
            ], 'filled')),
        ], 'filled');
    @endphp
    <meta name="description" content="@yield('meta_description', $defaultDescription)">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{!! $metaTitle !!}">
    <meta property="og:description" content="{!! $metaDescription !!}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($metaImage !== '')
        <meta property="og:image" content="{!! $metaImage !!}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{!! $metaTitle !!}">
    <meta name="twitter:description" content="{!! $metaDescription !!}">
    @if($metaImage !== '')
        <meta name="twitter:image" content="{!! $metaImage !!}">
    @endif

    <script type="application/ld+json">{!! json_encode($businessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

    @if(filled(config('services.google_analytics.id')))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json(config('services.google_analytics.id')));
        </script>
    @endif
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
    @include('components.whatsapp-float', ['message' => "Hi Waka Shots! I'd love to enquire about booking a photography session with you."])
    @stack('scripts')
</body>
</html>
