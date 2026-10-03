<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Page;

class DiseaseController extends Controller
{
    public function index()
    {
        set_alternates('diseases.index');

        return view('diseases.index', [
            'groups' => Disease::published()->orderBy('sort')->get()->groupBy('group'),
            'intro' => Page::published()->where('section', 'destek')->where('legacy_id', 46)->first(),
        ]);
    }

    public function show(string $slug)
    {
        $disease = Disease::published()->whereSlug($slug)->firstOrFail();
        set_alternates('diseases.show', $disease);

        return view('diseases.show', [
            'disease' => $disease,
            'siblings' => Disease::published()->orderBy('sort')->get(),
        ]);
    }
}
