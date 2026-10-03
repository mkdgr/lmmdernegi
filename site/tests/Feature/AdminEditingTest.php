<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\DiseaseResource\Pages\EditDisease;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Models\Disease;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SeedsSiteContent;
use Tests\TestCase;

class AdminEditingTest extends TestCase
{
    use RefreshDatabase, SeedsSiteContent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSite();
        $this->actingAs(User::factory()->create(['is_active' => true]));
    }

    public function test_editing_keeps_both_languages(): void
    {
        $d = Disease::first();

        Livewire::test(EditDisease::class, ['record' => $d->getKey()])
            ->assertSchemaStateSet(['name.tr' => 'Akut Miyeloid Lösemi', 'name.en' => 'Acute Myeloid Leukemia'])
            ->fillForm(['summary.tr' => 'Yeni özet', 'summary.en' => 'New summary'])
            ->call('save')
            ->assertHasNoFormErrors();

        $d->refresh();
        $this->assertSame('Yeni özet', $d->getTranslation('summary', 'tr'));
        $this->assertSame('New summary', $d->getTranslation('summary', 'en'));
        $this->assertSame('Acute Myeloid Leukemia', $d->getTranslation('name', 'en'));
    }

    public function test_creating_post_with_turkish_only_generates_slug_and_no_english(): void
    {
        Livewire::test(CreatePost::class)
            ->fillForm([
                'type' => 'etkinlik', 'audience' => 'hasta', 'is_published' => true, 'published_at' => now(),
                'title.tr' => 'Kış Hasta Buluşması', 'title.en' => '', 'body.tr' => '<p>Program</p>',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = Post::latest('id')->first();
        $this->assertSame('kis-hasta-bulusmasi', $post->slugFor('tr'));
        $this->assertFalse($post->hasLocale('en'));
        $this->get('/haberler/kis-hasta-bulusmasi')->assertOk();
        $this->get('/en/news')->assertDontSee('Kış Hasta Buluşması');
    }

    public function test_site_settings_are_saved(): void
    {
        Livewire::test(SiteSettings::class)
            ->fillForm(['phone' => '0312 000 00 00', 'email' => 'yeni@example.com', 'whatsapp' => '0530 000 00 00'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('0312 000 00 00', Setting::get('phone'));
        $this->get('/')->assertSee('0312 000 00 00')->assertSee('https://wa.me/905300000000', false);
    }
}
