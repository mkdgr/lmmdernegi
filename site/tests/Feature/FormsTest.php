<?php

namespace Tests\Feature;

use App\Mail\AdminNotification;
use App\Models\ContactMessage;
use App\Models\MembershipApplication;
use App\Models\NewsletterSubscriber;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\SeedsSiteContent;
use Tests\TestCase;

class FormsTest extends TestCase
{
    use RefreshDatabase, SeedsSiteContent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSite();
        Mail::fake();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_question_is_saved_and_admin_is_notified(): void
    {
        $this->post('/uzmana-sorun', [
            'name' => 'Ayşe Yılmaz', 'email' => 'ayse@example.com', 'relation' => 'yakin',
            'disease_id' => 1, 'question' => 'Tedavi sırasında aşı olabilir mi?', 'kvkk' => '1',
        ])->assertRedirect('http://localhost/uzmana-sorun')->assertSessionHas('sent');

        $q = Question::sole();
        $this->assertSame('yeni', $q->status);
        $this->assertNotNull($q->consent_at);
        Mail::assertSent(AdminNotification::class, fn ($m) => $m->title === 'Uzmana sorulan yeni soru' && $m->replyToEmail === 'ayse@example.com');
    }

    public function test_question_requires_consent_and_shows_turkish_errors(): void
    {
        $this->from('/uzmana-sorun')->post('/uzmana-sorun', ['name' => '', 'email' => 'x', 'relation' => 'hasta', 'question' => 'kısa'])
            ->assertSessionHasErrors(['name', 'email', 'question', 'kvkk']);
        $this->assertSame(0, Question::count());

        $this->followRedirects($this->from('/uzmana-sorun')->post('/uzmana-sorun', ['relation' => 'hasta']))
            ->assertSee('Ad soyad alanı zorunludur.');
    }

    public function test_honeypot_silently_drops_bots(): void
    {
        $this->post('/uzmana-sorun', [
            'name' => 'Bot', 'email' => 'bot@example.com', 'relation' => 'hasta', 'question' => 'Spam spam spam spam', 'kvkk' => '1',
            'website' => 'http://spam.example',
        ])->assertRedirect()->assertSessionHas('sent');

        $this->assertSame(0, Question::count());
        Mail::assertNothingSent();
    }

    public function test_contact_and_membership_forms(): void
    {
        $this->post('/iletisim', ['name' => 'Ali', 'email' => 'ali@example.com', 'message' => 'Merhaba, gönüllü olmak istiyorum.', 'kvkk' => '1'])
            ->assertSessionHas('sent');
        $this->assertSame(1, ContactMessage::count());

        $this->post('/uye-olun', ['name' => 'Ali Veli', 'email' => 'ali@example.com', 'phone' => '05551112233', 'tckn' => '12345678901', 'kvkk' => '1'])
            ->assertSessionHas('sent');
        $app = MembershipApplication::sole();
        $this->assertSame('12345678901', $app->tckn);
        // T.C. kimlik no veritabanında şifreli saklanır
        $this->assertStringNotContainsString('12345678901', \DB::table('membership_applications')->value('tckn'));
    }

    public function test_english_form_redirects_back_to_english_page(): void
    {
        $this->post('/en/contact', ['name' => 'John', 'email' => 'john@example.com', 'message' => 'Hello there!', 'kvkk' => '1'])
            ->assertRedirect('http://localhost/en/contact');
        $this->assertSame('en', ContactMessage::sole()->locale);
    }

    public function test_newsletter_subscribe_and_unsubscribe(): void
    {
        $this->from('/')->post('/bulten', ['email' => 'Abone@Example.com', 'kvkk' => '1'])->assertSessionHas('newsletter');
        $this->from('/')->post('/bulten', ['email' => 'abone@example.com', 'kvkk' => '1']);

        $sub = NewsletterSubscriber::sole();
        $this->assertSame('abone@example.com', $sub->email);

        $this->get('/bulten/ayril/'.$sub->token)->assertOk()->assertSee('aboneliğiniz sonlandırıldı');
        $this->assertNotNull($sub->fresh()->unsubscribed_at);
    }
}
