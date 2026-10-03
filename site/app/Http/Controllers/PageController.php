<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::published()->whereSlug($slug)->first();

        if (! $page) {
            // Diğer dildeki slug ile gelindiyse doğru adrese yönlendir
            $other = app()->getLocale() === 'tr' ? 'en' : 'tr';
            $page = Page::published()->whereSlug($slug, $other)->first();
            abort_unless($page, 404);

            return redirect(lroute(in_array($page->section, ['rehber', 'destek'], true) ? 'support.show' : 'page', $page->slugFor()), 301);
        }

        if (in_array($page->section, ['rehber', 'destek'], true)) {
            return redirect(lroute('support.show', $page->slugFor()), 301);
        }

        set_alternates('page', $page);

        return view('pages.show', [
            'page' => $page,
            'crumb' => null,
            'related' => Page::published()->where('section', $page->section)->whereKeyNot($page->id)->orderBy('sort')->get(),
        ]);
    }
}
