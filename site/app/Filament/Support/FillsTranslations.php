<?php

namespace App\Filament\Support;

/**
 * Düzenleme sayfaları için: Filament formu doldururken çevrilebilir alanların
 * tüm dillerini ({"tr": "...", "en": "..."}) yükler.
 */
trait FillsTranslations
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($this->getRecord()->getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $this->getRecord()->getTranslations($attribute);
        }

        return $data;
    }
}
