<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PortfolioItem;
use Illuminate\View\View;

class PageController extends Controller
{
    //
    public function home(): View
    {
        return view('index', [
            'featuredCategories' => Category::with('portfolioItems')->orderBy('name')->get(),
            'portfolioItems' => PortfolioItem::with('category')->latest()->take(7)->get(),
        ]);
    }
}
