{{-- Studio stats band, shared by the homepage About teaser and the About page. --}}
<div class="flex gap-12 mt-10 pt-8 border-t border-line">
  @foreach($siteSetting->content('stats') as $stat)
    <div><strong class="block font-serif text-3xl text-gold-bright font-normal">{{ $stat['value'] ?? '' }}</strong><span class="text-xs tracking-wide uppercase text-silver-dim">{{ $stat['label'] ?? '' }}</span></div>
  @endforeach
</div>
