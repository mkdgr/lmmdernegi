#!/usr/bin/env bash
# cPanel'e yüklenecek, vendor/ dahil yayın paketini üretir: ../llm-site-YYYYMMDD.zip
# .env, veritabanı, loglar ve yüklenen dosyalar pakete girmez.
set -euo pipefail
cd "$(dirname "$0")/.."
ROOT="$(pwd)"
OUT="$(cd .. && pwd)/llm-site-$(date +%Y%m%d).zip"
BUILD="$(mktemp -d)"

echo "→ Dosyalar kopyalanıyor"
mkdir -p "$BUILD/llm-site"
tar -C "$ROOT" -cf - \
  --exclude ./.env --exclude ./vendor --exclude ./node_modules --exclude ./.git \
  --exclude './storage/logs/*.log' --exclude './storage/framework/cache/data/*' --exclude './storage/framework/sessions/*' \
  --exclude './storage/framework/views/*.php' --exclude './storage/app/public/*' --exclude './database/*.sqlite' \
  --exclude ./public/storage --exclude './bootstrap/cache/*.php' --exclude ./tests --exclude ./deploy \
  . | tar -C "$BUILD/llm-site" -xf -

echo "→ Üretim bağımlılıkları kuruluyor"
(cd "$BUILD/llm-site" && composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --quiet && php artisan filament:assets --quiet && find vendor -name .git -type d -prune -exec rm -rf {} +)

echo "→ Paket oluşturuluyor"
(cd "$BUILD" && rm -f "$OUT" && zip -qr "$OUT" llm-site)
rm -rf "$BUILD"
echo "✓ $OUT ($(du -h "$OUT" | cut -f1))"
