<?php

namespace App\Http\Controllers;

use App\Models\JournalPost;
use Illuminate\View\View;

class JournalController extends Controller
{
    //
    public function index(): View
    {
        return view('journal', [
            'posts' => JournalPost::with('category')->where('is_published', true)->latest()->get(),
        ]);
    }
}
