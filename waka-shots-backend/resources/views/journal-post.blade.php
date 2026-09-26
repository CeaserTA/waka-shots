@extends('layouts.app')
@section('title', $post->title.' — Waka Shots Photography')
@section('meta_description', $post->excerpt() ?: $post->title.' — a note from the Waka Shots studio journal in Kampala.')
@section('content')
<!-- POST HEADER -->
<section class="relative pt-44 pb-16 border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #0a0908;">
  <div class="max-w-[820px] mx-auto px-[6vw] md:px-0">
    <a href="{{ route('journal') }}" class="anim-fadeup inline-flex items-center gap-2 font-mono text-[0.7rem] tracking-[0.16em] uppercase text-silver-dim hover:text-gold-bright transition-colors">
      <span aria-hidden="true">←</span> Journal
    </a>
    <span class="eyebrow anim-fadeup mt-10 font-mono text-xs tracking-[0.22em] uppercase text-gold flex items-center gap-2.5" style="animation-delay:.1s;">{{ $post->category?->name ?? 'Journal' }}</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.2rem,5.4vw,4rem)] leading-[1.1] mt-4" style="animation-delay:.2s;">{{ $post->title }}</h1>
    <time datetime="{{ $post->created_at->toDateString() }}" class="anim-fadeup block mt-6 text-sm text-silver-dim" style="animation-delay:.3s;">{{ $post->created_at->format('j F Y') }}</time>
  </div>
</section>

<!-- POST BODY -->
<article class="py-20">
  <div class="max-w-[820px] mx-auto px-[6vw] md:px-0">
    @if(filled($post->content))
      <div class="journal-body reveal">{!! $body !!}</div>
    @else
      <p class="text-ivory-dim font-light">This post doesn't have any written content yet.</p>
    @endif
  </div>
</article>

<!-- CTA -->
<section class="text-center py-[130px] border-t border-b border-line" style="background:linear-gradient(180deg, rgba(198,161,91,0.06), transparent), #151316;">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <span class="eyebrow font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center justify-center gap-2.5">Enjoyed This?</span>
    <h2 class="font-serif text-[clamp(2rem,3.6vw,3.1rem)] mt-4 mb-10 mx-auto text-center">Let's write your story next.</h2>
    <div class="flex flex-wrap justify-center gap-4">
      <a href="{{ route('contact') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400 inline-block">Book a Session</a>
      <a href="{{ route('journal') }}" class="text-xs tracking-[0.14em] uppercase px-7 py-4 rounded-sm border border-line-strong text-ivory hover:border-gold hover:text-gold-bright transition-all duration-400 inline-block">More From the Journal</a>
    </div>
  </div>
</section>
@endsection
