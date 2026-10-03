<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\Page;
use App\Models\Post;
use App\Models\Publication;
use App\Models\Story;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Eski siteden (losemilenfomamiyelom.org, Adasoft CMS) çekilen içeriği aktarır.
 * Kaynak: database/legacy/pages.json (+ media/, galleries.json, documents.json)
 * Her içerik legacy_id taşır; eski /TR,{id}/... adresleri bu sayede yeni adrese yönlenir.
 */
class LegacyContentSeeder extends Seeder
{
    private array $pages = [];

    private array $documents = [];

    /** Hastalık sayfaları: legacy_id => [kısaltma, EN kısaltma, grup, sıra, EN ad] */
    private const DISEASES = [
        23 => ['AML', null, 'losemi', 1, 'Acute Myeloid Leukemia'],
        24 => ['ALL', null, 'losemi', 2, 'Acute Lymphoblastic Leukemia'],
        25 => ['KML', 'CML', 'losemi', 3, 'Chronic Myeloid Leukemia'],
        26 => ['KLL', 'CLL', 'losemi', 4, 'Chronic Lymphocytic Leukemia'],
        27 => ['HL', null, 'lenfoma', 5, 'Hodgkin Lymphoma'],
        29 => ['HDL', 'NHL', 'lenfoma', 6, 'Non-Hodgkin Lymphoma'],
        30 => ['MM', null, 'miyelom', 7, 'Multiple Myeloma'],
    ];

    /** Hastalık özetleri (TR / EN) — yeni yazıldı; dernek hekimlerince gözden geçirilmeli */
    private const DISEASE_SUMMARIES = [
        23 => ['Kemik iliğindeki miyeloid öncü hücrelerden gelişen, hızlı seyirli bir kan kanseri. Erişkinlerde en sık görülen akut lösemidir.',
            'A fast-growing blood cancer that starts in the myeloid precursor cells of the bone marrow. It is the most common acute leukemia in adults.'],
        24 => ['Lenfoid öncü hücrelerden (lenfoblastlar) gelişen, hızlı seyirli bir lösemi. Çocukluk çağının en sık kanseridir; erişkinlerde de görülür.',
            'A fast-growing leukemia that develops from lymphoid precursor cells (lymphoblasts). It is the most common childhood cancer and also occurs in adults.'],
        25 => ['Kemik iliğinde yavaş seyreden, Philadelphia kromozomu ile ilişkili bir lösemi. Günümüzde ağızdan alınan hedefe yönelik ilaçlarla çoğu hastada uzun süre kontrol altında tutulabilir.',
            'A slowly progressing leukemia linked to the Philadelphia chromosome. Today, oral targeted medicines keep it under control for a long time in most patients.'],
        26 => ['Olgun görünümlü lenfositlerin kanda ve kemik iliğinde birikmesiyle seyreden, çoğunlukla yavaş ilerleyen bir lösemi. Genellikle orta ve ileri yaşlarda görülür.',
            'A usually slow-growing leukemia in which mature-looking lymphocytes build up in the blood and bone marrow. It mostly affects middle-aged and older adults.'],
        27 => ['Lenf sisteminden kaynaklanan, Reed-Sternberg hücreleriyle tanımlanan bir lenfoma türü. Sıklıkla genç erişkinlerde görülür ve tedaviye iyi yanıt verir.',
            'A lymphoma of the lymphatic system defined by Reed-Sternberg cells. It often affects young adults and usually responds well to treatment.'],
        29 => ['Lenfositlerden kaynaklanan, çok sayıda alt türü olan lenfoma grubu. Alt türüne göre yavaş ya da hızlı seyredebilir.',
            'A group of lymphomas arising from lymphocytes, with many subtypes. Depending on the subtype it may grow slowly or quickly.'],
        30 => ['Kemik iliğindeki plazma hücrelerinin kontrolsüz çoğalmasıyla oluşan bir kan kanseri. Kemik ağrısı, kansızlık ve böbrek sorunlarına yol açabilir.',
            'A blood cancer caused by the uncontrolled growth of plasma cells in the bone marrow. It can cause bone pain, anaemia and kidney problems.'],
    ];

    /** Sayfalar: legacy_id => [bölüm, TR slug, sıra, TR başlık (boşsa eskisi), TR özet, EN başlık] */
    private const PAGES = [
        46 => ['destek', 'hastalar-icin-genel-bilgiler', 1, 'Kan kanserleri hakkında genel bilgiler', 'Kan ve kemik iliği nasıl çalışır, lösemi, lenfoma ve miyelom nedir? Tanı ve tedavi sürecine hazırlanmak için temel bilgiler.', 'Blood cancers: the basics'],
        105 => ['destek', 'enfeksiyonlardan-korunma', 2, 'Enfeksiyonlardan korunma', 'Tedavi sırasında bağışıklık sistemi zayıflar. Enfeksiyon riskini azaltmak için günlük hayatta dikkat edilmesi gerekenler.', 'Preventing infections'],
        106 => ['destek', 'beslenme', 3, 'Tedavi sürecinde beslenme', 'Kemoterapi ve kök hücre nakli döneminde güvenli ve dengeli beslenme önerileri.', 'Nutrition during treatment'],
        13 => ['destek', 'hasta-ve-hekim-haklari', 4, 'Hasta ve hekim hakları', 'Türkiye\'de sağlık hizmeti alırken sahip olduğunuz haklar ve hekimlerin hak ve sorumlulukları.', 'Patient and physician rights'],
        32 => ['destek', 'sikca-sorulan-sorular', 5, 'Derneğe sıkça sorulan sorular', 'Hastalarımızın ve yakınlarının derneğimize en sık yönelttiği sorular ve yanıtları.', 'Frequently asked questions'],
        14 => ['destek', 'alternatif-tedaviler', 6, 'Alternatif tedaviler hakkında', 'Bitkisel ürünler ve "mucize" tedavi vaatleri: hekiminize danışmadan neden kullanmamalısınız?', 'About alternative treatments'],
        221 => ['destek', 'covid-19-bilgilendirme', 7, 'COVID-19 ve aşı bilgilendirmesi', 'Lösemi, lenfoma ve miyelom hastaları için COVID-19 hastalığı ve aşısı hakkında bilgilendirme.', 'COVID-19 and vaccination'],
        12 => ['katilim', 'kok-hucre-vericisi-olmak', 1, 'Kök hücre vericisi olun', 'Kök hücre bağışı nedir, kimler verici olabilir, nasıl kayıt olunur?', 'Become a stem cell donor'],
        20 => ['kurumsal', 'yonetim-kurulu', 2, 'Yönetim kurulu', null, 'Board of directors'],
        21 => ['kurumsal', 'tuzuk', 3, 'Tüzük', 'Lösemi Lenfoma Miyelom Derneği tüzüğü.', 'Statute'],
        85 => ['kurumsal', 'uluslararasi-baglantilar', 4, 'Uluslararası bağlantılar', 'Kök hücre bağışı ve hasta destek alanında uluslararası kuruluşlar.', 'International links'],
        252 => ['yasal', 'kongre-aydinlatma-metni', 10, 'Kongre aydınlatma metni', null, null],
        253 => ['yasal', 'yuruyus-aydinlatma-metni', 11, 'Yürüyüş aydınlatma metni', null, null],
        59 => ['destek', 'cep-telefonlari-ve-baz-istasyonlari', 20, 'Cep telefonları ve baz istasyonları hakkında', null, null],
    ];

    /** Yayından kaldırılacak eski sayfalar (güncelliğini yitirmiş) */
    private const UNPUBLISHED = [59];

    /** Bölüm/form sayfaları: içerik olarak aktarılmaz (yeni sitede karşılıkları var) */
    private const SKIP = [4, 5, 6, 7, 8, 9, 10, 11, 15, 22, 54, 70, 89, 90, 91, 92, 100, 104, 126, 160, 163, 168, 239, 314];

    /** İngilizce bilimsel haberler/özetler */
    private const SCIENTIFIC = [71, 72, 74, 75, 76, 77, 78, 79, 81, 84, 94, 95, 98];

    /** Afişlerden okunan kesin tarih ve yerler: id => [başlangıç, bitiş, yer] */
    private const EVENT_FACTS = [
        301 => ['2026-05-21 09:00', '2026-05-23 18:00', 'Divan Otel, Ankara'],
        303 => ['2026-09-15 19:00', null, 'Çevrim içi — YouTube canlı yayın'],
        304 => ['2026-09-20 16:00', null, 'Ankara Üniversitesi 10. Yıl Yerleşkesi Güneş Meydanı, Ankara'],
        305 => ['2026-09-22 19:00', null, 'Çevrim içi — YouTube canlı yayın'],
        312 => ['2026-09-21 18:15', null, 'Holiday Inn Çukurambar, Ankara'],
    ];

    /** İngilizce sitede de listelensin diye seçili etkinliklerin İngilizce başlıkları */
    private const POST_TITLES_EN = [
        312 => 'New Horizons and Innovative Approaches in AML Treatment',
        305 => 'CML Will End! — patient information livestream',
        304 => 'Walk With Us Against Lymphoma — 12th annual walk',
        303 => 'Lymphoma Will End! — patient information livestream',
        302 => 'What We Learned in Madrid, Chicago and Stockholm',
        301 => '6th Leukemia Lymphoma Myeloma Congress',
        311 => '14th Patient Congress',
        300 => '3rd Leukemia Lymphoma Myeloma Youth Camp',
        299 => 'Everything You Want to Know About Multiple Myeloma',
        290 => '13th Patient Congress',
        286 => '2nd Leukemia Lymphoma Myeloma Youth Camp',
        170 => 'Lymphoma Walk, Ankara (September 2017)',
    ];

    public function run(): void
    {
        $dir = database_path('legacy');
        $this->pages = collect(json_decode(File::get($dir.'/pages.json'), true))->keyBy('id')->all();
        $this->documents = json_decode(File::get($dir.'/documents.json'), true);
        $galleries = json_decode(File::get($dir.'/galleries.json'), true);

        $this->copyMedia($dir.'/media');

        $this->seedDiseases();
        $this->seedPages();
        $this->seedStory();
        $this->seedPublications();
        $this->seedPosts($galleries);
    }

    private function copyMedia(string $from): void
    {
        $disk = Storage::disk('public');
        foreach (File::files($from) as $file) {
            $target = 'legacy/'.$file->getFilename();
            if (! $disk->exists($target)) {
                $disk->put($target, File::get($file->getPathname()));
            }
        }
    }

    /** Eski HTML'deki belge bağlantılarını yeni konumlarına çevirir. */
    private function html(int $id): string
    {
        $html = $this->pages[$id]['html'] ?? '';
        $html = preg_replace_callback('#href="(/Eklenti/[^"]+)"#', function ($m) {
            $doc = $this->documents[$m[1]] ?? null;
            if (! $doc) {
                return $m[0];
            }
            $dir = $doc['in_repo'] ? 'legacy' : 'legacy-arsiv';

            return 'href="/storage/'.$dir.'/'.$doc['name'].'" target="_blank" rel="noopener"';
        }, $html);
        // Görsel yolları: /storage/legacy/... (göreli değil, kök göreli)
        $html = str_replace(['<h2></h2>', '<p> </p>'], '', $html);

        return trim($html);
    }

    private function relImage(?string $path): ?string
    {
        return $path ? ltrim(Str::after($path, '/storage/'), '/') : null;
    }

    private function seedDiseases(): void
    {
        foreach (self::DISEASES as $id => [$abbr, $abbrEn, $group, $sort, $nameEn]) {
            $p = $this->pages[$id];
            $nameTr = trim(preg_replace('/\s*\([^)]*\)\s*$/u', '', $p['title']));
            [$sumTr, $sumEn] = self::DISEASE_SUMMARIES[$id];

            Disease::updateOrCreate(['legacy_id' => $id], [
                'abbr' => $abbr,
                'abbr_translated' => $abbrEn ? ['en' => $abbrEn] : [],
                'name' => ['tr' => $nameTr, 'en' => $nameEn],
                'slug' => ['tr' => Str::slug(str_replace(['ı', 'İ'], 'i', $nameTr)), 'en' => Str::slug($nameEn)],
                'group' => $group,
                'summary' => ['tr' => $sumTr, 'en' => $sumEn],
                'body' => ['tr' => $this->html($id)],
                'sort' => $sort,
                'is_published' => true,
            ]);
        }
    }

    private function seedPages(): void
    {
        foreach (self::PAGES as $id => [$section, $slug, $sort, $title, $summary, $titleEn]) {
            $p = $this->pages[$id];
            Page::updateOrCreate(['legacy_id' => $id], [
                'section' => $section,
                'title' => array_filter(['tr' => $title ?: $p['title'], 'en' => $titleEn]),
                'slug' => array_filter(['tr' => $slug, 'en' => $titleEn ? Str::slug($titleEn) : null]),
                'summary' => array_filter(['tr' => $summary]),
                'body' => ['tr' => $this->html($id)],
                'sort' => $sort,
                'is_published' => ! in_array($id, self::UNPUBLISHED, true),
            ]);
        }

        // "Ben de Varım" sosyal farkındalık kampanyası: 4 eski sayfa tek sayfada
        $body = collect([90, 91, 92])->map(fn ($i) => '<h2>'.e($this->pages[$i]['title']).'</h2>'.$this->html($i))->implode("\n");
        Page::updateOrCreate(['legacy_id' => 89], [
            'section' => 'kurumsal',
            'title' => ['tr' => 'Ben de Varım — Sosyal farkındalık kampanyası'],
            'slug' => ['tr' => 'ben-de-varim-kampanyasi'],
            'summary' => ['tr' => 'Derneğimizin lösemi, lenfoma ve miyelom hastalarına yönelik sosyal farkındalık kampanyası.'],
            'body' => ['tr' => $body],
            'sort' => 6,
            'is_published' => true,
        ]);
    }

    private function seedStory(): void
    {
        $p = $this->pages[5];
        // Eski metin Türkçe karakter olmadan yazılmış; özgün hâliyle korunur
        $html = preg_replace('#<p><img[^>]*></p>|<img[^>]*>#', '', $this->html(5));
        $html = preg_replace('#<p>\s*Yaprak Dolek Aydan - 22\.09\.2012\s*</p>#u', '', $html);

        Story::updateOrCreate(['legacy_id' => 5], [
            'kind' => 'yasayan',
            'title' => ['tr' => 'KML\'den korkmayın, o sizden korksun!'],
            'slug' => ['tr' => 'kmlden-korkmayin-o-sizden-korksun'],
            'person_name' => 'Yaprak Dolek Aydan',
            'condition' => ['tr' => 'KML', 'en' => 'CML'],
            'quote' => ['tr' => 'Hastalığım hakkında bilgi sahibi oldukça, ilaç tedavisinin mümkün olduğunu öğrenince öyle rahatladım ki… En sonunda kabul ettim ki korkularımın hepsi yersiz.'],
            'body' => ['tr' => trim($html)],
            'has_consent' => true, // eski sitede yayımlanmış hikâye
            'is_featured' => true,
            'is_published' => true,
            'published_at' => '2012-09-22 10:00:00',
        ]);
    }

    private function seedPublications(): void
    {
        foreach ($this->pages as $id => $p) {
            if (! $p['pdf'] || ! preg_match('#/sayi-(\d+)(?:-(\d+))?\.html$#', $p['path'], $m)) {
                continue;
            }
            $no = (int) $m[1];
            $label = isset($m[2]) && $m[2] !== '' ? "Sayı {$m[1]}–{$m[2]}" : "Sayı {$no}";
            Publication::updateOrCreate(['legacy_id' => $id], [
                'kind' => 'bulten',
                'title' => ['tr' => "LLMBİR Bülten — {$label}", 'en' => 'LLMBİR Bulletin — '.str_replace('Sayı', 'Issue', $label)],
                'issue_no' => $no,
                'external_url' => 'https://www.losemilenfomamiyelom.org'.$p['path'],
                'is_published' => true,
            ]);
        }

        // Tanıtım broşürü ve COVID-19 aşı bilgilendirmesi
        $brochure = $this->pages[54]['documents'][0] ?? null;
        Publication::updateOrCreate(['legacy_id' => 54], [
            'kind' => 'brosur',
            'title' => ['tr' => 'Dernek tanıtım broşürü', 'en' => 'Association brochure (Turkish)'],
            'file' => $this->relImage($brochure),
            'external_url' => $brochure ? null : 'https://www.losemilenfomamiyelom.org/TR,54/tanitim-brosuru.html',
            'is_published' => true,
        ]);
        Publication::updateOrCreate(['legacy_id' => 218], [
            'kind' => 'brosur',
            'title' => ['tr' => 'COVID-19 aşısı hakkında bilgilendirme', 'en' => 'COVID-19 vaccine information (Turkish)'],
            'external_url' => 'https://www.losemilenfomamiyelom.org'.$this->pages[218]['path'],
            'is_published' => true,
        ]);
        $campaign = $this->documents['/Eklenti/14,kampanya-kitapcigimiz.pdf?0'] ?? null;
        if ($campaign) {
            Publication::updateOrCreate(['legacy_id' => 90], [
                'kind' => 'brosur',
                'title' => ['tr' => '"Ben de Varım" kampanya kitapçığı'],
                'file' => $campaign['in_repo'] ? 'legacy/'.$campaign['name'] : null,
                'external_url' => $campaign['in_repo'] ? null : $campaign['url'],
                'is_published' => true,
            ]);
        }
    }

    private function seedPosts(array $galleries): void
    {
        $handled = array_merge(array_keys(self::DISEASES), array_keys(self::PAGES), self::SKIP, [5, 218]);

        $candidates = collect($this->pages)
            ->reject(fn ($p) => $p['pdf'] || blank($p['title']) || in_array($p['id'], $handled, true))
            ->sortKeys();

        // Tarihi bilinmeyenler için sıralama amaçlı yaklaşık yayın tarihi (komşu kesin tarihler arasında doğrusal)
        $known = $candidates->mapWithKeys(fn ($p, $id) => [$id => isset(self::EVENT_FACTS[$id]) ? substr(self::EVENT_FACTS[$id][0], 0, 10) : $p['date']])
            ->filter()->map(fn ($d) => Carbon::parse($d)->timestamp)->all();
        $known[60] = Carbon::parse('2012-01-01')->timestamp;
        ksort($known);

        $titleCount = $candidates->countBy(fn ($p) => mb_strtolower($p['title']));

        foreach ($candidates as $id => $p) {
            $title = trim($p['title']);
            $date = isset(self::EVENT_FACTS[$id]) ? Carbon::parse(self::EVENT_FACTS[$id][0]) : ($p['date'] ? Carbon::parse($p['date'].' 10:00') : null);
            $approx = $date ?? Carbon::createFromTimestamp($this->interpolate($known, $id));

            $type = match (true) {
                in_array($id, self::SCIENTIFIC, true) => 'bilimsel',
                (bool) preg_match('/genel kurul/iu', $title) => 'duyuru',
                $id === 80 => 'haber',
                default => 'etkinlik',
            };
            $audience = match (true) {
                $type === 'bilimsel' => 'hekim',
                $type === 'duyuru' => 'herkes',
                (bool) preg_match('/hasta|bitecek|yürü|kamp|buluşma|öğrenmek istediğiniz|farkındalık|lenfoma günü|kml günü/iu', $title) => 'hasta',
                default => 'hekim',
            };

            $html = $this->html($id);
            if (isset($galleries[$id])) {
                $html = collect($galleries[$id])->map(fn ($src) => '<p><img src="'.$src.'" alt="" loading="lazy"></p>')->implode('');
            }
            preg_match('#https?://(?:www\.)?(?:youtube\.com/watch\?v=|youtu\.be/|canliyayin)[^"\s<]*#i', $html, $video);

            $image = $this->relImage($galleries[$id][0] ?? ($p['images'][0] ?? null));

            $base = $title;
            if (($titleCount[mb_strtolower($title)] ?? 0) > 1 && ! preg_match('/20\d\d/', $title)) {
                $base .= ' '.$approx->year;
            }

            $facts = self::EVENT_FACTS[$id] ?? null;
            $post = Post::firstOrNew(['legacy_id' => $id]);
            $post->fill([
                'type' => $type,
                'audience' => $audience,
                'title' => array_filter(['tr' => $title, 'en' => self::POST_TITLES_EN[$id] ?? ($type === 'bilimsel' ? $title : null)]),
                'body' => ['tr' => $html],
                'image' => $image,
                'event_starts_at' => $type === 'etkinlik' ? $date : null,
                'event_ends_at' => $facts && $facts[1] ? Carbon::parse($facts[1]) : null,
                'location' => $facts ? ['tr' => $facts[2]] : null,
                'video_url' => $video[0] ?? null,
                'published_at' => $approx,
                'is_published' => true,
            ]);
            if (! $post->exists) {
                $post->slug = array_filter([
                    'tr' => Post::uniqueSlug($base, 'tr'),
                    'en' => isset(self::POST_TITLES_EN[$id]) ? Post::uniqueSlug(self::POST_TITLES_EN[$id].($base !== $title ? ' '.$approx->year : ''), 'en') : null,
                ]);
            }
            $post->save();
        }
    }

    private function interpolate(array $known, int $id): int
    {
        $ids = array_keys($known);
        $prev = null;
        $next = null;
        foreach ($ids as $k) {
            if ($k <= $id) {
                $prev = $k;
            }
            if ($k >= $id && $next === null) {
                $next = $k;
            }
        }
        $prev ??= $ids[0];
        $next ??= end($ids);
        if ($prev === $next) {
            return $known[$prev];
        }

        return (int) ($known[$prev] + ($known[$next] - $known[$prev]) * (($id - $prev) / ($next - $prev)));
    }
}
