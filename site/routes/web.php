<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;

/*
 * Her rota iki kez tanımlanır: Türkçe (önek yok) ve İngilizce (/en).
 * Adlar dil önekli: tr.diseases.show, en.diseases.show — lroute() yardımcıyı kullanın.
 * İngilizce grup önce kaydedilir ki Türkçe {slug} rotası "/en" adresini yutmasın.
 */
foreach (['en' => 'en', 'tr' => ''] as $locale => $prefix) {
    $p = fn (string $key) => config("site.paths.{$key}.{$locale}");

    Route::middleware("locale:{$locale}")->prefix($prefix)->name("{$locale}.")->group(function () use ($p) {
        Route::get('/', Controllers\HomeController::class)->name('home');

        Route::get($p('diseases'), [Controllers\DiseaseController::class, 'index'])->name('diseases.index');
        Route::get($p('diseases').'/{slug}', [Controllers\DiseaseController::class, 'show'])->name('diseases.show');

        Route::get($p('support'), [Controllers\SupportController::class, 'index'])->name('support.index');
        Route::get($p('support').'/{slug}', [Controllers\SupportController::class, 'show'])->name('support.show');

        Route::get($p('news'), [Controllers\PostController::class, 'index'])->name('news.index');
        Route::get($p('news').'/{slug}', [Controllers\PostController::class, 'show'])->name('news.show');
        Route::get($p('events'), [Controllers\PostController::class, 'events'])->name('events.index');

        Route::get($p('stories'), [Controllers\StoryController::class, 'index'])->name('stories.index');
        Route::get($p('stories').'/{slug}', [Controllers\StoryController::class, 'show'])->name('stories.show');

        Route::get($p('publications'), Controllers\PublicationController::class)->name('publications.index');
        Route::get($p('professionals'), Controllers\ProfessionalsController::class)->name('professionals');
        Route::get($p('search'), Controllers\SearchController::class)->name('search');

        Route::get($p('ask'), [Controllers\QuestionController::class, 'create'])->name('ask.create');
        Route::post($p('ask'), [Controllers\QuestionController::class, 'store'])->name('ask.store')->middleware('throttle:forms');

        Route::get($p('contact'), [Controllers\ContactController::class, 'create'])->name('contact.create');
        Route::post($p('contact'), [Controllers\ContactController::class, 'store'])->name('contact.store')->middleware('throttle:forms');

        Route::get($p('membership'), [Controllers\MembershipController::class, 'create'])->name('membership.create');
        Route::post($p('membership'), [Controllers\MembershipController::class, 'store'])->name('membership.store')->middleware('throttle:forms');

        Route::post($p('newsletter'), [Controllers\NewsletterController::class, 'store'])->name('newsletter.store')->middleware('throttle:forms');
        Route::get($p('newsletter').'/ayril/{token}', [Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

        Route::get($p('donate'), [Controllers\DonationController::class, 'create'])->name('donate.create');
        Route::post($p('donate'), [Controllers\DonationController::class, 'store'])->name('donate.store')->middleware('throttle:forms');
        Route::get($p('donate').'/sonuc/{donation:order_id}', [Controllers\DonationController::class, 'result'])->name('donate.result');

        // Kurumsal ve yasal sayfalar: /hakkimizda, /en/about-us ...
        Route::get('{slug}', [Controllers\PageController::class, 'show'])
            ->where('slug', '^(?!admin$|en$|livewire|filament|storage|up$)[a-z0-9-]+$')
            ->name('page');
    });
}

// Garanti BBVA dönüş adresleri (dil bağımsız, CSRF muaf)
Route::post('odeme/garanti/basarili', [Controllers\DonationController::class, 'callback'])->name('donate.callback.success');
Route::post('odeme/garanti/basarisiz', [Controllers\DonationController::class, 'callback'])->name('donate.callback.fail');

Route::get('sitemap.xml', Controllers\SitemapController::class)->name('sitemap');

// Eski site adresleri (/TR,23/akut-miyeloid-losemi-aml.html) ve panelden tanımlanan yönlendirmeler
Route::fallback(Controllers\LegacyRedirectController::class);
