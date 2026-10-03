<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\SeedsSiteContent;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase, SeedsSiteContent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSite();
    }

    public static function pages(): array
    {
        return [
            'ana sayfa' => ['/', 'yalnız değilsiniz'],
            'home (en)' => ['/en', 'not alone'],
            'hastalıklar' => ['/hastaliklar', 'Akut Miyeloid Lösemi'],
            'hastalık' => ['/hastaliklar/akut-miyeloid-losemi', 'Kemik iliği kanseri'],
            'condition (en)' => ['/en/conditions/acute-myeloid-leukemia', 'Turkish only'],
            'size destek' => ['/size-destek', 'Yeni tanı aldım'],
            'rehber' => ['/size-destek/yeni-tani-aldim', 'Rehber'],
            'haberler' => ['/haberler', 'AML Tedavisinde Yeni Ufuklar'],
            'etkinlikler' => ['/etkinlikler', 'AML Tedavisinde Yeni Ufuklar'],
            'etkinlik' => ['/haberler/aml-tedavisinde-yeni-ufuklar', 'Ankara'],
            'hikâyeler' => ['/hikayeler', 'Korkularım yersizdi'],
            'yayınlar' => ['/yayinlar', 'Sayı 1'],
            'sağlık çalışanları' => ['/saglik-calisanlari', 'Rituximab Infusion'],
            'arama' => ['/ara?q=beslenme', 'Beslenme'],
            'uzmana sorun' => ['/uzmana-sorun', 'Sorumu gönder'],
            'iletişim' => ['/iletisim', '0530 156 87 68'],
            'üyelik' => ['/uye-olun', 'Başvurumu gönder'],
            'bağış' => ['/bagis', 'TR02 0006 2001 3610 0006 2936 42'],
            'kurumsal sayfa' => ['/hakkimizda', '2011'],
            'about (en)' => ['/en/about-us', 'About us'],
            'sitemap' => ['/sitemap.xml', '/hastaliklar/akut-miyeloid-losemi'],
        ];
    }

    #[DataProvider('pages')]
    public function test_page_renders(string $url, string $expect): void
    {
        $this->get($url)->assertOk()->assertSee($expect, false);
    }

    public function test_unpublished_post_is_hidden(): void
    {
        $this->get('/haberler/taslak-haber')->assertNotFound();
        $this->get('/haberler')->assertDontSee('Taslak haber');
    }

    public function test_unknown_page_returns_404_with_search(): void
    {
        $this->get('/boyle-bir-sayfa-yok')->assertNotFound()->assertSee('Aradığınız sayfayı bulamadık');
    }

    public function test_language_switcher_points_to_translated_url(): void
    {
        $this->get('/hastaliklar/akut-miyeloid-losemi')
            ->assertSee('href="http://localhost/en/conditions/acute-myeloid-leukemia"', false)
            ->assertSee('hreflang="en"', false);
    }

    public function test_page_without_english_links_language_switcher_to_english_home(): void
    {
        $this->get('/size-destek/beslenme')->assertOk()
            ->assertSee('<a href="http://localhost/en" lang="en"', false);
    }

    public function test_english_slug_under_turkish_prefix_redirects(): void
    {
        $this->get('/about-us')->assertRedirect('http://localhost/hakkimizda')->assertStatus(301);
    }
}
