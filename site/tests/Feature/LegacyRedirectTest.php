<?php

namespace Tests\Feature;

use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteContent;
use Tests\TestCase;

class LegacyRedirectTest extends TestCase
{
    use RefreshDatabase, SeedsSiteContent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSite();
    }

    public function test_old_disease_url_redirects_permanently(): void
    {
        $this->get('/TR,23/akut-miyeloid-losemi-aml.html')
            ->assertStatus(301)->assertRedirect('http://localhost/hastaliklar/akut-miyeloid-losemi');
    }

    public function test_old_post_and_page_urls_redirect(): void
    {
        $this->get('/TR,312/aml-tedavisinde-yeni-ufuklar-ve-yenilikci-yaklasim.html')
            ->assertRedirect('http://localhost/haberler/aml-tedavisinde-yeni-ufuklar');
        $this->get('/TR,106/losemi-lenfoma-miyelom-hastalarinda-beslenme.html')
            ->assertRedirect('http://localhost/size-destek/beslenme');
    }

    public function test_old_section_pages_redirect_to_new_sections(): void
    {
        $this->get('/TR,126/online-bagis---aidat-islemleri.html')->assertRedirect('http://localhost/bagis');
        $this->get('/TR,15/iletisim.html')->assertRedirect('http://localhost/iletisim');
        $this->get('/TR,8/ana-sayfa.html')->assertRedirect('http://localhost');
    }

    public function test_panel_defined_redirect_is_used_and_counted(): void
    {
        Redirect::create(['from_path' => '/Eski-Sayfa.html', 'to_path' => '/hakkimizda']);

        $this->get('/eski-sayfa.html')->assertStatus(301)->assertRedirect('/hakkimizda');
        $this->assertSame(1, Redirect::first()->hits);
    }

    public function test_unknown_legacy_id_is_404(): void
    {
        $this->get('/TR,99999/yok.html')->assertNotFound();
    }
}
