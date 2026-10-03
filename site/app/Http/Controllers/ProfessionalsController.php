<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;

class ProfessionalsController extends Controller
{
    public function __invoke()
    {
        set_alternates('professionals');
        $locale = app()->getLocale();

        return view('professionals', [
            'intro' => Page::published()->where('legacy_id', 11)->first(),
            'events' => Post::published()->where('audience', 'hekim')->whereNotNull("title->{$locale}")
                ->orderByDesc('event_starts_at')->orderByDesc('published_at')->limit(6)->get(),
            'articles' => Post::published()->where('type', 'bilimsel')->whereNotNull("title->{$locale}")
                ->orderByDesc('published_at')->get(),
        ]);
    }
}
