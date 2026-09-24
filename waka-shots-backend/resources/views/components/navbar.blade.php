<header id="siteHeader" class="fixed top-0 left-0 right-0 z-[500] flex items-center justify-between px-[6vw] py-6 transition-all duration-500 border-b border-transparent">
  <a href="{{ route('home') }}" class="font-script text-4xl leading-none flex items-center gap-2.5">
    <span class="relative w-[9px] h-[9px] rounded-full border border-gold inline-block"><span class="absolute inset-[2.5px] rounded-full bg-gold"></span></span>
    {{ $siteSetting->studio_name ?? 'Waka Shots' }}
  </a>
  <nav class="hidden md:flex gap-9 items-center">
    <a href="{{ route('home') }}" class="text-sm tracking-wide uppercase {{ request()->routeIs('home') ? 'text-gold-bright' : 'text-ivory-dim hover:text-ivory' }} relative">Home</a>
    <a href="{{ route('portfolio') }}" class="text-sm tracking-wide uppercase text-ivory-dim hover:text-ivory transition-colors relative group">Portfolio<span class="absolute left-0 -bottom-1 w-0 h-px bg-gold nav-underline transition-all duration-300 group-hover:w-full"></span></a>
    <a href="{{ route('services') }}" class="text-sm tracking-wide uppercase text-ivory-dim hover:text-ivory transition-colors relative group">Services<span class="absolute left-0 -bottom-1 w-0 h-px bg-gold nav-underline transition-all duration-300 group-hover:w-full"></span></a>
    <a href="{{ route('about') }}" class="text-sm tracking-wide uppercase text-ivory-dim hover:text-ivory transition-colors relative group">About<span class="absolute left-0 -bottom-1 w-0 h-px bg-gold nav-underline transition-all duration-300 group-hover:w-full"></span></a>
    <a href="{{ route('journal') }}" class="text-sm tracking-wide uppercase text-ivory-dim hover:text-ivory transition-colors relative group">Journal<span class="absolute left-0 -bottom-1 w-0 h-px bg-gold nav-underline transition-all duration-300 group-hover:w-full"></span></a>
    <a href="{{ route('films') }}" class="text-sm tracking-wide uppercase text-ivory-dim hover:text-ivory transition-colors relative group">Films<span class="absolute left-0 -bottom-1 w-0 h-px bg-gold nav-underline transition-all duration-300 group-hover:w-full"></span></a>
    <a href="{{ route('contact') }}" class="text-sm tracking-wide uppercase text-ivory-dim hover:text-ivory transition-colors relative group">Contact<span class="absolute left-0 -bottom-1 w-0 h-px bg-gold nav-underline transition-all duration-300 group-hover:w-full"></span></a>
    <a href="{{ route('contact') }}" class="text-sm tracking-wide uppercase border border-line-strong px-5 py-2.5 rounded-sm text-gold-bright hover:bg-gold hover:text-black hover:border-gold transition-all">Book Now</a>
    <a href="{{ url('/admin') }}" target="_blank" rel="noopener" title="Admin portal" aria-label="Sign in to the admin portal" data-cursor="Admin" class="inline-flex items-center justify-center p-2.5 rounded-sm border border-line text-ivory-dim hover:text-gold-bright hover:border-line-strong transition-all duration-300">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <rect x="3" y="11" width="18" height="11" rx="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
      </svg>
    </a>
  </nav>
  <button class="burger md:hidden flex flex-col gap-1.5 z-[600]" id="burgerBtn" aria-label="Open menu">
    <span class="w-6 h-px bg-ivory"></span><span class="w-6 h-px bg-ivory"></span><span class="w-6 h-px bg-ivory"></span>
  </button>
</header>

<div class="mobile-menu fixed inset-0 z-[550] flex flex-col items-center justify-center gap-8" id="mobileMenu">
  <a href="{{ route('home') }}" class="font-serif text-2xl text-gold-bright">Home</a>
  <a href="{{ route('portfolio') }}" class="font-serif text-2xl">Portfolio</a>
  <a href="{{ route('services') }}" class="font-serif text-2xl">Services</a>
  <a href="{{ route('about') }}" class="font-serif text-2xl">About</a>
  <a href="{{ route('journal') }}" class="font-serif text-2xl">Journal</a>
  <a href="{{ route('films') }}" class="font-serif text-2xl">Films</a>
  <a href="{{ route('contact') }}" class="font-serif text-2xl">Book Now</a>
  <a href="{{ url('/admin') }}" target="_blank" rel="noopener" aria-label="Sign in to the admin portal" class="mt-3 inline-flex items-center gap-2.5 text-xs tracking-[0.18em] uppercase text-ivory-dim hover:text-gold-bright transition-colors">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
      <rect x="3" y="11" width="18" height="11" rx="2"/>
      <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    </svg>
    Admin
  </a>
</div>