<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\View\View;

class FilmController extends Controller
{
    //
    public function index(): View
    {
        return view('films', [
            'films' => Film::with('category')->latest()->get(),
        ]);
    }
}
