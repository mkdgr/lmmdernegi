<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * TR/EN çevrilebilir içerik için ortak davranışlar.
 * - Boş çeviriler saklanmaz (bir dilde içerik yoksa o dil için null döner).
 * - Slug her dil için ayrı tutulur; boşsa başlıktan üretilir.
 */
trait Translatable
{
    use HasTranslations;

    public static function bootTranslatable(): void
    {
        static::saving(function ($model) {
            // Boş çevirileri ("" ya da null) JSON'dan çıkar: "İngilizcesi var mı?" kontrolleri doğru çalışsın
            foreach ($model->getTranslatableAttributes() as $attr) {
                if (array_key_exists($attr, $model->getAttributes())) {
                    $clean = array_filter($model->getTranslations($attr), fn ($v) => ! blank(is_string($v) ? trim(strip_tags($v, '<img><iframe>')) : $v));
                    $model->attributes[$attr] = $clean ? json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
                }
            }

            if (! in_array('slug', $model->translatable, true)) {
                return;
            }
            $source = $model->slugSource ?? 'title';
            foreach (['tr', 'en'] as $locale) {
                $slug = $model->getTranslation('slug', $locale, false);
                $title = $model->getTranslation($source, $locale, false);
                if (blank($slug) && filled($title)) {
                    $model->setTranslation('slug', $locale, static::uniqueSlug($title, $locale, $model->getKey()));
                }
            }
        });
    }

    public static function uniqueSlug(string $title, string $locale, $ignoreId = null): string
    {
        $base = Str::slug(str_replace(['ı', 'İ'], ['i', 'i'], $title), '-', 'tr') ?: 'icerik';
        $base = Str::limit($base, 90, '');
        $slug = $base;
        $i = 2;
        while (static::query()->where("slug->{$locale}", $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeWhereSlug(Builder $query, string $slug, ?string $locale = null): Builder
    {
        return $query->where('slug->'.($locale ?? app()->getLocale()), $slug);
    }

    /** Bu kaydın istenen dilde içeriği var mı? */
    public function hasLocale(string $locale): bool
    {
        $field = $this->slugSource ?? 'title';

        return filled($this->getTranslation($field, $locale, false));
    }

    public function slugFor(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        return $this->getTranslation('slug', $locale, false) ?: $this->getTranslation('slug', 'tr', false);
    }
}
