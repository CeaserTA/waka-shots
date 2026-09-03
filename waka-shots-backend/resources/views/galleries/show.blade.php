@extends('layouts.gallery')

@section('title', $gallery->client_name . ' · ' . $gallery->event_name)

@section('content')
<div class="mx-auto max-w-[1440px] px-5 py-8 sm:px-8 lg:px-12 lg:py-12">
    <header class="border-b border-line pb-10">
        <div class="flex items-center justify-between gap-5">
            <a href="{{ route('home') }}" class="font-script text-3xl leading-none text-gold-bright">{{ $siteSetting->studio_name ?? 'Waka Shots' }}</a>
            <span class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-silver-dim">Private gallery</span>
        </div>
        <div class="reveal mt-16 max-w-3xl">
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

    <section class="reveal flex flex-col gap-6 border-b border-line py-7 sm:flex-row sm:items-center sm:justify-between">
        <p class="max-w-xl text-sm leading-7 text-silver">This gallery is private. Please don't share this link publicly.</p>
        <div class="flex w-fit flex-col items-start gap-2 sm:items-end">
            <a href="{{ route('gallery.download-all', $gallery->access_token) }}" data-cursor="Download" class="inline-flex w-fit items-center gap-3 rounded-sm border border-gold bg-gold px-5 py-3 font-mono text-xs uppercase tracking-[0.12em] text-black transition hover:bg-gold-bright">
                <span aria-hidden="true">↓</span> Download All
            </a>
            <span class="font-mono text-xs text-silver-dim">{{ count($images) }} {{ \Illuminate\Support\Str::plural('photo', count($images)) }}</span>
        </div>
    </section>

    @if (count($images))
        <section class="columns-1 gap-4 py-10 sm:columns-2 lg:columns-3">
            @foreach ($images as $image)
                <figure class="gallery-photo reveal group relative mb-4 overflow-hidden rounded-sm border border-line break-inside-avoid" style="transition-delay:{{ min($loop->index * 40, 400) }}ms">
                    <button type="button" class="lightbox-trigger block w-full cursor-zoom-in text-left" data-cursor="View" data-full-image="{{ route('gallery.thumb', [$gallery->access_token, $image['id']]) }}" data-image-name="{{ $image['name'] }}" aria-label="View {{ $image['name'] }}">
                        <img src="{{ $image['thumbnailLink'] }}" alt="{{ $image['name'] }}" loading="lazy" class="block w-full h-auto" referrerpolicy="no-referrer">
                    </button>
                    <a href="{{ route('gallery.download', [$gallery->access_token, $image['id']]) }}" data-cursor="Download" class="gallery-icon absolute bottom-3 right-3 inline-flex h-10 w-10 items-center justify-center rounded-full text-ivory transition" aria-label="Download {{ $image['name'] }}" title="Download image">
                        <span aria-hidden="true">↓</span>
                    </a>
                </figure>
            @endforeach
        </section>
    @else
        <p class="py-20 text-center font-serif text-2xl text-ivory-dim">Your photos are being prepared.</p>
    @endif

    @if (! $testimonial)
    <section class="border-t border-line py-12" aria-labelledby="testimonial-heading">
        <div class="reveal mx-auto max-w-2xl">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-gold">Your feedback matters</p>
            <h2 id="testimonial-heading" class="mt-3 font-serif text-3xl font-normal text-ivory">Loved your photos? Leave a review.</h2>

            @if (session('error'))
                <p class="mt-5 border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</p>
            @endif

            @if ($testimonial?->status === 'pending')
                <p class="mt-6 text-sm leading-7 text-silver">Your review has been received. Thank you.</p>
            @elseif ($testimonial?->status === 'approved')
                <div class="mt-6 border border-line p-5">
                    <p class="text-gold" aria-label="Rating: {{ $testimonial->rating }} out of 5">{{ str_repeat('★', $testimonial->rating) }}{{ str_repeat('☆', 5 - $testimonial->rating) }}</p>
                    <blockquote class="mt-3 font-serif text-xl leading-relaxed text-ivory">“{{ $testimonial->quote }}”</blockquote>
                    <p class="mt-4 text-sm text-silver">Thank you for sharing your experience.</p>
                </div>
            @else
                @if ($errors->any())
                    <div class="mt-5 border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                        <p>Please correct the highlighted fields and try again.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('gallery.testimonial', $gallery->access_token) }}" class="mt-7 space-y-6">
                    @csrf
                    <div>
                        <label for="rating" class="block font-mono text-xs uppercase tracking-[0.14em] text-silver">Rating</label>
                        <select id="rating" name="rating" required class="mt-2 w-full rounded-sm border border-line bg-transparent px-4 py-3 text-ivory focus:border-gold focus:outline-none">
                            <option value="">Choose a rating</option>
                            @for ($rating = 5; $rating >= 1; $rating--)
                                <option value="{{ $rating }}" @selected((string) old('rating') === (string) $rating)>{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="quote" class="block font-mono text-xs uppercase tracking-[0.14em] text-silver">Your review</label>
                        <textarea id="quote" name="quote" rows="5" minlength="10" maxlength="1000" required class="mt-2 w-full rounded-sm border border-line bg-transparent px-4 py-3 text-ivory placeholder:text-silver-dim focus:border-gold focus:outline-none" placeholder="Tell us what you loved about your experience.">{{ old('quote') }}</textarea>
                    </div>
                    <button type="submit" data-cursor="Submit" class="rounded-sm border border-gold bg-gold px-6 py-3 font-mono text-xs uppercase tracking-[0.12em] text-black transition hover:bg-gold-bright">Submit review</button>
                </form>
            @endif
        </div>
    </section>
    @endif

    <div class="reveal flex justify-center border-t border-line pt-10">
        <a href="{{ route('home') }}" data-cursor="Home" class="inline-block rounded-sm border border-gold bg-gold px-7 py-4 text-xs uppercase tracking-[0.14em] text-black transition-all duration-400 hover:-translate-y-0.5 hover:bg-gold-bright">
            Back to {{ $siteSetting->studio_name ?? 'Waka Shots' }}
        </a>
    </div>
</div>

@if (session('success'))
    <div id="review-success-toast" class="fixed inset-x-5 top-5 z-40 mx-auto flex max-w-md items-center justify-between gap-5 border border-gold/60 bg-[#151316] px-5 py-4 text-sm text-ivory shadow-2xl sm:inset-x-auto sm:right-6 sm:left-auto" role="status" aria-live="polite">
        <span>{{ session('success') }}</span>
        <button type="button" class="text-xl leading-none text-silver transition hover:text-gold" aria-label="Dismiss message">×</button>
    </div>
@endif

<!-- LIGHTBOX — WebGL Morph Slider viewer, same as the public portfolio -->
<div class="lightbox" id="galleryLightbox" aria-hidden="true">
  <button class="lightbox-close" id="galleryLightboxClose" aria-label="Close">&times;</button>
  <div class="lightbox-content">
    <div class="morph-slider">
      <div class="morph-slider-stage" id="galleryMorphStage" role="group" aria-roledescription="carousel" aria-label="Photo viewer" tabindex="0"></div>
      <div class="morph-slider-caption" id="galleryMorphCaption" aria-live="polite"></div>
      <div class="morph-slider-controls">
        <button type="button" class="morph-slider-btn" id="galleryMorphPrev" aria-label="Previous image">&larr;</button>
        <button type="button" class="morph-slider-btn" id="galleryMorphNext" aria-label="Next image">&rarr;</button>
      </div>
      <div class="morph-slider-indicators" id="galleryMorphIndicators" role="tablist" aria-label="Slides"></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        // Lightbox/slider behaviour now lives in resources/js/gallery-lightbox.js
        // (bundled via app.js) so it can share the MorphSlider engine with the
        // public portfolio page.
        const reviewToast = document.getElementById('review-success-toast');
        if (reviewToast) {
            const dismissReviewToast = () => reviewToast.remove();
            reviewToast.querySelector('button').addEventListener('click', dismissReviewToast);
            window.setTimeout(dismissReviewToast, 5000);
        }
    })();
</script>
@endpush
