<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use Translatable;

    public const KINDS = [
        'iyilesen' => 'Ben de kanseri yendim',
        'yasayan' => 'Hastalıkla yaşamak',
        'yakin' => 'Hasta yakını',
    ];

    public array $translatable = ['title', 'slug', 'condition', 'quote', 'body'];

    protected $guarded = [];

    protected $casts = [
        'has_consent' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function kindLabel(): string
    {
        return __(self::KINDS[$this->kind] ?? $this->kind);
    }

    public function initials(): string
    {
        return mb_strtoupper(mb_substr($this->person_name, 0, 1));
    }
}
