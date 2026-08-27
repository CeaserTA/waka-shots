@extends('layouts.app')
@section('title', 'Journal')
@section('content')
<!-- PAGE HEADER -->
<section class="relative h-[56vh] min-h-[380px] flex items-end overflow-hidden">
  <div class="hero-bg absolute inset-0 bg-cover" style="background-image:url('https://images.unsplash.com/photo-1633150747731-c945ec51b663?auto=format&fit=crop&w=1800&q=80'); background-position:center 25%;">
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.45) 0%, rgba(10,9,8,0.35) 40%, rgba(10,9,8,0.95) 100%);"></div>
  </div>
  <div class="relative z-[2] w-full px-[6vw] pb-16">
    <span class="eyebrow anim-fadeup font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Notes From the Studio</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.4rem,6vw,4.6rem)] leading-[1.08] mt-4" style="animation-delay:.15s;">Journal</h1>
  </div>
</section>

<!-- ARTICLES -->
<section class="py-28">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    @if($posts->isEmpty())
    <p class="reveal text-center text-ivory-dim font-light">No journal posts have been published yet.</p>
    @else
    <div class="reveal grid grid-cols-1 md:grid-cols-3 gap-x-9 gap-y-16">
      @foreach($posts as $post)
        <article class="group block">
          <div class="mb-5 aspect-[4/3] bg-charcoal flex items-center justify-center"><span class="font-mono text-xs uppercase tracking-widest text-gold">Waka Shots Journal</span></div>
          <span class="font-mono text-[0.68rem] tracking-[0.14em] uppercase text-gold mb-3 block">{{ $post->category->name }}</span>
          <h4 class="font-serif text-xl mb-2.5 leading-snug">{{ $post->title }}</h4>
          <span class="text-xs text-silver-dim">From the studio</span>
        </article>
      @endforeach
    </div>
    @endif
  </div>
</section>

<!-- CTA -->
<section class="text-center py-[130px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #151316;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Enjoyed These?</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">Let's write your story next.</h2>
    <a href="{{ route('contact') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 inline-block">Book a Session</a>
  </div>
</section>
@endsection
