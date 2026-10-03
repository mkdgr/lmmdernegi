# Kurulum ve yayına alma rehberi

Lösemi Lenfoma Miyelom Derneği web sitesi — Laravel 12 + Filament 4.

- **Site:** `https://www.losemilenfomamiyelom.org` (Türkçe), `/en` (İngilizce)
- **Yönetim paneli:** `https://www.losemilenfomamiyelom.org/admin`

---

## 1. Sunucu gereksinimleri

| Gereksinim | Not |
|---|---|
| PHP **8.2 veya üzeri** (8.3 önerilir) | cPanel → "Select PHP Version" / "MultiPHP Manager" |
| PHP eklentileri | `pdo_mysql`, `mbstring`, `intl`, `fileinfo`, `gd`, `zip`, `curl`, `openssl`, `bcmath` |
| MySQL 5.7+ / MariaDB 10.3+ | cPanel → "MySQL Databases" |
| Disk | Kod ~120 MB + yüklenen dosyalar (bülten arşivi ~800 MB) → **en az 2 GB** |
| SSH / Terminal | Önerilir (cPanel → "Terminal"). Yoksa bkz. bölüm 7. |

---

## 2. Paketi hazırlama (geliştirici bilgisayarında)

```bash
cd site
./deploy/paket-olustur.sh        # → ../llm-site-YYYYMMDD.zip
```

Betik `composer install --no-dev` çalıştırır, Filament dosyalarını yayınlar ve `vendor/` dahil
tek bir zip üretir. `.env`, veritabanı ve yüklenen dosyalar pakete girmez.

---

## 3. cPanel'e yükleme

### Önerilen yerleşim

```
/home/KULLANICI/llm-site/          ← zip buraya açılır (tüm Laravel uygulaması)
/home/KULLANICI/public_html  →  /home/KULLANICI/llm-site/public   (alan adının kök dizini)
```

**Alan adının kök dizinini `llm-site/public` olarak ayarlayın** (cPanel → "Domains" → alan adı → "Document Root").
Bu sayede `.env` ve kaynak kodu web'den erişilemez.

> Kök dizin değiştirilemiyorsa: `llm-site/public` içindekileri `public_html`'e kopyalayın ve
> `public_html/index.php` içindeki iki yolu `__DIR__.'/../llm-site/vendor/autoload.php'` ve
> `__DIR__.'/../llm-site/bootstrap/app.php'` olarak değiştirin.

### Veritabanı

cPanel → "MySQL Databases": bir veritabanı ve kullanıcı oluşturun, kullanıcıya **ALL PRIVILEGES** verin.

### `.env` dosyası

`llm-site/.env.example` dosyasını `llm-site/.env` olarak kopyalayın ve düzenleyin:

```dotenv
APP_NAME="Lösemi Lenfoma Miyelom Derneği"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.losemilenfomamiyelom.org
APP_LOCALE=tr

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=kullanici_llm
DB_USERNAME=kullanici_llm
DB_PASSWORD=********

# E-posta (form bildirimleri için) — cPanel'de oluşturduğunuz bir e-posta hesabı
MAIL_MAILER=smtp
MAIL_HOST=mail.losemilenfomamiyelom.org
MAIL_PORT=465
MAIL_SCHEME=smtps
MAIL_USERNAME=web@losemilenfomamiyelom.org
MAIL_PASSWORD=********
MAIL_FROM_ADDRESS=web@losemilenfomamiyelom.org
MAIL_FROM_NAME="${APP_NAME}"
SITE_NOTIFY_EMAIL=info@losemilenfomamiyelom.org

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync

# Garanti BBVA Sanal POS — bkz. bölüm 5 (boş bırakılırsa bağış talebi kaydedilir, ödeme alınmaz)
GARANTI_MODE=TEST
GARANTI_MERCHANT_ID=
GARANTI_TERMINAL_ID=
GARANTI_PROV_PASSWORD=
GARANTI_STORE_KEY=
```

### Kurulum komutları (cPanel → Terminal)

```bash
cd ~/llm-site
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force          # eski siteden aktarılan içerik + yeni sayfalar
php artisan storage:link
php artisan legacy:download          # ⚠ bkz. bölüm 4
php artisan make:filament-user       # ilk yönetici hesabı
php artisan optimize
```

`storage/` ve `bootstrap/cache/` klasörleri web sunucusu tarafından yazılabilir olmalıdır (cPanel'de genellikle varsayılan olarak öyledir; değilse izinleri 755 yapın).

---

## 4. ⚠ Alan adını yeni siteye çevirmeden ÖNCE

Eski sitedeki **LLMBİR Bülten PDF'leri (37 sayı) ve büyük sunum dosyaları** (~800 MB) depoda tutulmaz.
Bunlar eski siteden indirilir — eski site kapanınca indirilemez:

```bash
php artisan legacy:download --dry-run   # neyin ineceğini listeler
php artisan legacy:download             # indirir; yarıda kalırsa yeniden çalıştırın
```

Komut bitince panelde **Yayınlar ve bülten** listesindeki tüm kayıtların "Dosya" sütunu işaretli olmalıdır.

Eski site adresleri (`/TR,23/akut-miyeloid-losemi-aml.html` gibi) yeni adreslere **otomatik 301 yönlendirilir**;
Google sıralaması korunur. Ek yönlendirme gerekirse panel → **Ayarlar → Yönlendirmeler**.

Yayına aldıktan sonra Google Search Console'a `https://www.losemilenfomamiyelom.org/sitemap.xml` adresini bildirin.

---

## 5. Garanti BBVA bağış entegrasyonu

Site **3D Pay Hosting (ortak ödeme sayfası)** modelini kullanır: kart bilgisi bu sitede hiç alınmaz,
bağışçı Garanti'nin sayfasına yönlendirilir. (Eski sitede kart bilgisi sitenin kendi formunda alınıyordu.)

1. Garanti BBVA'dan **3D Pay Hosting / OOS** yetkili sanal POS için şu bilgileri isteyin:
   üye işyeri no (merchant id), terminal no, provizyon kullanıcısı şifresi, 3D güvenlik anahtarı (store key).
2. Bankaya **dönüş adreslerini** bildirin:
   - Başarılı: `https://www.losemilenfomamiyelom.org/odeme/garanti/basarili`
   - Başarısız: `https://www.losemilenfomamiyelom.org/odeme/garanti/basarisiz`
3. Bilgileri `.env`'e yazın, `GARANTI_MODE=TEST` ile **test kartlarıyla uçtan uca deneme** yapın,
   panelde **Bağışlar** listesinde kaydın "Başarılı" göründüğünü kontrol edin.
4. Sonra `GARANTI_MODE=PROD` yapıp `php artisan optimize` çalıştırın.

> Hash (imza) formülleri Garanti'nin `apiversion=512` dokümanına göre yazıldı (`app/Services/GarantiPos.php`).
> Banka farklı bir sürüm/alan sırası verirse yalnızca bu dosya güncellenir; testleri `tests/Unit/GarantiPosTest.php`.
> Bankadan gelen yanıtın imzası doğrulanmadan hiçbir bağış "Başarılı" sayılmaz.

**Düzenli (aylık) bağış:** İlk ödeme alınır, kayıt "Her ay düzenli" olarak işaretlenir ve dernek bağışçıyla iletişime geçer.
Otomatik tekrarlayan çekim için Garanti ile ayrıca "tekrarlı ödeme" anlaşması gerekir.

---

## 6. Yönetim paneli — kısa kullanım

| Menü | Ne yapılır |
|---|---|
| **İçerik → Hastalıklar** | 7 hastalık sayfası. Türkçe / English sekmeleri. Ara başlıkları "Başlık 2" yapın; sayfanın "Bu sayfada" menüsü bunlardan oluşur. |
| **İçerik → Sayfalar ve rehberler** | "Hasta rehberi" bölümündeki sayfalar ana sayfadaki **Şu an neredesiniz?** alanında görünür (sıra alanıyla). Kurumsal ve yasal sayfalar da buradadır. |
| **İçerik → Haberler ve etkinlikler** | Yeni etkinlik: tür "Etkinlik", tarih, yer, afiş, canlı yayın bağlantısı. Tarihi gelecekteyse "Yaklaşan etkinlikler"de görünür. |
| **İçerik → Hikâyeler** | **Yazılı yayın izni** işaretlenmeden hikâye yayınlanamaz (sağlık verisi). |
| **İçerik → Yayınlar ve bülten** | PDF yükleme. |
| **Başvurular** | Uzmana sorulan sorular, iletişim mesajları, üyelik başvuruları, bağışlar, bülten aboneleri. Yeni kayıtlar menüde sayıyla görünür ve bildirim e-postası gelir. Listeler **CSV indir** ile Excel'e aktarılabilir. |
| **Ayarlar → Site ayarları** | Telefon, WhatsApp, e-posta, adres, IBAN'lar, sosyal medya, ana sayfa fotoğrafı ve rakamlar. |
| **Ayarlar → Panel kullanıcıları** | Personel hesapları. "Panele girebilir" kapatılınca kullanıcı giremez. |

İngilizce sekmesi boş bırakılan içerik İngilizce sitede listelenmez; tek sayfası açılırsa Türkçe metin
"yalnızca Türkçe mevcut" notuyla gösterilir.

---

## 7. SSH/Terminal yoksa

1. Paketi geliştirici bilgisayarında hazırlayın (bölüm 2) ve File Manager ile yükleyip açın.
2. Veritabanını geliştirici bilgisayarında oluşturup (`php artisan migrate --seed` MySQL'e karşı)
   cPanel → phpMyAdmin ile içe aktarın.
3. `public/storage` bağlantısı için: File Manager'da `storage/app/public` klasörünü `public/storage` olarak kopyalayın
   (bağlantı yerine kopya; yeni yüklemeler için hosting firmasından `php artisan storage:link` çalıştırmasını isteyin).
4. Yine de `legacy:download` için bir kerelik terminal erişimi gerekir; hosting desteğinden isteyin.

---

## 8. Güncelleme

```bash
cd ~/llm-site
php artisan down
# yeni paketi açın (.env ve storage/ klasörünü koruyun)
php artisan migrate --force
php artisan optimize
php artisan up
```

## 9. Yedekleme

- **Veritabanı:** cPanel → "Backup" ya da günlük `mysqldump` cron'u.
- **Yüklenen dosyalar:** `llm-site/storage/app/public/` klasörü.

## 10. Geliştirme ortamı

```bash
cd site
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed       # local ortamda deneme yöneticisi: admin@example.com / password
php artisan storage:link
php artisan serve
php artisan test                 # 59 test
```
