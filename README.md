# Lösemi Lenfoma Miyelom Derneği — web sitesi

losemilenfomamiyelom.org için yeni site.

| Klasör | İçerik |
|---|---|
| [`site/`](site/) | **Canlı site**: Laravel 12 + Filament 4 yönetim paneli, TR/EN. Kurulum: [`site/KURULUM.md`](site/KURULUM.md) |
| [`tasarim/`](tasarim/) | Onaylanan arayüz tasarımının statik HTML prototipi (referans) |

## Öne çıkanlar

- **Hastaya odaklı yapı:** "Şu an neredesiniz?" rehberi (Endişeliyim → Hasta yakınıyım), "Konuşmak ister misiniz?" destek bandı, 7 hastalık sayfası
- **Yönetim paneli (`/admin`, Türkçe):** hastalıklar, rehberler, haber/etkinlikler, hikâyeler, yayınlar; gelen sorular, mesajlar, üyelik başvuruları, bağışlar; site ayarları
- **Çok dilli:** Türkçe (`/`) ve İngilizce (`/en`), dile göre çevrilmiş adresler, hreflang
- **Güvenli bağış:** Garanti BBVA 3D Pay Hosting — kart bilgisi sitede alınmaz, banka yanıtı imzayla doğrulanır
- **Eski siteden geçiş:** 197 sayfa, 394 galeri fotoğrafı, 38 bülten sayısı aktarıldı; eski `/TR,{id}/...` adresleri 301 ile yeni adreslere yönlenir
- **Erişilebilirlik:** yazı boyutu, yüksek kontrast, klavyeyle gezinme, WCAG 2.1 AA hedefi
- **Testler:** `cd site && php artisan test`
