<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Filament\Support\CsvExport;
use App\Models\NewsletterSubscriber;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Başvurular';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Abone';

    protected static ?string $pluralModelLabel = 'Bülten aboneleri';

    protected static ?string $navigationLabel = 'Bülten aboneleri';

    public static function canCreate(): bool
    {
        return false;
    }

    /** Filament'in Her Kelimeyi Büyük yazmasını engelle (Türkçe başlık düzeni) */
    public static function getTitleCasePluralModelLabel(): string
    {
        return static::getPluralModelLabel();
    }

    public static function getTitleCaseModelLabel(): string
    {
        return static::getModelLabel();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('email')->label('E-posta')->searchable()->copyable(),
                TextColumn::make('locale')->label('Dil'),
                TextColumn::make('consent_at')->label('Onay tarihi')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('unsubscribed_at')->label('Ayrıldı')->dateTime('d.m.Y')->placeholder('—'),
            ])
            ->filters([TernaryFilter::make('active')->label('Aktif aboneler')->nullable()
                ->queries(true: fn ($q) => $q->whereNull('unsubscribed_at'), false: fn ($q) => $q->whereNotNull('unsubscribed_at'))])
            ->recordActions([DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->headerActions([
                CsvExport::make('bulten-aboneleri', fn () => NewsletterSubscriber::query()->whereNull('unsubscribed_at'), [
                    'E-posta' => 'email', 'Dil' => 'locale', 'Onay' => fn ($r) => $r->consent_at?->format('d.m.Y H:i'),
                    'Ayrılma bağlantısı' => fn ($r) => lroute('newsletter.unsubscribe', $r->token, $r->locale ?: 'tr'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListNewsletterSubscribers::route('/')];
    }
}
