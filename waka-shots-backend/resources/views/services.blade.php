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
@if($services->isNotEmpty())
<section class="pb-4">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="reveal flex flex-wrap gap-3">
      @foreach($services as $service)
        <a href="#service-{{ $service->id }}" class="pkg-nav-link">{{ $service->name }}</a>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- SERVICES -->
<section id="our-services" class="py-24 scroll-mt-24">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    @forelse($services as $service)
      <div id="service-{{ $service->id }}" class="mb-20 scroll-mt-24">
        @if($service->has_packages)
          <div class="reveal mb-8"><span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">{{ $service->name }}</span><p class="mt-4 max-w-2xl text-ivory-dim font-light">{{ $service->description }}</p></div>
          @if($service->thumbnail_path)
            <div class="reveal mb-8 max-w-2xl overflow-hidden rounded-sm">
              <img src="{{ \Illuminate\Support\Str::startsWith($service->thumbnail_path, ['http://', 'https://']) ? $service->thumbnail_path : \Illuminate\Support\Facades\Storage::disk('r2')->url($service->thumbnail_path) }}" alt="{{ $service->name }}" class="w-full aspect-[16/9] object-cover saturate-90 brightness-95">
            </div>
          @endif
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
          @php $imageRight = $loop->iteration % 2 === 0; @endphp
          <div class="reveal grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-20 items-center">
            <div class="order-1 {{ $imageRight ? 'md:order-2' : 'md:order-1' }} relative aspect-[4/5] overflow-hidden rounded-sm bg-panel">
              @if($service->thumbnail_path)
                <img src="{{ \Illuminate\Support\Str::startsWith($service->thumbnail_path, ['http://', 'https://']) ? $service->thumbnail_path : \Illuminate\Support\Facades\Storage::disk('r2')->url($service->thumbnail_path) }}" alt="{{ $service->name }}" class="w-full h-full object-cover saturate-90 brightness-95">
              @endif
            </div>
            <div class="order-2 {{ $imageRight ? 'md:order-1' : 'md:order-2' }}">
              @if($service->tagline)
                <div class="font-mono text-gold-dim text-sm tracking-wide mb-6">{{ $service->tagline }}</div>
              @endif
              <h2 class="font-serif text-[clamp(1.9rem,3vw,2.6rem)] mb-4">{{ $service->name }}</h2>
              @if($service->amount !== null)
                <span class="price-tag mb-6"><span class="price-from">From</span> UGX {{ number_format((float) $service->amount) }}</span>
              @endif
              @if($service->description)
                <p class="text-ivory-dim font-light mb-6 max-w-[480px] mt-6">{{ $service->description }}</p>
              @endif
              <a href="{{ route('contact') }}?service={{ $service->id }}" class="inline-block text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400">Enquire Now</a>
            </div>
          </div>
        @endif
      </div>
    @empty
      <p class="reveal text-ivory-dim font-light">Our services list is being updated — check back soon.</p>
    @endforelse
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

<!-- CTA -->
<section class="text-center py-[130px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #0a0908;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Not Sure Which Fits?</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">Tell us about your project — we'll guide you from there.</h2>
    <a href="{{ route('contact') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 inline-block">Start Your Enquiry</a>
  </div>
</section>
@endsection
