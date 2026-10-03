<?php

namespace App\Filament\Support;

use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * TR/EN sekmeli form alanları.
 * Kullanım: Translatable::tabs(fn (string $l, bool $required) => [TextInput::make("title.$l")->required($required)])
 * Türkçe zorunlu dildir; İngilizce boş bırakılabilir (o zaman İngilizce sitede Türkçe metin + bilgi notu gösterilir).
 */
class Translatable
{
    public static function tabs(callable $fields, string $label = 'İçerik'): Tabs
    {
        return Tabs::make($label)
            ->tabs([
                Tab::make('Türkçe')->icon('heroicon-o-language')->schema($fields('tr', true)),
                Tab::make('English')->icon('heroicon-o-globe-alt')->schema($fields('en', false)),
            ])
            ->persistTabInQueryString()
            ->columnSpanFull();
    }
}
