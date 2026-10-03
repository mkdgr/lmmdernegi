<?php

namespace App\Providers;

use App\Models\Disease;
use App\Models\Page;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Form gönderimleri: IP başına dakikada 6
        RateLimiter::for('forms', fn (Request $request) => Limit::perMinute(6)->by($request->ip()));

        Paginator::defaultView('partials.pagination');

        // Menü verileri (her istekte bir kez)
        View::composer('partials.header', function ($view) {
            static $data = null;
            $data ??= [
                'navDiseases' => Disease::published()->orderBy('sort')->get(),
                'navJourney' => Page::published()->where('section', 'rehber')->orderBy('sort')->get(),
            ];
            $view->with($data);
        });

        // Varsayılan dil değiştirici: her dilin ana sayfası (sayfalar kendi karşılığını set_alternates ile verir)
        View::composer('layouts.app', function ($view) {
            if (! $view->offsetExists('alternates') && ! View::shared('alternates')) {
                $view->with('alternates', [
                    'tr' => route('tr.home'),
                    'en' => route('en.home'),
                ]);
            }
        });
    }
}
