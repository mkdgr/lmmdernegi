<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoryResource\Pages;
use App\Filament\Support\Translatable;
use App\Models\Story;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class StoryResource extends Resource
{
    protected static ?string $model = Story::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Hikâye';

    protected static ?string $pluralModelLabel = 'Hikâyeler';

    protected static ?string $navigationLabel = 'Hikâyeler';

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
            Section::make('Hikâye sahibi')->columns(3)->schema([
                TextInput::make('person_name')->label('Ad (sitede görünecek hâli)')->required()->maxLength(120)
                    ->helperText('Örn. "Ayşe K." — tam ad için açık izin alın.'),
                Select::make('kind')->label('Tür')->options(Story::KINDS)->required()->native(false),
                FileUpload::make('photo')->label('Fotoğraf')->image()->disk('public')->directory('hikayeler')->imageEditor()->maxSize(4096),
                Toggle::make('has_consent')->label('Yazılı yayın izni alındı')
                    ->accepted(fn (Get $get) => (bool) $get('is_published'))
                    ->validationMessages(['accepted' => 'Yayın izni alınmadan hikâye yayınlanamaz.'])
                    ->helperText('KVKK: Sağlık verisi içerdiği için açık rıza olmadan yayınlamayın.'),
                Toggle::make('is_published')->label('Yayında')->live(),
                Toggle::make('is_featured')->label('Ana sayfada öne çıkar'),
                DateTimePicker::make('published_at')->label('Yayın tarihi')->native(false)->seconds(false)->default(now()),
            ]),
            Translatable::tabs(fn (string $l, bool $req) => [
                Grid::make(2)->schema([
                    TextInput::make("title.$l")->label('Başlık')->required($req)->maxLength(160),
                    TextInput::make("condition.$l")->label('Hastalık / durum')->maxLength(120)->helperText('Örn. "KML, 12 yıldır tedavide"'),
                ]),
                Textarea::make("quote.$l")->label('Öne çıkan alıntı')->rows(2)->maxLength(300)->helperText('Ana sayfa kartında görünür.'),
                RichEditor::make("body.$l")->label('Hikâye'),
                TextInput::make("slug.$l")->label('Adres (slug)')->maxLength(120),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                TextColumn::make('title')->label('Başlık')->wrap(),
                TextColumn::make('person_name')->label('Kişi'),
                TextColumn::make('kind')->label('Tür')->formatStateUsing(fn ($s) => Story::KINDS[$s] ?? $s)->badge(),
                IconColumn::make('has_consent')->label('İzin')->boolean(),
                IconColumn::make('is_published')->label('Yayında')->boolean(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStories::route('/'),
            'create' => Pages\CreateStory::route('/create'),
            'edit' => Pages\EditStory::route('/{record}/edit'),
        ];
    }
}
