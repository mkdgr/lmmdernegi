<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Filament\Support\CsvExport;
use App\Models\Donation;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Bağış kayıtları yalnızca görüntülenir; ödeme durumu bankadan gelen yanıtla değişir. */
class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Başvurular';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Bağış';

    protected static ?string $pluralModelLabel = 'Bağışlar';

    protected static ?string $navigationLabel = 'Bağışlar';

    protected static ?string $recordTitleAttribute = 'order_id';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Bağış')->columns(4)->schema([
                TextEntry::make('order_id')->label('Sipariş no')->copyable(),
                TextEntry::make('status')->label('Durum')->badge()->formatStateUsing(fn ($s) => Donation::STATUSES[$s] ?? $s)
                    ->color(fn ($s) => ['basarili' => 'success', 'basarisiz' => 'danger'][$s] ?? 'warning'),
                TextEntry::make('amount')->label('Tutar')->formatStateUsing(fn ($s) => number_format($s, 0, ',', '.').' ₺'),
                TextEntry::make('paid_at')->label('Ödeme zamanı')->dateTime('d.m.Y H:i')->placeholder('—'),
                TextEntry::make('type')->label('Tür')->formatStateUsing(fn ($s) => Donation::TYPES[$s] ?? $s),
                TextEntry::make('frequency')->label('Sıklık')->formatStateUsing(fn ($s) => Donation::FREQUENCIES[$s] ?? $s),
                TextEntry::make('created_at')->label('Oluşturuldu')->dateTime('d.m.Y H:i'),
                TextEntry::make('locale')->label('Dil'),
            ]),
            Section::make('Bağışçı')->columns(4)->schema([
                TextEntry::make('name')->label('Ad soyad'),
                TextEntry::make('donor_type')->label('Bağışçı türü'),
                TextEntry::make('company')->label('Kurum')->placeholder('—'),
                TextEntry::make('tax_no')->label('Vergi no')->placeholder('—'),
                TextEntry::make('tckn')->label('T.C. kimlik no')->placeholder('—'),
                TextEntry::make('email')->label('E-posta')->copyable(),
                TextEntry::make('phone')->label('Telefon')->placeholder('—'),
                TextEntry::make('is_anonymous')->label('Adı gizli')->formatStateUsing(fn ($s) => $s ? 'Evet' : 'Hayır'),
            ]),
            Section::make('Banka yanıtı')->collapsed()->columns(4)->schema([
                TextEntry::make('auth_code')->label('Onay kodu')->placeholder('—'),
                TextEntry::make('host_ref')->label('Referans')->placeholder('—'),
                TextEntry::make('bank_code')->label('Dönüş kodu')->placeholder('—'),
                TextEntry::make('masked_pan')->label('Kart')->placeholder('—'),
                TextEntry::make('bank_message')->label('Mesaj')->columnSpanFull()->placeholder('—'),
                KeyValueEntry::make('bank_response')->label('Ham yanıt')->columnSpanFull(),
            ]),
        ]);
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
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('name')->label('Bağışçı')->searchable()
                    ->description(fn (Donation $r) => $r->is_anonymous ? 'Adı gizli kalsın' : null),
                TextColumn::make('type')->label('Tür')->formatStateUsing(fn ($s) => Donation::TYPES[$s] ?? $s),
                TextColumn::make('amount')->label('Tutar')->formatStateUsing(fn ($s) => number_format($s, 0, ',', '.').' ₺')->sortable(),
                IconColumn::make('frequency')->label('Aylık')->boolean()->state(fn (Donation $r) => $r->frequency === 'aylik'),
                TextColumn::make('status')->label('Durum')->badge()->formatStateUsing(fn ($s) => Donation::STATUSES[$s] ?? $s)
                    ->color(fn ($s) => ['basarili' => 'success', 'basarisiz' => 'danger'][$s] ?? 'warning'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Durum')->options(Donation::STATUSES),
                SelectFilter::make('type')->label('Tür')->options(Donation::TYPES),
            ])
            ->recordActions([ViewAction::make()])
            ->headerActions([
                CsvExport::make('bagislar', fn () => Donation::query()->latest(), [
                    'Tarih' => fn ($r) => $r->created_at->format('d.m.Y H:i'), 'Sipariş' => 'order_id', 'Durum' => 'status',
                    'Tür' => 'type', 'Sıklık' => 'frequency', 'Tutar' => 'amount', 'Ad soyad' => 'name', 'Kurum' => 'company',
                    'T.C.' => 'tckn', 'Vergi no' => 'tax_no', 'E-posta' => 'email', 'Telefon' => 'phone',
                    'Adı gizli' => fn ($r) => $r->is_anonymous ? 'evet' : 'hayır', 'Onay kodu' => 'auth_code',
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonations::route('/'),
            'view' => Pages\ViewDonation::route('/{record}'),
        ];
    }
}
