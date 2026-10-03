<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use Translatable;

    public const SECTIONS = [
        'rehber' => 'Hasta rehberi (Şu an neredesiniz?)',
        'destek' => 'Size destek',
        'kurumsal' => 'Kurumsal',
        'katilim' => 'Destek olun',
        'yasal' => 'Yasal metinler',
    ];

    public array $translatable = ['title', 'slug', 'summary', 'body'];

    protected $guarded = [];

    protected $casts = ['is_published' => 'boolean'];
}
