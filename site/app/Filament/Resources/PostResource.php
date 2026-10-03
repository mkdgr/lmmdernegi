<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Support\Translatable;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'haber / etkinlik';

    protected static ?string $pluralModelLabel = 'Haberler ve etkinlikler';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
                Section::make('Genel')->columnSpan(2)->columns(2)->schema([
                    Select::make('type')->label('Tür')->options(Post::TYPES)->required()->default('etkinlik')->native(false)->live(),
                    Select::make('audience')->label('Kime yönelik')->options(Post::AUDIENCES)->default('herkes')->native(false),
                    DateTimePicker::make('event_starts_at')->label('Etkinlik başlangıcı')->native(false)->seconds(false)
                        ->visible(fn (Get $get) => $get('type') === 'etkinlik'),
                    DateTimePicker::make('event_ends_at')->label('Etkinlik bitişi')->native(false)->seconds(false)
                        ->visible(fn (Get $get) => $get('type') === 'etkinlik'),
                    TextInput::make('video_url')->label('Canlı yayın / video bağlantısı')->url()->columnSpanFull()
                        ->helperText('YouTube vb. Etkinlik geçtiyse "Kaydı izleyin" olarak görünür.'),
                    TextInput::make('registration_url')->label('Kayıt formu bağlantısı')->url()->columnSpanFull(),
                ]),
                Section::make('Yayın')->columnSpan(1)->schema([
                    Toggle::make('is_published')->label('Yayında')->default(true),
                    Toggle::make('is_featured')->label('Öne çıkar'),
                    DateTimePicker::make('published_at')->label('Yayın tarihi')->native(false)->seconds(false)->default(now()),
                    FileUpload::make('image')->label('Afiş / görsel')->image()->disk('public')->directory('etkinlikler')
                        ->imageEditor()->maxSize(6144),
                ]),
            ]),
            Translatable::tabs(fn (string $l, bool $req) => [
                Grid::make(2)->schema([
                    TextInput::make("title.$l")->label('Başlık')->required($req)->maxLength(200),
                    TextInput::make("slug.$l")->label('Adres (slug)')->maxLength(120),
                ]),
                TextInput::make("location.$l")->label('Yer')->maxLength(200)->helperText('Örn. Divan Otel, Ankara · ya da "Çevrim içi"'),
                Textarea::make("excerpt.$l")->label('Kısa açıklama')->rows(2)->maxLength(400),
                RichEditor::make("body.$l")->label('İçerik')->fileAttachmentsDisk('public')->fileAttachmentsDirectory('icerik'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('image')->label('')->disk('public')->height(48),
                TextColumn::make('title')->label('Başlık')->searchable(query: fn ($q, $s) => $q->where('title->tr', 'like', "%{$s}%"))->wrap()->limit(80),
                TextColumn::make('type')->label('Tür')->formatStateUsing(fn ($state) => Post::TYPES[$state] ?? $state)->badge(),
                TextColumn::make('event_starts_at')->label('Etkinlik tarihi')->date('d.m.Y')->sortable(),
                TextColumn::make('published_at')->label('Yayın')->date('d.m.Y')->sortable()->toggleable(),
                IconColumn::make('is_published')->label('Yayında')->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')->label('Tür')->options(Post::TYPES),
                SelectFilter::make('audience')->label('Kime yönelik')->options(Post::AUDIENCES),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('view')->label('Sitede gör')->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
                    ->url(fn (Post $r) => lroute('news.show', $r->slugFor('tr'), 'tr'))->openUrlInNewTab(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
