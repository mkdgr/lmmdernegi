<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Page;
use App\Models\Post;
use App\Models\Story;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $urls = [];
        $add = function (string $locale, string $route, $model = null) use (&$urls) {
            if ($model && ! $model->hasLocale($locale)) {
                return;
            }
            $urls[] = [
                'loc' => lroute($route, $model ? $model->slugFor($locale) : [], $locale),
                'lastmod' => $model?->updated_at?->toAtomString(),
            ];
        };

        foreach (array_keys(config('site.locales')) as $locale) {
            app()->setLocale($locale);
            foreach (['home', 'diseases.index', 'support.index', 'news.index', 'events.index', 'stories.index',
                'publications.index', 'professionals', 'ask.create', 'donate.create', 'membership.create', 'contact.create'] as $r) {
                $add($locale, $r);
            }
            Disease::published()->get()->each(fn ($m) => $add($locale, 'diseases.show', $m));
            Page::published()->get()->each(fn ($m) => $add($locale, in_array($m->section, ['rehber', 'destek']) ? 'support.show' : 'page', $m));
            Post::published()->get()->each(fn ($m) => $add($locale, 'news.show', $m));
            Story::published()->get()->each(fn ($m) => $add($locale, 'stories.show', $m));
        }

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }
}
