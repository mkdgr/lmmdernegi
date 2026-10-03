<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'kullanıcı';

    protected static ?string $pluralModelLabel = 'Panel kullanıcıları';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Ad soyad')->required(),
            TextInput::make('email')->label('E-posta')->email()->required()->unique(ignoreRecord: true),
            TextInput::make('password')->label('Şifre')->password()->revealable()
                ->required(fn (string $operation) => $operation === 'create')
                ->rule('min:10')
                ->dehydrated(fn ($state) => filled($state))
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->helperText('En az 10 karakter. Düzenlerken boş bırakırsanız şifre değişmez.'),
            Toggle::make('is_active')->label('Panele girebilir')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ad soyad'),
                TextColumn::make('email')->label('E-posta'),
                IconColumn::make('is_active')->label('Etkin')->boolean(),
                TextColumn::make('created_at')->label('Eklendi')->date('d.m.Y'),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageUsers::route('/')];
    }
}
