<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    use Translatable;

    public const GROUPS = ['losemi' => 'Lösemi', 'lenfoma' => 'Lenfoma', 'miyelom' => 'Miyelom'];

    public array $translatable = ['abbr_translated', 'name', 'slug', 'summary', 'body'];

    protected string $slugSource = 'name';

    protected $guarded = [];

    protected $casts = [
        'faq' => 'array',
        'reviewed_at' => 'date',
        'is_published' => 'boolean',
    ];

    public function displayAbbr(): string
    {
        return $this->getTranslation('abbr_translated', app()->getLocale(), false) ?: $this->abbr;
    }

    public function groupLabel(): string
    {
        return __(self::GROUPS[$this->group] ?? $this->group);
    }

    /** Sıkça sorulan sorular, geçerli dilde. */
    public function faqItems(): array
    {
        $locale = app()->getLocale();

        return collect($this->faq ?? [])
            ->map(fn ($i) => [
                'question' => $i['question'][$locale] ?? $i['question']['tr'] ?? null,
                'answer' => $i['answer'][$locale] ?? $i['answer']['tr'] ?? null,
            ])
            ->filter(fn ($i) => filled($i['question']) && filled($i['answer']))
            ->values()->all();
    }
}
