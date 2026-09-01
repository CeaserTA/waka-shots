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
    <div class="reveal grid grid-cols-1 md:grid-cols-2 gap-8">
      @forelse($films as $film)
        <div>
          <div class="video-card" data-video-id="{{ $film->youtube_id }}" data-cursor="Play">
            <img src="https://img.youtube.com/vi/{{ $film->youtube_id }}/hqdefault.jpg" alt="{{ $film->category->name }} film thumbnail">
            <div class="play-btn"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
            <div class="video-label">{{ $film->category->name }} film</div>
          </div>
          <p class="mt-4 text-ivory-dim font-light text-sm">A Waka Shots film from {{ $film->category->name }}.</p>
        </div>
      @empty
        <p class="text-ivory-dim font-light">No films posted yet — check back soon.</p>
      @endforelse
    </div>
  </div>
</section>

<!-- SUBSCRIBE CTA -->
<section class="text-center py-[130px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #151316;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Watch More</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">More films live on our YouTube channel.</h2>
    @if($siteSetting?->youtube_url)
      <a href="{{ $siteSetting->youtube_url }}" target="_blank" rel="noopener" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 inline-block">Subscribe on YouTube</a>
    @endif
  </div>
</section>
@endsection
