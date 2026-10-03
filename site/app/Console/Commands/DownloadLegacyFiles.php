<?php

namespace App\Console\Commands;

use App\Models\Publication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Eski sitedeki büyük dosyaları (LLMBİR Bülten PDF'leri, sunumlar) yeni sunucuya indirir.
 * Bu dosyalar git deposunda tutulmaz (toplam ~800 MB).
 *
 * ALAN ADI YENİ SİTEYE YÖNLENDİRİLMEDEN ÖNCE çalıştırılmalıdır:
 *   php artisan legacy:download
 * Tekrar çalıştırılabilir; inmiş dosyaları atlar.
 */
class DownloadLegacyFiles extends Command
{
    protected $signature = 'legacy:download {--dry-run : Yalnızca listele, indirme}';

    protected $description = 'Eski siteden bülten PDF\'lerini ve büyük belgeleri indirir';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $failed = 0;

        // 1) Bülten ve broşürler
        $pubs = Publication::query()->whereNull('file')->where('external_url', 'like', '%losemilenfomamiyelom.org%')->get();
        $this->info("Yayınlar: {$pubs->count()} dosya");
        foreach ($pubs as $pub) {
            $name = 'yayinlar/'.Str::slug($pub->getTranslation('title', 'tr')).'.pdf';
            $this->line("  {$pub->external_url} → {$name}");
            if ($this->option('dry-run')) {
                continue;
            }
            if ($this->download($pub->external_url, $name)) {
                $pub->update(['file' => $name, 'external_url' => null]);
            } else {
                $failed++;
            }
        }

        // 2) İçeriklerde bağlantısı olan büyük belgeler (sunumlar vb.)
        $docs = collect(json_decode(File::get(database_path('legacy/documents.json')), true))->where('in_repo', false);
        $this->info("Belgeler: {$docs->count()} dosya");
        foreach ($docs as $doc) {
            $name = 'legacy-arsiv/'.$doc['name'];
            $this->line("  {$doc['url']} → {$name}");
            if ($this->option('dry-run') || $disk->exists($name)) {
                continue;
            }
            $this->download($doc['url'], $name) || $failed++;
        }

        $failed ? $this->error("{$failed} dosya indirilemedi; komutu yeniden çalıştırın.") : $this->info('Tamamlandı.');

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function download(string $url, string $path): bool
    {
        try {
            $tmp = tempnam(sys_get_temp_dir(), 'llm');
            $res = Http::timeout(600)->withOptions(['sink' => $tmp])->retry(2, 3000)->get($url);
            if (! $res->successful() || filesize($tmp) < 1000) {
                $this->warn("    başarısız ({$res->status()})");

                return false;
            }
            Storage::disk('public')->put($path, fopen($tmp, 'r'));
            @unlink($tmp);

            return true;
        } catch (\Throwable $e) {
            $this->warn('    hata: '.$e->getMessage());

            return false;
        }
    }
}
