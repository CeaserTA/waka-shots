<?php

namespace App\Http\Controllers;

use App\Models\JournalPost;
use Illuminate\Support\Carbon;
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

        // Posts have no pages of their own; they are all shown in full on /journal,
        // so that page changes whenever a post is published or edited.
        $journal = Url::create(route('journal'));
        $latestPost = JournalPost::where('is_published', true)->max('updated_at');

        if ($latestPost) {
            $journal->setLastModificationDate(Carbon::parse($latestPost));
        }

        return $sitemap->add($journal);
    }
}
