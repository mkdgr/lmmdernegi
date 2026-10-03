<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipApplicationResource\Pages;
use App\Filament\Support\CsvExport;
use App\Models\MembershipApplication;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class MembershipApplicationResource extends Resource
{
    protected static ?string $model = MembershipApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Başvurular';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'üyelik başvurusu';

    protected static ?string $pluralModelLabel = 'Üyelik başvuruları';

    public static function getNavigationBadge(): ?string
    {
        $n = MembershipApplication::where('status', 'yeni')->count();

        return $n ? (string) $n : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                TextEntry::make('name')->label('Ad soyad'),
                TextEntry::make('tckn')->label('T.C. kimlik no')->placeholder('—'),
                TextEntry::make('birth_date')->label('Doğum tarihi')->date('d.m.Y')->placeholder('—'),
                TextEntry::make('email')->label('E-posta')->copyable(),
                TextEntry::make('phone')->label('Telefon'),
                TextEntry::make('city')->label('Şehir')->placeholder('—'),
                TextEntry::make('occupation')->label('Meslek')->placeholder('—'),
                TextEntry::make('relation')->label('Bağı')->formatStateUsing(fn ($s) => MembershipApplication::RELATIONS[$s] ?? $s)->placeholder('—'),
                TextEntry::make('created_at')->label('Başvuru tarihi')->dateTime('d.m.Y H:i'),
                TextEntry::make('address')->label('Adres')->columnSpanFull()->placeholder('—'),
                TextEntry::make('note')->label('Not')->columnSpanFull()->placeholder('—'),
                Select::make('status')->label('Durum')->options(MembershipApplication::STATUSES)->native(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Tarih')->date('d.m.Y')->sortable(),
                TextColumn::make('name')->label('Ad soyad')->searchable(),
                TextColumn::make('email')->label('E-posta')->searchable(),
                TextColumn::make('phone')->label('Telefon'),
                TextColumn::make('city')->label('Şehir'),
                TextColumn::make('status')->label('Durum')->badge()->formatStateUsing(fn ($s) => MembershipApplication::STATUSES[$s] ?? $s)
                    ->color(fn ($s) => ['yeni' => 'warning', 'onaylandi' => 'success', 'reddedildi' => 'danger'][$s] ?? 'gray'),
            ])
            ->filters([SelectFilter::make('status')->label('Durum')->options(MembershipApplication::STATUSES)])
            ->recordActions([EditAction::make()->label('Aç')])
            ->headerActions([
                CsvExport::make('uyelik-basvurulari', fn () => MembershipApplication::query()->latest(), [
                    'Tarih' => fn ($r) => $r->created_at->format('d.m.Y'), 'Ad soyad' => 'name', 'T.C.' => 'tckn',
                    'E-posta' => 'email', 'Telefon' => 'phone', 'Şehir' => 'city', 'Meslek' => 'occupation', 'Durum' => 'status',
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembershipApplications::route('/'),
            'edit' => Pages\EditMembershipApplication::route('/{record}/edit'),
        ];
    }
}
