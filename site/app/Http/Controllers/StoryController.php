<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index(Request $request)
    {
        set_alternates('stories.index');
        $locale = app()->getLocale();
        $kind = $request->query('tur');

        return view('stories.index', [
            'stories' => Story::published()->whereNotNull("title->{$locale}")
                ->when(array_key_exists($kind, Story::KINDS), fn ($q) => $q->where('kind', $kind))
                ->orderByDesc('is_featured')->orderByDesc('published_at')->paginate(12)->withQueryString(),
            'kind' => $kind,
        ]);
    }

    public function show(string $slug)
    {
        $story = Story::published()->whereSlug($slug)->firstOrFail();
        set_alternates('stories.show', $story);

        return view('stories.show', [
            'story' => $story,
            'more' => Story::published()->whereKeyNot($story->id)->whereNotNull('title->'.app()->getLocale())
                ->inRandomOrder()->limit(2)->get(),
        ]);
    }
}
