@extends('layouts.gallery')

@section('title', $gallery->client_name . ' · ' . $gallery->event_name)

@section('content')
<div class="mx-auto max-w-[1440px] px-5 py-8 sm:px-8 lg:px-12 lg:py-12">
    <header class="border-b border-line pb-10">
        <div class="flex items-center justify-between gap-5">
            <a href="{{ route('home') }}" class="font-serif text-lg tracking-wide text-gold-bright">Waka Shots</a>
            <span class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-silver-dim">Private gallery</span>
        </div>
        <div class="mt-16 max-w-3xl">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-gold">{{ $gallery->event_name }}</p>
            <h1 class="mt-3 font-serif text-4xl font-normal leading-tight text-ivory sm:text-6xl">{{ $gallery->client_name }}</h1>
            <div class="mt-6 flex flex-wrap gap-x-8 gap-y-2 text-sm text-ivory-dim">
                <span>{{ $gallery->event_date->format('d M Y') }}</span>
                @if ($gallery->expires_at)
                    <span>Available until {{ $gallery->expires_at->format('d M Y') }}</span>
                @endif
            </div>
        </div>
    </header>

    <section class="flex flex-col gap-6 border-b border-line py-7 sm:flex-row sm:items-center sm:justify-between">
        <p class="max-w-xl text-sm leading-7 text-silver">This gallery is private. Please don't share this link publicly.</p>
        <a href="{{ route('gallery.download-all', $gallery->access_token) }}" class="inline-flex w-fit items-center gap-3 rounded-sm border border-gold bg-gold px-5 py-3 font-mono text-xs uppercase tracking-[0.12em] text-black transition hover:bg-gold-bright">
            <span aria-hidden="true">↓</span> Download All
        </a>
    </section>

    @if (count($images))
        <section class="grid grid-cols-1 gap-4 py-10 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($images as $image)
                <figure class="gallery-photo group relative overflow-hidden rounded-sm border border-line">
                    <button type="button" class="lightbox-trigger h-full w-full cursor-zoom-in text-left" data-full-image="{{ route('gallery.preview', [$gallery->access_token, $image['id']]) }}" data-image-name="{{ $image['name'] }}" aria-label="View {{ $image['name'] }}">
                        <img src="{{ $image['thumbnailLink'] }}" alt="{{ $image['name'] }}" loading="lazy" class="h-full w-full object-cover" referrerpolicy="no-referrer">
                    </button>
                    <a href="{{ route('gallery.download', [$gallery->access_token, $image['id']]) }}" class="gallery-icon absolute bottom-3 right-3 inline-flex h-10 w-10 items-center justify-center rounded-full text-ivory transition" aria-label="Download {{ $image['name'] }}" title="Download image">
                        <span aria-hidden="true">↓</span>
                    </a>
                </figure>
            @endforeach
        </section>
    @else
        <p class="py-20 text-center font-serif text-2xl text-ivory-dim">Your photos are being prepared.</p>
    @endif

    <div class="flex justify-center border-t border-line pt-10">
        <a href="{{ route('home') }}" class="inline-block rounded-sm border border-gold bg-gold px-7 py-4 text-xs uppercase tracking-[0.14em] text-black transition-all duration-400 hover:-translate-y-0.5 hover:bg-gold-bright">
            Back to Waka Shots
        </a>
    </div>
</div>

<div id="gallery-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/95 p-5" role="dialog" aria-modal="true" aria-label="Photo preview">
    <button type="button" id="gallery-lightbox-close" class="absolute right-5 top-5 inline-flex h-11 w-11 items-center justify-center rounded-full border border-line text-2xl text-ivory" aria-label="Close preview">×</button>
    <img id="gallery-lightbox-image" src="" alt="" class="max-h-[90vh] max-w-full object-contain">
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const lightbox = document.getElementById('gallery-lightbox');
        const lightboxImage = document.getElementById('gallery-lightbox-image');
        const close = () => { lightbox.classList.add('hidden'); lightbox.classList.remove('flex'); lightboxImage.src = ''; };
        document.querySelectorAll('.lightbox-trigger').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                lightboxImage.src = trigger.dataset.fullImage;
                lightboxImage.alt = trigger.dataset.imageName;
                lightbox.classList.remove('hidden');
                lightbox.classList.add('flex');
            });
        });
        document.getElementById('gallery-lightbox-close').addEventListener('click', close);
        lightbox.addEventListener('click', (event) => { if (event.target === lightbox) close(); });
        document.addEventListener('keydown', (event) => { if (event.key === 'Escape') close(); });
    })();
</script>
@endpush
