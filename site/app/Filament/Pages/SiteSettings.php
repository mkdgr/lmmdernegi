<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** İletişim bilgileri, banka hesapları, sosyal medya ve ana sayfa ayarları. */
class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Site ayarları';

    protected static ?string $title = 'Site ayarları';

    protected string $view = 'filament.pages.settings';

    /** Basit (tek değerli) ayar anahtarları */
    private const SCALARS = ['phone', 'whatsapp', 'email', 'notify_email', 'social_instagram', 'social_facebook', 'social_x', 'social_youtube', 'home_hero_photo'];

    /** Çevrilebilir ayarlar: {"tr": "...", "en": "..."} */
    private const TRANSLATED = ['phone_hours', 'address'];

    private const LISTS = ['bank_accounts', 'home_facts'];

    public ?array $data = [];

    public function mount(): void
    {
        $raw = Setting::allValues();
        $data = [];
        foreach (self::SCALARS as $k) {
            $data[$k] = $raw[$k]['v'] ?? null;
        }
        foreach (array_merge(self::TRANSLATED, self::LISTS) as $k) {
            $data[$k] = $raw[$k] ?? [];
        }
        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('İletişim')->columns(2)->schema([
                TextInput::make('phone')->label('Telefon')->required()->placeholder('0530 156 87 68'),
                TextInput::make('whatsapp')->label('WhatsApp hattı')->placeholder('0530 156 87 68')
                    ->helperText('Doluysa ana sayfada WhatsApp düğmesi görünür.'),
                TextInput::make('phone_hours.tr')->label('Arama saatleri (TR)')->placeholder('Hafta içi 09.00–17.00'),
                TextInput::make('phone_hours.en')->label('Arama saatleri (EN)')->placeholder('Weekdays 09:00–17:00'),
                TextInput::make('email')->label('E-posta')->email()->required(),
                TextInput::make('notify_email')->label('Bildirim e-postası')->email()
                    ->helperText('Form başvuruları ve bağış bildirimleri bu adrese gider.'),
                TextInput::make('address.tr')->label('Adres (TR)'),
                TextInput::make('address.en')->label('Adres (EN)'),
            ]),
            Section::make('Banka hesapları')->description('Bağış sayfasında ve İngilizce sitede görünür.')->schema([
                Repeater::make('bank_accounts')->hiddenLabel()->addActionLabel('Hesap ekle')->columns(3)->schema([
                    TextInput::make('bank')->label('Banka / şube')->required(),
                    TextInput::make('iban')->label('IBAN')->required(),
                    TextInput::make('swift')->label('SWIFT'),
                ]),
            ]),
            Section::make('Sosyal medya')->columns(2)->schema([
                TextInput::make('social_instagram')->label('Instagram')->url(),
                TextInput::make('social_facebook')->label('Facebook')->url(),
                TextInput::make('social_x')->label('X (Twitter)')->url(),
                TextInput::make('social_youtube')->label('YouTube')->url(),
            ]),
            Section::make('Ana sayfa')->schema([
                FileUpload::make('home_hero_photo')->label('Üst bölüm fotoğrafı')->image()->disk('public')->directory('ana-sayfa')
                    ->imageEditor()->maxSize(6144)
                    ->helperText('Dikey ya da kare, sıcak bir fotoğraf önerilir (ör. hasta buluşmasından). En az 900 px genişlik.'),
                Repeater::make('home_facts')->label('Rakamlarla derneğimiz')->addActionLabel('Rakam ekle')->columns(3)->maxItems(4)->schema([
                    TextInput::make('value')->label('Rakam')->required()->placeholder('2011'),
                    TextInput::make('label.tr')->label('Açıklama (TR)')->required()->placeholder('yılından beri'),
                    TextInput::make('label.en')->label('Açıklama (EN)')->placeholder('since'),
                ]),
            ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach (self::SCALARS as $k) {
            $v = $data[$k] ?? null;
            Setting::put($k, is_array($v) ? (array_values($v)[0] ?? null) : $v);
        }
        foreach (self::TRANSLATED as $k) {
            Setting::put($k, (array) ($data[$k] ?? []));
        }
        foreach (self::LISTS as $k) {
            Setting::put($k, array_values((array) ($data[$k] ?? [])));
        }

        Notification::make()->title('Ayarlar kaydedildi')->success()->send();
    }
}
