<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JournalPost;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\View\View;

class PageController extends Controller
{
    //
    public function home(): View
    {
        return view('index', [
            'featuredCategories' => Category::with('portfolioItems')->orderBy('name')->get(),
            'portfolioItems' => PortfolioItem::with('category')->latest()->take(7)->get(),
            'journalPosts' => JournalPost::with('category')
                ->where('is_published', true)
                ->latest()
                ->take(3)
                ->get(),
            'services' => Service::with('packages.packageFeatures')->orderBy('sort_order')->take(3)->get(),
            'testimonials' => Testimonial::approved()
                ->featured()
                ->with('gallery')
                ->orderBy('sort_order')
                ->take(6)
                ->get(),
        ]);
    }
}
