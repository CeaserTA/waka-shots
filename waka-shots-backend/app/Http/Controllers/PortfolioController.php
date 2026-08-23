<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    //
    public function index(): View
    {
        return view('portfolio', [
            'categories' => Category::with('portfolioItems')->orderBy('name')->get(),
        ]);
    }
}
