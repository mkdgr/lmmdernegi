<?php

namespace App\Http\Controllers;

use App\Models\Publication;

class PublicationController extends Controller
{
    public function __invoke()
    {
        set_alternates('publications.index');

        $all = Publication::published()->orderByDesc('published_on')->orderByDesc('issue_no')->get();

        return view('publications.index', [
            'brochures' => $all->where('kind', 'brosur'),
            'bulletins' => $all->where('kind', 'bulten'),
            'reports' => $all->where('kind', 'rapor'),
        ]);
    }
}
