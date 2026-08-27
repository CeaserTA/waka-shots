@extends('layouts.app')
@section('title', 'Portfolio')
@section('content')
<!-- PAGE HEADER -->
<section class="relative h-[56vh] min-h-[380px] flex items-end overflow-hidden">
  <div class="hero-bg absolute inset-0 bg-cover" style="background-image:url('https://images.unsplash.com/photo-1696962678565-bee84e6b9cb6?auto=format&fit=crop&w=1800&q=80'); background-position:center 25%;">
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.45) 0%, rgba(10,9,8,0.35) 40%, rgba(10,9,8,0.95) 100%);"></div>
  </div>
  <div class="relative z-[2] w-full px-[6vw] pb-16">
    <span class="eyebrow anim-fadeup font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Our Work</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.4rem,6vw,4.6rem)] leading-[1.08] mt-4" style="animation-delay:.15s;">Portfolio</h1>
  </div>
</section>

<!-- FILTERS -->
<section class="pt-16 pb-6">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal flex flex-wrap gap-3 border-b border-line pb-10">
      <button class="filter-btn active font-mono text-xs tracking-[0.14em] uppercase px-5 py-2.5 border border-line-strong rounded-sm text-ivory-dim hover:text-gold-bright" data-filter="all">All Work</button>
      @foreach($categories as $category)
        <button class="filter-btn font-mono text-xs tracking-[0.14em] uppercase px-5 py-2.5 border border-line-strong rounded-sm text-ivory-dim hover:text-gold-bright" data-filter="{{ $category->slug }}">{{ $category->name }}</button>
      @endforeach
    </div>
  </div>
</section>

<!-- GALLERY WALL -->
@if($categories->flatMap->portfolioItems->isEmpty())
<section class="pb-32">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal columns-2 md:columns-3 gap-5 md:gap-9">

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="weddings">
        <img src="https://images.unsplash.com/photo-1608009232260-9b527a5bb9bd?auto=format&fit=crop&w=900&q=80" alt="Wedding couple" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Wedding · Kampala</span>
          <div class="font-serif text-lg mb-2">A Kampala Wedding</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>85mm</span><span>f/1.8</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="portraits">
        <img src="https://images.unsplash.com/photo-1530785602389-07594beb8b73?auto=format&fit=crop&w=900&q=80" alt="Editorial portrait" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Portrait · Studio</span>
          <div class="font-serif text-lg mb-2">The Modern Gentleman</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>50mm</span><span>f/2</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="commercial">
        <img src="https://images.unsplash.com/photo-1578509566163-068acd11b8e7?auto=format&fit=crop&w=900&q=80" alt="Brand campaign shoot" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Commercial</span>
          <div class="font-serif text-lg mb-2">Brand Story — Amara</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>35mm</span><span>f/4</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="corporate">
        <img src="https://images.unsplash.com/photo-1617244147299-5ef406921c35?auto=format&fit=crop&w=900&q=80" alt="Corporate portrait" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Corporate</span>
          <div class="font-serif text-lg mb-2">Leadership Series</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>70mm</span><span>f/2.8</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="fashion">
        <img src="https://images.unsplash.com/photo-1625646741211-711bdd65c570?auto=format&fit=crop&w=900&q=80" alt="Fashion editorial" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Fashion · Editorial</span>
          <div class="font-serif text-lg mb-2">Kitenge Reimagined</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>135mm</span><span>f/2</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="events">
        <img src="https://images.unsplash.com/photo-1660675133902-acd1b057f75d?auto=format&fit=crop&w=900&q=80" alt="Cultural celebration event" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Event · Kampala</span>
          <div class="font-serif text-lg mb-2">The Product Launch</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>24mm</span><span>f/2.8</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="weddings">
        <img src="https://images.unsplash.com/photo-1633150747731-c945ec51b663?auto=format&fit=crop&w=900&q=80" alt="Wedding reception" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Wedding · Reception</span>
          <div class="font-serif text-lg mb-2">First Dance, Entebbe</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>35mm</span><span>f/1.4</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="portraits">
        <img src="https://images.unsplash.com/photo-1568782517100-09bf22d88c2d?auto=format&fit=crop&w=900&q=80" alt="Personal branding portrait" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Portrait · Personal</span>
          <div class="font-serif text-lg mb-2">Michael, Founder</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>85mm</span><span>f/1.8</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="corporate">
        <img src="https://images.unsplash.com/photo-1495603889488-42d1d66e5523?auto=format&fit=crop&w=900&q=80" alt="Office corporate shoot" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Corporate · Office</span>
          <div class="font-serif text-lg mb-2">Team, Kampala HQ</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>50mm</span><span>f/4</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="graduation">
        <img src="https://images.unsplash.com/photo-1631131426242-0abfa7f209c2?auto=format&fit=crop&w=900&q=80" alt="Graduation portrait, Kampala" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Graduation · Kampala</span>
          <div class="font-serif text-lg mb-2">Class of 2026</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>85mm</span><span>f/2</span></div>
        </div>
      </div>

      <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="graduation">
        <img src="https://images.unsplash.com/photo-1719606545131-51daa664485d?auto=format&fit=crop&w=900&q=80" alt="Graduation portrait" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
        <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
          <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">Graduation · Studio</span>
          <div class="font-serif text-lg mb-2">Milestone Portrait</div>
          <div class="font-mono text-[0.62rem] text-silver flex gap-3 border-t border-gold/25 pt-2"><span>50mm</span><span>f/2.8</span></div>
        </div>
      </div>

    </div>
  </div>
</section>
@else
<section class="pb-32">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal columns-2 md:columns-3 gap-5 md:gap-9">
      @foreach($categories as $category)
        @foreach($category->portfolioItems as $item)
          <div class="gallery-item group relative overflow-hidden rounded-sm bg-panel cursor-pointer break-inside-avoid mb-5 md:mb-9" data-category="{{ $category->slug }}">
            <img src="{{ \Illuminate\Support\Str::startsWith($item->image_path, ['http://', 'https://']) ? $item->image_path : \Illuminate\Support\Facades\Storage::disk('r2')->url($item->image_path) }}" alt="{{ $item->title }}" class="w-full h-auto saturate-[.92] brightness-[.92] transition-transform duration-[1100ms] group-hover:scale-[1.06] group-hover:saturate-100 group-hover:brightness-100">
            <div class="absolute inset-0 flex flex-col justify-end p-5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background:linear-gradient(0deg, rgba(10,9,8,0.92) 0%, rgba(10,9,8,0) 55%);">
              <span class="font-mono text-[0.65rem] tracking-[0.16em] uppercase text-gold-bright mb-1">{{ $category->name }}</span>
              <div class="font-serif text-lg mb-2">{{ $item->title }}</div>
            </div>
          </div>
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
@endsection
