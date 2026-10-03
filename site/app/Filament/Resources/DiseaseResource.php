<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiseaseResource\Pages;
use App\Filament\Support\Translatable;
use App\Models\Disease;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
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
use Filament\Tables\Table;
use UnitEnum;

class DiseaseResource extends Resource
{
    protected static ?string $model = Disease::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Hastalık';

    protected static ?string $pluralModelLabel = 'Hastalıklar';

    protected static ?string $navigationLabel = 'Hastalıklar';

    protected static ?string $recordTitleAttribute = 'name';

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
            Section::make('Genel')->columns(4)->schema([
                TextInput::make('abbr')->label('Kısaltma (TR)')->required()->maxLength(12)->helperText('Örn. AML, KML'),
                TextInput::make('abbr_translated.en')->label('Kısaltma (EN)')->maxLength(12)->helperText('Farklıysa: KML → CML'),
                Select::make('group')->label('Grup')->options(Disease::GROUPS)->required()->native(false),
                TextInput::make('sort')->label('Sıra')->numeric()->default(0),
                Toggle::make('is_published')->label('Yayında')->default(true),
                TextInput::make('reviewed_by')->label('Gözden geçiren hekim')->columnSpan(2),
                DatePicker::make('reviewed_at')->label('Gözden geçirme tarihi')->native(false),
            ]),
            Translatable::tabs(fn (string $l, bool $req) => [
                Grid::make(2)->schema([
                    TextInput::make("name.$l")->label('Hastalık adı')->required($req)->maxLength(160),
                    TextInput::make("slug.$l")->label('Adres (slug)')->maxLength(120)->helperText('Boş bırakırsanız addan üretilir.'),
                ]),
                Textarea::make("summary.$l")->label('Kısa özet')->rows(2)->maxLength(400)->helperText('Hastalık listesinde ve sayfanın başında görünür.'),
                RichEditor::make("body.$l")->label('Sayfa içeriği')
                    ->helperText('Ara başlıkları "Başlık 2" yapın; sayfanın yanındaki "Bu sayfada" menüsü bu başlıklardan oluşur.')
                    ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('icerik'),
            ]),
            Section::make('Sıkça sorulan sorular')->collapsible()->schema([
                Repeater::make('faq')->hiddenLabel()->addActionLabel('Soru ekle')->collapsible()
                    ->itemLabel(fn (array $state) => $state['question']['tr'] ?? null)
                    ->schema([
                        TextInput::make('question.tr')->label('Soru (TR)')->required(),
                        Textarea::make('answer.tr')->label('Yanıt (TR)')->rows(3)->required(),
                        TextInput::make('question.en')->label('Question (EN)'),
                        Textarea::make('answer.en')->label('Answer (EN)')->rows(3),
                    ])->columns(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('abbr')->label('Kısaltma')->weight('bold'),
                TextColumn::make('name')->label('Ad')->searchable(query: fn ($q, $search) => $q->where('name->tr', 'like', "%{$search}%")),
                TextColumn::make('group')->label('Grup')->formatStateUsing(fn ($state) => Disease::GROUPS[$state] ?? $state)->badge(),
                IconColumn::make('has_en')->label('EN')->boolean()->state(fn (Disease $r) => filled($r->getTranslation('body', 'en', false))),
                IconColumn::make('is_published')->label('Yayında')->boolean(),
                TextColumn::make('updated_at')->label('Güncellendi')->since()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('view')->label('Sitede gör')->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
                    ->url(fn (Disease $r) => lroute('diseases.show', $r->slugFor('tr'), 'tr'))->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiseases::route('/'),
            'create' => Pages\CreateDisease::route('/create'),
            'edit' => Pages\EditDisease::route('/{record}/edit'),
        ];
    }
}
