@extends('layouts.gallery')

@section('title', 'Enter access code')

@section('content')
<main class="flex min-h-[100svh] items-center justify-center px-6 py-16 text-center">
    <div class="w-full max-w-lg">
        <a href="{{ route('home') }}" class="font-script text-3xl leading-none text-gold-bright">{{ $siteSetting->studio_name ?? 'Waka Shots' }}</a>
        <h1 class="mt-7 font-serif text-4xl text-ivory">This gallery is private.</h1>
        <p class="mt-5 text-sm leading-7 text-silver">Enter the access code from your gallery email to view your photos.</p>

        <form method="POST" action="{{ route('gallery.unlock', $token) }}" class="mx-auto mt-9 flex max-w-sm flex-col gap-4">
            @csrf
            <label for="code" class="sr-only">Access code</label>
            <input
                id="code"
                name="code"
                type="text"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="20"
                required
                autofocus
                value="{{ old('code') }}"
                placeholder="6-digit code"
                class="w-full border border-gold-bright/40 bg-transparent px-5 py-4 text-center font-mono text-2xl tracking-[0.4em] text-ivory placeholder:text-base placeholder:tracking-normal placeholder:text-silver/60 focus:border-gold-bright focus:outline-none"
            >
            @error('code')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            <button type="submit" class="bg-gold-bright px-6 py-4 text-sm font-semibold uppercase tracking-[0.2em] text-black transition hover:opacity-90">
                Unlock gallery
            </button>
        </form>

        <p class="mt-8 text-xs leading-6 text-silver">Can't find your code? Contact the studio and we'll send a new one.</p>
    </div>
</main>
@endsection
