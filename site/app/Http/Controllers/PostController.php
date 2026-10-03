<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        set_alternates('news.index');
        $locale = app()->getLocale();
        $type = $request->query('tur');

        $posts = Post::published()->whereNotNull("title->{$locale}")
            ->where('type', '!=', 'bilimsel')
            ->when(in_array($type, ['haber', 'etkinlik', 'duyuru'], true), fn ($q) => $q->where('type', $type))
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(12)->withQueryString();

        return view('posts.index', ['posts' => $posts, 'type' => $type]);
    }

    public function events()
    {
        set_alternates('events.index');
        $locale = app()->getLocale();

        return view('posts.events', [
            'upcoming' => Post::published()->upcoming()->whereNotNull("title->{$locale}")->get(),
            'past' => Post::published()->where('type', 'etkinlik')->whereNotNull("title->{$locale}")
                ->where(fn ($q) => $q->whereNull('event_starts_at')->orWhere('event_starts_at', '<', now()->startOfDay()))
                ->orderByDesc('event_starts_at')->orderByDesc('published_at')->paginate(12),
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::published()->whereSlug($slug)->firstOrFail();
        set_alternates('news.show', $post);

        return view('posts.show', [
            'post' => $post,
            'more' => Post::published()->whereKeyNot($post->id)
                ->when($post->type === 'bilimsel',
                    fn ($q) => $q->where('type', 'bilimsel'),
                    fn ($q) => $q->where('type', '!=', 'bilimsel'))
                ->whereNotNull('title->'.app()->getLocale())
                ->orderByDesc('published_at')->limit(3)->get(),
        ]);
    }
}
