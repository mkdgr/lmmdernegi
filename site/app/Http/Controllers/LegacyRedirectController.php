<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Page;
use App\Models\Post;
use App\Models\Publication;
use App\Models\Redirect;
use App\Models\Story;
use Illuminate\Http\Request;

/**
 * Bilinmeyen adresler için son durak:
 * 1) Panelden tanımlanan yönlendirmeler (redirects tablosu)
 * 2) Eski sitenin /TR,{id}/... adresleri → içerikteki legacy_id ile yeni adres
 * 3) Hiçbiri değilse 404
 */
class LegacyRedirectController extends Controller
{
    /** Eski sitede içerik olmayan "bölüm" sayfaları. */
    private const SECTION_MAP = [
        8 => 'home', 10 => 'diseases.index', 11 => 'professionals', 6 => 'ask.create',
        15 => 'contact.create', 22 => 'membership.create', 126 => 'donate.create', 7 => 'donate.create',
        163 => 'news.index', 168 => 'news.index', 160 => 'publications.index', 54 => 'publications.index',
        5 => 'stories.index',
    ];

    public function __invoke(Request $request)
    {
        $path = Redirect::normalize($request->getPathInfo());

        if ($redirect = Redirect::where('from_path', $path)->first()) {
            $redirect->increment('hits');

            return redirect($redirect->to_path, $redirect->status_code);
        }

        if (preg_match('#^/(tr|en),(\d+)(/|$)#i', $path, $m)) {
            if ($url = $this->legacyUrl((int) $m[2])) {
                return redirect($url, 301);
            }
        }

        app()->setLocale(str_starts_with($path, '/en/') ? 'en' : 'tr');
        abort(404);
    }

    private function legacyUrl(int $id): ?string
    {
        app()->setLocale('tr');

        if ($disease = Disease::published()->where('legacy_id', $id)->first()) {
            return lroute('diseases.show', $disease->slugFor());
        }
        if ($page = Page::published()->where('legacy_id', $id)->first()) {
            return in_array($page->section, ['rehber', 'destek'], true)
                ? lroute('support.show', $page->slugFor())
                : lroute('page', $page->slugFor());
        }
        if ($post = Post::published()->where('legacy_id', $id)->first()) {
            return lroute('news.show', $post->slugFor());
        }
        if ($story = Story::published()->where('legacy_id', $id)->first()) {
            return lroute('stories.show', $story->slugFor());
        }
        if (Publication::where('legacy_id', $id)->exists()) {
            return lroute('publications.index');
        }
        if (isset(self::SECTION_MAP[$id])) {
            return lroute(self::SECTION_MAP[$id]);
        }

        return null;
    }
}
