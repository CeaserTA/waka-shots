<?php

namespace App\Http\Controllers;

use App\Models\JournalPost;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    /**
     * Built on each request rather than written to disk by a scheduled job,
     * so it is always current without depending on a running scheduler.
     * Client galleries (/gallery/*) and the admin panel are deliberately absent.
     */
    public function __invoke(): Sitemap
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home'))->setPriority(1.0))
            ->add(Url::create(route('portfolio')))
            ->add(Url::create(route('services')))
            ->add(Url::create(route('films')))
            ->add(Url::create(route('about')))
            ->add(Url::create(route('contact')));

        $posts = JournalPost::published()->whereNotNull('slug')->latest('updated_at')->get(['slug', 'updated_at']);

        // The index lists every post, so it changes whenever the newest one does.
        $journal = Url::create(route('journal'));

        if ($posts->isNotEmpty()) {
            $journal->setLastModificationDate($posts->first()->updated_at);
        }

        $sitemap->add($journal);

        foreach ($posts as $post) {
            $sitemap->add(Url::create(route('journal.show', $post->slug))->setLastModificationDate($post->updated_at));
        }

        return $sitemap;
    }
}
