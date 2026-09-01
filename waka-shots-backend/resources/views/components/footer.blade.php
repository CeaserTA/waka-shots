<footer class="pt-14 pb-6">
  <div class="max-w-[1320px] mx-auto px-[6vw]">
    <div class="grid grid-cols-1 md:grid-cols-[1.4fr_0.8fr_0.8fr_0.8fr] gap-8 pb-8 border-b border-line">
      <div>
        <a href="{{ route('home') }}" class="font-script text-4xl leading-none flex items-center gap-2.5 mb-3">
          <span class="relative w-[9px] h-[9px] rounded-full border border-gold inline-block"><span class="absolute inset-[2.5px] rounded-full bg-gold"></span></span>
          {{ $siteSetting->studio_name ?? 'Waka Shots' }}
        </a>
        <p class="text-ivory-dim font-light text-sm max-w-[280px]">{{ $siteSetting->footer_about_text ?: 'Capturing moments. Creating stories. A photography studio based in Kampala, working across Uganda and East Africa.' }}</p>
      </div>
      <div>
        <h5 class="font-mono text-[0.7rem] tracking-[0.16em] uppercase text-silver-dim mb-3">Studio</h5>
        <a href="{{ route('portfolio') }}" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">Portfolio</a>
        <a href="{{ route('services') }}" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">Services</a>
        <a href="{{ route('about') }}" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">About</a>
        <a href="{{ route('journal') }}" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">Journal</a>
        <a href="{{ route('films') }}" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">Films</a>
      </div>
      @if($siteSetting?->contact_email || $siteSetting?->contact_phone || $siteSetting?->address)
      <div>
        <h5 class="font-mono text-[0.7rem] tracking-[0.16em] uppercase text-silver-dim mb-3">Contact</h5>
        @if($siteSetting?->contact_email)
          <p class="text-ivory-dim text-sm font-light mb-3">{{ $siteSetting->contact_email }}</p>
        @endif
        @if($siteSetting?->contact_phone)
          <p class="text-ivory-dim text-sm font-light mb-3">{{ $siteSetting->contact_phone }}</p>
        @endif
        @if($siteSetting?->address)
          <p class="text-ivory-dim text-sm font-light mb-3">{{ $siteSetting->address }}</p>
        @endif
      </div>
      @endif
      @if($siteSetting?->instagram_url || $siteSetting?->facebook_url || $siteSetting?->tiktok_url || $siteSetting?->youtube_url || $siteSetting?->whatsapp_number)
        <div>
          <h5 class="font-mono text-[0.7rem] tracking-[0.16em] uppercase text-silver-dim mb-3">Follow</h5>
          @if($siteSetting->instagram_url)
            <a href="{{ $siteSetting->instagram_url }}" target="_blank" rel="noopener" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">Instagram</a>
          @endif
          @if($siteSetting->facebook_url)
            <a href="{{ $siteSetting->facebook_url }}" target="_blank" rel="noopener" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">Facebook</a>
          @endif
          @if($siteSetting->tiktok_url)
            <a href="{{ $siteSetting->tiktok_url }}" target="_blank" rel="noopener" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">TikTok</a>
          @endif
          @if($siteSetting->youtube_url)
            <a href="{{ $siteSetting->youtube_url }}" target="_blank" rel="noopener" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">YouTube</a>
          @endif
          @if($siteSetting->whatsapp_number)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSetting->whatsapp_number) }}" target="_blank" rel="noopener" class="block text-ivory-dim text-sm font-light mb-2 hover:text-gold-bright transition-colors">WhatsApp</a>
          @endif
        </div>
      @endif
    </div>
    <div class="flex justify-between items-center text-xs text-silver-dim flex-wrap gap-3.5 pt-5">
      <span>© {{ date('Y') }} {{ $siteSetting->studio_name ?? 'Waka Shots' }} Photography. All rights reserved.</span>
      <span>Photography · Weddings · Portraits · Events · Brands</span>
    </div>
  </div>
</footer>