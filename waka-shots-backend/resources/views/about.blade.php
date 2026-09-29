@extends('layouts.app')
@section('title', $siteSetting->pageTitle('About'))
@section('meta_description', $siteSetting->content('about.meta_description'))
@section('content')
<!-- PAGE HEADER -->
<section class="relative h-[56vh] min-h-[380px] flex items-end overflow-hidden">
  <div class="hero-bg absolute inset-0 bg-cover" style="background-image:url('{{ $siteSetting->contentImageUrl('about.hero_image') }}'); background-position:center 30%;">
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.45) 0%, rgba(10,9,8,0.35) 40%, rgba(10,9,8,0.95) 100%);"></div>
  </div>
  <div class="relative z-[2] w-full px-[6vw] pb-16">
    <span class="eyebrow anim-fadeup font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">{{ $siteSetting->content('about.hero_eyebrow') }}</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.4rem,6vw,4.6rem)] leading-[1.08] mt-4" style="animation-delay:.15s;">{{ $siteSetting->content('about.hero_heading') }}</h1>
  </div>
</section>

<!-- STORY -->
<section class="py-28">
  <div class="max-w-[1320px] mx-auto px-[6vw] grid grid-cols-1 md:grid-cols-[0.85fr_1.15fr] gap-12 md:gap-20 items-center">
    <div class="reveal relative aspect-[4/5] overflow-hidden rounded-sm">
      <div class="absolute -top-3.5 -left-3.5 w-[70px] h-[70px] border-t border-l border-gold z-[2]"></div>
      <div class="absolute -bottom-3.5 -right-3.5 w-[70px] h-[70px] border-b border-r border-gold z-[2]"></div>
      <img src="{{ $siteSetting->imageUrl($siteSetting->story_image) ?? 'https://images.unsplash.com/photo-1649532349871-b5b10b5ab9c4?auto=format&fit=crop&w=900&q=80' }}" alt="Waka Shots photographer at work" class="w-full h-full object-cover saturate-90 brightness-95">
    </div>
    <div class="reveal">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">{{ $siteSetting->content('about.story_eyebrow') }}</span>
      @if($siteSetting?->story_heading)
        <h2 class="font-serif text-[clamp(1.9rem,3.2vw,2.7rem)] my-4 mb-6">{{ $siteSetting->story_heading }}</h2>
      @else
        <h2 class="font-serif text-[clamp(1.9rem,3.2vw,2.7rem)] my-4 mb-6">More than photographs.<br>Moments with meaning.</h2>
      @endif
      @if($storyParagraphs = \App\Models\SiteSetting::paragraphs($siteSetting?->story_text))
        @foreach($storyParagraphs as $paragraph)
          <p class="text-ivory-dim font-light max-w-[520px] {{ $loop->last ? '' : 'mb-4.5' }}">{{ $paragraph }}</p>
        @endforeach
      @else
        <p class="text-ivory-dim font-light max-w-[520px] mb-4.5">Waka Shots is a Kampala-based photography studio built around one idea: that the best images come from patience, not performance. We spend more time watching than directing, so what we deliver feels like memory, not a photoshoot.</p>
        <p class="text-ivory-dim font-light max-w-[520px] mb-4.5">From wedding mornings to boardroom portraits, every project is shaped around the people in front of the lens — their pace, their light, their story. We've carried that approach across weddings, portraits, events and brand campaigns throughout Uganda and beyond.</p>
      @endif
      @include('components.stats')
    </div>
  </div>
</section>

<!-- MEET THE PHOTOGRAPHER -->
<section class="py-28 bg-charcoal">
  <div class="max-w-[1320px] mx-auto px-[6vw] grid grid-cols-1 md:grid-cols-[1.15fr_0.85fr] gap-12 md:gap-20 items-center">
    <div class="reveal">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Meet the Photographer</span>
      <h2 class="font-serif text-[clamp(1.9rem,3.2vw,2.7rem)] my-4 mb-6">{{ $siteSetting->photographer_heading ?: 'Behind every frame.' }}</h2>
      @if($bioParagraphs = \App\Models\SiteSetting::paragraphs($siteSetting?->photographer_bio))
        @foreach($bioParagraphs as $paragraph)
          <p class="text-ivory-dim font-light max-w-[520px] {{ $loop->last ? '' : 'mb-4.5' }}">{{ $paragraph }}</p>
        @endforeach
      @else
        <p class="text-ivory-dim font-light max-w-[520px] mb-4.5">Waka Shots was founded on the belief that photography should feel like collaboration, not direction. What started as a small wedding photography practice in Kampala has grown into a full studio working across weddings, portraiture and commercial work — but the approach has stayed the same: show up early, listen closely, and let the moment lead.</p>
        <p class="text-ivory-dim font-light max-w-[520px]">Every project, big or small, gets the same attention to light, timing and story.</p>
      @endif
    </div>
    <div class="reveal relative aspect-[4/5] overflow-hidden rounded-sm">
      <img src="{{ $siteSetting->imageUrl($siteSetting->photographer_image) ?? 'https://images.unsplash.com/photo-1565884280295-98eb83e41c65?auto=format&fit=crop&w=900&q=80' }}" alt="Portrait of the photographer" class="w-full h-full object-cover saturate-90 brightness-95">
    </div>
  </div>
</section>

<!-- VALUES -->
<section class="py-28">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal mb-16">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">{{ $siteSetting->content('about.values_eyebrow') }}</span>
      <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5 max-w-[640px]">{{ $siteSetting->content('about.values_heading') }}</h2>
    </div>
    <div class="reveal grid grid-cols-1 md:grid-cols-2 gap-px bg-line border border-line">
      @foreach($siteSetting->content('about.values') as $value)
        <div class="bg-black p-10">
          <div class="font-mono text-gold-dim text-sm tracking-wide mb-5">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
          <h3 class="font-serif text-xl mb-3">{{ $value['title'] ?? '' }}</h3>
          <p class="text-ivory-dim font-light text-sm">{{ $value['description'] ?? '' }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- CTA -->
<section class="text-center py-[130px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #151316;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">{{ $siteSetting->content('about.cta_eyebrow') }}</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">{{ $siteSetting->content('about.cta_heading') }}</h2>
    <a href="{{ route('contact') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 inline-block">Start Your Enquiry</a>
  </div>
</section>
@endsection
