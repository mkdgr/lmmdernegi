# Arayüz prototipi

Lösemi Lenfoma Miyelom Derneği web sitesinin yeni arayüzü için tıklanabilir HTML/CSS prototipi.
Arayüz onaylandıktan sonra bu dosyalar Laravel + Filament projesine Blade şablonları olarak taşınacak.

## Sayfalar

| Dosya | İçerik |
|---|---|
| `index.html` | Ana sayfa: hızlı bağış, "size nasıl yardımcı olabiliriz" kartları, hastalıklar, etkinlik, haberler, hikâye, bülten |
| `hastalik-aml.html` | Hastalık sayfası şablonu (7 hastalık bu şablonu kullanacak) |
| `bagis.html` | Online bağış ve aidat — kart bilgisi **sitede alınmaz**, Garanti BBVA 3D Pay Hosting sayfasına yönlendirilir |
| `haberler.html` | Haber ve etkinlik listesi, kategori filtresi |
| `en/index.html` | İngilizce ana sayfa |

Tarayıcıda doğrudan `index.html` dosyasını açmanız yeterli.

## Düzenleme

`*.html` dosyaları **üretilmiş** dosyalardır; doğrudan düzenlemeyin.

- Sayfa içerikleri: `_src/pages/`
- Ortak parçalar (header, footer, ikonlar): `_src/partials/`
- Stil ve tasarım değişkenleri: `assets/css/main.css`
- Etkileşimler: `assets/js/main.js`

Değişiklikten sonra: `python3 tasarim/build.py`

## Tasarım sistemi

- Kurumsal mavi `#005495`, aksan turuncu `#F28425` (eski sitedeki kurumsal renkler)
- Yazı tipi: Nunito (başlık) + Nunito Sans (metin) — Türkçe karakter desteği tam
- Erişilebilirlik: yazı boyutu (A / A+ / A++), yüksek kontrast, klavye ile gezinme, "içeriğe geç" bağlantısı, WCAG AA hedefi
- Mobil öncelikli; 360px'ten geniş ekranlara kadar test edildi
