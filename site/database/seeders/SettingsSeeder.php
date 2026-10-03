<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/** Başlangıç ayarları (eski siteden). Panelde "Site ayarları" sayfasından değiştirilebilir. */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'phone' => '0530 156 87 68',
            'email' => 'info@losemilenfomamiyelom.org',
            'notify_email' => 'info@losemilenfomamiyelom.org',
            'address' => ['tr' => 'Hoşdere Cad. No: 198/5, Çankaya / Ankara', 'en' => 'Hoşdere Cad. No: 198/5, Çankaya, Ankara, Türkiye'],
            // Eski sitedeki bağlantılar. Instagram/YouTube hesapları dernekçe teyit edilip panelden eklenmeli.
            'social_facebook' => 'https://www.facebook.com/llmhastadernegi',
            'social_x' => 'https://x.com/LLMbirligi',
            'bank_accounts' => [
                ['bank' => 'Garanti BBVA', 'iban' => 'TR02 0006 2001 3610 0006 2936 42', 'swift' => 'TGBATRISXXX'],
                ['bank' => 'Fibabanka — Yıldız Şube', 'iban' => 'TR27 0010 3000 0000 0024 1921 26', 'swift' => 'FBHLTRIS'],
            ],
            'home_hero_photo' => 'legacy/galeri-170-1100-dsc2216jpg.jpg',
            'home_facts' => [
                ['value' => '2011', 'label' => ['tr' => 'yılından beri', 'en' => 'founded']],
                ['value' => '14', 'label' => ['tr' => 'hasta kongresi', 'en' => 'patient congresses']],
                ['value' => '12', 'label' => ['tr' => 'lenfoma yürüyüşü', 'en' => 'lymphoma walks']],
                ['value' => '38', 'label' => ['tr' => 'LLMBİR Bülten sayısı', 'en' => 'bulletin issues']],
            ],
        ];

        foreach ($defaults as $key => $value) {
            if (! Setting::query()->whereKey($key)->exists()) {
                Setting::put($key, $value);
            }
        }
    }
}
