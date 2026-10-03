<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedirectResource\Pages;
use App\Models\Redirect;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnRight;

    protected static string|UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'yönlendirme';

    protected static ?string $pluralModelLabel = 'Yönlendirmeler';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('from_path')->label('Eski adres')->required()->placeholder('/eski-sayfa.html')
                ->helperText('Alan adı olmadan, / ile başlayan yol. Eski sitenin /TR,23/... adresleri zaten otomatik yönlendirilir.')
                ->unique(ignoreRecord: true),
            TextInput::make('to_path')->label('Yeni adres')->required()->placeholder('/hastaliklar/akut-miyeloid-losemi'),
            Select::make('status_code')->label('Tür')->options([301 => 'Kalıcı (301)', 302 => 'Geçici (302)'])->default(301)->native(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_path')->label('Eski adres')->searchable(),
                TextColumn::make('to_path')->label('Yeni adres')->searchable(),
                TextColumn::make('status_code')->label('Kod'),
                TextColumn::make('hits')->label('Kullanım')->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageRedirects::route('/')];
    }
}
