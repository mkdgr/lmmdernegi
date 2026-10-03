<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Page;
use App\Models\Post;
use App\Models\Story;

class HomeController extends Controller
{
    public function __invoke()
    {
        set_alternates('home');

        $locale = app()->getLocale();

        $events = Post::published()->whereNotNull("title->{$locale}")
            ->whereIn('type', ['etkinlik', 'haber', 'duyuru'])
            ->orderByRaw('CASE WHEN event_starts_at >= ? THEN 0 ELSE 1 END', [now()->startOfDay()])
            ->orderByDesc('published_at')
            ->limit(4)->get();

        return view('home', [
            'journey' => Page::published()->where('section', 'rehber')->orderBy('sort')->limit(5)->get(),
            'diseases' => Disease::published()->orderBy('sort')->get(),
            'stories' => Story::published()->whereNotNull("quote->{$locale}")
                ->orderByDesc('is_featured')->orderByDesc('published_at')->limit(3)->get(),
            'events' => $events,
        ]);
    }
}
