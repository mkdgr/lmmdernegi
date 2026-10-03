<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Translatable;

    public const TYPES = [
        'haber' => 'Haber',
        'etkinlik' => 'Etkinlik',
        'duyuru' => 'Duyuru',
        'bilimsel' => 'Bilimsel yazı',
    ];

    public const AUDIENCES = [
        'herkes' => 'Herkes',
        'hasta' => 'Hasta ve yakınları',
        'hekim' => 'Sağlık çalışanları',
    ];

    public array $translatable = ['title', 'slug', 'excerpt', 'body', 'location'];

    protected $guarded = [];

    protected $casts = [
        'event_starts_at' => 'datetime',
        'event_ends_at' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('type', 'etkinlik')->where('event_starts_at', '>=', now()->startOfDay())->orderBy('event_starts_at');
    }

    public function displayDate()
    {
        return $this->event_starts_at ?? $this->published_at ?? $this->created_at;
    }

    public function typeLabel(): string
    {
        return __(self::TYPES[$this->type] ?? $this->type);
    }

    public function isPast(): bool
    {
        return $this->event_starts_at && ($this->event_ends_at ?? $this->event_starts_at)->endOfDay()->isPast();
    }
}
