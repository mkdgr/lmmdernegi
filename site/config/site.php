<?php

return [
    'locales' => ['tr' => 'Türkçe', 'en' => 'English'],

    // Rota yolları dile göre çevrilir: /hastaliklar/aml  ↔  /en/conditions/aml
    'paths' => [
        'diseases' => ['tr' => 'hastaliklar', 'en' => 'conditions'],
        'support' => ['tr' => 'size-destek', 'en' => 'support'],
        'news' => ['tr' => 'haberler', 'en' => 'news'],
        'events' => ['tr' => 'etkinlikler', 'en' => 'events'],
        'stories' => ['tr' => 'hikayeler', 'en' => 'stories'],
        'publications' => ['tr' => 'yayinlar', 'en' => 'publications'],
        'ask' => ['tr' => 'uzmana-sorun', 'en' => 'ask-an-expert'],
        'donate' => ['tr' => 'bagis', 'en' => 'donate'],
        'membership' => ['tr' => 'uye-olun', 'en' => 'membership'],
        'contact' => ['tr' => 'iletisim', 'en' => 'contact'],
        'search' => ['tr' => 'ara', 'en' => 'search'],
        'professionals' => ['tr' => 'saglik-calisanlari', 'en' => 'healthcare-professionals'],
        'newsletter' => ['tr' => 'bulten', 'en' => 'newsletter'],
    ],

    // Yöneticiye giden bildirimler (panelden "Ayarlar" ile değiştirilebilir)
    'notify_email' => env('SITE_NOTIFY_EMAIL', 'info@losemilenfomamiyelom.org'),

    /*
     * Garanti BBVA Sanal POS — 3D Pay Hosting (ortak ödeme sayfası).
     * Kart bilgisi bu sitede hiç alınmaz; bağışçı bankanın sayfasına yönlendirilir.
     * Bilgiler banka tarafından verilir; .env dosyasına yazılır, koda yazılmaz.
     */
    'garanti' => [
        'mode' => env('GARANTI_MODE', 'TEST'), // TEST | PROD
        'merchant_id' => env('GARANTI_MERCHANT_ID'),
        'terminal_id' => env('GARANTI_TERMINAL_ID'),
        'prov_user_id' => env('GARANTI_PROV_USER_ID', 'PROVAUT'),
        'prov_password' => env('GARANTI_PROV_PASSWORD'),
        'user_id' => env('GARANTI_USER_ID', 'PROVAUT'),
        'store_key' => env('GARANTI_STORE_KEY'),
        'security_level' => env('GARANTI_SECURITY_LEVEL', '3D_OOS_PAY'),
        'api_version' => env('GARANTI_API_VERSION', '512'),
        'company_name' => env('GARANTI_COMPANY_NAME', 'LOSEMI LENFOMA MIYELOM DERNEGI'),
        'gateway' => [
            'TEST' => 'https://sanalposprovtest.garanti.com.tr/servlet/gt3dengine',
            'PROD' => 'https://sanalposprov.garanti.com.tr/servlet/gt3dengine',
        ],
    ],
];
