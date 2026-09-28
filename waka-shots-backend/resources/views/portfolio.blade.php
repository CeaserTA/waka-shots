@extends('layouts.app')
@section('title', 'Portfolio — Waka Shots Photography')
@section('meta_description', 'Selected work from Waka Shots: weddings, introduction ceremonies, portraits, graduations and brand campaigns photographed in Kampala and across Uganda.')
@section('content')
<!-- PAGE HEADER -->
<section class="relative h-[56vh] min-h-[380px] flex items-end overflow-hidden">
  <div class="hero-bg absolute inset-0 bg-cover" style="background-image:url('{{ $siteSetting->imageUrl($siteSetting->portfolio_hero_image) ?? 'https://images.unsplash.com/photo-1696962678565-bee84e6b9cb6?auto=format&fit=crop&w=1800&q=80' }}'); background-position:center 25%;">
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.45) 0%, rgba(10,9,8,0.35) 40%, rgba(10,9,8,0.95) 100%);"></div>
  </div>
  <div class="relative z-[2] w-full px-[6vw] pb-16">
    <span class="eyebrow anim-fadeup font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">{{ $siteSetting->portfolio_hero_eyebrow ?: 'Our Work' }}</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.4rem,6vw,4.6rem)] leading-[1.08] mt-4" style="animation-delay:.15s;">{{ $siteSetting->portfolio_hero_heading ?: 'Portfolio' }}</h1>
  </div>
</section>

<!-- FILTERS -->
<section class="pt-16 pb-6">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal flex flex-wrap gap-3 border-b border-line pb-10" role="group" aria-label="Filter portfolio by category">
      <button type="button" class="filter-btn active font-mono text-xs tracking-[0.14em] uppercase px-5 py-2.5 border border-line-strong rounded-sm text-ivory-dim hover:text-gold-bright" data-filter="all" aria-pressed="true">All Work</button>
      @foreach($categories as $category)
        <button type="button" class="filter-btn font-mono text-xs tracking-[0.14em] uppercase px-5 py-2.5 border border-line-strong rounded-sm text-ivory-dim hover:text-gold-bright" data-filter="{{ $category->slug }}" aria-pressed="false">{{ $category->name }}</button>
      @endforeach
    </div>
  </div>
</section>

<!-- GALLERY WALL -->
@if($categories->flatMap->portfolioItems->isEmpty())
<section class="pb-32">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <p class="reveal text-center text-ivory-dim font-light">No portfolio items have been published yet.</p>
  </div>
</section>
@else
<section class="pb-32">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal columns-2 md:columns-3 gap-5 md:gap-9">
      @foreach($categories as $category)
        @foreach($category->portfolioItems as $item)
          {{-- A real button, so the photo can be opened from the keyboard. Inner
               elements are spans: a button may only contain phrasing content. --}}
          <button type="button" class="gallery-item group relative block w-full p-0 text-left overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gold" data-category="{{ $category->slug }}" data-full="{{ $item->lightboxUrl() }}" data-caption="{{ $item->displayCaption() }}" aria-haspopup="dialog" aria-label="View photo: {{ $item->displayAlt() }}">
            {{-- sizes mirrors the column layout: 2 columns (20px gap) on phones, 3 (36px gaps) from md, capped by the 1320px container; 6vw side padding throughout. --}}
            <x-portfolio-image :item="$item" sizes="(min-width: 1320px) calc((1320px - 12vw - 72px) / 3), (min-width: 768px) calc((88vw - 72px) / 3), calc((88vw - 20px) / 2)" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100" />
            <span class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
              {{-- Untitled items caption with the category name, so skip the label to avoid repeating it. --}}
              @if($item->hasTitle())
                <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">{{ $category->name }}</span>
              @endif
              <span class="block font-serif text-lg mb-2">{{ $item->displayCaption() }}</span>
            </span>
          </button>
        @endforeach
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- CTA -->
<section class="text-center py-[130px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #151316;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Like What You See?</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">Let's plan your own session.</h2>
    <a href="{{ route('contact') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 inline-block">Book a Session</a>
  </div>
</section>

<!-- LIGHTBOX — WebGL Morph Slider viewer (reactbits.dev-inspired) -->
<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Portfolio image viewer" aria-hidden="true">
  <button class="lightbox-close" id="lightboxClose" aria-label="Close">&times;</button>
  <div class="lightbox-content">
    <div class="morph-slider">
      <div class="morph-slider-stage" id="morphSliderStage" role="group" aria-roledescription="carousel" aria-label="Portfolio image viewer" tabindex="0"></div>
      <div class="morph-slider-caption" id="morphSliderCaption" aria-live="polite"></div>
      <div class="morph-slider-controls">
        <button type="button" class="morph-slider-btn" id="morphSliderPrev" aria-label="Previous image">&larr;</button>
        <button type="button" class="morph-slider-btn" id="morphSliderNext" aria-label="Next image">&rarr;</button>
      </div>
      <div class="morph-slider-indicators" id="morphSliderIndicators" role="group" aria-label="Choose image"></div>
    </div>
  </div>
</div>
@endsection
