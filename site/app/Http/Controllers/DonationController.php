<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\GarantiPos;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DonationController extends Controller
{
    public const AMOUNTS = [100, 200, 500, 1000];

    public function create(Request $request)
    {
        set_alternates('donate.create');

        return view('donate.create', [
            'amounts' => self::AMOUNTS,
            'selected' => (int) $request->query('tutar', 500),
            'posReady' => GarantiPos::make()->isConfigured(),
        ]);
    }

    public function store(Request $request)
    {
        if ($this->isSpam($request)) {
            return redirect(lroute('donate.create'));
        }

        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(Donation::TYPES))],
            'frequency' => ['required', Rule::in(array_keys(Donation::FREQUENCIES))],
            'amount' => ['nullable', 'integer'],
            'custom' => ['nullable', 'integer', 'min:10', 'max:1000000'],
            'donor_type' => ['required', Rule::in(['bireysel', 'kurumsal'])],
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'required_if:donor_type,kurumsal', 'string', 'max:160'],
            'tckn' => ['nullable', 'digits:11'],
            'tax_no' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'anonymous' => ['nullable', 'boolean'],
            'kvkk' => ['accepted'],
        ], [
            'custom.min' => __('En az 10 ₺ bağış yapabilirsiniz.'),
        ]);

        $amount = (int) ($data['custom'] ?? 0) ?: (int) ($data['amount'] ?? 0);
        if ($amount < 10) {
            return back()->withInput()->withErrors(['custom' => __('Lütfen bir tutar seçin ya da girin.')]);
        }

        $donation = Donation::create([
            ...Arr::except($data, ['amount', 'custom', 'anonymous', 'kvkk']),
            'order_id' => Donation::newOrderId(),
            'amount' => $amount,
            'is_anonymous' => (bool) ($data['anonymous'] ?? false),
            'consent_at' => now(),
            'ip' => $request->ip(),
            'locale' => app()->getLocale(),
        ]);

        session()->push('donations', $donation->order_id);

        $pos = GarantiPos::make();
        if (! $pos->isConfigured()) {
            // Sanal POS bilgileri henüz girilmedi: kayıt alınır, dernek bağışçıya döner.
            $donation->update(['bank_message' => 'Sanal POS yapılandırılmadı — ödeme alınmadı']);

            return redirect(lroute('donate.result', $donation->order_id));
        }

        return view('donate.redirect', [
            'action' => $pos->gatewayUrl(),
            'fields' => $pos->paymentFields($donation, route('donate.callback.success'), route('donate.callback.fail')),
        ]);
    }

    /** Garanti'den dönüş (başarılı ya da başarısız adres). */
    public function callback(Request $request)
    {
        $data = $request->all();
        $donation = Donation::where('order_id', $request->input('orderid', $request->input('oid')))->first();

        if (! $donation) {
            Log::warning('Garanti dönüşü: sipariş bulunamadı', Arr::only($data, ['orderid', 'oid', 'mdstatus', 'procreturncode']));

            return redirect(lroute('donate.create', [], 'tr'));
        }

        $pos = GarantiPos::make();
        $verified = $pos->verifyResponse($data);
        // Ek güvence: bankanın onayladığı tutar kayıttakiyle aynı olmalı (kuruş)
        $amountOk = ! isset($data['txnamount']) || (string) $data['txnamount'] === (string) ($donation->amount * 100);
        $approved = $verified && $amountOk && $pos->isApproved($data);

        if ($donation->status !== 'basarili') {
            $donation->update([
                'status' => $approved ? 'basarili' : 'basarisiz',
                'paid_at' => $approved ? now() : null,
                'auth_code' => $data['authcode'] ?? null,
                'host_ref' => $data['hostrefnum'] ?? null,
                'bank_code' => $data['procreturncode'] ?? null,
                'bank_message' => match (true) {
                    ! $verified => 'İmza (hash) doğrulanamadı',
                    ! $amountOk => 'Tutar uyuşmuyor',
                    default => $data['errmsg'] ?? $data['mderrormessage'] ?? $data['response'] ?? null,
                },
                'masked_pan' => $data['MaskedPan'] ?? $data['maskedpan'] ?? null,
                // Kart verisi tutulmaz; yalnızca sonuçla ilgili alanlar saklanır
                'bank_response' => Arr::only($data, ['mdstatus', 'procreturncode', 'response', 'errmsg', 'mderrormessage',
                    'authcode', 'hostrefnum', 'txnamount', 'txncurrencycode', 'orderid', 'hashparams']),
            ]);

            if ($approved) {
                $this->notifyAdmin('Yeni online bağış', [
                    'Tür' => Donation::TYPES[$donation->type] ?? $donation->type,
                    'Sıklık' => Donation::FREQUENCIES[$donation->frequency] ?? $donation->frequency,
                    'Tutar' => number_format($donation->amount, 0, ',', '.').' ₺',
                    'Bağışçı' => $donation->name.($donation->is_anonymous ? ' (adı gizli kalsın)' : ''),
                    'E-posta' => $donation->email,
                    'Sipariş no' => $donation->order_id,
                ], url('/admin/donations/'.$donation->id));
            }
        }

        session()->push('donations', $donation->order_id);

        return redirect(lroute('donate.result', $donation->order_id, $donation->locale ?: 'tr'));
    }

    public function result(Donation $donation)
    {
        // Ayrıntılar yalnızca bu bağışı yapan oturuma gösterilir
        abort_unless(in_array($donation->order_id, session('donations', []), true), 404);
        set_alternates('donate.create');

        return view('donate.result', ['donation' => $donation, 'posReady' => GarantiPos::make()->isConfigured()]);
    }
}
