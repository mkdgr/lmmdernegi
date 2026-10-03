<?php

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

if (! function_exists('lroute')) {
    /** Geçerli dildeki rota: lroute('diseases.show', 'aml') */
    function lroute(string $name, mixed $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        return route(($locale ?? app()->getLocale()).'.'.$name, $parameters, $absolute);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            return Setting::get($key, $default);
        } catch (Throwable) {
            return $default;
        }
    }
}

if (! function_exists('media_url')) {
    /** Panelden yüklenen dosya (storage) ya da tema varlığı (assets/...) için URL. */
    function media_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }
        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return $path;
        }
        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        // Alan adından bağımsız: isteğin geldiği adresle /storage/... üret
        return asset('storage/'.ltrim($path, '/'));
    }
}

if (! function_exists('set_alternates')) {
    /**
     * Dil değiştiricinin gideceği adresler. Model verilirse yalnızca
     * çevirisi olan dillere bağlantı verilir; yoksa o dilin ana sayfasına gidilir.
     */
    function set_alternates(string $routeName, mixed $model = null, array $params = []): void
    {
        $alternates = [];
        foreach (array_keys(config('site.locales')) as $locale) {
            if ($model && method_exists($model, 'hasLocale') && ! $model->hasLocale($locale)) {
                $alternates[$locale] = lroute('home', [], $locale);

                continue;
            }
            $p = $model ? array_merge([$model->slugFor($locale)], $params) : $params;
            $alternates[$locale] = lroute($routeName, $p, $locale);
        }
        View::share('alternates', $alternates);
    }
}

if (! function_exists('phone_href')) {
    function phone_href(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '90'.substr($digits, 1);
        }

        return 'tel:+'.$digits;
    }
}

if (! function_exists('tr_date')) {
    function tr_date($date, string $format = 'j F Y'): string
    {
        return $date ? $date->locale(app()->getLocale())->translatedFormat($format) : '';
    }
}

if (! function_exists('page_url')) {
    /**
     * Kurumsal/yasal sayfa bağlantısı, Türkçe slug ile: page_url('hakkimizda').
     * Sayfa yoksa ya da yayında değilse ana sayfaya düşer.
     */
    function page_url(string $trSlug, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        static $map = null;
        if ($map === null) {
            try {
                $map = Page::published()->get()->mapWithKeys(fn ($p) => [
                    $p->getTranslation('slug', 'tr', false) => $p,
                ])->all();
            } catch (Throwable) {
                $map = [];
            }
        }
        $page = $map[$trSlug] ?? null;
        if (! $page) {
            return lroute('home', [], $locale);
        }
        $route = in_array($page->section, ['rehber', 'destek'], true) ? 'support.show' : 'page';

        return lroute($route, $page->slugFor($locale), $locale);
    }
}

if (! function_exists('whatsapp_href')) {
    function whatsapp_href(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '90'.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits;
    }
}
