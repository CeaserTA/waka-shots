@extends('layouts.app')
@section('title', 'Waka Shots Photography — Stories, Beautifully Captured')
@section('content')
<!-- HERO -->
<section class="relative h-[100svh] min-h-[640px] flex items-end overflow-hidden">
  <div class="hero-bg absolute inset-0 bg-cover" style="background-image:url('https://images.unsplash.com/photo-1631131426242-0abfa7f209c2?auto=format&fit=crop&w=1800&q=80'); background-position:center 30%;">
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.35) 0%, rgba(10,9,8,0.25) 40%, rgba(10,9,8,0.92) 100%), linear-gradient(90deg, rgba(10,9,8,0.55) 0%, rgba(10,9,8,0) 40%);"></div>
  </div>
  <span class="float-tag" style="top:16%; left:8%; animation-delay:0s;">f/1.8</span>
  <span class="float-tag" style="top:28%; right:10%; animation-delay:1.4s;">85mm</span>
  <span class="float-tag" style="top:52%; left:5%; animation-delay:2.6s;">Kampala, UG</span>
  <span class="float-tag" style="top:65%; right:6%; animation-delay:0.8s;">Golden Hour</span>
  <span class="float-tag" style="top:12%; right:32%; animation-delay:3.4s;">35mm</span>
  <div class="relative z-[2] w-full px-[6vw] pb-[7vw] flex flex-col md:flex-row justify-between items-start md:items-end gap-10">
    <div class="max-w-[760px]">
      <span class="eyebrow anim-fadeup font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5" style="animation-delay:.4s;">Waka Shots Photography — Kampala, Uganda</span>
      <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.6rem,7vw,6rem)] leading-[1.08] my-4" style="animation-delay:.6s;">Stories,<br><em class="italic text-gold-bright font-light">beautifully</em> captured.</h1>
      <p class="anim-fadeup text-ivory-dim max-w-[440px] font-light" style="animation-delay:.85s;">We make timeless, intentional imagery for <span class="tagline-rotator text-gold-bright"><span class="tagline-active">weddings</span><span>portraits</span><span>graduations</span><span>brand stories</span></span> across East Africa — one honest frame at a time.</p>
    </div>
    <div class="anim-fadeup flex flex-row md:flex-col gap-3.5" style="animation-delay:1.05s;">
      <a href="{{ route('portfolio') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm text-center bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 whitespace-nowrap">View Portfolio</a>
      <a href="{{ route('contact') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm text-center border border-line-strong text-ivory hover:border-gold hover:text-gold-bright hover:-translate-y-0.5 transition-all duration-400 whitespace-nowrap">Book a Session</a>
    </div>
  </div>
  <div class="hidden md:flex absolute bottom-6 left-[6vw] z-[2] items-center gap-3 font-mono text-[0.68rem] tracking-[0.2em] uppercase text-silver-dim">
    <span>Scroll</span><span class="scroll-line relative w-px h-[34px] bg-line-strong overflow-hidden"></span>
  </div>
</section>

<!-- MARQUEE -->
<!-- <div class="marquee-fade border-t border-b border-line bg-charcoal overflow-hidden py-5" style="--fade-color:#151316;">
  <div class="marquee-track flex whitespace-nowrap">
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Weddings</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Portraits</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Events</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Corporate</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Fashion</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Lifestyle</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Commercial</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Weddings</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Portraits</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Events</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Corporate</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Fashion</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Lifestyle</span>
    <span class="font-serif italic text-2xl text-silver-dim px-8 flex items-center gap-8 after:content-['◆'] after:not-italic after:text-[0.6rem] after:text-gold-dim">Commercial</span>
  </div>
</div> -->

 <!-- THE CONTACT SHEET — scattered parallax collage, not a grid -->
<!-- <section id="contactSheet" class="scatter-section py-24 md:py-0 md:min-h-[1000px] overflow-hidden">
  <div class="max-w-[1320px] mx-auto px-[6vw] relative md:h-full md:min-h-[1000px]">

    <div class="relative z-10 pt-4 md:pt-20 mb-10 md:mb-0 md:max-w-[420px]">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">The Contact Sheet</span>
      <h2 class="mask-reveal font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5"><span class="mask-inner">Every kind of story we're trusted to tell.</span></h2>
    </div> -->

     <!-- Desktop: scattered, overlapping, parallax --> 
    <!-- <div class="hidden md:block">
      <div class="scatter-photo absolute" style="top:4%; left:34%; width:210px; height:270px;" data-depth="0.16" data-rot="4">
        <img src="https://images.unsplash.com/photo-1512060847456-85a2a1bf8b25?auto=format&fit=crop&w=500&q=80" alt="Wedding couple">
        <div class="scatter-caption">Wedding</div>
      </div>
      <div class="scatter-photo absolute" style="top:2%; left:60%; width:170px; height:210px;" data-depth="0.08" data-rot="-3">
        <img src="https://images.unsplash.com/photo-1530785602389-07594beb8b73?auto=format&fit=crop&w=500&q=80" alt="Portrait">
        <div class="scatter-caption">Portrait · Studio</div>
      </div>
      <div class="scatter-photo absolute" style="top:30%; left:8%; width:200px; height:150px;" data-depth="0.22" data-rot="6">
        <img src="https://images.unsplash.com/photo-1495603889488-42d1d66e5523?auto=format&fit=crop&w=500&q=80" alt="Corporate portrait">
        <div class="scatter-caption">Corporate</div>
      </div>
      <div class="scatter-photo absolute" style="top:38%; left:40%; width:260px; height:320px;" data-depth="0.14" data-rot="-5">
        <img src="https://images.unsplash.com/photo-1696962678565-bee84e6b9cb6?auto=format&fit=crop&w=600&q=80" alt="Fashion editorial">
        <div class="scatter-caption">Fashion · Editorial</div>
      </div>
      <div class="scatter-photo absolute" style="top:34%; left:74%; width:190px; height:240px;" data-depth="0.10" data-rot="3">
        <img src="https://images.unsplash.com/photo-1631131426242-0abfa7f209c2?auto=format&fit=crop&w=500&q=80" alt="Graduation portrait, Kampala">
        <div class="scatter-caption">Graduation · Kampala</div>
      </div>
      <div class="scatter-photo absolute" style="top:66%; left:20%; width:210px; height:160px;" data-depth="0.20" data-rot="-4">
        <img src="https://images.unsplash.com/photo-1633150747731-c945ec51b663?auto=format&fit=crop&w=500&q=80" alt="Wedding reception">
        <div class="scatter-caption">Wedding · Reception</div>
      </div>
      <div class="scatter-photo absolute" style="top:60%; left:58%; width:180px; height:220px;" data-depth="0.12" data-rot="5">
        <img src="https://images.unsplash.com/photo-1527201987695-67c06571957e?auto=format&fit=crop&w=500&q=80" alt="Personal branding portrait">
        <div class="scatter-caption">Portrait · Personal</div>
      </div>
    </div> -->

    <!-- Mobile fallback: simple offset stack, no absolute positioning -->
    <!-- <div class="grid grid-cols-2 gap-3 md:hidden">
      <img src="https://images.unsplash.com/photo-1512060847456-85a2a1bf8b25?auto=format&fit=crop&w=500&q=80" alt="Wedding couple" class="rounded-sm object-cover w-full aspect-[3/4] border border-line-strong -rotate-2">
      <img src="https://images.unsplash.com/photo-1530785602389-07594beb8b73?auto=format&fit=crop&w=500&q=80" alt="Portrait" class="rounded-sm object-cover w-full aspect-[3/4] border border-line-strong rotate-2 mt-6">
      <img src="https://images.unsplash.com/photo-1631131426242-0abfa7f209c2?auto=format&fit=crop&w=500&q=80" alt="Graduation portrait, Kampala" class="rounded-sm object-cover w-full aspect-[3/4] border border-line-strong rotate-1">
      <img src="https://images.unsplash.com/photo-1495603889488-42d1d66e5523?auto=format&fit=crop&w=500&q=80" alt="Corporate portrait" class="rounded-sm object-cover w-full aspect-[3/4] border border-line-strong -rotate-1 mt-4">
    </div>
  </div>
</section>  -->


<!-- SELECTED WORK — scroll-driven rotating-wheel orbit filmstrip (zeustheagency.com-inspired) -->
<section id="filmstripWrapper" class="relative h-[190vh] md:h-[280vh]">
  <div class="orbit-stage sticky top-0 h-[78vh] md:h-screen overflow-hidden flex flex-col justify-center">
    <div class="max-w-[1320px] mx-auto px-[6vw] w-full mb-5 md:mb-10 relative z-10">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Selected Work</span>
      <h2 class="mask-reveal font-serif text-[clamp(1.8rem,3.2vw,2.7rem)] mt-3.5"><span class="mask-inner"></span></h2>
    </div>
    <div id="filmstripTrack" class="filmstrip-track pl-[6vw]">
      @foreach($portfolioItems as $item)
        <div class="filmstrip-item w-[78vw] md:w-[34vw] aspect-[4/3] rounded-sm overflow-hidden border border-line-strong">
          <img src="{{ $item->image_path }}" alt="{{ $item->title }}">
        </div>
      @endforeach
    </div>
  </div>
</section>

<div class="pt-2 pb-4 md:py-4 flex justify-center">
  <a href="{{ route('portfolio') }}" class="reveal text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm border border-line-strong text-ivory hover:border-gold hover:text-gold-bright hover:-translate-y-0.5 transition-all duration-400">View Full Portfolio →</a>
</div>

<!-- ABOUT TEASER -->
<section class="bg-charcoal py-32">
  <div class="max-w-[1320px] mx-auto px-[6vw] grid grid-cols-1 md:grid-cols-[0.85fr_1.15fr] gap-12 md:gap-20 items-center">
    <div class="reveal relative aspect-[4/5] overflow-hidden rounded-sm">
      <div class="absolute -top-3.5 -left-3.5 w-[70px] h-[70px] border-t border-l border-gold z-[2]"></div>
      <div class="absolute -bottom-3.5 -right-3.5 w-[70px] h-[70px] border-b border-r border-gold z-[2]"></div>
      <img src="https://images.unsplash.com/photo-1649532349871-b5b10b5ab9c4?auto=format&fit=crop&w=900&q=80" alt="Waka Shots photographer at work" class="w-full h-full object-cover saturate-90 brightness-95">
    </div>
    <div class="reveal">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">About Waka Shots</span>
      <h2 class="font-serif text-[clamp(1.9rem,3.2vw,2.7rem)] my-4 mb-6">More than photographs.<br>Moments with meaning.</h2>
      <p class="text-ivory-dim font-light max-w-[520px] mb-6">Waka Shots is a Kampala-based photography studio built around one idea: that the best images come from patience, not performance. We spend more time watching than directing, so what we deliver feels like memory, not a photoshoot.</p>
      <a href="{{ route('about') }}" class="inline-block text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm border border-line-strong text-ivory hover:border-gold hover:text-gold-bright hover:-translate-y-0.5 transition-all duration-400">Our Story →</a>
      <div class="flex gap-12 mt-10 pt-8 border-t border-line">
        <div><strong class="block font-serif text-3xl text-gold-bright font-normal">120+</strong><span class="text-xs tracking-wide uppercase text-silver-dim">Stories Told</span></div>
        <div><strong class="block font-serif text-3xl text-gold-bright font-normal">7</strong><span class="text-xs tracking-wide uppercase text-silver-dim">Years Behind the Lens</span></div>
        <div><strong class="block font-serif text-3xl text-gold-bright font-normal">5</strong><span class="text-xs tracking-wide uppercase text-silver-dim">Countries Shot In</span></div>
      </div>
    </div>
  </div>
</section>

<!-- SERVICES TEASER -->
<section class="py-20">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal flex justify-between items-end gap-10 flex-wrap mb-10">
      <div>
        <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Services</span>
        <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5 max-w-[640px]">Five ways to work with us.</h2>
      </div>
      <a href="{{ route('services') }}" class="text-xs tracking-[0.14em] uppercase text-gold hover:text-gold-bright transition-colors pb-1.5">See All Services →</a>
    </div>
    <div class="reveal grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
      @foreach($featuredCategories as $category)
        <a href="{{ route('portfolio') }}?category={{ $category->slug }}" class="group spotlight-card bg-black hover:bg-panel transition-colors duration-500 p-10 flex flex-col min-h-[320px] border border-line -mt-px -ml-px">
          <div class="font-mono text-gold-dim text-sm tracking-wide mb-8">Waka Shots — {{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}</div>
          <h3 class="font-serif text-2xl mb-4 max-w-[220px]">{{ $category->name }}</h3>
          <p class="text-ivory-dim font-light text-sm flex-grow">{{ $category->portfolioItems->count() }} stories in this collection.</p>
          <span class="mt-4 text-xs tracking-[0.14em] uppercase text-gold inline-flex items-center gap-2 w-fit border-b border-transparent group-hover:border-gold transition-all">View Work &rarr;</span>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- PARTNERS — cinematic full-bleed band, bonjour.paris-inspired -->
<section class="partners-band">
  <div class="partners-band-bg" style="background-image:url('https://images.unsplash.com/photo-1660675133902-acd1b057f75d?auto=format&fit=crop&w=1800&q=80');"></div>
  <div class="partners-band-overlay"></div>
  <div class="reveal relative z-10 text-center px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Trusted By</span>
    <a href="{{ route('contact') }}" data-cursor="Partner" class="group mt-5 inline-flex items-center gap-4 font-serif text-[clamp(2.2rem,5vw,4rem)] text-ivory hover:text-gold-bright transition-colors duration-500">
      Our Partners
      <span class="text-gold-bright text-3xl transition-transform duration-500 group-hover:translate-x-2">→</span>
    </a>
  </div>
</section>
<div class="marquee-fade bg-black border-b border-line overflow-hidden py-6" style="--fade-color:#0a0908;">
  <div class="marquee-track flex whitespace-nowrap">
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Amara Foods</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Kiboko Hotels</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Nyati Bank</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Savanna Airlines</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Equator Media Group</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Zawadi Events</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Kampala Business Council</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Muzuri Fashion House</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Amara Foods</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Kiboko Hotels</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Nyati Bank</span>
    <span class="font-serif text-lg text-ivory-dim px-8 flex items-center gap-8 after:content-['◆'] after:text-[0.6rem] after:text-gold-dim">Savanna Airlines</span>
  </div>
</div>

<!-- TESTIMONIALS -->
<section class="py-20" style="background:radial-gradient(ellipse at top right, rgba(198,161,91,0.08), transparent 55%), #0a0908;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal mb-10">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Client Stories</span>
      <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5 max-w-[640px]">Told in their own words.</h2>
    </div>
    <div class="reveal grid grid-cols-1 md:grid-cols-3 gap-px bg-line border border-line">
      <div class="spotlight-card bg-black p-9 flex flex-col gap-5">
        <div class="text-gold text-sm tracking-widest">★★★★★</div>
        <blockquote class="font-serif italic text-lg leading-snug text-ivory">Waka Shots captured our day in a way we never imagined. Every photograph tells a story.</blockquote>
        <div class="flex items-center gap-3 mt-auto pt-4">
          <img src="https://images.unsplash.com/photo-1530785602389-07594beb8b73?auto=format&fit=crop&w=100&q=80" alt="Sarah" class="w-[42px] h-[42px] rounded-full object-cover saturate-90">
          <div><div class="text-sm text-ivory">Sarah &amp; Daniel</div><div class="text-xs text-silver-dim tracking-wide">Wedding, Kampala</div></div>
        </div>
      </div>
      <div class="spotlight-card bg-black p-9 flex flex-col gap-5">
        <div class="text-gold text-sm tracking-widest">★★★★★</div>
        <blockquote class="font-serif italic text-lg leading-snug text-ivory">Professional, patient and quietly brilliant. Our brand shoot looked better than we'd hoped for.</blockquote>
        <div class="flex items-center gap-3 mt-auto pt-4">
          <img src="https://images.unsplash.com/photo-1527201987695-67c06571957e?auto=format&fit=crop&w=100&q=80" alt="Grace" class="w-[42px] h-[42px] rounded-full object-cover saturate-90">
          <div><div class="text-sm text-ivory">Grace N.</div><div class="text-xs text-silver-dim tracking-wide">Founder, Amara Foods</div></div>
        </div>
      </div>
      <div class="spotlight-card bg-black p-9 flex flex-col gap-5">
        <div class="text-gold text-sm tracking-widest">★★★★★</div>
        <blockquote class="font-serif italic text-lg leading-snug text-ivory">My portrait session felt effortless. The final images actually look like me, just more confident.</blockquote>
        <div class="flex items-center gap-3 mt-auto pt-4">
          <img src="https://images.unsplash.com/photo-1565884280295-98eb83e41c65?auto=format&fit=crop&w=100&q=80" alt="Michael" class="w-[42px] h-[42px] rounded-full object-cover saturate-90">
          <div><div class="text-sm text-ivory">Michael K.</div><div class="text-xs text-silver-dim tracking-wide">Personal Branding</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNAL TEASER -->
<section class="bg-charcoal py-20">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal flex justify-between items-end gap-10 flex-wrap mb-10">
      <div>
        <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Journal</span>
        <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5 max-w-[640px]">Notes from behind the lens.</h2>
      </div>
      <a href="{{ route('journal') }}" class="text-xs tracking-[0.14em] uppercase text-gold hover:text-gold-bright transition-colors pb-1.5">Read the Journal →</a>
    </div>
    <div class="reveal grid grid-cols-1 md:grid-cols-3 gap-9">
      @foreach($portfolioItems as $item)
        <a href="{{ route('portfolio') }}" class="group block">
          <div class="overflow-hidden mb-5"><img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="w-full aspect-[4/3] object-cover saturate-90 brightness-95 transition-transform duration-700 group-hover:scale-105"></div>
          <span class="font-mono text-[0.68rem] tracking-[0.14em] uppercase text-gold mb-3 block">{{ $item->category->name }}</span>
          <h4 class="font-serif text-xl mb-2.5 leading-snug">{{ $item->title }}</h4>
          <span class="text-xs text-silver-dim">Featured work</span>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- CTA -->
<section class="text-center py-[150px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #151316;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Let's Talk</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">Ready to create something unforgettable?</h2>
    <div class="flex gap-4.5 justify-center flex-wrap">
      <a href="{{ route('contact') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400">Start Your Enquiry</a>
      <a href="tel:+256000000000" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm border border-line-strong text-ivory hover:border-gold hover:text-gold-bright hover:-translate-y-0.5 transition-all duration-400">Call the Studio</a>
    </div>
  </div>
</section>
@endsection
