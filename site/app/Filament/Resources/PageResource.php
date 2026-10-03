<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\Translatable;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Sayfa';

    protected static ?string $pluralModelLabel = 'Sayfalar ve rehberler';

    protected static ?string $navigationLabel = 'Sayfalar ve rehberler';

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
            Section::make('Genel')->columns(3)->schema([
                Select::make('section')->label('Bölüm')->options(Page::SECTIONS)->required()->native(false)
                    ->helperText('"Hasta rehberi" sayfaları ana sayfadaki "Şu an neredesiniz?" alanında görünür.'),
                TextInput::make('sort')->label('Sıra')->numeric()->default(0),
                Toggle::make('is_published')->label('Yayında')->default(true)->inline(false),
                FileUpload::make('image')->label('Kapak görseli')->image()->disk('public')->directory('sayfalar')
                    ->imageEditor()->maxSize(4096)->columnSpanFull(),
            ]),
            Translatable::tabs(fn (string $l, bool $req) => [
                Grid::make(2)->schema([
                    TextInput::make("title.$l")->label('Başlık')->required($req)->maxLength(160),
                    TextInput::make("slug.$l")->label('Adres (slug)')->maxLength(120)->helperText('Boş bırakırsanız başlıktan üretilir.'),
                ]),
                Textarea::make("summary.$l")->label('Kısa özet')->rows(2)->maxLength(400),
                RichEditor::make("body.$l")->label('İçerik')->fileAttachmentsDisk('public')->fileAttachmentsDirectory('icerik'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('section')
            ->columns([
                TextColumn::make('title')->label('Başlık')->searchable(query: fn ($q, $s) => $q->where('title->tr', 'like', "%{$s}%"))->wrap(),
                TextColumn::make('section')->label('Bölüm')->formatStateUsing(fn ($state) => Page::SECTIONS[$state] ?? $state)->badge(),
                TextColumn::make('sort')->label('Sıra')->sortable(),
                IconColumn::make('has_en')->label('EN')->boolean()->state(fn (Page $r) => $r->hasLocale('en')),
                IconColumn::make('is_published')->label('Yayında')->boolean(),
                TextColumn::make('updated_at')->label('Güncellendi')->since()->sortable(),
            ])
            ->filters([SelectFilter::make('section')->label('Bölüm')->options(Page::SECTIONS)])
            ->recordActions([
                EditAction::make(),
                Action::make('view')->label('Sitede gör')->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
                    ->url(fn (Page $r) => in_array($r->section, ['rehber', 'destek']) ? lroute('support.show', $r->slugFor('tr'), 'tr') : lroute('page', $r->slugFor('tr'), 'tr'))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
