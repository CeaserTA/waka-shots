@extends('layouts.app')
@section('title', 'Services')
@section('content')
<!-- PAGE HEADER -->
<section class="relative h-[56vh] min-h-[380px] flex items-end overflow-hidden">
  <div class="hero-bg absolute inset-0 bg-cover" style="background-image:url('https://images.unsplash.com/photo-1565884280295-98eb83e41c65?auto=format&fit=crop&w=1800&q=80'); background-position:center 25%;">
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.45) 0%, rgba(10,9,8,0.35) 40%, rgba(10,9,8,0.95) 100%);"></div>
  </div>
  <div class="relative z-[2] w-full px-[6vw] pb-16">
    <span class="eyebrow anim-fadeup font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">What We Offer</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.4rem,6vw,4.6rem)] leading-[1.08] mt-4" style="animation-delay:.15s;">Services</h1>
  </div>
</section>

<!-- INTRO -->
<section class="pt-24 pb-10">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <p class="reveal text-ivory-dim font-light text-lg max-w-[640px]">Every project starts as a conversation, not a package. The tiers below are starting points — the scope, timeline and pricing are always shaped around what your story actually needs.</p>
  </div>
</section>

<!-- SUB-NAV -->
<section class="pb-4">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal flex flex-wrap gap-3">
      <a href="#weddings" class="pkg-nav-link">Weddings</a>
      <a href="#introduction" class="pkg-nav-link">Introduction</a>
      <a href="#portraits" class="pkg-nav-link">Portraits</a>
      <a href="#graduation" class="pkg-nav-link">Graduation</a>
      <a href="#brand" class="pkg-nav-link">Brand</a>
    </div>
  </div>
</section>

<!-- WEDDINGS -->
@if($services->isEmpty())
<section id="weddings" class="py-24 scroll-mt-24">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal mb-16 max-w-[640px]">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Weddings</span>
      <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5 mb-5">Three ways to document your day.</h2>
      <p class="text-ivory-dim font-light">For couples who want their day documented with emotion, elegance and intention — from first light to the last dance. Every tier can be adjusted to fit your venue, guest count and timeline.</p>
    </div>
    <div class="reveal grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 pt-4">
      <div class="package-card">
        <div class="pkg-tier">Silver</div>
        <span class="pkg-tier-sub">Wedding Package</span>
        <div class="pkg-price"><span>From</span>UGX 2,500,000</div>
        <ul>
          <li>1 lead photographer</li>
          <li>Full-day coverage (up to 8 hours)</li>
          <li>300+ professionally edited photos</li>
          <li>Private online gallery</li>
          <li>Print release for personal use</li>
        </ul>
        <a href="{{ route('contact') }}" class="text-center text-xs tracking-[0.14em] uppercase px-6 py-4 rounded-sm border border-line-strong text-ivory hover:border-gold hover:text-gold-bright transition-all duration-400">Choose Silver</a>
      </div>
      <div class="package-card is-featured">
        <span class="pkg-badge">Most Popular</span>
        <div class="pkg-tier">Gold</div>
        <span class="pkg-tier-sub">Wedding Package</span>
        <div class="pkg-price"><span>From</span>UGX 4,500,000</div>
        <ul>
          <li>Everything in Silver</li>
          <li>2nd photographer</li>
          <li>Pre-wedding engagement session</li>
          <li>500+ professionally edited photos</li>
          <li>Premium leather-bound album</li>
          <li>Drone aerial coverage</li>
        </ul>
        <a href="{{ route('contact') }}" class="text-center text-xs tracking-[0.14em] uppercase px-6 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright transition-all duration-400">Choose Gold</a>
      </div>
      <div class="package-card">
        <div class="pkg-tier">Platinum</div>
        <span class="pkg-tier-sub">Wedding Package</span>
        <div class="pkg-price"><span>From</span>UGX 7,500,000</div>
        <ul>
          <li>Everything in Gold</li>
          <li>Multi-day coverage (traditional + white wedding)</li>
          <li>Same-day edit highlight film</li>
          <li>Unlimited edited photos</li>
          <li>Luxury fine-art album (2 copies)</li>
          <li>Dedicated day-of coordinator liaison</li>
        </ul>
        <a href="{{ route('contact') }}" class="text-center text-xs tracking-[0.14em] uppercase px-6 py-4 rounded-sm border border-line-strong text-ivory hover:border-gold hover:text-gold-bright transition-all duration-400">Choose Platinum</a>
      </div>
    </div>
  </div>
</section>

<!-- INTRODUCTION (KUHINGIRA / KWANJULA) -->
<section id="introduction" class="py-24 bg-charcoal scroll-mt-24">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal mb-16 max-w-[640px]">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Introduction · Kuhingira / Kwanjula</span>
      <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5 mb-5">Honouring the traditional introduction.</h2>
      <p class="text-ivory-dim font-light">The introduction ceremony — known as Kuhingira or Kwanjula depending on tradition — carries its own colour, ritual and rhythm. We shoot it with the same care as the wedding day, tuned to its pace and customs.</p>
    </div>
    <div class="reveal grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 pt-4">
      <div class="package-card">
        <div class="pkg-tier">Silver</div>
        <span class="pkg-tier-sub">Introduction Package</span>
        <div class="pkg-price"><span>From</span>UGX 1,800,000</div>
        <ul>
          <li>1 lead photographer</li>
          <li>Half-day coverage (up to 5 hours)</li>
          <li>250+ professionally edited photos</li>
          <li>Private online gallery</li>
        </ul>
        <a href="{{ route('contact') }}" class="text-center text-xs tracking-[0.14em] uppercase px-6 py-4 rounded-sm border border-line-strong text-ivory hover:border-gold hover:text-gold-bright transition-all duration-400">Choose Silver</a>
      </div>
      <div class="package-card is-featured">
        <span class="pkg-badge">Most Popular</span>
        <div class="pkg-tier">Gold</div>
        <span class="pkg-tier-sub">Introduction Package</span>
        <div class="pkg-price"><span>From</span>UGX 3,200,000</div>
        <ul>
          <li>Everything in Silver</li>
          <li>2nd photographer</li>
          <li>Full-day coverage</li>
          <li>400+ professionally edited photos</li>
          <li>Printed photo album</li>
          <li>Drone aerial coverage</li>
        </ul>
        <a href="{{ route('contact') }}" class="text-center text-xs tracking-[0.14em] uppercase px-6 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright transition-all duration-400">Choose Gold</a>
      </div>
      <div class="package-card">
        <div class="pkg-tier">Platinum</div>
        <span class="pkg-tier-sub">Introduction Package</span>
        <div class="pkg-price"><span>From</span>UGX 5,000,000</div>
        <ul>
          <li>Everything in Gold</li>
          <li>Preparation (kukyala) coverage included</li>
          <li>Same-day highlight video</li>
          <li>Unlimited edited photos</li>
          <li>Luxury album (2 copies)</li>
          <li>Extended family portrait session</li>
        </ul>
        <a href="{{ route('contact') }}" class="text-center text-xs tracking-[0.14em] uppercase px-6 py-4 rounded-sm border border-line-strong text-ivory hover:border-gold hover:text-gold-bright transition-all duration-400">Choose Platinum</a>
      </div>
    </div>
  </div>
</section>

<!-- PORTRAIT EXPERIENCE -->
<section id="portraits" class="py-24 scroll-mt-24">
  <div class="max-w-[1320px] mx-auto px-[6vw] grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-20 items-center">
    <div class="reveal relative aspect-[4/5] overflow-hidden rounded-sm">
      <img src="https://images.unsplash.com/photo-1568782517100-09bf22d88c2d?auto=format&fit=crop&w=900&q=80" alt="Portrait photography" class="w-full h-full object-cover saturate-90 brightness-95">
    </div>
    <div class="reveal">
      <div class="font-mono text-gold-dim text-sm tracking-wide mb-6">f/2.8 — Considered</div>
      <h2 class="font-serif text-[clamp(1.9rem,3vw,2.6rem)] mb-4">The Portrait Experience</h2>
      <span class="price-tag mb-6"><span class="price-from">From</span> UGX 600,000</span>
      <p class="text-ivory-dim font-light mb-6 max-w-[480px] mt-6">Personal branding, editorial portraits and timeless personal photography for people who want to be seen as they actually are — confident, unposed, unmistakably themselves.</p>
      <ul class="space-y-3 mb-8">
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Studio or on-location session</li>
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Wardrobe &amp; styling guidance beforehand</li>
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Fully retouched final selects</li>
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> LinkedIn &amp; social-ready crops included</li>
      </ul>
      <a href="{{ route('contact') }}" class="inline-block text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400">Enquire Now</a>
    </div>
  </div>
</section>

<!-- GRADUATION EXPERIENCE -->
<section id="graduation" class="py-24 scroll-mt-24">
  <div class="max-w-[1320px] mx-auto px-[6vw] grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-20 items-center">
    <div class="reveal order-2 md:order-1">
      <div class="font-mono text-gold-dim text-sm tracking-wide mb-6">f/4 — Celebratory</div>
      <h2 class="font-serif text-[clamp(1.9rem,3vw,2.6rem)] mb-4">The Graduation Experience</h2>
      <span class="price-tag mb-6"><span class="price-from">From</span> UGX 500,000</span>
      <p class="text-ivory-dim font-light mb-6 max-w-[480px] mt-6">A milestone worth marking properly. Whether it's a solo portrait session in cap and gown or coverage of the full ceremony with family, we help you hold onto the day in a way snapshots on the lawn can't.</p>
      <ul class="space-y-3 mb-8">
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Studio or on-campus portrait session</li>
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Optional full ceremony coverage</li>
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Family and group portraits included</li>
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Fast turnaround for sharing right away</li>
      </ul>
      <a href="{{ route('contact') }}" class="inline-block text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400">Enquire Now</a>
    </div>
    <div class="reveal order-1 md:order-2 relative aspect-[4/5] overflow-hidden rounded-sm">
      <img src="https://images.unsplash.com/photo-1631131426242-0abfa7f209c2?auto=format&fit=crop&w=900&q=80" alt="Graduation photography, Kampala" class="w-full h-full object-cover saturate-90 brightness-95">
    </div>
  </div>
</section>

<!-- BRAND EXPERIENCE -->
<section id="brand" class="py-24 bg-charcoal scroll-mt-24">
  <div class="max-w-[1320px] mx-auto px-[6vw] grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-20 items-center">
    <div class="reveal relative aspect-[4/5] overflow-hidden rounded-sm">
      <img src="https://images.unsplash.com/photo-1578509566163-068acd11b8e7?auto=format&fit=crop&w=900&q=80" alt="Brand photography" class="w-full h-full object-cover saturate-90 brightness-95">
    </div>
    <div class="reveal">
      <div class="font-mono text-gold-dim text-sm tracking-wide mb-6">f/5.6 — Structured</div>
      <h2 class="font-serif text-[clamp(1.9rem,3vw,2.6rem)] mb-4">The Brand Experience</h2>
      <span class="price-tag mb-6"><span class="price-from">From</span> UGX 1,500,000</span>
      <p class="text-ivory-dim font-light mb-6 max-w-[480px] mt-6">Photography created to elevate businesses, campaigns and digital presence — consistent visual language, shot with your brand guidelines and platforms in mind from the start.</p>
      <ul class="space-y-3 mb-8">
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Product, team &amp; workplace photography</li>
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Campaign &amp; editorial concepts</li>
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Usage rights scoped to your needs</li>
        <li class="flex items-start gap-3 text-sm text-ivory-dim font-light"><span class="text-gold mt-0.5">—</span> Ongoing retainer options available</li>
      </ul>
      <a href="{{ route('contact') }}" class="inline-block text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400">Enquire Now</a>
    </div>
  </div>
</section>

<!-- PROCESS -->
<section class="bg-charcoal py-32">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal mb-16">
      <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">How We Work</span>
      <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5 max-w-[640px]">Four steps, in order, every time.</h2>
    </div>
    <div class="reveal border-t border-line">
      <div class="grid grid-cols-[50px_1fr] md:grid-cols-[90px_1fr_1.4fr] gap-6 md:gap-10 py-9 border-b border-line items-center">
        <span class="font-mono text-gold-dim text-sm">01</span>
        <h4 class="font-serif text-2xl font-normal">Discover</h4>
        <p class="col-span-2 md:col-span-1 text-ivory-dim font-light text-sm max-w-[480px]">A conversation — in person or on a call — to understand your story, your day and what matters most to capture.</p>
      </div>
      <div class="grid grid-cols-[50px_1fr] md:grid-cols-[90px_1fr_1.4fr] gap-6 md:gap-10 py-9 border-b border-line items-center">
        <span class="font-mono text-gold-dim text-sm">02</span>
        <h4 class="font-serif text-2xl font-normal">Plan</h4>
        <p class="col-span-2 md:col-span-1 text-ivory-dim font-light text-sm max-w-[480px]">We map locations, light and timing, so the day runs smoothly and nothing important is left to chance.</p>
      </div>
      <div class="grid grid-cols-[50px_1fr] md:grid-cols-[90px_1fr_1.4fr] gap-6 md:gap-10 py-9 border-b border-line items-center">
        <span class="font-mono text-gold-dim text-sm">03</span>
        <h4 class="font-serif text-2xl font-normal">Create</h4>
        <p class="col-span-2 md:col-span-1 text-ivory-dim font-light text-sm max-w-[480px]">On the day, we work quietly and attentively — present enough to catch what actually happens.</p>
      </div>
      <div class="grid grid-cols-[50px_1fr] md:grid-cols-[90px_1fr_1.4fr] gap-6 md:gap-10 py-9 border-b border-line items-center">
        <span class="font-mono text-gold-dim text-sm">04</span>
        <h4 class="font-serif text-2xl font-normal">Deliver</h4>
        <p class="col-span-2 md:col-span-1 text-ivory-dim font-light text-sm max-w-[480px]">A curated, edited gallery delivered within two to four weeks, ready to keep and share.</p>
      </div>
    </div>
  </div>
</section>

@else
<section id="weddings" class="py-24 scroll-mt-24">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal mb-16 max-w-[640px]"><span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Our Services</span><h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-3.5 mb-5">Packages shaped around your story.</h2><p class="text-ivory-dim font-light">Choose a starting point below and we will tailor the details to your day, your people, and your vision.</p></div>
    @foreach($services as $service)
      <div id="service-{{ $service->id }}" class="mb-20 scroll-mt-24">
        <div class="reveal mb-8"><span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">{{ $service->name }}</span><p class="mt-4 max-w-2xl text-ivory-dim font-light">{{ $service->description }}</p></div>
        @if($service->thumbnail_path)
          <div class="reveal mb-8 max-w-2xl overflow-hidden rounded-sm">
            <img src="{{ \Illuminate\Support\Str::startsWith($service->thumbnail_path, ['http://', 'https://']) ? $service->thumbnail_path : \Illuminate\Support\Facades\Storage::disk('r2')->url($service->thumbnail_path) }}" alt="{{ $service->name }}" class="w-full aspect-[16/9] object-cover saturate-90 brightness-95">
          </div>
        @endif
        @if($service->has_packages)
          <div class="reveal grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 pt-4">
            @foreach($service->packages as $package)
              <div class="package-card {{ $package->tier_name === 'Gold' ? 'is-featured' : '' }}">
                @if($package->tier_name === 'Gold')<span class="pkg-badge">Most Popular</span>@endif
                <div class="pkg-tier">{{ $package->tier_name }}</div><span class="pkg-tier-sub">{{ $service->name }}</span>
                <div class="pkg-price"><span>From</span>UGX {{ number_format($package->price) }}</div>
                <ul>@foreach($package->packageFeatures as $feature)<li>{{ $feature->feature_text }}</li>@endforeach</ul>
                <a href="{{ route('contact') }}?service={{ $service->id }}&package={{ $package->id }}" class="text-center text-xs tracking-[0.14em] uppercase px-6 py-4 rounded-sm {{ $package->tier_name === 'Gold' ? 'bg-gold text-black border border-gold hover:bg-gold-bright' : 'border border-line-strong text-ivory hover:border-gold hover:text-gold-bright' }} transition-all duration-400">Choose {{ $package->tier_name }}</a>
              </div>
            @endforeach
          </div>
        @else
          <div class="reveal flex flex-col gap-5">
            @if($service->amount !== null)
              <div class="font-serif text-3xl text-gold-bright">UGX {{ number_format((float) $service->amount) }}</div>
            @endif
            <p class="text-ivory-dim font-light">This is a bespoke service. <a href="{{ route('contact') }}?service={{ $service->id }}" class="text-gold">Start an enquiry &rarr;</a></p>
          </div>
        @endif
      </div>
    @endforeach
  </div>
</section>
@endif

<!-- CTA -->
<section class="text-center py-[130px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #0a0908;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Not Sure Which Fits?</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">Tell us about your project — we'll guide you from there.</h2>
    <a href="{{ route('contact') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 inline-block">Start Your Enquiry</a>
  </div>
</section>
@endsection
