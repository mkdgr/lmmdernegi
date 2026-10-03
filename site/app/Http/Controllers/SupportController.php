<?php

namespace App\Http\Controllers;

use App\Models\Page;

class SupportController extends Controller
{
    public function index()
    {
        set_alternates('support.index');

        return view('support.index', [
            'journey' => Page::published()->where('section', 'rehber')->orderBy('sort')->get(),
            'guides' => Page::published()->where('section', 'destek')->orderBy('sort')->get(),
        ]);
    }

    public function show(string $slug)
    {
        $page = Page::published()->whereIn('section', ['rehber', 'destek'])->whereSlug($slug)->firstOrFail();
        set_alternates('support.show', $page);

        return view('pages.show', [
            'page' => $page,
            'crumb' => ['label' => __('Size destek'), 'url' => lroute('support.index')],
            'related' => Page::published()->where('section', $page->section)->whereKeyNot($page->id)->orderBy('sort')->get(),
        ]);
    }
}
