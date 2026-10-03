<?php

namespace App\Filament\Resources\DiseaseResource\Pages;

use App\Filament\Resources\DiseaseResource;
use App\Filament\Support\FillsTranslations;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDisease extends EditRecord
{
    use FillsTranslations;

    protected static string $resource = DiseaseResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
