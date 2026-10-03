<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Başvurular';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Mesaj';

    protected static ?string $pluralModelLabel = 'İletişim mesajları';

    protected static ?string $navigationLabel = 'İletişim mesajları';

    public static function getNavigationBadge(): ?string
    {
        $n = ContactMessage::where('status', 'yeni')->count();

        return $n ? (string) $n : null;
    }

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

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(3)->schema([
                TextEntry::make('name')->label('Ad soyad'),
                TextEntry::make('email')->label('E-posta')->copyable(),
                TextEntry::make('phone')->label('Telefon')->placeholder('—'),
                TextEntry::make('subject')->label('Konu')->placeholder('—'),
                TextEntry::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i'),
                Select::make('status')->label('Durum')->options(ContactMessage::STATUSES)->native(false),
                TextEntry::make('message')->label('Mesaj')->columnSpanFull()->prose(),
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
                TextColumn::make('subject')->label('Konu')->placeholder('—')->limit(40),
                TextColumn::make('message')->label('Mesaj')->limit(60)->searchable(),
                TextColumn::make('status')->label('Durum')->badge()->formatStateUsing(fn ($s) => ContactMessage::STATUSES[$s] ?? $s)
                    ->color(fn ($s) => $s === 'yeni' ? 'warning' : 'gray'),
            ])
            ->filters([SelectFilter::make('status')->label('Durum')->options(ContactMessage::STATUSES)])
            ->recordActions([
                EditAction::make()->label('Aç'),
                Action::make('mail')->label('Yanıtla')->icon('heroicon-o-envelope')->color('gray')->url(fn (ContactMessage $r) => 'mailto:'.$r->email),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
