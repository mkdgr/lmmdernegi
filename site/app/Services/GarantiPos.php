<?php

namespace App\Services;

use App\Models\Donation;

/**
 * Garanti BBVA Sanal POS — 3D Pay Hosting (ortak ödeme sayfası, "3D_OOS_PAY").
 *
 * Akış: siteden bankaya imzalı (hash) bir form POST edilir; kart bilgisi bankanın
 * sayfasında girilir; banka sonucu successurl/errorurl adresine POST eder.
 * Sonuç ancak hash doğrulanırsa kabul edilir.
 *
 * ÖNEMLİ: Hash formülleri Garanti'nin "apiversion 512" entegrasyon dokümanına göre
 * yazılmıştır. Canlıya almadan önce bankanın verdiği test terminaliyle uçtan uca
 * deneme yapılmalı (bkz. site/KURULUM.md).
 */
class GarantiPos
{
    public function __construct(private array $config) {}

    public static function make(): self
    {
        return new self(config('site.garanti'));
    }

    public function isConfigured(): bool
    {
        foreach (['merchant_id', 'terminal_id', 'prov_password', 'store_key'] as $key) {
            if (blank($this->config[$key] ?? null)) {
                return false;
            }
        }

        return true;
    }

    public function gatewayUrl(): string
    {
        $mode = strtoupper($this->config['mode']) === 'PROD' ? 'PROD' : 'TEST';

        return $this->config['gateway'][$mode];
    }

    /** Bankaya gönderilecek form alanları. */
    public function paymentFields(Donation $donation, string $successUrl, string $errorUrl): array
    {
        $c = $this->config;
        $amount = (string) ($donation->amount * 100); // kuruş
        $currency = '949'; // TRY
        $type = 'sales';
        $installment = '';

        $fields = [
            'mode' => strtoupper($c['mode']) === 'PROD' ? 'PROD' : 'TEST',
            'apiversion' => $c['api_version'],
            'secure3dsecuritylevel' => $c['security_level'],
            'terminalprovuserid' => $c['prov_user_id'],
            'terminaluserid' => $c['user_id'],
            'terminalmerchantid' => $c['merchant_id'],
            'terminalid' => $c['terminal_id'],
            'orderid' => $donation->order_id,
            'customeremailaddress' => $donation->email,
            'customeripaddress' => $donation->ip ?: request()->ip(),
            'txntype' => $type,
            'txnamount' => $amount,
            'txncurrencycode' => $currency,
            'txninstallmentcount' => $installment,
            'successurl' => $successUrl,
            'errorurl' => $errorUrl,
            'companyname' => $c['company_name'],
            'lang' => $donation->locale === 'en' ? 'en' : 'tr',
            'txntimestamp' => now()->toIso8601String(),
            'refreshtime' => '5',
        ];

        $fields['secure3dhash'] = $this->requestHash($donation->order_id, $amount, $currency, $successUrl, $errorUrl, $type, $installment);

        return $fields;
    }

    public function requestHash(string $orderId, string $amount, string $currency, string $successUrl, string $errorUrl, string $type, string $installment): string
    {
        $c = $this->config;

        return strtoupper(hash('sha512',
            $c['terminal_id'].$orderId.$amount.$currency.$successUrl.$errorUrl.$type.$installment.$c['store_key'].$this->hashedPassword()
        ));
    }

    /** SHA1(şifre + 9 haneye tamamlanmış terminal no), büyük harf. */
    public function hashedPassword(): string
    {
        return strtoupper(sha1($this->config['prov_password'].str_pad((string) $this->config['terminal_id'], 9, '0', STR_PAD_LEFT)));
    }

    /**
     * Bankadan dönen yanıtın imzasını doğrular.
     * hashparams: imzaya giren alanların adları (":" ile ayrılmış); değerleri + store key → hash.
     */
    public function verifyResponse(array $data): bool
    {
        $params = array_values(array_filter(explode(':', (string) ($data['hashparams'] ?? ''))));
        $received = (string) ($data['hash'] ?? '');
        if (! $params || $received === '') {
            return false;
        }

        $plain = '';
        foreach ($params as $param) {
            $plain .= (string) ($data[$param] ?? '');
        }

        if (isset($data['hashparamsval']) && $data['hashparamsval'] !== $plain) {
            return false;
        }

        $plain .= $this->config['store_key'];

        $candidates = [
            strtoupper(hash('sha512', $plain)),             // apiversion 512
            base64_encode(pack('H*', sha1($plain))),        // eski sürüm
        ];

        foreach ($candidates as $expected) {
            if (hash_equals($expected, $received) || hash_equals(strtoupper($expected), strtoupper($received))) {
                return true;
            }
        }

        return false;
    }

    /** 3D doğrulaması tamam ve provizyon onaylı mı? */
    public function isApproved(array $data): bool
    {
        return in_array((string) ($data['mdstatus'] ?? ''), ['1', '2', '3', '4'], true)
            && (string) ($data['procreturncode'] ?? '') === '00';
    }
}
