# Arayüz prototipi

Lösemi Lenfoma Miyelom Derneği web sitesinin yeni arayüzü için tıklanabilir HTML/CSS prototipi.
Arayüz onaylandıktan sonra bu dosyalar Laravel + Filament projesine Blade şablonları olarak taşınacak.

## Sayfalar

| Dosya | İçerik |
|---|---|
| `index.html` | Ana sayfa: arama, "Şu an neredesiniz?" yolculuk menüsü, "Konuşmak ister misiniz?" destek bandı, hastalıklar, hikâyeler, etkinlikler, destek olun |
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

- Yön: "sakin ama kendinden emin" — Lymphoma Action'ın hastaya göre kurgusu + Blood Cancer UK / DKMS'in sade, cesur görsel dili
- Düz renk blokları; degrade ve gölge yok. Kurumsal mavi `#005495` baskın, turuncu `#F28425` yalnızca eylem rengi
- Yazı tipi: Bricolage Grotesque (başlık) + Figtree (metin) — Türkçe karakter desteği tam
- Taralı `photo--empty` alanları dernek arşivinden gelecek fotoğraflar için yer tutucu
- Erişilebilirlik: yazı boyutu (A / A+ / A++), yüksek kontrast, klavye ile gezinme, "içeriğe geç" bağlantısı, WCAG AA hedefi
- Mobil öncelikli; 360px'ten geniş ekranlara kadar test edildi
