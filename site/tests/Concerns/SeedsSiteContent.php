<?php

namespace Tests\Concerns;

use App\Models\Disease;
use App\Models\Page;
use App\Models\Post;
use App\Models\Publication;
use App\Models\Setting;
use App\Models\Story;

/** Testler için küçük, hızlı bir içerik seti (gerçek seed 65 MB medya kopyalar). */
trait SeedsSiteContent
{
    protected function seedSite(): void
    {
        Setting::put('phone', '0530 156 87 68');
        Setting::put('bank_accounts', [['bank' => 'Garanti BBVA', 'iban' => 'TR02 0006 2001 3610 0006 2936 42', 'swift' => 'TGBATRISXXX']]);

        Disease::create([
            'abbr' => 'AML', 'group' => 'losemi', 'sort' => 1, 'legacy_id' => 23,
            'name' => ['tr' => 'Akut Miyeloid Lösemi', 'en' => 'Acute Myeloid Leukemia'],
            'summary' => ['tr' => 'Hızlı seyirli bir kan kanseri.', 'en' => 'A fast-growing blood cancer.'],
            'body' => ['tr' => '<h2>AML nedir?</h2><p>Kemik iliği kanseri.</p><h2>Belirtiler</h2><p>Ateş, halsizlik.</p>'],
            'faq' => [['question' => ['tr' => 'Bulaşıcı mı?'], 'answer' => ['tr' => 'Hayır.']]],
        ]);
        Disease::create([
            'abbr' => 'KML', 'abbr_translated' => ['en' => 'CML'], 'group' => 'losemi', 'sort' => 2,
            'name' => ['tr' => 'Kronik Miyeloid Lösemi', 'en' => 'Chronic Myeloid Leukemia'],
        ]);

        Page::create(['section' => 'rehber', 'sort' => 1, 'title' => ['tr' => 'Yeni tanı aldım', 'en' => 'Newly diagnosed'],
            'summary' => ['tr' => 'İlk haftalar'], 'body' => ['tr' => '<p>Rehber</p>', 'en' => '<p>Guide</p>']]);
        Page::create(['section' => 'destek', 'sort' => 1, 'legacy_id' => 106, 'title' => ['tr' => 'Beslenme'], 'body' => ['tr' => '<p>Beslenme önerileri</p>']]);
        Page::create(['section' => 'kurumsal', 'sort' => 1, 'title' => ['tr' => 'Hakkımızda', 'en' => 'About us'], 'slug' => ['tr' => 'hakkimizda', 'en' => 'about-us'], 'body' => ['tr' => '<p>2011</p>', 'en' => '<p>2011</p>']]);
        Page::create(['section' => 'yasal', 'sort' => 1, 'title' => ['tr' => 'KVKK aydınlatma metni'], 'slug' => ['tr' => 'kvkk-aydinlatma-metni'], 'body' => ['tr' => '<p>KVKK</p>']]);

        Post::create(['type' => 'etkinlik', 'audience' => 'hasta', 'legacy_id' => 312,
            'title' => ['tr' => 'AML Tedavisinde Yeni Ufuklar', 'en' => 'New Horizons in AML'],
            'location' => ['tr' => 'Ankara'], 'event_starts_at' => now()->addWeek(), 'published_at' => now()->subDay()]);
        Post::create(['type' => 'etkinlik', 'audience' => 'hekim', 'title' => ['tr' => 'Eski Sempozyum'],
            'event_starts_at' => now()->subYear(), 'published_at' => now()->subYear(), 'body' => ['tr' => '<p>Program</p>']]);
        Post::create(['type' => 'bilimsel', 'audience' => 'hekim', 'title' => ['tr' => 'Rituximab Infusion', 'en' => 'Rituximab Infusion'], 'published_at' => now()->subYears(5)]);
        Post::create(['type' => 'haber', 'title' => ['tr' => 'Taslak haber'], 'is_published' => false]);

        Story::create(['kind' => 'yasayan', 'person_name' => 'Yaprak', 'title' => ['tr' => 'KML ile yaşamak'],
            'quote' => ['tr' => 'Korkularım yersizdi.'], 'body' => ['tr' => '<p>Hikâye</p>'], 'has_consent' => true,
            'is_published' => true, 'published_at' => now()]);

        Publication::create(['kind' => 'bulten', 'issue_no' => 1, 'title' => ['tr' => 'LLMBİR Bülten — Sayı 1'], 'external_url' => 'https://example.org/sayi-1.pdf']);
    }
}
