<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Support\CsvExport;
use App\Models\Question;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Başvurular';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'soru';

    protected static ?string $pluralModelLabel = 'Uzmana sorulanlar';

    public static function getNavigationBadge(): ?string
    {
        $n = Question::where('status', 'yeni')->count();

        return $n ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Gelen soru')->columns(3)->schema([
                TextEntry::make('name')->label('Ad soyad'),
                TextEntry::make('email')->label('E-posta')->copyable(),
                TextEntry::make('phone')->label('Telefon')->placeholder('—'),
                TextEntry::make('relation')->label('Kim soruyor')->formatStateUsing(fn ($s) => Question::RELATIONS[$s] ?? $s),
                TextEntry::make('disease.name')->label('Hastalık')->placeholder('—'),
                TextEntry::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i'),
                TextEntry::make('question')->label('Soru')->columnSpanFull()->prose(),
            ]),
            Section::make('Yanıt')->schema([
                Select::make('status')->label('Durum')->options(Question::STATUSES)->required()->native(false),
                Textarea::make('answer')->label('Yanıt notu')->rows(8)
                    ->helperText('Yanıtı soruyu sorana e-posta ile gönderin ("E-posta ile yanıtla" düğmesi); buraya kayıt için yazın.'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('name')->label('Ad soyad')->searchable(),
                TextColumn::make('disease.abbr')->label('Hastalık')->placeholder('—'),
                TextColumn::make('question')->label('Soru')->limit(70)->wrap()->searchable(),
                TextColumn::make('status')->label('Durum')->badge()
                    ->formatStateUsing(fn ($s) => Question::STATUSES[$s] ?? $s)
                    ->color(fn ($s) => ['yeni' => 'warning', 'yanitlandi' => 'success'][$s] ?? 'gray'),
            ])
            ->filters([SelectFilter::make('status')->label('Durum')->options(Question::STATUSES)])
            ->recordActions([
                EditAction::make()->label('Aç'),
                Action::make('mail')->label('E-posta ile yanıtla')->icon('heroicon-o-envelope')->color('gray')
                    ->url(fn (Question $r) => 'mailto:'.$r->email.'?subject='.rawurlencode('Sorunuz hakkında — Lösemi Lenfoma Miyelom Derneği')),
            ])
            ->headerActions([
                CsvExport::make('sorular', fn () => Question::query()->with('disease')->latest(), [
                    'Tarih' => fn ($r) => $r->created_at->format('d.m.Y H:i'), 'Ad soyad' => 'name', 'E-posta' => 'email',
                    'Telefon' => 'phone', 'Hastalık' => 'disease.abbr', 'Soru' => 'question', 'Durum' => 'status',
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
