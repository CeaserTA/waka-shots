@extends('layouts.gallery')

@section('title', 'Gallery unavailable')

@section('content')
<main class="flex min-h-[100svh] items-center justify-center px-6 py-16 text-center">
    <div class="max-w-lg">
        <a href="{{ route('home') }}" class="font-script text-3xl leading-none text-gold-bright">{{ $siteSetting->studio_name ?? 'Waka Shots' }}</a>
        <h1 class="mt-7 font-serif text-4xl text-ivory">Something went wrong loading your photos.</h1>
        <p class="mt-5 text-sm leading-7 text-silver">Please try again shortly. If the problem continues, contact the studio so the gallery connection can be checked.</p>
    </div>
</main>
@endsection
