<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Page;
use App\Models\Post;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        set_alternates('search');
        $q = trim(Str::limit((string) $request->query('q'), 100, ''));
        $locale = app()->getLocale();
        $results = collect();

        if (mb_strlen($q) >= 2) {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $match = fn ($query, array $fields) => $query->where(function ($w) use ($fields, $like, $locale) {
                foreach ($fields as $f) {
                    $w->orWhere("{$f}->{$locale}", 'like', $like);
                }
            });

            $results = collect()
                ->concat($match(Disease::published(), ['name', 'summary', 'body'])->get()
                    ->map(fn ($m) => ['type' => __('Hastalık'), 'title' => $m->name, 'text' => $m->summary ?: $m->body, 'url' => lroute('diseases.show', $m->slugFor()), 'rank' => 0]))
                ->concat($match(Page::published(), ['title', 'summary', 'body'])->get()
                    ->map(fn ($m) => ['type' => __('Rehber'), 'title' => $m->title, 'text' => $m->summary ?: $m->body,
                        'url' => in_array($m->section, ['rehber', 'destek']) ? lroute('support.show', $m->slugFor()) : lroute('page', $m->slugFor()), 'rank' => 1]))
                ->concat($match(Post::published(), ['title', 'excerpt', 'body'])->orderByDesc('published_at')->limit(30)->get()
                    ->map(fn ($m) => ['type' => $m->typeLabel(), 'title' => $m->title, 'text' => $m->excerpt ?: $m->body, 'url' => lroute('news.show', $m->slugFor()), 'rank' => 2]))
                ->concat($match(Story::published(), ['title', 'quote', 'body'])->get()
                    ->map(fn ($m) => ['type' => __('Hikâye'), 'title' => $m->title, 'text' => $m->quote ?: $m->body, 'url' => lroute('stories.show', $m->slugFor()), 'rank' => 3]))
                ->map(function ($r) use ($q) {
                    $r['text'] = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $r['text']))), 220);
                    $r['title_hit'] = Str::contains(mb_strtolower((string) $r['title']), mb_strtolower($q));

                    return $r;
                })
                ->sortBy([['title_hit', 'desc'], ['rank', 'asc']])->values();
        }

        return view('search', ['q' => $q, 'results' => $results]);
    }
}
