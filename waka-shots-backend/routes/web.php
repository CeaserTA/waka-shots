<?php

use App\Models\Service;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/journal', [JournalController::class, 'index'])->name('journal');
Route::get('/films', [FilmController::class, 'index'])->name('films');
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/about', fn () => view('about'))->name('about');
Route::get('/contact', fn () => view('contact', [
    'services' => Service::with('packages')->orderBy('name')->get(),
]))->name('contact');
Route::post('/contact', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'service_id' => ['nullable', 'exists:services,id'],
        'message' => ['required', 'string'],
    ]);

    return to_route('contact')->with('success', 'Thank you. We will be in touch soon.');
})->name('contact.submit');
Route::post('/enquiries', [EnquiryController::class, 'store'])->name('enquiries.store');
