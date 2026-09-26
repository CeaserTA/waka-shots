<?php

namespace App\Http\Controllers;

use App\Models\JournalPost;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\View\View;

class JournalController extends Controller
{
    //
    public function index(): View
    {
        return view('journal', [
            'posts' => JournalPost::with('category')->published()->latest()->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $post = JournalPost::with('category')->published()->where('slug', $slug)->firstOrFail();

        return view('journal-post', [
            'post' => $post,
            // Sanitized by Filament, since the body is HTML from the admin editor.
            'body' => RichContentRenderer::make($post->content)->toHtml(),
        ]);
    }
}
