@extends('layouts.app')
@section('title', 'Contact')
@push('head')
<style>
  .field label{ font-family:'Space Mono', monospace; font-size:0.68rem; letter-spacing:0.12em; text-transform:uppercase; color:#aab0b6; display:block; margin-bottom:10px; }
  .field input, .field select, .field textarea{
    width:100%; background:transparent; border:1px solid rgba(236,231,219,0.16);
    color:#ece7db; padding:14px 16px; font-family:'Manrope', sans-serif; font-size:0.94rem;
    border-radius:2px; transition:border-color .3s;
  }
  .field input:focus, .field select:focus, .field textarea:focus{
    outline:none; border-color:#c6a15b;
  }
  .field select{ appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23c6a15b' stroke-width='1.4' fill='none'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 16px center; }
  .field select option{ background:#151316; color:#ece7db; }
</style>
@endpush
@section('content')
<!-- PAGE HEADER -->
<section class="relative h-[46vh] min-h-[320px] flex items-end overflow-hidden">
  <div class="hero-bg absolute inset-0 bg-cover" style="background-image:url('https://images.unsplash.com/photo-1708170236215-b6edcad7f49a?auto=format&fit=crop&w=1800&q=80'); background-position:center 30%;">
    <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(10,9,8,0.5) 0%, rgba(10,9,8,0.4) 40%, rgba(10,9,8,0.96) 100%);"></div>
  </div>
  <div class="relative z-[2] w-full px-[6vw] pb-16">
    <span class="eyebrow anim-fadeup font-mono text-xs tracking-[0.22em] uppercase text-gold inline-flex items-center gap-2.5">Let's Talk</span>
    <h1 class="anim-fadeup font-serif font-normal text-[clamp(2.4rem,6vw,4.6rem)] leading-[1.08] mt-4" style="animation-delay:.15s;">Let's create something unforgettable.</h1>
  </div>
</section>

<!-- FORM + INFO -->
<section class="py-28">
  <div class="max-w-[1320px] mx-auto px-[6vw] grid grid-cols-1 md:grid-cols-[1.3fr_0.7fr] gap-16">

    <form method="POST" action="{{ route('enquiries.store') }}" class="reveal space-y-7">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="field"><label for="name">Name</label><input type="text" id="name" name="name" value="{{ old('name') }}" required></div>
        <div class="field"><label for="email">Email</label><input type="email" id="email" name="email" value="{{ old('email') }}" required></div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="field"><label for="phone">Phone</label><input type="tel" id="phone" name="phone" value="{{ old('phone') }}"></div>
        <div class="field">
          <label for="service_id">Service</label>
          <select id="service_id" name="service_id">
            <option value="">Select a service</option>
            @foreach($services as $service)
              <option value="{{ $service->id }}" @selected(old('service_id', request('service')) == $service->id)>{{ $service->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label for="package_id">Package (optional)</label>
          <select id="package_id" name="package_id">
            <option value="">No specific package</option>
            @foreach($services as $service)
              @foreach($service->packages as $package)
                <option value="{{ $package->id }}" @selected(old('package_id', request('package')) == $package->id)>{{ $service->name }} — {{ $package->tier_name }}</option>
              @endforeach
            @endforeach
          </select>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="field"><label for="preferred_date">Preferred Date</label><input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date') }}"></div>
        <div class="field"><label for="location">Location</label><input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="e.g. Kampala, Entebbe"></div>
      </div>
      <div class="field">
        <label for="budget">Budget Range</label>
        <select id="budget" name="budget">
            <option value="">Select a budget range</option>
            @foreach(['Under UGX 1,000,000', 'UGX 1,000,000 – 3,000,000', 'UGX 3,000,000 – 6,000,000', 'UGX 6,000,000+', 'Not sure yet'] as $budget)
              <option value="{{ $budget }}" @selected(old('budget') === $budget)>{{ $budget }}</option>
            @endforeach
        </select>
      </div>
      <div class="field">
        <label for="details">Tell Us About Your Project</label>
        <textarea id="details" name="details" rows="5" placeholder="Share a bit about your day, your brand, or what you have in mind...">{{ old('details') }}</textarea>
      </div>
      <input type="hidden" name="status" value="pending">
      <button type="submit" class="text-xs tracking-[0.14em] uppercase px-8 py-4 rounded-sm bg-gold text-black border border-gold hover:bg-gold-bright hover:-translate-y-0.5 transition-all duration-400">Send Enquiry</button>
      @if(session('success'))<p class="text-sm text-gold font-light pt-2">{{ session('success') }}</p>@endif
      @if($errors->any())<p class="text-sm text-red-300 font-light pt-2">Please check the highlighted details and try again.</p>@endif
    </form>

    <div class="reveal">
      @if($siteSetting?->contact_email || $siteSetting?->contact_phone || $siteSetting?->address)
      <div class="border border-line rounded-sm p-9 mb-8">
        <h3 class="font-serif text-xl mb-6">Studio Details</h3>
        <div class="space-y-4 text-sm">
          @if($siteSetting->contact_email)
          <div>
            <span class="font-mono text-[0.65rem] tracking-[0.14em] uppercase text-gold block mb-1">Email</span>
            <span class="text-ivory-dim font-light">{{ $siteSetting->contact_email }}</span>
          </div>
          @endif
          @if($siteSetting->contact_phone)
          <div>
            <span class="font-mono text-[0.65rem] tracking-[0.14em] uppercase text-gold block mb-1">Phone</span>
            <span class="text-ivory-dim font-light">{{ $siteSetting->contact_phone }}</span>
          </div>
          @endif
          @if($siteSetting->address)
          <div>
            <span class="font-mono text-[0.65rem] tracking-[0.14em] uppercase text-gold block mb-1">Studio</span>
            <span class="text-ivory-dim font-light">{{ $siteSetting->address }}</span>
          </div>
          @endif
        </div>
      </div>
      @endif
      <div class="relative aspect-[4/5] overflow-hidden rounded-sm">
        <img src="https://images.unsplash.com/photo-1649532349871-b5b10b5ab9c4?auto=format&fit=crop&w=700&q=80" alt="Waka Shots studio" class="w-full h-full object-cover saturate-90 brightness-95">
      </div>
    </div>

  </div>
</section>
@endsection
