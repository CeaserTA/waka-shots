@extends('layouts.app')
@section('title', $post->title.' — Waka Shots Photography')
@section('meta_description', $post->excerpt() ?: $post->title.' — a note from the Waka Shots studio journal in Kampala.')
{{-- Without a thumbnail, the layout falls back to the site's hero image. --}}
@if($post->thumbnailUrl())
  @section('og_image', $post->thumbnailUrl())
@endif
@section('content')
<!-- POST HEADER: the thumbnail is the cover; the gradient melts it into the page -->
@php($thumbnail = $post->thumbnailUrl())
<section data-post-hero class="relative flex items-end overflow-hidden {{ $thumbnail ? 'h-[88vh] min-h-[560px]' : 'min-h-[60vh] pt-44' }}">
  @if($thumbnail)
    <div data-post-hero-media class="absolute inset-x-0 -top-[6%] h-[112%] will-change-transform" aria-hidden="true">
      <img src="{{ $thumbnail }}" alt="" class="hero-bg w-full h-full object-cover saturate-90 brightness-90">
    </div>
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.7) 0%, rgba(10,9,8,0.15) 28%, rgba(10,9,8,0.35) 55%, rgba(10,9,8,0.92) 84%, #0a0908 100%);"></div>
  @else
    <div class="absolute inset-0" style="background:radial-gradient(ellipse at 70% 20%, rgba(198,161,91,0.12), transparent 60%), linear-gradient(180deg, #151316 0%, #0a0908 100%);"></div>
  @endif

  <div data-post-hero-content class="relative z-[2] w-full max-w-[1000px] mx-auto px-[6vw] md:px-8 pb-14 md:pb-20 will-change-transform">
    <a href="{{ route('journal') }}" class="anim-fadeup inline-flex items-center gap-2 font-mono text-[0.7rem] tracking-[0.16em] uppercase text-ivory-dim hover:text-gold-bright transition-colors">
      <span aria-hidden="true">←</span> Journal
    </a>
    <span class="eyebrow anim-fadeup mt-8 font-mono text-xs tracking-[0.22em] uppercase text-gold flex items-center gap-2.5" style="animation-delay:.1s;">{{ $post->category?->name ?? 'Journal' }}</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.4rem,6vw,4.8rem)] leading-[1.05] mt-4 max-w-[16ch] [text-shadow:0_2px_30px_rgba(10,9,8,0.5)]" style="animation-delay:.2s;">{{ $post->title }}</h1>
    <div class="anim-fadeup mt-7 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-ivory-dim" style="animation-delay:.3s;">
      <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('j F Y') }}</time>
      <span class="w-1 h-1 rounded-full bg-gold" aria-hidden="true"></span>
      <span>{{ $post->readingMinutes() }} min read</span>
    </div>
  </div>
</section>

<!-- POST BODY -->
<article class="pt-10 pb-24 md:pt-14">
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
