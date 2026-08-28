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

@if($services->isEmpty())
<section class="py-32">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <p class="reveal text-center text-ivory-dim font-light">No services have been published yet.</p>
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
