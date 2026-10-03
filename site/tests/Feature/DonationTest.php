<?php

namespace Tests\Feature;

use App\Mail\AdminNotification;
use App\Models\Donation;
use App\Services\GarantiPos;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\SeedsSiteContent;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use RefreshDatabase, SeedsSiteContent;

    private array $form = [
        'type' => 'bagis', 'frequency' => 'tek', 'amount' => '500', 'donor_type' => 'bireysel',
        'name' => 'Ayşe Yılmaz', 'email' => 'ayse@example.com', 'kvkk' => '1',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSite();
        Mail::fake();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    private function configurePos(): void
    {
        config(['site.garanti' => array_merge(config('site.garanti'), [
            'mode' => 'TEST', 'merchant_id' => '7000679', 'terminal_id' => '30691297',
            'prov_password' => '123qweASD/', 'store_key' => '12345678', 'api_version' => '512',
        ])]);
    }

    public function test_without_pos_configuration_request_is_recorded_without_payment(): void
    {
        $res = $this->post('/bagis', $this->form);
        $d = Donation::sole();
        $res->assertRedirect('http://localhost/bagis/sonuc/'.$d->order_id);
        $this->assertSame('beklemede', $d->status);
        $this->assertSame(500, $d->amount);

        $this->get('/bagis/sonuc/'.$d->order_id)->assertOk()->assertSee('Bağış talebiniz alındı');
    }

    public function test_custom_amount_overrides_preset_and_minimum_is_enforced(): void
    {
        $this->post('/bagis', [...$this->form, 'custom' => '750']);
        $this->assertSame(750, Donation::sole()->amount);

        $this->post('/bagis', [...$this->form, 'amount' => null, 'custom' => '5'])->assertSessionHasErrors('custom');
        $this->assertSame(1, Donation::count());
    }

    public function test_corporate_donor_needs_company_name(): void
    {
        $this->post('/bagis', [...$this->form, 'donor_type' => 'kurumsal'])->assertSessionHasErrors('company');
    }

    public function test_result_page_is_private_to_the_donor_session(): void
    {
        $d = Donation::create([...collect($this->form)->except(['amount', 'kvkk'])->all(),
            'order_id' => 'LLMTEST1', 'amount' => 100, 'consent_at' => now()]);
        $this->get('/bagis/sonuc/'.$d->order_id)->assertNotFound();
    }

    public function test_with_pos_configured_donor_is_sent_to_bank_with_signed_form(): void
    {
        $this->configurePos();
        $res = $this->post('/bagis', $this->form)->assertOk();
        $d = Donation::sole();

        $res->assertSee('action="https://sanalposprovtest.garanti.com.tr/servlet/gt3dengine"', false)
            ->assertSee('name="orderid" value="'.$d->order_id.'"', false)
            ->assertSee('name="txnamount" value="50000"', false)
            ->assertSee('name="secure3dsecuritylevel" value="3D_OOS_PAY"', false)
            ->assertDontSee('cardnumber');

        $pos = GarantiPos::make();
        $expected = $pos->requestHash($d->order_id, '50000', '949', route('donate.callback.success'), route('donate.callback.fail'), 'sales', '');
        $res->assertSee('name="secure3dhash" value="'.$expected.'"', false);
        $this->assertSame(128, strlen($expected)); // SHA-512, büyük harf hex
    }

    private function bankResponse(Donation $d, array $override = []): array
    {
        $data = array_merge([
            'orderid' => $d->order_id, 'mdstatus' => '1', 'procreturncode' => '00', 'response' => 'Approved',
            'authcode' => '304919', 'hostrefnum' => '123456789012', 'txnamount' => (string) ($d->amount * 100), 'txncurrencycode' => '949',
        ], $override);
        $params = ['clientid', 'oid', 'authcode', 'procreturncode', 'response', 'mdstatus'];
        $data['clientid'] = '30691297';
        $data['oid'] = $d->order_id;
        $data['hashparams'] = implode(':', $params).':';
        $plain = implode('', array_map(fn ($p) => $data[$p] ?? '', $params));
        $data['hashparamsval'] = $plain;
        $data['hash'] = strtoupper(hash('sha512', $plain.'12345678'));

        return $data;
    }

    public function test_valid_bank_callback_marks_donation_paid_and_notifies(): void
    {
        $this->configurePos();
        $this->post('/bagis', $this->form);
        $d = Donation::sole();

        $this->post('/odeme/garanti/basarili', $this->bankResponse($d))
            ->assertRedirect('http://localhost/bagis/sonuc/'.$d->order_id);

        $d->refresh();
        $this->assertSame('basarili', $d->status);
        $this->assertSame('304919', $d->auth_code);
        $this->assertNotNull($d->paid_at);
        Mail::assertSent(AdminNotification::class, fn ($m) => $m->title === 'Yeni online bağış');

        $this->get('/bagis/sonuc/'.$d->order_id)->assertOk()->assertSee('Teşekkür ederiz!');
    }

    public function test_tampered_callback_is_rejected(): void
    {
        $this->configurePos();
        $this->post('/bagis', $this->form);
        $d = Donation::sole();

        $data = $this->bankResponse($d);
        $data['hash'] = strtoupper(hash('sha512', 'sahte'));
        $this->post('/odeme/garanti/basarili', $data);

        $d->refresh();
        $this->assertSame('basarisiz', $d->status);
        $this->assertSame('İmza (hash) doğrulanamadı', $d->bank_message);
        Mail::assertNotSent(AdminNotification::class);
    }

    public function test_declined_payment_is_recorded_as_failed(): void
    {
        $this->configurePos();
        $this->post('/bagis', $this->form);
        $d = Donation::sole();

        $this->post('/odeme/garanti/basarisiz', $this->bankResponse($d, ['procreturncode' => '51', 'response' => 'Declined', 'errmsg' => 'Limit yetersiz']));

        $d->refresh();
        $this->assertSame('basarisiz', $d->status);
        $this->assertSame('Limit yetersiz', $d->bank_message);
    }

    public function test_paid_donation_cannot_be_flipped_by_replayed_failure(): void
    {
        $this->configurePos();
        $this->post('/bagis', $this->form);
        $d = Donation::sole();
        $this->post('/odeme/garanti/basarili', $this->bankResponse($d));
        $this->post('/odeme/garanti/basarisiz', $this->bankResponse($d, ['procreturncode' => '99']));

        $this->assertSame('basarili', $d->fresh()->status);
    }

    public function test_amount_mismatch_is_rejected(): void
    {
        $this->configurePos();
        $this->post('/bagis', $this->form);
        $d = Donation::sole();

        $this->post('/odeme/garanti/basarili', $this->bankResponse($d, ['txnamount' => '100']));

        $this->assertSame('basarisiz', $d->fresh()->status);
        $this->assertSame('Tutar uyuşmuyor', $d->fresh()->bank_message);
    }
}
