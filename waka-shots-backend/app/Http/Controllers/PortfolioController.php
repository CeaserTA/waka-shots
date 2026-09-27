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
            // chaperone() hands each item its already-loaded category, so the
            // caption/alt fallback to the category name costs no extra queries.
            'categories' => Category::with(['portfolioItems' => fn ($items) => $items->chaperone()])->orderBy('name')->get(),
        ]);
    }
}
