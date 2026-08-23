@extends('layouts.app')
@section('title', 'Films')
@section('content')
<!-- PAGE HEADER -->
<section class="relative h-[56vh] min-h-[380px] flex items-end overflow-hidden">
  <div class="hero-bg absolute inset-0 bg-cover" style="background-image:url('https://images.unsplash.com/photo-1608009232260-9b527a5bb9bd?auto=format&fit=crop&w=1800&q=80'); background-position:center 25%;">
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.5) 0%, rgba(10,9,8,0.4) 40%, rgba(10,9,8,0.96) 100%);"></div>
  </div>
  <div class="relative z-[2] w-full px-[6vw] pb-16">
    <span class="eyebrow anim-fadeup font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">On the Channel</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.4rem,6vw,4.6rem)] leading-[1.08] mt-4" style="animation-delay:.15s;">Films</h1>
  </div>
</section>

<!-- INTRO -->
<section class="pt-24 pb-10">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <p class="reveal text-ivory-dim font-light text-lg max-w-[640px]">Beyond the still frame — highlight reels, behind-the-scenes footage and full ceremony films from our YouTube channel. Press play to watch right here.</p>
  </div>
</section>

<!-- VIDEO GRID -->
<section class="pb-32">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    @if($films->isEmpty())
    <div class="reveal grid grid-cols-1 md:grid-cols-2 gap-8">

      <div>
        <div class="video-card" data-video-id="366ooN49spY" data-cursor="Play">
          <img src="https://img.youtube.com/vi/366ooN49spY/hqdefault.jpg" alt="Wedding highlight film thumbnail">
          <div class="play-btn"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
          <div class="video-label">Wedding Film · Highlight Reel</div>
        </div>
        <p class="mt-4 text-ivory-dim font-light text-sm">A same-day edit capturing the emotion of a full Kampala wedding, start to finish.</p>
      </div>

      <div>
        <div class="video-card" data-video-id="366ooN49spY" data-cursor="Play">
          <img src="https://img.youtube.com/vi/366ooN49spY/hqdefault.jpg" alt="Behind the scenes thumbnail">
          <div class="play-btn"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
          <div class="video-label">Behind the Scenes · Studio</div>
        </div>
        <p class="mt-4 text-ivory-dim font-light text-sm">A day in the studio — how we plan, light and shoot a portrait session.</p>
      </div>

      <div>
        <div class="video-card" data-video-id="366ooN49spY" data-cursor="Play">
          <img src="https://img.youtube.com/vi/366ooN49spY/hqdefault.jpg" alt="Graduation film thumbnail">
          <div class="play-btn"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
          <div class="video-label">Graduation Day · Class of 2026</div>
        </div>
        <p class="mt-4 text-ivory-dim font-light text-sm">Coverage from a graduation ceremony, from the procession to family portraits.</p>
      </div>

      <div>
        <div class="video-card" data-video-id="366ooN49spY" data-cursor="Play">
          <img src="https://img.youtube.com/vi/366ooN49spY/hqdefault.jpg" alt="Brand campaign film thumbnail">
          <div class="play-btn"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
          <div class="video-label">Brand Campaign · Behind the Lens</div>
        </div>
        <p class="mt-4 text-ivory-dim font-light text-sm">On set for a commercial shoot — from concept to final delivery.</p>
      </div>

    </div>
    @else
    <div class="reveal grid grid-cols-1 md:grid-cols-2 gap-8">
      @foreach($films as $film)
        <div>
          <div class="video-card" data-video-id="{{ $film->youtube_id }}" data-cursor="Play">
            <img src="https://img.youtube.com/vi/{{ $film->youtube_id }}/hqdefault.jpg" alt="{{ $film->category->name }} film thumbnail">
            <div class="play-btn"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
            <div class="video-label">{{ $film->category->name }} film</div>
          </div>
          <p class="mt-4 text-ivory-dim font-light text-sm">A Waka Shots film from {{ $film->category->name }}.</p>
        </div>
      @endforeach
    </div>
    @endif
  </div>
</section>

<!-- SUBSCRIBE CTA -->
<section class="text-center py-[130px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #151316;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Watch More</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">More films live on our YouTube channel.</h2>
    <a href="#" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 inline-block">Subscribe on YouTube</a>
  </div>
</section>
@endsection
