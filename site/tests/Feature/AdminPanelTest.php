<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteContent;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase, SeedsSiteContent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSite();
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_inactive_user_cannot_enter_panel(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => false]))->get('/admin')->assertForbidden();
    }

    public function test_admin_screens_render(): void
    {
        Question::create(['name' => 'A', 'email' => 'a@example.com', 'relation' => 'hasta', 'question' => 'Soru metni burada', 'consent_at' => now()]);
        $this->actingAs(User::factory()->create(['is_active' => true]));

        foreach ([
            '/admin', '/admin/diseases', '/admin/diseases/1/edit', '/admin/diseases/create',
            '/admin/pages', '/admin/pages/1/edit', '/admin/posts', '/admin/posts/1/edit', '/admin/posts/create',
            '/admin/stories', '/admin/stories/1/edit', '/admin/publications', '/admin/questions', '/admin/questions/1/edit',
            '/admin/contact-messages', '/admin/membership-applications', '/admin/donations', '/admin/newsletter-subscribers',
            '/admin/redirects', '/admin/users', '/admin/site-settings',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_edit_form_is_filled_with_both_languages(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $this->get('/admin/diseases/1/edit')->assertSee('Acute Myeloid Leukemia')->assertSee('Akut Miyeloid Lösemi');
    }

    public function test_content_lists_offer_create_button(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        foreach (['diseases', 'pages', 'posts', 'stories', 'publications'] as $r) {
            $this->get("/admin/{$r}")->assertSee("/admin/{$r}/create", false);
        }
    }
}
