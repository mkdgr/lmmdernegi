<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicationResource\Pages;
use App\Models\Publication;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PublicationResource extends Resource
{
    protected static ?string $model = Publication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Yayın';

    protected static ?string $pluralModelLabel = 'Yayınlar ve bülten';

    protected static ?string $navigationLabel = 'Yayınlar ve bülten';

    protected static ?string $recordTitleAttribute = 'title';

    /** Filament'in Her Kelimeyi Büyük yazmasını engelle (Türkçe başlık düzeni) */
    public static function getTitleCasePluralModelLabel(): string
    {
        return static::getPluralModelLabel();
    }

    public static function getTitleCaseModelLabel(): string
    {
        return static::getModelLabel();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(2)->schema([
                Select::make('kind')->label('Tür')->options(Publication::KINDS)->required()->native(false),
                TextInput::make('issue_no')->label('Sayı no')->numeric(),
                TextInput::make('title.tr')->label('Başlık (TR)')->required(),
                TextInput::make('title.en')->label('Title (EN)'),
                Textarea::make('description.tr')->label('Açıklama (TR)')->rows(2),
                Textarea::make('description.en')->label('Description (EN)')->rows(2),
                FileUpload::make('file')->label('PDF dosyası')->disk('public')->directory('yayinlar')
                    ->acceptedFileTypes(['application/pdf'])->maxSize(102400)->columnSpanFull(),
                TextInput::make('external_url')->label('ya da dış bağlantı')->url()->columnSpanFull()
                    ->helperText('Dosya yüklenmediyse bu bağlantı kullanılır.'),
                DatePicker::make('published_on')->label('Yayın tarihi')->native(false),
                Toggle::make('is_published')->label('Yayında')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('issue_no', 'desc')
            ->columns([
                TextColumn::make('title')->label('Başlık')->wrap(),
                TextColumn::make('kind')->label('Tür')->formatStateUsing(fn ($s) => Publication::KINDS[$s] ?? $s)->badge(),
                TextColumn::make('issue_no')->label('Sayı')->sortable(),
                IconColumn::make('file')->label('Dosya')->boolean()->state(fn (Publication $r) => filled($r->file)),
                IconColumn::make('is_published')->label('Yayında')->boolean(),
            ])
            ->filters([SelectFilter::make('kind')->label('Tür')->options(Publication::KINDS)])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPublications::route('/'),
            'create' => Pages\CreatePublication::route('/create'),
            'edit' => Pages\EditPublication::route('/{record}/edit'),
        ];
    }
}
