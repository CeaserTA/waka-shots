@extends('layouts.app')
@section('title', 'About')
@section('content')
<!-- PAGE HEADER -->
<section class="relative h-[56vh] min-h-[380px] flex items-end overflow-hidden">
  <div class="hero-bg absolute inset-0 bg-cover" style="background-image:url('https://images.unsplash.com/photo-1567531708788-4c44105d00ff?auto=format&fit=crop&w=1800&q=80'); background-position:center 30%;">
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.45) 0%, rgba(10,9,8,0.35) 40%, rgba(10,9,8,0.95) 100%);"></div>
  </div>
  <div class="relative z-[2] w-full px-[6vw] pb-16">
    <span class="eyebrow anim-fadeup font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Our Story</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.4rem,6vw,4.6rem)] leading-[1.08] mt-4" style="animation-delay:.15s;">About Waka Shots</h1>
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
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Who We Are</span>
      @if($siteSetting?->story_heading)
        <h2 class="font-serif text-[clamp(1.9rem,3.2vw,2.7rem)] my-4 mb-6">{{ $siteSetting->story_heading }}</h2>
      @else
        <h2 class="font-serif text-[clamp(1.9rem,3.2vw,2.7rem)] my-4 mb-6">More than photographs.<br>Moments with meaning.</h2>
      @endif
      @if($siteSetting?->story_text)
        @foreach(preg_split('/\n\s*\n/', trim($siteSetting->story_text)) as $paragraph)
          <p class="text-ivory-dim font-light max-w-[520px] {{ $loop->last ? '' : 'mb-4.5' }}">{{ trim($paragraph) }}</p>
        @endforeach
      @else
        <p class="text-ivory-dim font-light max-w-[520px] mb-4.5">Waka Shots is a Kampala-based photography studio built around one idea: that the best images come from patience, not performance. We spend more time watching than directing, so what we deliver feels like memory, not a photoshoot.</p>
        <p class="text-ivory-dim font-light max-w-[520px] mb-4.5">From wedding mornings to boardroom portraits, every project is shaped around the people in front of the lens — their pace, their light, their story. We've carried that approach across weddings, portraits, events and brand campaigns throughout Uganda and beyond.</p>
      @endif
      <div class="flex gap-12 mt-10 pt-8 border-t border-line">
        <div><strong class="block font-serif text-3xl text-gold-bright font-normal">120+</strong><span class="text-xs tracking-wide uppercase text-silver-dim">Stories Told</span></div>
        <div><strong class="block font-serif text-3xl text-gold-bright font-normal">7</strong><span class="text-xs tracking-wide uppercase text-silver-dim">Years Behind the Lens</span></div>
        <div><strong class="block font-serif text-3xl text-gold-bright font-normal">5</strong><span class="text-xs tracking-wide uppercase text-silver-dim">Countries Shot In</span></div>
      </div>
    </div>
  </div>
</section>

<!-- MEET THE PHOTOGRAPHER -->
<section class="py-28 bg-charcoal">
  <div class="max-w-[1320px] mx-auto px-[6vw] grid grid-cols-1 md:grid-cols-[1.15fr_0.85fr] gap-12 md:gap-20 items-center">
    <div class="reveal">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Meet the Photographer</span>
      <h2 class="font-serif text-[clamp(1.9rem,3.2vw,2.7rem)] my-4 mb-6">{{ $siteSetting->photographer_heading ?: 'Behind every frame.' }}</h2>
      @if($siteSetting?->photographer_bio)
        <p class="text-ivory-dim font-light max-w-[520px]">{{ $siteSetting->photographer_bio }}</p>
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
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">What We Believe</span>
      <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5 max-w-[640px]">Four principles behind everything we shoot.</h2>
    </div>
    <div class="reveal grid grid-cols-1 md:grid-cols-2 gap-px bg-line border border-line">
      <div class="bg-black p-10">
        <div class="font-mono text-gold-dim text-sm tracking-wide mb-5">01</div>
        <h3 class="font-serif text-xl mb-3">Patience over performance</h3>
        <p class="text-ivory-dim font-light text-sm">We'd rather wait for the real moment than manufacture a fake one.</p>
      </div>
      <div class="bg-black p-10">
        <div class="font-mono text-gold-dim text-sm tracking-wide mb-5">02</div>
        <h3 class="font-serif text-xl mb-3">Light tells the truth</h3>
        <p class="text-ivory-dim font-light text-sm">We build every shoot around natural, honest light rather than forcing a look.</p>
      </div>
      <div class="bg-black p-10">
        <div class="font-mono text-gold-dim text-sm tracking-wide mb-5">03</div>
        <h3 class="font-serif text-xl mb-3">Story before spectacle</h3>
        <p class="text-ivory-dim font-light text-sm">A quiet, honest frame will always outlast a flashy one.</p>
      </div>
      <div class="bg-black p-10">
        <div class="font-mono text-gold-dim text-sm tracking-wide mb-5">04</div>
        <h3 class="font-serif text-xl mb-3">Care in the details</h3>
        <p class="text-ivory-dim font-light text-sm">From first email to final gallery, every step is handled with the same intention.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="text-center py-[130px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #151316;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Let's Work Together</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">Ready to create something unforgettable?</h2>
    <a href="{{ route('contact') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 inline-block">Start Your Enquiry</a>
  </div>
</section>
@endsection
